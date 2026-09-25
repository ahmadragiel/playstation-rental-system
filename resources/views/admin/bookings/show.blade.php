<x-admin-layout title="Detail Booking">
    @section('title', 'Detail Booking')

    <x-admin.page-header :title="$booking->booking_number" :subtitle="__('Booking dibuat pada :datetime', ['datetime' => $booking->created_at->format('d M Y, H:i')])">
        <x-slot:actions>
            <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">← Kembali</a>
            <a href="{{ route('admin.schedule', ['date' => $booking->start_at->toDateString()]) }}" class="btn-secondary">Lihat di Jadwal</a>
        </x-slot:actions>
    </x-admin.page-header>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="panel p-5 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Status saat ini</p>
                        <div class="mt-2"><x-admin.status-badge :status="$booking->status" /></div>
                    </div>
                    <p class="text-3xl font-black text-white">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</p>
                </div>

                <dl class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Pelanggan</dt>
                        <dd class="mt-1.5 font-bold text-white">{{ $booking->customer->name }}</dd>
                        <dd class="mt-1 text-sm text-slate-400">{{ $booking->customer->whatsapp }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Paket</dt>
                        <dd class="mt-1.5 font-bold text-white">{{ $booking->detail?->package_name ?? $booking->package->name }}</dd>
                        <dd class="mt-1 text-sm text-slate-400">{{ $booking->duration_minutes }} menit</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Unit</dt>
                        <dd class="mt-1.5 font-bold text-white">{{ $booking->unit->code }}</dd>
                        <dd class="mt-1 text-sm text-slate-400">{{ $booking->unit->name }} · {{ $booking->unit->type->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Tanggal</dt>
                        <dd class="mt-1.5 font-bold text-white">{{ $booking->start_at->format('l, d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Waktu</dt>
                        <dd class="mt-1.5 font-bold text-white">{{ $booking->start_at->format('H:i') }}–{{ $booking->end_at->format('H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-wider text-slate-500">Durasi</dt>
                        <dd class="mt-1.5 font-bold text-white">{{ intdiv($booking->duration_minutes, 60) }} jam</dd>
                    </div>
                </dl>

                @if($booking->notes)
                    <div class="mt-6 rounded-xl border border-white/10 bg-slate-950/50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Catatan pelanggan</p>
                        <p class="mt-2 text-sm leading-6 text-slate-300">{{ $booking->notes }}</p>
                    </div>
                @endif
            </section>

            <section class="panel overflow-hidden">
                <div class="flex items-center justify-between border-b border-white/10 p-5">
                    <div>
                        <h3 class="font-bold text-white">Riwayat Pembayaran</h3>
                        <p class="mt-1 text-sm text-slate-500">Nominal divalidasi terhadap total booking.</p>
                    </div>
                    <a href="{{ route('admin.payments.index', ['search' => $booking->booking_number]) }}" class="btn-ghost">Kelola Pembayaran</a>
                </div>
                @if($booking->payments->isEmpty())
                    <div class="p-5"><x-admin.empty-state title="Belum ada pembayaran" description="Catat transaksi pembayaran di bawah atau melalui halaman Pembayaran." /></div>
                @else
                    <div class="divide-y divide-white/10">
                        @foreach($booking->payments as $payment)
                            <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-white">{{ $payment->transaction_number }}</span>
                                        <x-admin.status-badge :status="$payment->status" />
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">{{ $payment->method->label() }} · {{ $payment->paid_at?->format('d M Y, H:i') ?? $payment->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                <div class="text-left sm:text-right">
                                    <p class="font-black text-white">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</p>
                                    @if($payment->status->value === 'pending')
                                        <form method="POST" action="{{ route('admin.payments.status', $payment) }}" class="mt-2 flex gap-2">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="paid">
                                            <button class="btn-primary" data-loading="true">Konfirmasi Paid</button>
                                        </form>
                                    @elseif($payment->status->value === 'paid')
                                        <form method="POST" action="{{ route('admin.payments.status', $payment) }}" class="mt-2" x-data>
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="refunded">
                                            <button type="button" class="btn-danger" x-on:click="if (confirm('Refund pembayaran ini dan batalkan booking?')) $el.closest('form').submit()">Refund</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>

        <aside class="space-y-6">
            @if(in_array($booking->status->value, ['pending', 'confirmed'], true))
                <section class="panel p-5">
                    <h3 class="font-bold text-white">Catat Pembayaran</h3>
                    <p class="mt-1 text-sm leading-6 text-slate-500">Nominal harus sama persis dengan total booking.</p>
                    <form method="POST" action="{{ route('admin.payments.store') }}" class="mt-5 space-y-4" data-loading="true">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <div>
                            <label class="form-label" for="amount">Total</label>
                            <input id="amount" name="amount" type="number" step="0.01" min="0" value="{{ $booking->total_price }}" class="form-input" required>
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
                            <label class="form-label" for="payment_status">Status</label>
                            <select id="payment_status" name="status" class="form-select" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label" for="reference">Referensi</label>
                            <input id="reference" name="reference" class="form-input" maxlength="100" placeholder="Nomor transfer / ID QRIS">
                        </div>
                        <button class="btn-primary w-full" type="submit">Simpan Transaksi</button>
                    </form>
                </section>
            @endif

            <section class="panel p-5">
                <h3 class="font-bold text-white">Ubah Status Booking</h3>
                @php $availableStatuses = collect($statuses)->reject(fn ($status) => $status === \App\Enums\BookingStatus::Paid); @endphp
                @if($availableStatuses->isEmpty())
                    <p class="mt-3 text-sm leading-6 text-slate-500">Booking ini sudah memiliki status final.</p>
                @else
                    <form method="POST" action="{{ route('admin.bookings.status', $booking) }}" class="mt-5 space-y-4" x-data="{ status: '{{ $availableStatuses->first()->value }}' }" data-loading="true">
                        @csrf @method('PATCH')
                        <div>
                            <label class="form-label" for="booking_status">Status baru</label>
                            <select id="booking_status" name="status" class="form-select" x-model="status" required>
                                @foreach($availableStatuses as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="status === 'cancelled'" x-cloak>
                            <label class="form-label" for="reason">Alasan pembatalan</label>
                            <textarea id="reason" name="reason" class="form-textarea" maxlength="500" placeholder="Alasan dibatalkan admin"></textarea>
                        </div>
                        <button class="btn-primary w-full" type="submit">Perbarui Status</button>
                    </form>
                @endif
            </section>

            <section class="panel p-5 text-sm">
                <h3 class="font-bold text-white">Audit</h3>
                <dl class="mt-4 space-y-3">
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Dibuat oleh</dt><dd class="text-right font-medium text-slate-300">{{ $booking->creator?->name ?? 'Pelanggan' }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Dibuat</dt><dd class="text-right font-medium text-slate-300">{{ $booking->created_at->format('d M Y H:i') }}</dd></div>
                    <div class="flex justify-between gap-4"><dt class="text-slate-500">Diperbarui</dt><dd class="text-right font-medium text-slate-300">{{ $booking->updated_at->format('d M Y H:i') }}</dd></div>
                </dl>
            </section>
        </aside>
    </div>
</x-admin-layout>
