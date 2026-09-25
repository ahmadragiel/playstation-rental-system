@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'center',
])

@php
    $alignment = $align === 'left' ? 'text-left' : 'text-center';
@endphp

<div class="max-w-3xl {{ $alignment }}">
    @if ($eyebrow)
        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-amber-200/15 bg-amber-200/[0.06] px-3 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-amber-100/80">
            <span class="h-1.5 w-1.5 rounded-full bg-amber-200"></span>
            {{ $eyebrow }}
        </div>
    @endif
    <h2 class="text-balance text-3xl font-semibold tracking-[-0.03em] text-white sm:text-4xl lg:text-5xl">{{ $title }}</h2>
    @if ($description)
        <p class="mt-5 text-pretty text-base leading-8 text-zinc-400 sm:text-lg">{{ $description }}</p>
    @endif
</div>
