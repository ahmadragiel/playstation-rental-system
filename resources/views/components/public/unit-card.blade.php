@props([
    'unit',
])

@php
    $status = $unit->effectiveStatus();
    $statusValue = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $statusLabel = $status instanceof \App\Enums\UnitStatus
        ? $status->label()
        : ucfirst(str_replace('_', ' ', $statusValue));
    $statusClasses = match ($statusValue) {
        'available' => 'border-emerald-300/20 bg-emerald-300/[0.08] text-emerald-200',
        'booked' => 'border-sky-300/20 bg-sky-300/[0.08] text-sky-200',
        'in_use' => 'border-amber-200/20 bg-amber-200/[0.08] text-amber-100',
        'maintenance' => 'border-rose-300/20 bg-rose-300/[0.08] text-rose-200',
        default => 'border-white/10 bg-white/[0.05] text-zinc-300',
    };
    $photoUrl = $unit->photo_url;
    $gameNames = $unit->games->take(3)->pluck('name');
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-white/[0.08] bg-zinc-900/70 transition duration-300 hover:-translate-y-1 hover:border-white/[0.14]">
    <div class="relative aspect-[16/10] overflow-hidden border-b border-white/[0.06] bg-zinc-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_20%,rgba(255,255,255,0.08),transparent_38%)]"></div>
        <div class="absolute inset-0 opacity-30 [background-image:linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] [background-size:28px_28px]"></div>

        <div class="absolute inset-0 grid place-items-center">
            <div class="text-center text-zinc-700">
                <svg class="mx-auto h-12 w-12" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="7" y="17" width="50" height="30" rx="8" />
                    <path d="M18 30v7M14.5 33.5h7M44 31h.01M49 36h.01" stroke-linecap="round" />
                </svg>
                <span class="mt-2 block text-xs font-semibold uppercase tracking-[0.18em]">{{ $unit->code }}</span>
            </div>
        </div>

        @if ($photoUrl)
            <img src="{{ $photoUrl }}" alt="Unit {{ $unit->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]" loading="lazy" onerror="this.remove()">
        @endif

        <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-zinc-950 to-transparent"></div>
        <div class="absolute left-4 top-4 flex flex-wrap gap-2">
            <span class="rounded-full border border-white/10 bg-zinc-950/80 px-2.5 py-1 text-[0.65rem] font-semibold uppercase tracking-wider text-zinc-200 backdrop-blur">{{ $unit->type?->name ?? 'PlayStation' }}</span>
        </div>
        <span class="absolute bottom-4 right-4 inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[0.68rem] font-semibold {{ $statusClasses }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            {{ $statusLabel }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5 sm:p-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">{{ $unit->code }}</p>
            <h3 class="mt-1.5 text-xl font-semibold tracking-tight text-white">{{ $unit->name }}</h3>
        </div>

        <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-xl border border-white/[0.06] bg-white/[0.025] p-3">
                <dt class="text-[0.65rem] uppercase tracking-wider text-zinc-600">Lokasi</dt>
                <dd class="mt-1 truncate font-medium text-zinc-300">{{ $unit->location ?: 'Akan dikonfirmasi' }}</dd>
            </div>
            <div class="rounded-xl border border-white/[0.06] bg-white/[0.025] p-3">
                <dt class="text-[0.65rem] uppercase tracking-wider text-zinc-600">Kondisi</dt>
                <dd class="mt-1 truncate font-medium text-zinc-300">{{ $unit->condition ?: 'Standard' }}</dd>
            </div>
        </dl>

        <div class="mt-5">
            <p class="text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-zinc-600">Game terkait</p>
            <div class="mt-2 flex flex-wrap gap-1.5">
                @forelse ($gameNames as $gameName)
                    <span class="rounded-lg border border-white/[0.06] bg-white/[0.035] px-2 py-1 text-xs text-zinc-400">{{ $gameName }}</span>
                @empty
                    <span class="text-xs text-zinc-600">Belum ada daftar game.</span>
                @endforelse
                @if ($unit->games->count() > 3)
                    <span class="rounded-lg border border-white/[0.06] bg-white/[0.035] px-2 py-1 text-xs text-zinc-600">+{{ $unit->games->count() - 3 }}</span>
                @endif
            </div>
        </div>

        <div class="mt-auto pt-6">
            @if ($statusValue === 'available' && $unit->is_active)
                <a href="{{ route('booking.create', ['playstation_unit_id' => $unit->id]) }}" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-amber-200 px-4 text-sm font-bold text-zinc-950 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-200/60 focus:ring-offset-2 focus:ring-offset-zinc-900">
                    Book unit ini
                </a>
            @else
                <div class="flex h-11 w-full cursor-not-allowed items-center justify-center rounded-xl border border-white/[0.07] bg-white/[0.025] px-4 text-sm font-medium text-zinc-600" aria-disabled="true">
                    {{ $statusValue === 'maintenance' ? 'Unit dalam perawatan' : 'Lihat jadwal' }}
                </div>
                @if ($statusValue !== 'maintenance')
                    <a href="{{ route('schedule.index') }}" class="mt-2 block text-center text-xs font-medium text-zinc-500 transition hover:text-zinc-300">Cek jadwal unit</a>
                @endif
            @endif
        </div>
    </div>
</article>
