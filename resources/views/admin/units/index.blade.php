<x-admin-layout title="Unit PlayStation">
    @section('title', 'Unit PlayStation')

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Unit PlayStation</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola unit, kondisi, dan status ketersediaan unit.</p>
            </div>
            <a href="{{ route('admin.units.create') }}"
                class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Tambah Unit
            </a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <form method="GET" action="{{ route('admin.units.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <div class="lg:col-span-2">
                    <label for="q" class="mb-1 block text-sm font-medium text-slate-700">Cari unit</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}"
                        placeholder="Kode, nama, atau lokasi"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
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
                    <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                    <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected((string) request('status') === $status->value)>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="active" class="mb-1 block text-sm font-medium text-slate-700">Aktif</label>
                    <select id="active" name="active" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        <option value="active" @selected(request('active') === 'active' || request('active') === '1')>Aktif</option>
                        <option value="inactive" @selected(request('active') === 'inactive' || request('active') === '0')>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                <a href="{{ route('admin.units.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Terapkan</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Daftar Unit</h2>
                <span class="text-sm text-slate-500">{{ $units->total() }} unit</span>
            </div>

            @if ($units->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">⌁</div>
                    <h3 class="mt-4 font-semibold text-slate-900">Belum ada unit</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Tambahkan unit PlayStation pertama untuk mulai mengelola ketersediaan.</p>
                    <a href="{{ route('admin.units.create') }}" class="mt-5 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tambah Unit</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Unit</th>
                                <th class="px-5 py-3">Tipe</th>
                                <th class="px-5 py-3">Kondisi</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Lokasi</th>
                                <th class="px-5 py-3">Aktif</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @php
                                $statusColors = [
                                    'available' => 'bg-emerald-100 text-emerald-800',
                                    'booked' => 'bg-blue-100 text-blue-800',
                                    'in_use' => 'bg-cyan-100 text-cyan-800',
                                    'maintenance' => 'bg-amber-100 text-amber-800',
                                ];
                                $activeClass = 'bg-emerald-100 text-emerald-800';
                                $inactiveClass = 'bg-slate-100 text-slate-600';
                            @endphp
                            @foreach ($units as $unit)
                                @php
                                    $statusValue = $unit->status instanceof \App\Enums\UnitStatus
                                        ? $unit->status->value
                                        : (string) $unit->status;
                                    $statusLabel = $unit->status instanceof \App\Enums\UnitStatus
                                        ? $unit->status->label()
                                        : ucfirst($statusValue);
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($unit->photo)
                                                <img src="{{ $unit->photo_url }}" alt="{{ $unit->name }}" class="h-11 w-11 rounded-lg object-cover">
                                            @else
                                                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-100 font-semibold text-indigo-700">PS</div>
                                            @endif
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $unit->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $unit->code }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $unit->type?->name ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $unit->condition }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColors[$statusValue] ?? 'bg-slate-100 text-slate-700' }}">{{ $statusLabel }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $unit->location ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $unit->is_active ? $activeClass : $inactiveClass }}">{{ $unit->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('admin.units.edit', $unit) }}" class="mr-3 font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('admin.units.destroy', $unit) }}" class="inline" onsubmit="return confirm('Hapus unit ini? Tindakan tidak dapat dibatalkan.')">
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
                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $units->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
