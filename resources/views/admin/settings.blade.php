<x-admin-layout title="Pengaturan">
    <div class="space-y-6">
        <div>
            <p class="text-sm font-medium text-slate-500">Konfigurasi website</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pengaturan rental</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui informasi bisnis, kontak, hero, dan informasi pembayaran.</p>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                <p class="font-semibold">Periksa kembali data yang Anda masukkan.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="identity-title">
                <h2 id="identity-title" class="font-semibold text-slate-900">Identitas rental</h2>
                <p class="mt-1 text-sm text-slate-500">Informasi yang ditampilkan pada halaman publik.</p>
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="rental_name" class="block text-sm font-medium text-slate-700">Nama rental <span class="text-rose-500">*</span></label>
                        <input id="rental_name" name="rental_name" type="text" required value="{{ old('rental_name', $settings['rental_name'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('rental_name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-slate-700">Deskripsi</label>
                        <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $settings['description'] ?? '') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-slate-700">Alamat</label>
                        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $settings['address'] ?? '') }}</textarea>
                        @error('address')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-slate-700">WhatsApp</label>
                        <input id="whatsapp" name="whatsapp" type="text" value="{{ old('whatsapp', $settings['whatsapp'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="628xxxxxxxxxx">
                        @error('whatsapp')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="instagram" class="block text-sm font-medium text-slate-700">Instagram</label>
                        <input id="instagram" name="instagram" type="text" value="{{ old('instagram', $settings['instagram'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="@nexusplay">
                        @error('instagram')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="opening_hours" class="block text-sm font-medium text-slate-700">Jam buka</label>
                        <input id="opening_hours" name="opening_hours" type="text" value="{{ old('opening_hours', $settings['opening_hours'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Senin - Minggu, 10:00 - 23:00">
                        @error('opening_hours')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="appearance-title">
                <h2 id="appearance-title" class="font-semibold text-slate-900">Tampilan dan hero</h2>
                <p class="mt-1 text-sm text-slate-500">Gunakan aset dengan format JPG, PNG, atau WebP.</p>
                <div class="mt-5 grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="hero_title" class="block text-sm font-medium text-slate-700">Judul hero</label>
                        <input id="hero_title" name="hero_title" type="text" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('hero_title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="hero_subtitle" class="block text-sm font-medium text-slate-700">Subjudul hero</label>
                        <input id="hero_subtitle" name="hero_subtitle" type="text" value="{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('hero_subtitle')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="logo" class="block text-sm font-medium text-slate-700">Logo</label>
                        <input id="logo" name="logo" type="file" accept="image/*" class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-slate-500">Maksimal 2 MB. File lama akan diganti.</p>
                        @error('logo')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        @if (! empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="Logo saat ini" class="mt-3 h-20 rounded-lg border border-slate-200 object-contain p-2">
                        @endif
                    </div>
                    <div>
                        <label for="banner" class="block text-sm font-medium text-slate-700">Banner</label>
                        <input id="banner" name="banner" type="file" accept="image/*" class="mt-1 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-slate-500">Maksimal 5 MB. File lama akan diganti.</p>
                        @error('banner')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                        @if (! empty($bannerUrl))
                            <img src="{{ $bannerUrl }}" alt="Banner saat ini" class="mt-3 h-20 w-full rounded-lg border border-slate-200 object-cover">
                        @endif
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="payment-title">
                <h2 id="payment-title" class="font-semibold text-slate-900">Informasi pembayaran</h2>
                <p class="mt-1 text-sm text-slate-500">Informasi rekening atau instruksi pembayaran yang tampil kepada customer.</p>
                <div class="mt-5">
                    <label for="payment_information" class="block text-sm font-medium text-slate-700">Informasi pembayaran</label>
                    <textarea id="payment_information" name="payment_information" rows="6" class="mt-1 block w-full rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Transfer ke BCA 1234567890 a.n. Nexus Play">{{ old('payment_information', $settings['payment_information'] ?? '') }}</textarea>
                    @error('payment_information')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </section>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Simpan pengaturan</button>
            </div>
        </form>
    </div>
</x-admin-layout>
