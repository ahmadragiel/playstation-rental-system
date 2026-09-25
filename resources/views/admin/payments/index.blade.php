<x-admin-layout title="Pembayaran">
    @section('title', 'Pembayaran')

    <x-admin.page-header title="Transaksi Pembayaran" subtitle="Catat, konfirmasi, dan pantau pembayaran yang terhubung langsung ke booking." />

    <div class="grid gap-6 xl:grid-cols-4">
        <section class="panel p-5 xl:col-span-1">
            <h2 class="font-bold text-white">Tambah Pembayaran</h2>
            <p class="mt-1 text-sm leading-6 text-slate-500">Pilih booking Pending atau Confirmed.</p>
            <form method="POST" action="{{ route('admin.payments.store') }}" class="mt-5 space-y-4" data-loading="true">
                @csrf
                <div>
                    <label class="form-label" for="booking_id">Booking</label>
                    <select id="booking_id" name="booking_id" class="form-select" required>
                        <option value="">Pilih booking</option>
                        @foreach($bookings as $bookingItem)
                            <option value="{{ $bookingItem->id }}" @selected(old('booking_id') == $bookingItem->id)>
                                {{ $bookingItem->booking_number }} · {{ $bookingItem->customer->name }} · Rp {{ number_format((float) $bookingItem->total_price, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="amount">Total</label>
                    <input id="amount" name="amount" type="number" step="0.01" min="0" class="form-input" required>
                </div>
                <div>
                    <label class="form-label" for="method">Metode</label>
                    <select id="method" name="method" class="form-select" required>
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div>
                    <label class="form-label" for="reference">Referensi</label>
                    <input id="reference" name="reference" class="form-input" maxlength="100">
                </div>
                <button class="btn-primary w-full" type="submit">Simpan Pembayaran</button>
            </form>
        </section>

        <div class="space-y-6 xl:col-span-3">
            <form method="GET" class="panel grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="xl:col-span-2">
                    <label class="form-label" for="search">Pencarian</label>
                    <input id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-input" placeholder="Transaksi, booking, pelanggan">
                </div>
                <div>
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Semua</option>
                        @foreach($statuses as $status)<option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>@endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label" for="date_from">Dari</label>
                    <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="form-input">
                </div>
                <div>
                    <label class="form-label" for="date_to">Sampai</label>
                    <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="form-input">
                </div>
                <div class="flex items-end gap-2 md:col-span-2 xl:col-span-5">
                    <button class="btn-primary" data-loading="true">Terapkan Filter</button>
                    @if(array_filter($filters))<a href="{{ route('admin.payments.index') }}" class="btn-secondary">Reset</a>@endif
                </div>
            </form>

            <div class="panel overflow-hidden">
                @if($payments->isEmpty())
                    <div class="p-5"><x-admin.empty-state title="Belum ada transaksi" description="Transaksi pembayaran yang dicatat akan tampil di tabel ini." /></div>
                @else
                    <div class="overflow-x-auto">
                        <table class="data-table min-w-[880px]">
                            <thead><tr><th>Transaksi</th><th>Booking</th><th>Metode</th><th>Total</th><th>Waktu</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td><p class="font-bold text-white">{{ $payment->transaction_number }}</p><p class="mt-1 text-xs text-slate-500">{{ $payment->processor?->name ?? 'Sistem' }}</p></td>
                                        <td><a href="{{ route('admin.bookings.show', $payment->booking) }}" class="font-semibold text-cyan-300">{{ $payment->booking->booking_number }}</a><p class="mt-1 text-xs text-slate-500">{{ $payment->booking->customer->name }}</p></td>
                                        <td>{{ $payment->method->label() }}</td>
                                        <td class="font-bold text-white">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td>
                                        <td>{{ $payment->paid_at?->format('d M Y H:i') ?? $payment->created_at->format('d M Y H:i') }}</td>
                                        <td><x-admin.status-badge :status="$payment->status" /></td>
                                        <td class="text-right">
                                            @if($payment->status->value === 'pending')
                                                <form method="POST" action="{{ route('admin.payments.status', $payment) }}" class="inline">
                                                    @csrf @method('PATCH')<input type="hidden" name="status" value="paid">
                                                    <button class="btn-primary" data-loading="true">Paid</button>
                                                </form>
                                            @elseif($payment->status->value === 'paid')
                                                <form method="POST" action="{{ route('admin.payments.status', $payment) }}" class="inline" x-data>
                                                    @csrf @method('PATCH')<input type="hidden" name="status" value="refunded">
                                                    <button type="button" class="btn-danger" x-on:click="if (confirm('Refund pembayaran dan batalkan booking?')) $el.closest('form').submit()">Refund</button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-600">Final</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-white/10 p-4">{{ $payments->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
