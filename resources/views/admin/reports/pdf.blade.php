<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rental - Nexus Play</title>
    <style>
        @page { margin: 24px 28px 30px; }
        * { box-sizing: border-box; }
        body { color: #1e293b; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.4; margin: 0; }
        h1 { color: #0f172a; font-size: 20px; margin: 0 0 4px; }
        h2 { border-bottom: 1px solid #cbd5e1; color: #0f172a; font-size: 13px; margin: 18px 0 8px; padding-bottom: 5px; }
        h3 { color: #334155; font-size: 11px; margin: 0 0 5px; }
        p { margin: 0; }
        .muted { color: #64748b; }
        .header { border-bottom: 2px solid #2563eb; padding-bottom: 12px; }
        .meta { margin-top: 5px; }
        .summary { margin-top: 14px; width: 100%; }
        .summary td { border: 1px solid #e2e8f0; padding: 8px; width: 25%; }
        .summary .label { background: #f8fafc; color: #64748b; font-size: 8px; text-transform: uppercase; }
        .summary .value { color: #0f172a; font-size: 13px; font-weight: bold; }
        table { border-collapse: collapse; margin-top: 7px; width: 100%; }
        th { background: #f1f5f9; color: #475569; font-size: 8px; font-weight: bold; padding: 6px; text-align: left; text-transform: uppercase; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px; vertical-align: top; }
        .right { text-align: right; }
        .center { text-align: center; }
        .grid { display: table; table-layout: fixed; width: 100%; }
        .column { display: table-cell; padding-right: 12px; vertical-align: top; width: 50%; }
        .column:last-child { padding-left: 12px; padding-right: 0; }
        .empty { color: #94a3b8; padding: 14px 6px; text-align: center; }
        .footer { color: #94a3b8; font-size: 8px; margin-top: 18px; text-align: center; }
        .page-break { page-break-before: always; }
        .keep { page-break-inside: avoid; }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            h2 { page-break-after: avoid; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Rental</h1>
        <p class="muted">Nexus Play</p>
        <p class="meta"><strong>{{ $report['period_label'] }}</strong> · {{ $report['from']->format('d M Y') }} — {{ $report['to']->format('d M Y') }}</p>
    </div>

    <h2>Ringkasan</h2>
    <table class="summary">
        <tr>
            <td class="label">Pendapatan (paid)</td>
            <td class="label">Total booking</td>
            <td class="label">Selesai</td>
            <td class="label">Dibatalkan</td>
        </tr>
        <tr>
            <td class="value">Rp {{ number_format($report['revenue'], 0, ',', '.') }}</td>
            <td class="value">{{ number_format($report['booking_count'], 0, ',', '.') }}</td>
            <td class="value">{{ number_format($report['completed_count'], 0, ',', '.') }} ({{ number_format($report['completion_rate'], 1, ',', '.') }}%)</td>
            <td class="value">{{ number_format($report['cancelled_count'], 0, ',', '.') }} ({{ number_format($report['cancellation_rate'], 1, ',', '.') }}%)</td>
        </tr>
    </table>

    <div class="grid">
        <div class="column">
            <h3>Paket terlaris</h3>
            <table class="keep">
                <thead><tr><th>Paket</th><th class="right">Booking</th><th class="right">Nilai</th></tr></thead>
                <tbody>
                    @forelse ($report['top_packages'] as $package)
                        <tr><td>{{ $package['name'] }}</td><td class="right">{{ number_format($package['booking_count'], 0, ',', '.') }}</td><td class="right">Rp {{ number_format($package['revenue'], 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="empty">Belum ada data paket.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="column">
            <h3>Customer teratas</h3>
            <table class="keep">
                <thead><tr><th>Customer</th><th class="right">Booking</th><th class="right">Nilai</th></tr></thead>
                <tbody>
                    @forelse ($report['top_customers'] as $customer)
                        <tr><td>{{ $customer['name'] }}@if($customer['whatsapp'])<br><span class="muted">{{ $customer['whatsapp'] }}</span>@endif</td><td class="right">{{ number_format($customer['booking_count'], 0, ',', '.') }}</td><td class="right">Rp {{ number_format($customer['revenue'], 0, ',', '.') }}</td></tr>
                    @empty
                        <tr><td colspan="3" class="empty">Belum ada data customer.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <h2>Utilization per unit</h2>
    <table class="keep">
        <thead><tr><th>Unit</th><th>Kode</th><th>Status</th><th class="right">Booking</th><th class="right">Jam</th><th class="right">Utilisasi</th></tr></thead>
        <tbody>
            @forelse ($report['utilization'] as $unit)
                @php
                    $unitStatus = $unit['status'];
                    $unitStatusLabel = $unitStatus instanceof \App\Enums\UnitStatus ? $unitStatus->label() : ucfirst((string) $unitStatus);
                @endphp
                <tr><td>{{ $unit['name'] }}</td><td>{{ $unit['code'] }}</td><td>{{ $unitStatusLabel }}</td><td class="right">{{ number_format($unit['booking_count'], 0, ',', '.') }}</td><td class="right">{{ number_format($unit['hours'], 1, ',', '.') }} jam</td><td class="right">{{ number_format($unit['utilization_percentage'], 1, ',', '.') }}%</td></tr>
            @empty
                <tr><td colspan="6" class="empty">Belum ada data unit.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2 class="page-break">Detail booking</h2>
    <table>
        <thead>
            <tr>
                <th>Nomor booking</th>
                <th>Waktu</th>
                <th>Customer</th>
                <th>Unit</th>
                <th>Paket</th>
                <th class="right">Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report['bookings'] as $booking)
                @php
                    $bookingStatus = $booking->status;
                    $statusLabel = $bookingStatus instanceof \App\Enums\BookingStatus ? $bookingStatus->label() : ucfirst((string) $bookingStatus);
                @endphp
                <tr>
                    <td>{{ $booking->booking_number }}</td>
                    <td>{{ $booking->start_at?->format('d M Y H:i') }}<br><span class="muted">s/d {{ $booking->end_at?->format('H:i') }}</span></td>
                    <td>{{ $booking->customer?->name ?? '-' }}@if($booking->customer?->whatsapp)<br><span class="muted">{{ $booking->customer->whatsapp }}</span>@endif</td>
                    <td>{{ $booking->unit?->code ?? '-' }}<br><span class="muted">{{ $booking->unit?->name }}</span></td>
                    <td>{{ $booking->package?->name ?? '-' }}</td>
                    <td class="right">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                    <td>{{ $statusLabel }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty">Tidak ada booking pada periode ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">Dicetak {{ now()->translatedFormat('d F Y H:i') }}</p>
</body>
</html>
