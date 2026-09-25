@php
    $isEdit = $package !== null;
    $formAction = $isEdit ? route('admin.packages.update', $package) : route('admin.packages.store');
    $facilitiesValue = old('facilities');
    if (is_array($facilitiesValue)) {
        $facilitiesValue = implode("\n", $facilitiesValue);
    }
    if ($facilitiesValue === null && $package) {
        $facilitiesValue = implode("\n", $package->facilities ?? []);
    }
@endphp

<form method="POST" action="{{ $formAction }}" class="space-y-6">
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
            <label for="playstation_type_id" class="mb-1 block text-sm font-medium text-slate-700">Tipe PlayStation <span class="text-rose-500">*</span></label>
            <select id="playstation_type_id" name="playstation_type_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">Pilih tipe</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected((string) old('playstation_type_id', $package?->playstation_type_id) === (string) $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('playstation_type_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Paket <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" maxlength="255" required value="{{ old('name', $package?->name) }}" placeholder="Contoh: Paket 2 Jam" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="mb-1 block text-sm font-medium text-slate-700">Slug</label>
            <input id="slug" name="slug" type="text" maxlength="255" value="{{ old('slug', $package?->slug) }}" placeholder="Dibuat otomatis dari nama" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            <p class="mt-1 text-xs text-slate-500">Slug harus unik dan akan dibuat otomatis jika dikosongkan.</p>
            @error('slug')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="duration_minutes" class="mb-1 block text-sm font-medium text-slate-700">Durasi (menit) <span class="text-rose-500">*</span></label>
            <input id="duration_minutes" name="duration_minutes" type="number" min="1" step="1" required value="{{ old('duration_minutes', $package?->duration_minutes ?? 60) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('duration_minutes')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="price" class="mb-1 block text-sm font-medium text-slate-700">Harga <span class="text-rose-500">*</span></label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-sm text-slate-500">Rp</span>
                <input id="price" name="price" type="number" min="0" step="0.01" required value="{{ old('price', $package?->price) }}" class="w-full rounded-lg border border-slate-300 py-2 pl-10 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>
            @error('price')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Deskripsi</label>
        <textarea id="description" name="description" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Penjelasan singkat paket">{{ old('description', $package?->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="facilities" class="mb-1 block text-sm font-medium text-slate-700">Fasilitas</label>
        <textarea id="facilities" name="facilities" rows="5" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Satu fasilitas per baris&#10;Contoh:&#10;Extra 30 menit&#10;Snack 1 pcs">{{ $facilitiesValue }}</textarea>
        <p class="mt-1 text-xs text-slate-500">Masukkan satu fasilitas per baris atau pisahkan dengan koma.</p>
        @error('facilities')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
        @error('facilities.*')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $package?->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            Paket aktif dan dapat dipilih
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $package?->is_featured ?? false)) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            Tampilkan sebagai paket unggulan
        </label>
    </div>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
        <a href="{{ route('admin.packages.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Paket' }}</button>
    </div>
</form>
