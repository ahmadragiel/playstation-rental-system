@php
    $isEdit = $game !== null;
    $formAction = $isEdit ? route('admin.games.update', $game) : route('admin.games.store');
    $selectedUnitIds = collect(old('unit_ids', $game?->units->pluck('id')->all() ?? []))
        ->map(fn ($id) => (string) $id)
        ->all();
@endphp

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-semibold">Periksa kembali data yang diisi.</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Game <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" maxlength="255" required value="{{ old('name', $game?->name) }}" placeholder="Contoh: FIFA 24" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="mb-1 block text-sm font-medium text-slate-700">Slug</label>
            <input id="slug" name="slug" type="text" maxlength="255" value="{{ old('slug', $game?->slug) }}" placeholder="Dibuat otomatis dari nama" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-slate-500">Slug harus unik dan akan dibuat otomatis jika dikosongkan.</p>
            @error('slug')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="genre" class="mb-1 block text-sm font-medium text-slate-700">Genre</label>
            <input id="genre" name="genre" type="text" maxlength="255" value="{{ old('genre', $game?->genre) }}" placeholder="Contoh: Sports, Action" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('genre')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="platform" class="mb-1 block text-sm font-medium text-slate-700">Platform</label>
            <input id="platform" name="platform" type="text" maxlength="255" value="{{ old('platform', $game?->platform) }}" placeholder="Contoh: PS5, PS4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('platform')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="player_count" class="mb-1 block text-sm font-medium text-slate-700">Jumlah Pemain</label>
            <input id="player_count" name="player_count" type="text" maxlength="32" value="{{ old('player_count', $game?->player_count) }}" placeholder="Contoh: 1-4 pemain" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('player_count')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <fieldset>
        <legend class="text-sm font-semibold text-slate-700">Unit yang memiliki game ini</legend>
        <p class="mt-1 text-xs text-slate-500">Pilih satu atau beberapa unit yang memiliki game ini.</p>
        <div class="mt-3 grid gap-2 rounded-lg border border-slate-200 p-3 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($units as $availableUnit)
                <label class="flex items-start gap-2 rounded-md border border-slate-200 p-3 text-sm hover:bg-slate-50">
                    <input type="checkbox" name="unit_ids[]" value="{{ $availableUnit->id }}" @checked(in_array((string) $availableUnit->id, $selectedUnitIds, true)) class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span>
                        <span class="block font-semibold text-slate-800">{{ $availableUnit->code }}</span>
                        <span class="block text-xs text-slate-500">{{ $availableUnit->type?->name }} · {{ $availableUnit->name }}</span>
                    </span>
                </label>
            @empty
                <p class="col-span-full py-3 text-sm text-slate-500">Belum ada unit untuk ditautkan.</p>
            @endforelse
        </div>
        @error('unit_ids')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
    </fieldset>

    <div>
        <label for="cover" class="mb-1 block text-sm font-medium text-slate-700">Cover Game</label>
        <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="mt-1 text-xs text-slate-500">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
        @error('cover')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror

        @if ($game?->cover)
            <div class="mt-4 flex items-center gap-3">
                <img src="{{ $game->cover_url }}" alt="Cover {{ $game->name }}" class="h-24 w-36 rounded-lg object-cover">
                <p class="text-xs text-slate-500">Upload cover baru akan mengganti cover lama.</p>
            </div>
        @endif
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Deskripsi singkat game">{{ old('description', $game?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $game?->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        Game aktif dan dapat digunakan
    </label>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
        <a href="{{ route('admin.games.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Game' }}</button>
    </div>
</form>
