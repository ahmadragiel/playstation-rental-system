@props([
    'name',
    'title' => 'Konfirmasi',
    'description' => 'Tindakan ini tidak dapat dibatalkan.',
])

<div x-data="{ open: false }" x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true">
    <div x-show="open" x-cloak class="fixed inset-0 z-[70] grid place-items-center p-4" role="dialog" aria-modal="true" aria-labelledby="{{ $name }}-title">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-950/85 backdrop-blur-sm" x-on:click="open = false"></div>
        <div x-show="open" x-transition class="relative w-full max-w-md rounded-2xl border border-white/10 bg-slate-900 p-6 shadow-2xl">
            <h3 id="{{ $name }}-title" class="text-lg font-bold text-white">{{ $title }}</h3>
            <p class="mt-2 text-sm leading-6 text-slate-400">{{ $description }}</p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" class="btn-secondary" x-on:click="open = false">Batal</button>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
