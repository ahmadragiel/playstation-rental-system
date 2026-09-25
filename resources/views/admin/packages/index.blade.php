<x-admin-layout title="Paket Sewa">
    @section('title', 'Paket Sewa')

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Paket Sewa</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola durasi, harga, dan fasilitas paket playthrough.</p>
            </div>
            <a href="{{ route('admin.packages.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tambah Paket</a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.packages.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <label for="q" class="mb-1 block text-sm font-medium text-slate-700">Cari paket</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama, slug, atau deskripsi" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="type_id" class="mb-1 block text-sm font-medium text-slate-700">Tipe</label>
                    <select id="type_id" name="type_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua tipe</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" @selected((string) request('type_id') === (string) $type->id)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="active" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select id="active" name="active" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        <option value="active" @selected(request('active') === 'active' || request('active') === '1')>Aktif</option>
                        <option value="inactive" @selected(request('active') === 'inactive' || request('active') === '0')>Nonaktif</option>
                    </select>
                </div>
                <div>
                    <label for="featured" class="mb-1 block text-sm font-medium text-slate-700">Unggulan</label>
                    <select id="featured" name="featured" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        <option value="featured" @selected(request('featured') === 'featured' || request('featured') === '1')>Ya</option>
                        <option value="not_featured" @selected(request('featured') === 'not_featured' || request('featured') === '0')>Tidak</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                <a href="{{ route('admin.packages.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Terapkan</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Daftar Paket</h2>
                <span class="text-sm text-slate-500">{{ $packages->total() }} paket</span>
            </div>

            @if ($packages->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">▣</div>
                    <h3 class="mt-4 font-semibold text-slate-900">Belum ada paket sewa</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Buat paket pertama untuk menentukan durasi dan harga sewa.</p>
                    <a href="{{ route('admin.packages.create') }}" class="mt-5 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tambah Paket</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Paket</th>
                                <th class="px-5 py-3">Tipe</th>
                                <th class="px-5 py-3">Durasi</th>
                                <th class="px-5 py-3">Harga</th>
                                <th class="px-5 py-3">Fasilitas</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($packages as $package)
                                <tr class="hover:bg-slate-50">
                                    <td class="min-w-[220px] px-5 py-4">
                                        <p class="font-semibold text-slate-900">{{ $package->name }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $package->slug }}</p>
                                        @if ($package->is_featured)
                                            <span class="mt-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Unggulan</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $package->type?->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $package->duration_label }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">Rp {{ number_format((float) $package->price, 0, ',', '.') }}</td>
                                    <td class="max-w-[220px] px-5 py-4 text-slate-600">
                                        @if (filled($package->facilities))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach (array_slice($package->facilities, 0, 3) as $facility)
                                                    <span class="rounded bg-slate-100 px-2 py-1 text-xs">{{ $facility }}</span>
                                                @endforeach
                                                @if (count($package->facilities) > 3)
                                                    <span class="rounded bg-slate-100 px-2 py-1 text-xs">+{{ count($package->facilities) - 3 }}</span>
                                                @endif
                                            </div>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $package->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $package->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('admin.packages.edit', $package) }}" class="mr-3 font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('admin.packages.destroy', $package) }}" class="inline" onsubmit="return confirm('Hapus paket ini? Tindakan tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-rose-600 hover:text-rose-800">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $packages->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
