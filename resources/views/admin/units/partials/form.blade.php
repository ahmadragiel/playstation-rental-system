@php
    $isEdit = $unit !== null;
    $formAction = $isEdit ? route('admin.units.update', $unit) : route('admin.units.store');
    $currentStatus = old('status', data_get($unit, 'status.value', 'available'));
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
            <label for="playstation_type_id" class="mb-1 block text-sm font-medium text-slate-700">Tipe PlayStation <span class="text-rose-500">*</span></label>
            <select id="playstation_type_id" name="playstation_type_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                <option value="">Pilih tipe</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected((string) old('playstation_type_id', $unit?->playstation_type_id) === (string) $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('playstation_type_id')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="code" class="mb-1 block text-sm font-medium text-slate-700">Kode Unit <span class="text-rose-500">*</span></label>
            <input id="code" name="code" type="text" maxlength="32" required value="{{ old('code', $unit?->code) }}" placeholder="Contoh: PS5-001" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('code')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Nama Unit <span class="text-rose-500">*</span></label>
            <input id="name" name="name" type="text" maxlength="255" required value="{{ old('name', $unit?->name) }}" placeholder="Contoh: PlayStation 5 Lounge A" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="condition" class="mb-1 block text-sm font-medium text-slate-700">Kondisi <span class="text-rose-500">*</span></label>
            <input id="condition" name="condition" type="text" maxlength="255" required value="{{ old('condition', $unit?->condition ?? 'Good') }}" placeholder="Contoh: Good" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('condition')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status <span class="text-rose-500">*</span></label>
            <select id="status" name="status" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @foreach (\App\Enums\UnitStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($currentStatus === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="location" class="mb-1 block text-sm font-medium text-slate-700">Lokasi</label>
            <input id="location" name="location" type="text" maxlength="255" value="{{ old('location', $unit?->location) }}" placeholder="Contoh: Lantai 1 - Room A" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            @error('location')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="photo" class="mb-1 block text-sm font-medium text-slate-700">Foto Unit</label>
        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
        <p class="mt-1 text-xs text-slate-500">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</p>
        @error('photo')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror

        @if ($unit?->photo)
            <div class="mt-4 flex items-center gap-3">
                <img src="{{ $unit->photo_url }}" alt="Foto {{ $unit->name }}" class="h-20 w-20 rounded-lg object-cover">
                <p class="text-xs text-slate-500">Upload foto baru akan mengganti foto lama.</p>
            </div>
        @endif
    </div>

    <div>
        <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Catatan</label>
        <textarea id="notes" name="notes" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Catatan internal unit">{{ old('notes', $unit?->notes) }}</textarea>
        @error('notes')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $unit?->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        Unit aktif dan dapat digunakan
    </label>

    <div class="flex flex-wrap justify-end gap-3 border-t border-slate-200 pt-5">
        <a href="{{ route('admin.units.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Unit' }}</button>
    </div>
</form>
