@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition class="flex items-start gap-3 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-emerald-100" role="status">
        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
        <p class="flex-1 text-sm font-medium">{{ session('success') }}</p>
        <button type="button" class="opacity-70 hover:opacity-100" @click="show = false" aria-label="Tutup">&times;</button>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition class="flex items-start gap-3 rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-rose-100" role="alert">
        <svg class="mt-0.5 size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/></svg>
        <p class="flex-1 text-sm font-medium">{{ session('error') }}</p>
        <button type="button" class="opacity-70 hover:opacity-100" @click="show = false" aria-label="Tutup">&times;</button>
    </div>
@endif

@if($errors->any())
    <div class="rounded-2xl border border-rose-400/20 bg-rose-400/10 p-4 text-rose-100">
        <p class="text-sm font-bold">Periksa kembali data berikut:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif
