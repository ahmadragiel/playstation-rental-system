<x-admin-layout title="Pelanggan">
    @section('title', 'Pelanggan')

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Pelanggan</h1>
            <p class="mt-1 text-sm text-slate-500">Lihat data pelanggan dan riwayat sewa mereka.</p>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ session('error') }}</div>
        @endif

        <form method="GET" action="{{ route('admin.customers.index') }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label for="q" class="mb-1 block text-sm font-medium text-slate-700">Cari pelanggan</label>
                    <input id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Nama, WhatsApp, atau email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="bookings" class="mb-1 block text-sm font-medium text-slate-700">Booking</label>
                    <select id="bookings" name="bookings" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        <option value="">Semua pelanggan</option>
                        <option value="with" @selected(request('bookings') === 'with')>Sudah pernah booking</option>
                        <option value="without" @selected(request('bookings') === 'without')>Belum pernah booking</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                <a href="{{ route('admin.customers.index') }}" class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Terapkan</button>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <h2 class="font-semibold text-slate-900">Daftar Pelanggan</h2>
                <span class="text-sm text-slate-500">{{ $customers->total() }} pelanggan</span>
            </div>

            @if ($customers->isEmpty())
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">♙</div>
                    <h3 class="mt-4 font-semibold text-slate-900">Belum ada pelanggan</h3>
                    <p class="mx-auto mt-1 max-w-md text-sm text-slate-500">Data pelanggan akan muncul setelah pelanggan melakukan booking.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Pelanggan</th>
                                <th class="px-5 py-3">WhatsApp</th>
                                <th class="px-5 py-3">Email</th>
                                <th class="px-5 py-3">Alamat</th>
                                <th class="px-5 py-3">Total Booking</th>
                                <th class="px-5 py-3">Total Transaksi</th>
                                <th class="px-5 py-3">Booking Terakhir</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($customers as $customer)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">{{ strtoupper(substr($customer->name, 0, 1)) }}</div>
                                            <span class="font-semibold text-slate-900">{{ $customer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $customer->whatsapp }}</td>
                                    <td class="px-5 py-4 text-slate-600">{{ $customer->email ?: '-' }}</td>
                                    <td class="max-w-xs px-5 py-4 text-slate-600">{{ $customer->address ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4">
                                        <span class="inline-flex min-w-8 justify-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $customer->bookings_count }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">Rp {{ number_format((float) $customer->total_transactions, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $customer->last_booking_at?->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right">
                                        <a href="{{ route('admin.customers.show', $customer) }}" class="font-medium text-indigo-600 hover:text-indigo-800">Lihat detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
