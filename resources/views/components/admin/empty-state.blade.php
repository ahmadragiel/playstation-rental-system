@props([
    'title' => 'Belum ada data',
    'description' => 'Data yang tersedia akan ditampilkan di sini.',
])

<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-white/15 bg-slate-900/40 px-6 py-14 text-center">
    <div class="grid size-12 place-items-center rounded-2xl bg-white/5 text-slate-400">
        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16v13H4zM8 7V4h8v3M8 12h8M8 16h5"/></svg>
    </div>
    <h3 class="mt-4 font-bold text-white">{{ $title }}</h3>
    <p class="mt-1 max-w-md text-sm text-slate-500">{{ $description }}</p>
    @isset($action)<div class="mt-5">{{ $action }}</div>@endisset
</div>
