<x-admin-layout title="Game">
    @section('title', 'Game')

    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Game</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola judul game, platform, dan cover yang tersedia.</p>
            </div>
            <a href="{{ route('admin.games.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Tambah Game</a>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.games.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <label for="q" class="mb-1 block text-sm font-medium text-slate-700">Cari game</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama, genre, atau platform" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="platform" class="mb-1 block text-sm font-medium text-slate-700">Platform</label>
                    <select id="platform" name="platform" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua platform</option>
                        @foreach ($platforms as $platformOption)
                            <option value="{{ $platformOption }}" @selected(request('platform') === $platformOption)>{{ $platformOption }}</option>
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
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                <a href="{{ route('admin.games.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Terapkan</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Daftar Game</h2>
                <span class="text-sm text-slate-500">{{ $games->total() }} game</span>
            </div>

            @if ($games->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">♟</div>
                    <h3 class="mt-4 font-semibold text-slate-900">Belum ada game</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Tambahkan judul game pertama untuk mulai mengelola katalog.</p>
                    <a href="{{ route('admin.games.create') }}" class="mt-5 inline-flex rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Tambah Game</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Game</th>
                                <th class="px-5 py-3">Genre</th>
                                <th class="px-5 py-3">Platform</th>
                                <th class="px-5 py-3">Pemain</th>
                                <th class="px-5 py-3">Unit</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($games as $game)
                                <tr class="hover:bg-slate-50">
                                    <td class="min-w-[260px] px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($game->cover)
                                                <img src="{{ $game->cover_url }}" alt="{{ $game->name }}" class="h-12 w-16 rounded-md object-cover">
                                            @else
                                                <div class="flex h-12 w-16 items-center justify-center rounded-md bg-indigo-100 text-xs font-bold text-indigo-700">GAME</div>
                                            @endif
                                            <div>
                                                <p class="font-semibold text-slate-900">{{ $game->name }}</p>
                                                <p class="text-xs text-slate-500">{{ $game->slug }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $game->genre ?: '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $game->platform ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $game->player_count ?: '-' }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $game->units_count }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $game->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $game->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('admin.games.edit', $game) }}" class="mr-3 font-medium text-indigo-600 hover:text-indigo-800">Edit</a>
                                        <form method="POST" action="{{ route('admin.games.destroy', $game) }}" class="inline" onsubmit="return confirm('Hapus game ini? Tindakan tidak dapat dibatalkan.')">
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
                <div class="border-t border-slate-200 px-5 py-4">{{ $games->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
