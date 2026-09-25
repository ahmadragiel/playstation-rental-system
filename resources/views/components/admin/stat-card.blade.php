@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'chart',
    'tone' => 'cyan',
])

@php
    $tones = [
        'cyan' => 'bg-cyan-400/10 text-cyan-300 ring-cyan-400/20',
        'blue' => 'bg-blue-400/10 text-blue-300 ring-blue-400/20',
        'emerald' => 'bg-emerald-400/10 text-emerald-300 ring-emerald-400/20',
        'amber' => 'bg-amber-400/10 text-amber-300 ring-amber-400/20',
        'violet' => 'bg-violet-400/10 text-violet-300 ring-violet-400/20',
        'rose' => 'bg-rose-400/10 text-rose-300 ring-rose-400/20',
    ];
    $paths = [
        'chart' => '<path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>',
        'calendar' => '<path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/>',
        'wallet' => '<path d="M3 6a2 2 0 0 1 2-2h14v16H5a2 2 0 0 1-2-2V6Z"/><path d="M3 8h16M16 14h.01"/>',
        'device' => '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 21h10M12 17v4"/>',
    ];
@endphp

<div class="rounded-2xl border border-white/10 bg-slate-900/70 p-5 shadow-xl shadow-slate-950/20">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $label }}</p>
            <p class="mt-3 truncate text-2xl font-bold tracking-tight text-white">{{ $value }}</p>
            @if($hint)<p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>@endif
        </div>
        <span class="grid size-10 shrink-0 place-items-center rounded-xl ring-1 ring-inset {{ $tones[$tone] ?? $tones['cyan'] }}">
            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $paths[$icon] ?? $paths['chart'] !!}</svg>
        </span>
    </div>
</div>
