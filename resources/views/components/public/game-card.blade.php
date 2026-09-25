@props([
    'game',
])

@php
    $coverUrl = $game->cover_url;
    $initials = collect(preg_split('/\s+/', trim($game->name)))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_substr($word, 0, 1))
        ->implode('');
    $unitLabels = $game->units->take(3)->map(fn ($unit) => $unit->code)->filter();
@endphp

<article class="group flex h-full flex-col overflow-hidden rounded-3xl border border-white/[0.08] bg-zinc-900/70 transition duration-300 hover:-translate-y-1 hover:border-white/[0.14]">
    <div class="relative aspect-[16/9] overflow-hidden border-b border-white/[0.06] bg-zinc-950">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(245,158,11,0.10),transparent_42%)]"></div>
        <div class="absolute inset-0 opacity-20 [background-image:linear-gradient(rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.035)_1px,transparent_1px)] [background-size:24px_24px]"></div>
        <div class="absolute inset-0 grid place-items-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/[0.035] text-xl font-semibold text-zinc-500 shadow-inner backdrop-blur-sm">
                {{ $initials ?: 'G' }}
            </div>
        </div>
        @if ($coverUrl)
            <img src="{{ $coverUrl }}" alt="Cover {{ $game->name }}" class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]" loading="lazy" onerror="this.remove()">
        @endif
        <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-zinc-950 to-transparent"></div>
        @if ($game->platform)
            <span class="absolute bottom-3 left-4 rounded-full border border-white/10 bg-zinc-950/75 px-2.5 py-1 text-[0.65rem] font-semibold uppercase tracking-wider text-zinc-200 backdrop-blur">{{ $game->platform }}</span>
        @endif
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <h3 class="text-lg font-semibold tracking-tight text-white">{{ $game->name }}</h3>
            @if ($game->genre)
                <span class="shrink-0 rounded-lg border border-white/[0.07] bg-white/[0.035] px-2 py-1 text-[0.65rem] text-zinc-500">{{ $game->genre }}</span>
            @endif
        </div>

        <p class="mt-3 line-clamp-3 text-sm leading-6 text-zinc-500">
            {{ $game->description ?: 'Informasi detail game akan ditampilkan ketika tersedia.' }}
        </p>

        <div class="mt-5 flex flex-wrap gap-x-4 gap-y-2 text-xs text-zinc-500">
            @if ($game->player_count)
                <span class="inline-flex items-center gap-1.5">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M7 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm7.5-1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.8 16.2c.3-3 2.18-4.7 5.2-4.7 1.35 0 2.47.32 3.35.94A5.2 5.2 0 0 0 12 11.5c3.2 0 5.3 1.55 5.78 4.53A.75.75 0 0 1 17.03 17H2.8a.75.75 0 0 1-1-1.1Z" /></svg>
                    {{ $game->player_count }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3 4.75C3 3.78 3.78 3 4.75 3h10.5C16.22 3 17 3.78 17 4.75v10.5A1.75 1.75 0 0 1 15.25 17H4.75A1.75 1.75 0 0 1 3 15.25V4.75Zm2 1.5a.75.75 0 0 0 0 1.5h6a.75.75 0 0 0 0-1.5H5Zm0 4a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5H5Z" /></svg>
                {{ $game->units->count() }} unit
            </span>
        </div>

        @if ($unitLabels->isNotEmpty())
            <div class="mt-4 flex flex-wrap gap-1.5">
                @foreach ($unitLabels as $unitLabel)
                    <span class="rounded-md bg-white/[0.04] px-2 py-1 text-[0.68rem] text-zinc-500">{{ $unitLabel }}</span>
                @endforeach
                @if ($game->units->count() > 3)
                    <span class="rounded-md bg-white/[0.04] px-2 py-1 text-[0.68rem] text-zinc-600">+{{ $game->units->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <a href="{{ route('units.index') }}" class="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-zinc-300 transition group-hover:text-amber-100">
            Lihat unit yang tersedia
            <svg class="h-4 w-4 transition group-hover:translate-x-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69l-2.22-2.22a.75.75 0 1 1 1.06-1.06l3.5 3.5a.75.75 0 0 1 0 1.06l-3.5 3.5a.75.75 0 1 1-1.06-1.06l2.22-2.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
        </a>
    </div>
</article>
