@props([
    'booking',
])

@php
    $detail = $booking->detail;
    $packageName = $detail?->package_name ?? $booking->package?->name ?? 'Paket rental';
    $playstationType = $detail?->playstation_type ?? $booking->unit?->type?->name ?? 'PlayStation';
    $unitCode = $detail?->unit_code ?? $booking->unit?->code ?? '—';
    $unitName = $detail?->unit_name ?? $booking->unit?->name ?? '—';
    $customerName = $detail?->customer_name ?? $booking->customer?->name ?? 'Pelanggan';
    $customerWhatsapp = preg_replace('/\D+/', '', (string) ($detail?->customer_whatsapp ?? $booking->customer?->whatsapp ?? ''));
    $startAt = $detail?->start_at ?? $booking->start_at;
    $endAt = $detail?->end_at ?? $booking->end_at;
    $duration = $detail?->duration_minutes ?? $booking->duration_minutes ?? 0;
    $durationHours = intdiv((int) $duration, 60);
    $durationRemainder = (int) $duration % 60;
    $durationLabel = $durationHours > 0 && $durationRemainder > 0
        ? $durationHours . ' jam ' . $durationRemainder . ' menit'
        : ($durationHours > 0 ? $durationHours . ' jam' : $durationRemainder . ' menit');
    $status = $booking->status;
    $statusValue = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $statusLabel = $status instanceof \App\Enums\BookingStatus
        ? $status->label()
        : ucfirst(str_replace('_', ' ', $statusValue));
    $statusClasses = match ($statusValue) {
        'confirmed', 'paid' => 'border-emerald-300/20 bg-emerald-300/[0.08] text-emerald-200',
        'ongoing' => 'border-sky-300/20 bg-sky-300/[0.08] text-sky-200',
        'completed' => 'border-white/10 bg-white/[0.05] text-zinc-300',
        'cancelled' => 'border-rose-300/20 bg-rose-300/[0.08] text-rose-200',
        default => 'border-amber-200/20 bg-amber-200/[0.08] text-amber-100',
    };
    $facilityItems = collect($detail?->facilities ?? [])->filter(fn ($item) => is_scalar($item))->map(fn ($item) => (string) $item);
@endphp

<article class="overflow-hidden rounded-3xl border border-white/[0.09] bg-zinc-900/75 shadow-2xl shadow-black/10">
    <div class="flex flex-col gap-4 border-b border-white/[0.07] bg-white/[0.02] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
        <div>
            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-zinc-600">Nomor Booking</p>
            <p class="mt-1 font-mono text-lg font-semibold tracking-wide text-white">{{ $booking->booking_number ?? $booking->public_id }}</p>
        </div>
        <span class="inline-flex w-fit items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $statusClasses }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            {{ $statusLabel }}
        </span>
    </div>

    <div class="grid gap-px bg-white/[0.06] sm:grid-cols-2">
        <div class="bg-zinc-900 px-5 py-5 sm:px-7">
            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-zinc-600">Pelanggan</p>
            <p class="mt-2 font-semibold text-white">{{ $customerName }}</p>
            @if ($detail?->customer_email)
                <p class="mt-1 break-all text-sm text-zinc-500">{{ $detail->customer_email }}</p>
            @endif
        </div>
        <div class="bg-zinc-900 px-5 py-5 sm:px-7">
            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-zinc-600">Jadwal</p>
            @if ($startAt)
                <p class="mt-2 font-semibold text-white">{{ \Illuminate\Support\Carbon::parse($startAt)->translatedFormat('d M Y') }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ \Illuminate\Support\Carbon::parse($startAt)->format('H:i') }}@if ($endAt) – {{ \Illuminate\Support\Carbon::parse($endAt)->format('H:i') }} WIB @else WIB @endif</p>
            @else
                <p class="mt-2 text-sm text-zinc-500">Jadwal belum ditentukan.</p>
            @endif
        </div>
    </div>

    <div class="px-5 py-6 sm:px-7">
        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-zinc-600">Paket</p>
                <p class="mt-2 font-semibold text-white">{{ $packageName }}</p>
                <p class="mt-1 text-sm text-zinc-500">{{ $playstationType }} · {{ $durationLabel }}</p>
            </div>
            <div>
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-zinc-600">Unit</p>
                <p class="mt-2 font-semibold text-white">{{ $unitName }}</p>
                <p class="mt-1 text-sm text-zinc-500">Kode unit {{ $unitCode }}</p>
            </div>
        </div>

        @if ($facilityItems->isNotEmpty())
            <div class="mt-6 border-t border-white/[0.07] pt-5">
                <p class="text-[0.65rem] font-semibold uppercase tracking-[0.18em] text-zinc-600">Fasilitas Paket</p>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach ($facilityItems as $facility)
                        <div class="flex items-center gap-2 text-sm text-zinc-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-200/70"></span>
                            {{ $facility }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-4 border-t border-white/[0.07] pt-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs text-zinc-600">Total harga dari server</p>
                <p class="mt-1 text-xs text-zinc-600">Finalisasi dan pembayaran dikonfirmasi terpisah.</p>
            </div>
            <div class="sm:text-right">
                <p class="text-sm font-medium text-zinc-500">Total</p>
                <p class="mt-1 text-2xl font-semibold tracking-tight text-white">Rp {{ number_format((float) $booking->total_price, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    @if ($customerWhatsapp)
        <div class="border-t border-white/[0.07] bg-white/[0.018] px-5 py-4 sm:px-7">
            <p class="text-xs leading-5 text-zinc-500">Simpan nomor booking ini. Tim kami akan menghubungi pelanggan melalui WhatsApp untuk konfirmasi.</p>
        </div>
    @endif
</article>
