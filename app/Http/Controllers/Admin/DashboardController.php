<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\UnitStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PlaystationUnit;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * All dashboard values are calculated from the database. Relationships
     * used by the recent-bookings table are eager loaded so rendering the
     * table never performs one query per row.
     */
    public function index(): View
    {
        $now = CarbonImmutable::now();
        $todayStart = $now->startOfDay();
        $todayEnd = $now->endOfDay();
        $monthStart = $now->startOfMonth()->startOfDay();
        $monthEnd = $now->endOfMonth()->endOfDay();
        $chartStart = $todayStart->subDays(6);
        $chartEnd = $todayEnd;

        $bookingToday = Booking::query()
            ->whereBetween('start_at', [$todayStart, $todayEnd])
            ->count();

        $activeBookings = Booking::query()
            ->active()
            ->count();

        $revenueToday = Payment::query()
            ->where('status', PaymentStatus::Paid->value)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$todayStart, $todayEnd])
            ->sum('amount');

        $revenueMonth = Payment::query()
            ->where('status', PaymentStatus::Paid->value)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$monthStart, $monthEnd])
            ->sum('amount');

        $unitCounts = [
            'available' => PlaystationUnit::query()
                ->where('is_active', true)
                ->where('status', '!=', UnitStatus::Maintenance->value)
                ->whereDoesntHave('schedules', fn ($query) => $query
                    ->whereIn('status', ['reserved', 'in_progress'])
                    ->where('start_at', '<=', $now)
                    ->where('end_at', '>', $now))
                ->count(),
            'in_use' => PlaystationUnit::query()
                ->where('is_active', true)
                ->whereHas('schedules', fn ($query) => $query
                    ->where('status', 'in_progress')
                    ->where('start_at', '<=', $now)
                    ->where('end_at', '>', $now))
                ->count(),
            'maintenance' => PlaystationUnit::query()
                ->where('status', UnitStatus::Maintenance->value)
                ->count(),
        ];

        $revenueRows = Payment::query()
            ->where('status', PaymentStatus::Paid->value)
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$chartStart, $chartEnd])
            ->selectRaw('DATE(paid_at) as report_date, SUM(amount) as revenue')
            ->groupByRaw('DATE(paid_at)')
            ->orderBy('report_date')
            ->get();

        $bookingRows = Booking::query()
            ->whereBetween('start_at', [$chartStart, $chartEnd])
            ->selectRaw('DATE(start_at) as report_date, COUNT(*) as booking_count')
            ->groupByRaw('DATE(start_at)')
            ->orderBy('report_date')
            ->get();

        $revenueChartData = $this->dailySeries($chartStart, $chartEnd, $revenueRows, 'revenue');
        $bookingsChartData = $this->dailySeries($chartStart, $chartEnd, $bookingRows, 'booking_count');

        $units = PlaystationUnit::query()->orderBy('code')->get();
        $usageRows = Booking::query()
            ->whereBetween('start_at', [$chartStart, $chartEnd])
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

        $unitUsage = $units->map(function (PlaystationUnit $unit) use ($usageRows): array {
            $usage = $usageRows->get($unit->id);
            $minutes = (int) ($usage?->duration_minutes ?? 0);

            return [
                'unit_id' => (int) $unit->id,
                'code' => $unit->code,
                'name' => $unit->name,
                'status' => $unit->status,
                'booking_count' => (int) ($usage?->booking_count ?? 0),
                'duration_minutes' => $minutes,
                'hours' => round($minutes / 60, 2),
            ];
        })->values();

        $unitUsageChartData = [
            'labels' => $unitUsage->pluck('code')->values()->all(),
            'data' => $unitUsage->pluck('hours')->values()->all(),
            'series' => [[
                'name' => 'Jam pemakaian',
                'data' => $unitUsage->pluck('hours')->values()->all(),
            ]],
        ];

        $recentBookings = Booking::query()
            ->with(['customer', 'unit', 'package'])
            ->latest('start_at')
            ->latest('id')
            ->limit(8)
            ->get();

        $stats = [
            'booking_today' => $bookingToday,
            'active_bookings' => $activeBookings,
            'revenue_today' => (float) $revenueToday,
            'revenue_month' => (float) $revenueMonth,
            'unit_available' => $unitCounts['available'],
            'unit_in_use' => $unitCounts['in_use'],
            'unit_maintenance' => $unitCounts['maintenance'],
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'bookingToday' => $bookingToday,
            'activeBookings' => $activeBookings,
            'revenueToday' => (float) $revenueToday,
            'revenueMonth' => (float) $revenueMonth,
            'unitCounts' => $unitCounts,
            'revenueChartData' => $revenueChartData,
            'bookingsChartData' => $bookingsChartData,
            'unitUsageChartData' => $unitUsageChartData,
            'unitUsage' => $unitUsage,
            'recentBookings' => $recentBookings,
        ]);
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
            $labels[] = $cursor->format('d M');
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
}
