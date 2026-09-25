<x-admin-layout title="Jadwal Rental">
    @section('title', 'Jadwal Rental')

    <x-admin.page-header title="Jadwal Rental" subtitle="Pantau occupancy setiap unit berdasarkan tanggal, jam, dan status operational.">
        <x-slot:actions>
            <a href="{{ route('schedule.index', ['date' => $date->toDateString()]) }}" target="_blank" class="btn-secondary">Tampilan Publik</a>
        </x-slot:actions>
    </x-admin.page-header>

    <form method="GET" class="panel grid gap-4 p-4 md:grid-cols-4">
        <div>
            <label class="form-label" for="date">Tanggal</label>
            <input id="date" name="date" type="date" value="{{ $date->toDateString() }}" class="form-input">
        </div>
        <div>
            <label class="form-label" for="unit_id">Unit</label>
            <select id="unit_id" name="unit_id" class="form-select">
                <option value="">Semua unit</option>
                @foreach($allUnits as $unit)<option value="{{ $unit->id }}" @selected((string)($filters['unit_id'] ?? '') === (string)$unit->id)>{{ $unit->code }} · {{ $unit->name }}</option>@endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button class="btn-primary flex-1" data-loading="true">Tampilkan</button>
            <a href="{{ route('admin.schedule') }}" class="btn-secondary">Reset</a>
        </div>
        <div class="flex items-end justify-end gap-2">
            <a href="{{ route('admin.schedule', array_merge($filters, ['date' => $date->subDay()->toDateString()])) }}" class="btn-secondary">←</a>
            <a href="{{ route('admin.schedule', ['date' => $date->toDateString()]) }}" class="btn-secondary">Hari ini</a>
            <a href="{{ route('admin.schedule', array_merge($filters, ['date' => $date->addDay()->toDateString()])) }}" class="btn-secondary">→</a>
        </div>
    </form>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-white">{{ $date->translatedFormat('l, d F Y') }}</h2>
            <p class="mt-1 text-sm text-slate-500">{{ $units->count() }} unit dipantau · {{ $units->sum(fn ($unit) => $unit->day_schedules->count()) }} booking</p>
        </div>
        <div class="flex flex-wrap gap-2 text-xs font-semibold text-slate-400">
            <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-emerald-300">Available</span>
            <span class="rounded-full border border-blue-400/20 bg-blue-400/10 px-3 py-1.5 text-blue-300">Booked</span>
            <span class="rounded-full border border-cyan-400/20 bg-cyan-400/10 px-3 py-1.5 text-cyan-300">In Use</span>
            <span class="rounded-full border border-amber-400/20 bg-amber-400/10 px-3 py-1.5 text-amber-300">Maintenance</span>
        </div>
    </div>

    @if($units->isEmpty())
        <x-admin.empty-state title="Unit tidak ditemukan" description="Ubah filter unit untuk melihat jadwal lainnya." />
    @else
        <div class="grid gap-4 lg:grid-cols-2 2xl:grid-cols-3">
            @foreach($units as $unit)
                @php $effective = $unit->effectiveStatus($date->copy()->setTimeFromTimeString('12:00')); @endphp
                <article class="panel overflow-hidden">
                    <header class="flex items-center justify-between gap-3 border-b border-white/10 p-5">
                        <div class="min-w-0">
                            <p class="font-black text-white">{{ $unit->code }}</p>
                            <p class="mt-1 truncate text-xs text-slate-500">{{ $unit->type->name }} · {{ $unit->location }}</p>
                        </div>
                        <x-admin.status-badge :status="$effective" />
                    </header>
                    <div class="min-h-36 p-5">
                        @if($unit->day_schedules->isEmpty())
                            <div class="flex min-h-28 flex-col items-center justify-center rounded-xl border border-dashed border-emerald-400/15 bg-emerald-400/[0.03] text-center">
                                <span class="grid size-9 place-items-center rounded-full bg-emerald-400/10 text-emerald-300">✓</span>
                                <p class="mt-2 text-sm font-semibold text-slate-300">Tidak ada booking</p>
                                <p class="mt-1 text-xs text-slate-600">Unit tersedia sepanjang hari</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($unit->day_schedules as $schedule)
                                    <a href="{{ route('admin.bookings.show', $schedule->booking) }}" class="block rounded-xl border border-white/10 bg-slate-950/55 p-4 transition hover:border-cyan-400/25 hover:bg-cyan-400/[0.04]">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="font-bold text-white">{{ $schedule->start_at->format('H:i') }}–{{ $schedule->end_at->format('H:i') }}</p>
                                            <x-admin.status-badge :status="$schedule->status" />
                                        </div>
                                        <p class="mt-2 text-sm font-semibold text-cyan-300">{{ $schedule->booking->booking_number }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $schedule->booking->status->label() }} · {{ $schedule->booking->customer->name }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-admin-layout>
