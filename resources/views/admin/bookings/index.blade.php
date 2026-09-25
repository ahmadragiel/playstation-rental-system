<x-admin-layout title="Booking">
    @section('title', 'Manajemen Booking')

    <x-admin.page-header title="Manajemen Booking" subtitle="Cari, filter, dan pantau seluruh booking pelanggan dari satu tempat.">
        <x-slot:actions>
            <a href="{{ route('admin.schedule') }}" class="btn-secondary">Lihat Jadwal</a>
            <a href="{{ route('booking.create') }}" target="_blank" class="btn-primary">Form Booking Publik</a>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="GET" class="panel grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="md:col-span-2 xl:col-span-1">
            <label class="form-label" for="search">Pencarian</label>
            <input id="search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-input" placeholder="No. booking, nama, WhatsApp">
        </div>
        <div>
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">Semua status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}" @selected(($filters['status'] ?? '') === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="date_from">Dari tanggal</label>
            <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="form-input">
        </div>
        <div>
            <label class="form-label" for="date_to">Sampai tanggal</label>
            <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="form-input">
        </div>
        <div class="flex items-end gap-2">
            <button class="btn-primary flex-1" type="submit" data-loading="true">Terapkan</button>
            @if(array_filter($filters))
                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Reset</a>
            @endif
        </div>
    </form>

    <div class="panel overflow-hidden">
        @if($bookings->isEmpty())
            <div class="p-5">
                <x-admin.empty-state title="Booking tidak ditemukan" description="Coba ubah kata pencarian atau filter yang digunakan." />
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="data-table min-w-[980px]">
                    <thead>
                        <tr>
                            <th>Booking</th>
                            <th>Pelanggan</th>
                            <th>Paket / Unit</th>
                            <th>Jadwal</th>
                            <th>Total</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="font-bold text-cyan-300 hover:text-cyan-200">{{ $booking->booking_number }}</a>
                                    <p class="mt-1 text-xs text-slate-500">Dibuat {{ $booking->created_at->format('d M Y, H:i') }}</p>
                                </td>
                                <td>
                                    <p class="font-semibold text-white">{{ $booking->customer->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $booking->customer->whatsapp }}</p>
                                </td>
                                <td>
                                    <p class="font-medium text-slate-200">{{ $booking->package->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $booking->unit->code }} · {{ $booking->unit->name }}</p>
                                </td>
                                <td>
                                    <p class="font-medium text-slate-200">{{ $booking->start_at->format('d M Y') }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $booking->start_at->format('H:i') }}–{{ $booking->end_at->format('H:i') }}</p>
                                </td>
                                <td class="font-bold text-white">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</td>
                                <td>
                                    @php $paid = $booking->payments->firstWhere('status.value', 'paid'); @endphp
                                    @if($paid)
                                        <span class="text-xs font-semibold text-emerald-300">Lunas</span>
                                    @else
                                        <span class="text-xs font-semibold text-amber-300">Belum dibayar</span>
                                    @endif
                                </td>
                                <td><x-admin.status-badge :status="$booking->status" /></td>
                                <td class="text-right">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="btn-ghost">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-white/10 p-4">{{ $bookings->links() }}</div>
        @endif
    </div>
</x-admin-layout>
