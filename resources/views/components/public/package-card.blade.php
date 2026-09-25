@props([
    'package',
])

@php
    $facilityItems = collect($package->facilities ?? [])
        ->filter(fn ($item) => is_scalar($item))
        ->map(fn ($item) => (string) $item)
        ->values();
    $typeName = $package->type?->name ?? 'PlayStation';
    $description = \Illuminate\Support\Str::limit(
        filled($package->description) ? $package->description : 'Paket rental dengan durasi dan fasilitas yang sudah ditentukan.',
        145,
    );
@endphp

<article class="group relative flex h-full flex-col overflow-hidden rounded-3xl border border-white/[0.08] bg-zinc-900/70 p-1 shadow-2xl shadow-black/10 transition duration-300 hover:-translate-y-1 hover:border-white/[0.14] hover:bg-zinc-900">
    <div class="absolute inset-x-8 top-0 h-px bg-gradient-to-r from-transparent via-amber-200/30 to-transparent opacity-0 transition group-hover:opacity-100"></div>
    <div class="flex h-full flex-col rounded-[1.3rem] border border-white/[0.05] bg-zinc-950/45 p-5 sm:p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-zinc-500">{{ $typeName }}</p>
                <h3 class="mt-2 text-xl font-semibold tracking-tight text-white">{{ $package->name }}</h3>
            </div>
            @if ($package->is_featured)
                <span class="shrink-0 rounded-full border border-amber-200/20 bg-amber-200/[0.08] px-2.5 py-1 text-[0.64rem] font-bold uppercase tracking-wider text-amber-100">Pilihan</span>
            @endif
        </div>

        <div class="mt-6 flex items-end gap-1.5 border-b border-white/[0.07] pb-6">
            <span class="pb-1 text-sm font-medium text-zinc-500">Rp</span>
            <span class="text-3xl font-semibold tracking-[-0.04em] text-white">{{ number_format((float) $package->price, 0, ',', '.') }}</span>
            <span class="pb-1 text-xs text-zinc-600">/ sesi</span>
        </div>

        <div class="mt-5 flex items-center gap-2 text-sm text-zinc-300">
            <svg class="h-4 w-4 text-amber-200/80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4a.75.75 0 0 0-1.5 0v3.5c0 .27.14.52.38.64l2.5 1.25a.75.75 0 1 0 .67-1.34L10.75 9.38V6Z" clip-rule="evenodd" /></svg>
            <span>{{ $package->duration_label }}</span>
        </div>

        <p class="mt-4 text-sm leading-7 text-zinc-500">{{ $description }}</p>

        <div class="mt-5 min-h-20 space-y-2.5">
            @forelse ($facilityItems->take(3) as $facility)
                <div class="flex items-start gap-2 text-sm text-zinc-400">
                    <svg class="mt-1 h-3.5 w-3.5 shrink-0 text-emerald-300/80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                    <span>{{ $facility }}</span>
                </div>
            @empty
                <p class="text-sm italic text-zinc-600">Rincian fasilitas akan tampil pada konfirmasi booking.</p>
            @endforelse
        </div>

        <div class="mt-auto pt-6">
            <a href="{{ route('booking.create', ['package_id' => $package->id]) }}" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-white/10 bg-white/[0.045] px-4 text-sm font-semibold text-white transition hover:border-amber-200/30 hover:bg-amber-200 hover:text-zinc-950 focus:outline-none focus:ring-2 focus:ring-amber-200/60 focus:ring-offset-2 focus:ring-offset-zinc-950">
                Pilih paket ini
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69l-2.22-2.22a.75.75 0 1 1 1.06-1.06l3.5 3.5a.75.75 0 0 1 0 1.06l-3.5 3.5a.75.75 0 1 1-1.06-1.06l2.22-2.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
            </a>
        </div>
    </div>
</article>
