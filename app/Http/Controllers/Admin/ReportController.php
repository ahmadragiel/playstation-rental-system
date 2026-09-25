<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Barryvdh\LaravelDompdf\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

// barryvdh/laravel-dompdf v3 renamed the facade namespace. Keep the
// documented LaravelDompdf facade usable with both v2 and v3 installations.
if (! class_exists(Pdf::class)
    && class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
    class_alias(\Barryvdh\DomPDF\Facade\Pdf::class, Pdf::class);
}

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    /**
     * Display the filtered report.
     */
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $report = $this->reportService->getReportData($filters);

        return view('admin.reports.index', [
            'report' => $report,
            'filters' => $filters,
            'periods' => ReportService::PERIODS,
        ]);
    }

    /**
     * Export the report as an Excel-compatible UTF-8 CSV file.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);
        $report = $this->reportService->getReportData($filters);
        $filename = 'laporan-booking-'.$report['from']->format('Y-m-d').'-'.$report['to']->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($report): void {
            // UTF-8 BOM makes the file open correctly in Excel.
            echo "\xEF\xBB\xBF";
            $handle = fopen('php://output', 'wb');

            if ($handle === false) {
                return;
            }

            $write = function (array $row) use ($handle): void {
                fputcsv($handle, array_map(fn (mixed $value): string => $this->csvValue($value), $row), ',', '"', '');
            };

            $write(['Nexus Play - Laporan Rental']);
            $write(['Periode', $report['period_label']]);
            $write(['Rentang', $report['from']->format('d M Y').' - '.$report['to']->format('d M Y')]);
            $write([]);
            $write(['RINGKASAN']);
            $write(['Metric', 'Nilai']);
            $write(['Pendapatan', $report['revenue']]);
            $write(['Total booking', $report['booking_count']]);
            $write(['Selesai', $report['completed_count']]);
            $write(['Dibatalkan', $report['cancelled_count']]);
            $write(['Tingkat penyelesaian (%)', $report['completion_rate']]);
            $write(['Tingkat pembatalan (%)', $report['cancellation_rate']]);
            $write([]);
            $write(['PAKET TERLARIS']);
            $write(['Paket', 'Jumlah booking', 'Pendapatan']);
            foreach ($report['top_packages'] as $package) {
                $write([$package['name'], $package['booking_count'], $package['revenue']]);
            }
            $write([]);
            $write(['CUSTOMER TERATAS']);
            $write(['Customer', 'WhatsApp', 'Jumlah booking', 'Pendapatan']);
            foreach ($report['top_customers'] as $customer) {
                $write([$customer['name'], $customer['whatsapp'], $customer['booking_count'], $customer['revenue']]);
            }
            $write([]);
            $write(['UTILISASI UNIT']);
            $write(['Unit', 'Kode', 'Status', 'Jumlah booking', 'Jam', 'Utilisasi (%)']);
            foreach ($report['utilization'] as $unit) {
                $write([
                    $unit['name'],
                    $unit['code'],
                    $this->enumValue($unit['status']),
                    $unit['booking_count'],
                    $unit['hours'],
                    $unit['utilization_percentage'],
                ]);
            }
            $write([]);
            $write(['DETAIL BOOKING']);
            $write([
                'Nomor booking',
                'Tanggal',
                'Mulai',
                'Selesai',
                'Customer',
                'WhatsApp',
                'Unit',
                'Paket',
                'Status',
                'Total',
                'Paid',
            ]);

            foreach ($report['bookings'] as $booking) {
                $write([
                    $booking->booking_number,
                    $booking->start_at?->format('Y-m-d'),
                    $booking->start_at?->format('H:i'),
                    $booking->end_at?->format('H:i'),
                    $booking->customer?->name,
                    $booking->customer?->whatsapp,
                    $booking->unit?->name,
                    $booking->package?->name,
                    $this->enumValue($booking->status),
                    $booking->total_price,
                    $booking->paid_amount,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }

    /**
     * Render the report as a print-friendly PDF.
     */
    public function exportPdf(Request $request)
    {
        $filters = $this->validatedFilters($request);
        $report = $this->reportService->getReportData($filters);
        $filename = 'laporan-booking-'.$report['from']->format('Y-m-d').'-'.$report['to']->format('Y-m-d').'.pdf';

        return Pdf::loadView('admin.reports.pdf', [
            'report' => $report,
            'filters' => $filters,
        ])
            ->setPaper('a4', 'landscape')
            ->download($filename);
    }

    /**
     * @return array{period: string, from: string|null, to: string|null}
     */
    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'period' => ['nullable', Rule::in(array_keys(ReportService::PERIODS))],
            'from' => ['nullable', 'date', 'required_if:period,custom'],
            'to' => ['nullable', 'date', 'required_if:period,custom', 'after_or_equal:from'],
        ]);

        return [
            'period' => $validated['period'] ?? 'month',
            'from' => $validated['from'] ?? null,
            'to' => $validated['to'] ?? null,
        ];
    }

    /**
     * Make a value safe for a spreadsheet cell.
     *
     * A leading apostrophe prevents Excel-compatible spreadsheet programs from
     * treating user-controlled text (customer/package names, for example) as
     * a formula. Leading whitespace/control characters are considered too.
     */
    private function csvValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \BackedEnum) {
            $value = $value->value;
        } elseif ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        } elseif (is_bool($value)) {
            $value = $value ? '1' : '0';
        }

        $value = (string) $value;

        if (preg_match('/^[\x00-\x20]*[=+\-@]/u', $value) === 1) {
            return "'".$value;
        }

        return $value;
    }

    private function enumValue(mixed $value): string
    {
        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        return (string) $value;
    }
}
