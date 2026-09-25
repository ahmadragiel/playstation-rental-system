<x-admin-layout title="Laporan">
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Analitik dan ekspor</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Laporan rental</h1>
                <p class="mt-1 text-sm text-slate-500">Pilih periode, tinjau detail, lalu ekspor data dalam format yang Anda butuhkan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.reports.csv', $filters) }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">Export CSV</a>
                <a href="{{ route('admin.reports.pdf', $filters) }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Export PDF</a>
            </div>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="filter-title">
            <h2 id="filter-title" class="font-semibold text-slate-900">Filter laporan</h2>
            <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-4 grid gap-4 md:grid-cols-4">
                <div>
                    <label for="period" class="block text-sm font-medium text-slate-700">Periode</label>
                    <select id="period" name="period" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach ($periods as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['period'] ?? 'month') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="from" class="block text-sm font-medium text-slate-700">Dari tanggal</label>
                    <input id="from" name="from" type="date" value="{{ old('from', $filters['from'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label for="to" class="block text-sm font-medium text-slate-700">Sampai tanggal</label>
                    <input id="to" name="to" type="date" value="{{ old('to', $filters['to'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Terapkan filter</button>
                </div>
            </form>
            <p class="mt-3 text-xs text-slate-500">Rentang aktif: {{ $report['from']->format('d M Y') }} — {{ $report['to']->format('d M Y') }}.</p>
        </section>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pendapatan</p>
                <p class="mt-3 text-2xl font-bold tracking-tight text-slate-900">Rp {{ number_format($report['revenue'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Payment paid</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total booking</p>
                <p class="mt-3 text-2xl font-bold tracking-tight text-slate-900">{{ number_format($report['booking_count'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Dalam periode</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Selesai</p>
                <p class="mt-3 text-2xl font-bold tracking-tight text-emerald-600">{{ number_format($report['completed_count'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($report['completion_rate'], 1, ',', '.') }}% dari total</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Dibatalkan</p>
                <p class="mt-3 text-2xl font-bold tracking-tight text-rose-600">{{ number_format($report['cancelled_count'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ number_format($report['cancellation_rate'], 1, ',', '.') }}% dari total</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-1">
                <p class="text-sm font-medium text-slate-500">Periode</p>
                <p class="mt-3 truncate text-2xl font-bold tracking-tight text-slate-900">{{ $report['period_label'] }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $report['from']->format('d/m/Y') }} — {{ $report['to']->format('d/m/Y') }}</p>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="report-revenue-title">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="report-revenue-title" class="font-semibold text-slate-900">Tren pendapatan</h2>
                        <p class="mt-1 text-xs text-slate-500">Pembayaran paid per hari</p>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600">Paid only</span>
                </div>
                <div id="report-revenue-chart" class="mt-4 min-h-[280px]" aria-label="Grafik pendapatan laporan"></div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="report-booking-title">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="report-booking-title" class="font-semibold text-slate-900">Tren booking</h2>
                        <p class="mt-1 text-xs text-slate-500">Jumlah booking per hari</p>
                    </div>
                    <span class="text-xs font-semibold text-blue-600">All statuses</span>
                </div>
                <div id="report-bookings-chart" class="mt-4 min-h-[280px]" aria-label="Grafik booking laporan"></div>
            </section>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="top-packages-title">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 id="top-packages-title" class="font-semibold text-slate-900">Paket terlaris</h2>
                    <p class="text-xs text-slate-500">Paket dengan booking terbanyak</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3 font-semibold">#</th><th class="px-5 py-3 font-semibold">Paket</th><th class="px-5 py-3 font-semibold">Booking</th><th class="px-5 py-3 font-semibold">Nilai</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($report['top_packages'] as $index => $package)
                                <tr>
                                    <td class="px-5 py-4 text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4 font-medium text-slate-900">{{ $package['name'] }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ number_format($package['booking_count'], 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-slate-600">Rp {{ number_format($package['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada data paket.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="top-customers-title">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 id="top-customers-title" class="font-semibold text-slate-900">Customer teratas</h2>
                    <p class="text-xs text-slate-500">Berdasarkan jumlah booking</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr><th class="px-5 py-3 font-semibold">#</th><th class="px-5 py-3 font-semibold">Customer</th><th class="px-5 py-3 font-semibold">Booking</th><th class="px-5 py-3 font-semibold">Nilai</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($report['top_customers'] as $index => $customer)
                                <tr>
                                    <td class="px-5 py-4 text-slate-400">{{ $index + 1 }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900">{{ $customer['name'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $customer['whatsapp'] }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ number_format($customer['booking_count'], 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 text-slate-600">Rp {{ number_format($customer['revenue'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada data customer.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="utilization-title">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 id="utilization-title" class="font-semibold text-slate-900">Utilization per unit</h2>
                <p class="text-xs text-slate-500">Jam pemakaian booking selesai atau sedang berjalan dalam periode</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-5 py-3 font-semibold">Unit</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Booking</th><th class="px-5 py-3 font-semibold">Jam</th><th class="px-5 py-3 font-semibold">Utilisasi</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($report['utilization'] as $unit)
                            @php
                                $unitStatus = $unit['status'];
                                $unitStatusLabel = $unitStatus instanceof \App\Enums\UnitStatus ? $unitStatus->label() : ucfirst((string) $unitStatus);
                            @endphp
                            <tr>
                                <td class="px-5 py-4"><p class="font-medium text-slate-900">{{ $unit['code'] }}</p><p class="text-xs text-slate-500">{{ $unit['name'] }}</p></td>
                                <td class="px-5 py-4 text-slate-600">{{ $unitStatusLabel }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ number_format($unit['booking_count'], 0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ number_format($unit['hours'], 1, ',', '.') }} jam</td>
                                <td class="min-w-[180px] px-5 py-4">
                                    <div class="flex items-center gap-3"><div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-blue-600" style="width: {{ min(100, (float) $unit['utilization_percentage']) }}%"></div></div><span class="w-16 text-right text-xs font-medium text-slate-600">{{ number_format($unit['utilization_percentage'], 1, ',', '.') }}%</span></div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-8 text-center text-sm text-slate-500">Belum ada unit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="details-title">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 id="details-title" class="font-semibold text-slate-900">Detail booking</h2>
                <p class="text-xs text-slate-500">{{ number_format($report['booking_count'], 0, ',', '.') }} booking pada periode ini</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Nomor</th>
                            <th class="px-5 py-3 font-semibold">Waktu</th>
                            <th class="px-5 py-3 font-semibold">Customer</th>
                            <th class="px-5 py-3 font-semibold">Unit</th>
                            <th class="px-5 py-3 font-semibold">Paket</th>
                            <th class="px-5 py-3 font-semibold">Total</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($report['bookings'] as $booking)
                            @php
                                $bookingStatus = $booking->status;
                                $statusLabel = $bookingStatus instanceof \App\Enums\BookingStatus ? $bookingStatus->label() : ucfirst((string) $bookingStatus);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900">{{ $booking->booking_number }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $booking->start_at?->format('d M Y') }}<br><span class="text-xs text-slate-400">{{ $booking->start_at?->format('H:i') }} — {{ $booking->end_at?->format('H:i') }}</span></td>
                                <td class="px-5 py-4 text-slate-600">{{ $booking->customer?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $booking->unit?->code ?? '-' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $booking->package?->name ?? '-' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $statusLabel }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Tidak ada booking pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <script>
        (() => {
            const revenueLabels = @json($report['daily_revenue']['labels']);
            const revenueData = @json($report['daily_revenue']['data']);
            const bookingLabels = @json($report['daily_bookings']['labels']);
            const bookingData = @json($report['daily_bookings']['data']);

            const render = (id, options) => {
                const element = document.getElementById(id);
                if (element && typeof window.ApexCharts === 'function') {
                    new window.ApexCharts(element, { ...options, theme: { mode: 'dark' } }).render();
                }
            };

            const renderCharts = () => {
                render('report-revenue-chart', {
                    chart: { type: 'area', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Pendapatan', data: revenueData }],
                    xaxis: { categories: revenueLabels },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: ['#10b981'],
                    dataLabels: { enabled: false },
                    yaxis: { labels: { formatter: (value) => `Rp ${Number(value).toLocaleString('id-ID')}` } },
                });
                render('report-bookings-chart', {
                    chart: { type: 'bar', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Booking', data: bookingData }],
                    xaxis: { categories: bookingLabels },
                    colors: ['#3b82f6'],
                    dataLabels: { enabled: false },
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderCharts);
            } else {
                renderCharts();
            }
        })();
    </script>
</x-admin-layout>
