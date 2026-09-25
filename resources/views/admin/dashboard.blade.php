<x-admin-layout title="Dashboard">
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500">Ringkasan operasional</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Dashboard</h1>
                <p class="mt-1 text-sm text-slate-500">Pantau booking, pendapatan, dan utilisation unit secara real-time.</p>
            </div>
            <div class="text-sm text-slate-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Booking hari ini</p>
                    <span class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">Today</span>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($stats['booking_today'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Booking dengan jadwal hari ini</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Booking aktif</p>
                    <span class="rounded-lg bg-cyan-50 px-2 py-1 text-xs font-semibold text-cyan-700">Active</span>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($stats['active_bookings'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Menunggu, dikonfirmasi, dan berjalan</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Pendapatan hari ini</p>
                    <span class="rounded-lg bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">Paid</span>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Dari payment berstatus paid</p>
            </article>

            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-500">Pendapatan bulan ini</p>
                    <span class="rounded-lg bg-violet-50 px-2 py-1 text-xs font-semibold text-violet-700">Monthly</span>
                </div>
                <p class="mt-4 text-3xl font-bold tracking-tight text-slate-900">Rp {{ number_format($stats['revenue_month'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-500">Dari payment berstatus paid</p>
            </article>
        </div>

        <div class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4">
                <p class="text-sm font-medium text-emerald-800">Unit tersedia</p>
                <p class="mt-2 text-2xl font-bold text-emerald-900">{{ number_format($unitCounts['available'], 0, ',', '.') }}</p>
            </article>
            <article class="rounded-2xl border border-cyan-200 bg-cyan-50/60 p-4">
                <p class="text-sm font-medium text-cyan-800">Unit sedang digunakan</p>
                <p class="mt-2 text-2xl font-bold text-cyan-900">{{ number_format($unitCounts['in_use'], 0, ',', '.') }}</p>
            </article>
            <article class="rounded-2xl border border-amber-200 bg-amber-50/60 p-4">
                <p class="text-sm font-medium text-amber-800">Unit maintenance</p>
                <p class="mt-2 text-2xl font-bold text-amber-900">{{ number_format($unitCounts['maintenance'], 0, ',', '.') }}</p>
            </article>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="revenue-chart-title">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 id="revenue-chart-title" class="font-semibold text-slate-900">Revenue 7 hari</h2>
                        <p class="text-xs text-slate-500">Pembayaran berhasil per hari</p>
                    </div>
                    <span class="text-xs font-medium text-emerald-600">Paid only</span>
                </div>
                <div id="revenue-chart" class="min-h-[280px] w-full" aria-label="Grafik pendapatan tujuh hari"></div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="bookings-chart-title">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 id="bookings-chart-title" class="font-semibold text-slate-900">Booking 7 hari</h2>
                        <p class="text-xs text-slate-500">Jumlah booking per hari</p>
                    </div>
                    <span class="text-xs font-medium text-blue-600">All statuses</span>
                </div>
                <div id="bookings-chart" class="min-h-[280px] w-full" aria-label="Grafik booking tujuh hari"></div>
            </section>
        </div>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="unit-usage-title">
            <div class="mb-4 flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 id="unit-usage-title" class="font-semibold text-slate-900">Unit usage</h2>
                    <p class="text-xs text-slate-500">Jam pemakaian unit dalam 7 hari terakhir</p>
                </div>
                <span class="text-xs text-slate-500">Completed / ongoing</span>
            </div>
            <div id="unit-usage-chart" class="min-h-[280px] w-full" aria-label="Grafik penggunaan unit"></div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_1fr]">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="recent-bookings-title">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 id="recent-bookings-title" class="font-semibold text-slate-900">Booking terbaru</h2>
                        <p class="text-xs text-slate-500">8 booking terakhir</p>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Lihat laporan</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Booking</th>
                                <th class="px-5 py-3 font-semibold">Customer</th>
                                <th class="px-5 py-3 font-semibold">Paket</th>
                                <th class="px-5 py-3 font-semibold">Unit</th>
                                <th class="px-5 py-3 font-semibold">Jadwal</th>
                                <th class="px-5 py-3 font-semibold">Total</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($recentBookings as $booking)
                                @php
                                    $bookingStatus = $booking->status;
                                    $statusLabel = $bookingStatus instanceof \App\Enums\BookingStatus
                                        ? $bookingStatus->label()
                                        : ucfirst((string) $bookingStatus);
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900">{{ $booking->booking_number }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $booking->customer?->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $booking->package?->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $booking->unit?->code ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $booking->start_at?->format('d M Y, H:i') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-900">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $statusLabel }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada booking.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="unit-usage-table-title">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 id="unit-usage-table-title" class="font-semibold text-slate-900">Ringkasan usage unit</h2>
                    <p class="text-xs text-slate-500">Detail jam pemakaian 7 hari</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Unit</th>
                                <th class="px-5 py-3 font-semibold">Booking</th>
                                <th class="px-5 py-3 font-semibold">Jam</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($unitUsage as $usage)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900">{{ $usage['code'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $usage['name'] }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ number_format($usage['booking_count'], 0, ',', '.') }}</td>
                                    <td class="px-5 py-4 font-medium text-slate-900">{{ number_format($usage['hours'], 1, ',', '.') }} jam</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada unit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>

    <script>
        (() => {
            const revenueLabels = @json($revenueChartData['labels']);
            const revenueData = @json($revenueChartData['data']);
            const bookingLabels = @json($bookingsChartData['labels']);
            const bookingData = @json($bookingsChartData['data']);
            const unitLabels = @json($unitUsageChartData['labels']);
            const unitData = @json($unitUsageChartData['data']);

            const renderChart = (elementId, options) => {
                const element = document.getElementById(elementId);
                if (!element || typeof window.ApexCharts !== 'function') {
                    return;
                }

                new window.ApexCharts(element, options).render();
            };

            const renderCharts = () => {
                renderChart('revenue-chart', {
                    chart: { type: 'area', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Pendapatan', data: revenueData }],
                    xaxis: { categories: revenueLabels },
                    stroke: { curve: 'smooth', width: 3 },
                    dataLabels: { enabled: false },
                    colors: ['#059669'],
                    yaxis: { labels: { formatter: (value) => `Rp ${Number(value).toLocaleString('id-ID')}` } }
                });

                renderChart('bookings-chart', {
                    chart: { type: 'bar', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Booking', data: bookingData }],
                    xaxis: { categories: bookingLabels },
                    dataLabels: { enabled: false },
                    colors: ['#2563eb'],
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } }
                });

                renderChart('unit-usage-chart', {
                    chart: { type: 'bar', height: 280, toolbar: { show: false } },
                    series: [{ name: 'Jam pemakaian', data: unitData }],
                    xaxis: { categories: unitLabels },
                    dataLabels: { enabled: false },
                    colors: ['#0891b2'],
                    plotOptions: { bar: { borderRadius: 6, columnWidth: '55%' } },
                    yaxis: { title: { text: 'Jam' } }
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
