<x-admin-layout :title="'Detail ' . $customer->name">
    @section('title', 'Detail ' . $customer->name)

    @php
        $currency = static fn ($value): string => 'Rp ' . number_format((float) $value, 0, ',', '.');
        $bookingColors = [
            'pending' => 'bg-amber-100 text-amber-800',
            'confirmed' => 'bg-blue-100 text-blue-800',
            'paid' => 'bg-violet-100 text-violet-800',
            'ongoing' => 'bg-cyan-100 text-cyan-800',
            'completed' => 'bg-emerald-100 text-emerald-800',
            'cancelled' => 'bg-rose-100 text-rose-800',
        ];
    @endphp

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.customers.index') }}" class="mt-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">← Kembali</a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ $customer->name }}</h1>
                    <p class="mt-1 text-sm text-slate-500">Detail pelanggan dan riwayat transaksi.</p>
                </div>
            </div>
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $customer->whatsapp) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Hubungi WhatsApp</a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ session('error') }}</div>
        @endif

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total Booking</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalBookings }}</p>
                <p class="mt-1 text-xs text-slate-500">Seluruh riwayat booking</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total Transaksi Paid</p>
                <p class="mt-2 text-2xl font-bold text-slate-900">{{ $currency($totalPaidAmount) }}</p>
                <p class="mt-1 text-xs text-slate-500">Total nominal pembayaran berhasil</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Jumlah Transaksi Paid</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $paidTransactionsCount }}</p>
                <p class="mt-1 text-xs text-slate-500">Transaksi berstatus paid</p>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-1">
                <h2 class="font-semibold text-slate-900">Informasi Pelanggan</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Nama</dt>
                        <dd class="mt-1 font-medium text-slate-900">{{ $customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">WhatsApp</dt>
                        <dd class="mt-1 text-slate-700">{{ $customer->whatsapp }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Email</dt>
                        <dd class="mt-1 break-all text-slate-700">{{ $customer->email ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Alamat</dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-700">{{ $customer->address ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Catatan</dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-700">{{ $customer->notes ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-slate-500">Terdaftar</dt>
                        <dd class="mt-1 text-slate-700">{{ $customer->created_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                <h2 class="font-semibold text-slate-900">Booking Terakhir</h2>
                @if ($latestBooking)
                    @php
                        $latestStatus = $latestBooking->status instanceof \App\Enums\BookingStatus
                            ? $latestBooking->status->value
                            : (string) $latestBooking->status;
                        $latestStatusLabel = $latestBooking->status instanceof \App\Enums\BookingStatus
                            ? $latestBooking->status->label()
                            : ucfirst($latestStatus);
                    @endphp
                    <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">{{ $latestBooking->booking_number }}</p>
                                <p class="mt-1 text-xs text-slate-500">Dibuat {{ $latestBooking->created_at?->format('d M Y H:i') }}</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $bookingColors[$latestStatus] ?? 'bg-slate-100 text-slate-700' }}">{{ $latestStatusLabel }}</span>
                        </div>

                        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-xs text-slate-500">Waktu Mulai</dt>
                                <dd class="mt-1 font-medium text-slate-800">{{ $latestBooking->start_at?->format('d M Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Waktu Selesai</dt>
                                <dd class="mt-1 font-medium text-slate-800">{{ $latestBooking->end_at?->format('d M Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Unit</dt>
                                <dd class="mt-1 font-medium text-slate-800">{{ $latestBooking->unit?->code ?? '-' }} · {{ $latestBooking->unit?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Paket</dt>
                                <dd class="mt-1 font-medium text-slate-800">{{ $latestBooking->package?->name ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Durasi</dt>
                                <dd class="mt-1 font-medium text-slate-800">{{ $latestBooking->duration_minutes }} menit</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-slate-500">Total</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $currency($latestBooking->total_price) }}</dd>
                            </div>
                        </dl>

                        @if ($latestBooking->detail)
                            <div class="mt-4 border-t border-slate-200 pt-4">
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Detail Booking</h3>
                                <dl class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs text-slate-500">Nama Paket</dt>
                                        <dd class="mt-1 text-slate-800">{{ $latestBooking->detail->package_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Tipe PlayStation</dt>
                                        <dd class="mt-1 text-slate-800">{{ $latestBooking->detail->playstation_type }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Kode Unit</dt>
                                        <dd class="mt-1 text-slate-800">{{ $latestBooking->detail->unit_code }} · {{ $latestBooking->detail->unit_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Pelanggan</dt>
                                        <dd class="mt-1 text-slate-800">{{ $latestBooking->detail->customer_name }} · {{ $latestBooking->detail->customer_whatsapp }}</dd>
                                    </div>
                                </dl>
                                @if (filled($latestBooking->detail->facilities))
                                    <div class="mt-4 flex flex-wrap gap-1.5">
                                        @foreach ($latestBooking->detail->facilities as $facility)
                                            <span class="rounded bg-indigo-50 px-2 py-1 text-xs text-indigo-700">{{ $facility }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="mt-4 border-t border-slate-200 pt-4 text-sm text-slate-500">Detail booking belum tersedia.</p>
                        @endif

                        @if ($latestBooking->payments->isNotEmpty())
                            <div class="mt-4 border-t border-slate-200 pt-4">
                                <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Pembayaran</h3>
                                <div class="mt-2 space-y-2">
                                    @foreach ($latestBooking->payments as $payment)
                                        @php
                                            $paymentStatus = $payment->status instanceof \App\Enums\PaymentStatus
                                                ? $payment->status->value
                                                : (string) $payment->status;
                                            $paymentStatusLabel = $payment->status instanceof \App\Enums\PaymentStatus
                                                ? $payment->status->label()
                                                : ucfirst($paymentStatus);
                                        @endphp
                                        <div class="flex flex-wrap items-center justify-between gap-2 text-sm">
                                            <span class="text-slate-700">{{ $payment->transaction_number }} · {{ $payment->method?->label() ?? $payment->method }}</span>
                                            <span class="font-semibold text-slate-900">{{ $currency($payment->amount) }} <span class="ml-1 text-xs font-medium text-slate-500">({{ $paymentStatusLabel }})</span></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-4 rounded-lg border border-dashed border-slate-300 px-6 py-10 text-center text-sm text-slate-500">Belum ada booking untuk pelanggan ini.</div>
                @endif
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Riwayat Booking</h2>
                <span class="text-sm text-slate-500">{{ $bookings->total() }} booking</span>
            </div>
            @if ($bookings->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-slate-500">Belum ada riwayat booking.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Booking</th>
                                <th class="px-5 py-3">Waktu</th>
                                <th class="px-5 py-3">Unit / Paket</th>
                                <th class="px-5 py-3">Total</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($bookings as $booking)
                                @php
                                    $statusValue = $booking->status instanceof \App\Enums\BookingStatus
                                        ? $booking->status->value
                                        : (string) $booking->status;
                                    $statusLabel = $booking->status instanceof \App\Enums\BookingStatus
                                        ? $booking->status->label()
                                        : ucfirst($statusValue);
                                @endphp
                                <tr>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $booking->booking_number }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $booking->created_at?->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $booking->start_at?->format('d M Y H:i') }}<br><span class="text-xs text-slate-400">s/d {{ $booking->end_at?->format('d M Y H:i') }}</span></td>
                                    <td class="px-5 py-4 text-slate-600">{{ $booking->unit?->code ?? '-' }} · {{ $booking->package?->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $currency($booking->total_price) }}</td>
                                    <td class="whitespace-nowrap px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $bookingColors[$statusValue] ?? 'bg-slate-100 text-slate-700' }}">{{ $statusLabel }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
