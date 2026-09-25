<x-admin-layout title="Tambah Game">
    @section('title', 'Tambah Game')

    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.games.index') }}" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">← Kembali</a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Tambah Game</h1>
                <p class="mt-1 text-sm text-slate-500">Tambahkan judul game yang tersedia untuk disewakan.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            @include('admin.games.partials.form', ['game' => null])
        </div>
    </div>
</x-admin-layout>
