<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PlaystationUnit;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ReportService
{
    public function __construct(private readonly SettingService $settings) {}

    /**
     * The periods supported by the report screen.
     *
     * @var array<string, string>
     */
    public const PERIODS = [
        'today' => 'Hari ini',
        'week' => 'Minggu ini',
        'month' => 'Bulan ini',
        'year' => 'Tahun ini',
        'custom' => 'Rentang khusus',
    ];

    /**
     * Build all data used by the HTML, CSV and PDF reports.
     *
     * Revenue is deliberately calculated from paid payments rather than from
     * booking totals. This keeps the report aligned with money actually
     * received and excludes pending, failed and refunded payments.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getReportData(array $filters = []): array
    {
        $period = $this->resolvePeriod($filters);
        $from = $period['from'];
        $to = $period['to'];

        $bookingQuery = Booking::query()->whereBetween('start_at', [$from, $to]);

        // Aggregate the status counters in one query. CASE WHEN is supported
        // by the database drivers used by the application.
        $summary = (clone $bookingQuery)
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as completed_count',
                [BookingStatus::Completed->value]
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as cancelled_count',
                [BookingStatus::Cancelled->value]
            )
            ->first();

        $bookingCount = (int) ($summary?->booking_count ?? 0);
        $completedCount = (int) ($summary?->completed_count ?? 0);
        $cancelledCount = (int) ($summary?->cancelled_count ?? 0);

        $revenue = (float) Payment::query()
            ->where('status', PaymentStatus::Paid->value)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $dailyRevenue = $this->dailySeries(
            $from,
            $to,
            Payment::query()
                ->where('status', PaymentStatus::Paid->value)
                ->whereNotNull('paid_at')
                ->whereBetween('paid_at', [$from, $to])
                ->selectRaw('DATE(paid_at) as report_date, SUM(amount) as revenue')
                ->groupByRaw('DATE(paid_at)')
                ->orderBy('report_date')
                ->get(),
            'revenue'
        );

        $dailyBookings = $this->dailySeries(
            $from,
            $to,
            (clone $bookingQuery)
                ->selectRaw('DATE(start_at) as report_date, COUNT(*) as booking_count')
                ->groupByRaw('DATE(start_at)')
                ->orderBy('report_date')
                ->get(),
            'booking_count'
        );

        $topPackages = $this->topPackages($from, $to);
        $topCustomers = $this->topCustomers($from, $to);
        $utilization = $this->unitUtilization($from, $to);

        // Eager-load every relationship used by the detail table. The detail
        // query therefore remains constant regardless of the number of rows.
        $bookings = Booking::query()
            ->with(['customer', 'unit', 'package'])
            ->withSum([
                'payments as paid_amount' => function (Builder $query): void {
                    $query->where('status', PaymentStatus::Paid->value);
                },
            ], 'amount')
            ->whereBetween('start_at', [$from, $to])
            ->orderByDesc('start_at')
            ->orderByDesc('id')
            ->get();

        $completionRate = $bookingCount > 0 ? ($completedCount / $bookingCount) * 100 : 0.0;
        $cancellationRate = $bookingCount > 0 ? ($cancelledCount / $bookingCount) * 100 : 0.0;

        return [
            'period' => $period['period'],
            'period_label' => $period['label'],
            'from' => $from,
            'to' => $to,
            'revenue' => $revenue,
            // Friendly aliases make the service convenient for API/UI
            // consumers while the canonical keys remain easy to read.
            'total_revenue' => $revenue,
            'booking_count' => $bookingCount,
            'total_bookings' => $bookingCount,
            'completed_count' => $completedCount,
            'completed' => $completedCount,
            'cancelled_count' => $cancelledCount,
            'cancelled' => $cancelledCount,
            'completion_rate' => round($completionRate, 2),
            'cancellation_rate' => round($cancellationRate, 2),
            'daily_revenue' => $dailyRevenue,
            'daily_bookings' => $dailyBookings,
            'top_packages' => $topPackages,
            'top_customers' => $topCustomers,
            'utilization' => $utilization,
            'bookings' => $bookings,
        ];
    }

    /**
     * Backwards-friendly short name for callers that use the service as a
     * report generator.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function generate(array $filters = []): array
    {
        return $this->getReportData($filters);
    }

    /**
     * Alias for consumers that refer to the report payload simply as data.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function getData(array $filters = []): array
    {
        return $this->getReportData($filters);
    }

    /**
     * Resolve the requested period into an inclusive start/end date range.
     *
     * @param  array<string, mixed>  $filters
     * @return array{period: string, label: string, from: CarbonImmutable, to: CarbonImmutable}
     */
    public function resolvePeriod(array $filters = []): array
    {
        $period = strtolower(trim((string) ($filters['period'] ?? 'month')));
        $period = array_key_exists($period, self::PERIODS) ? $period : 'month';
        $now = CarbonImmutable::now();

        [$from, $to] = match ($period) {
            'today' => [$now->startOfDay(), $now->endOfDay()],
            'week' => [$now->startOfWeek()->startOfDay(), $now->endOfWeek()->endOfDay()],
            'year' => [$now->startOfYear()->startOfDay(), $now->endOfYear()->endOfDay()],
            'custom' => $this->customRange($filters),
            default => [$now->startOfMonth()->startOfDay(), $now->endOfMonth()->endOfDay()],
        };

        return [
            'period' => $period,
            'label' => self::PERIODS[$period],
            'from' => $from,
            'to' => $to,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    private function customRange(array $filters): array
    {
        $fromValue = $filters['from'] ?? null;
        $toValue = $filters['to'] ?? null;

        if (! $fromValue || ! $toValue) {
            throw ValidationException::withMessages([
                'from' => 'Tanggal awal wajib diisi untuk rentang khusus.',
                'to' => 'Tanggal akhir wajib diisi untuk rentang khusus.',
            ]);
        }

        try {
            $from = CarbonImmutable::parse((string) $fromValue)->startOfDay();
            $to = CarbonImmutable::parse((string) $toValue)->endOfDay();
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'from' => 'Rentang tanggal tidak valid.',
                'to' => 'Rentang tanggal tidak valid.',
            ]);
        }

        if ($from->greaterThan($to)) {
            throw ValidationException::withMessages([
                'from' => 'Tanggal awal tidak boleh melewati tanggal akhir.',
                'to' => 'Tanggal akhir tidak boleh melewati tanggal awal.',
            ]);
        }

        if ($from->diffInDays($to) > 366) {
            throw ValidationException::withMessages([
                'to' => 'Rentang laporan maksimal 366 hari.',
            ]);
        }

        return [$from, $to];
    }

    /**
     * @param  Collection<int, object>  $rows
     * @return array{labels: list<string>, data: list<float|int>, series: list<array{name: string, data: list<float|int>}>}
     */
    private function dailySeries(CarbonImmutable $from, CarbonImmutable $to, $rows, string $valueKey): array
    {
        $values = [];

        foreach ($rows as $row) {
            $date = (string) $row->report_date;
            $values[$date] = $valueKey === 'revenue'
                ? (float) $row->{$valueKey}
                : (int) $row->{$valueKey};
        }

        $labels = [];
        $data = [];
        $cursor = $from->startOfDay();

        while ($cursor->lessThanOrEqualTo($to)) {
            $date = $cursor->format('Y-m-d');
            $labels[] = $cursor->format('d M Y');
            $data[] = $values[$date] ?? 0;
            $cursor = $cursor->addDay();
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'series' => [[
                'name' => $valueKey === 'revenue' ? 'Pendapatan' : 'Booking',
                'data' => $data,
            ]],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function topPackages(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = Booking::query()
            ->whereBetween('start_at', [$from, $to])
            ->where('status', '!=', BookingStatus::Cancelled->value)
            ->whereNotNull('package_id')
            ->select('package_id')
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw('COALESCE(SUM(total_price), 0) as revenue')
            ->groupBy('package_id')
            ->orderByDesc('booking_count')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $packages = Package::query()
            ->whereIn('id', $rows->pluck('package_id')->all())
            ->get()
            ->keyBy('id');

        return $rows->map(function (object $row) use ($packages): array {
            $package = $packages->get($row->package_id);

            return [
                'package_id' => (int) $row->package_id,
                'name' => $package?->name ?? 'Paket tidak tersedia',
                'booking_count' => (int) $row->booking_count,
                'revenue' => (float) $row->revenue,
            ];
        })->values()->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function topCustomers(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $rows = Booking::query()
            ->whereBetween('start_at', [$from, $to])
            ->where('status', '!=', BookingStatus::Cancelled->value)
            ->whereNotNull('customer_id')
            ->select('customer_id')
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw('COALESCE(SUM(total_price), 0) as revenue')
            ->groupBy('customer_id')
            ->orderByDesc('booking_count')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $customers = Customer::query()
            ->whereIn('id', $rows->pluck('customer_id')->all())
            ->get()
            ->keyBy('id');

        return $rows->map(function (object $row) use ($customers): array {
            $customer = $customers->get($row->customer_id);

            return [
                'customer_id' => (int) $row->customer_id,
                'name' => $customer?->name ?? 'Customer tidak tersedia',
                'whatsapp' => $customer?->whatsapp,
                'booking_count' => (int) $row->booking_count,
                'revenue' => (float) $row->revenue,
            ];
        })->values()->all();
    }

    /**
     * Calculate booked hours per unit. Completed and currently ongoing
     * bookings represent actual usage; pending and cancelled bookings do not.
     *
     * @return list<array<string, mixed>>
     */
    private function unitUtilization(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $units = PlaystationUnit::query()->orderBy('code')->get();
        $rows = Booking::query()
            ->whereBetween('start_at', [$from, $to])
            ->whereNotNull('playstation_unit_id')
            ->whereIn('status', [
                BookingStatus::Completed->value,
                BookingStatus::Ongoing->value,
            ])
            ->select('playstation_unit_id')
            ->selectRaw('COUNT(*) as booking_count')
            ->selectRaw('COALESCE(SUM(duration_minutes), 0) as duration_minutes')
            ->groupBy('playstation_unit_id')
            ->get()
            ->keyBy('playstation_unit_id');

        $days = $from->startOfDay()->diffInDays($to->startOfDay()) + 1;
        $capacityHours = max(1, $days * $this->operatingHoursPerDay());

        return $units->map(function (PlaystationUnit $unit) use ($rows, $capacityHours): array {
            $usage = $rows->get($unit->id);
            $minutes = (int) ($usage?->duration_minutes ?? 0);
            $hours = round($minutes / 60, 2);
            $bookingCount = (int) ($usage?->booking_count ?? 0);
            $percentage = round(($hours / $capacityHours) * 100, 2);

            return [
                'unit_id' => (int) $unit->id,
                'code' => $unit->code,
                'name' => $unit->name,
                'status' => $unit->status,
                'booking_count' => $bookingCount,
                'duration_minutes' => $minutes,
                'hours' => $hours,
                'utilization_percentage' => $percentage,
            ];
        })->values()->all();
    }

    private function operatingHoursPerDay(): float
    {
        $value = trim((string) $this->settings->get('opening_hours', '10:00-02:00'));
        [$opening, $closing] = array_pad(explode('-', $value, 2), 2, '02:00');

        $toMinutes = static function (string $time): int {
            [$hour, $minute] = array_pad(array_map('intval', explode(':', trim($time), 2)), 2, 0);

            return ($hour * 60) + $minute;
        };

        $duration = $toMinutes($closing) - $toMinutes($opening);
        if ($duration <= 0) {
            $duration += 24 * 60;
        }

        return round($duration / 60, 2);
    }
}
