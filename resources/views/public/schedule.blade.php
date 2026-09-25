@php
    $dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $monthNames = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $dateKey = $date->toDateString();
    $previousDate = $date->copy()->subDay()->toDateString();
    $nextDate = $date->copy()->addDay()->toDateString();
@endphp

<x-public.layout :settings="$settings" title="Jadwal Rental" description="Lihat status dan jadwal reservasi setiap unit PlayStation rental pada {{ $date->format('d F Y') }}.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-14 sm:py-20">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(245,158,11,0.07),transparent_32%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-9 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Jadwal</span>
            </nav>

            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Jadwal Rental</p>
                    <h1 class="mt-4 text-balance text-4xl font-semibold tracking-[-0.045em] text-white sm:text-5xl">Pantau ketersediaan per unit.</h1>
                    <p class="mt-5 text-sm leading-7 text-zinc-400 sm:text-base">Status di bawah berdasarkan jadwal yang tercatat pada tanggal pilihan. Booking tetap perlu melalui pemeriksaan server.</p>
                </div>

                <div class="flex items-center justify-between gap-2 rounded-2xl border border-white/[0.08] bg-zinc-900/65 p-2 sm:justify-start">
                    @if ($date->greaterThan(today()->startOfDay()))
                        <a href="{{ route('schedule.index', ['date' => $previousDate]) }}" class="grid h-11 w-11 place-items-center rounded-xl text-zinc-400 transition hover:bg-white/[0.05] hover:text-white" aria-label="Jadwal sehari sebelumnya">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span class="grid h-11 w-11 place-items-center text-zinc-800" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                    <div class="min-w-40 px-2 text-center">
                        <p class="text-[0.65rem] font-semibold uppercase tracking-[0.16em] text-zinc-600">{{ $dayNames[$date->dayOfWeek] }}</p>
                        <p class="mt-1 text-sm font-semibold text-white">{{ $date->day }} {{ $monthNames[$date->month] }} {{ $date->year }}</p>
                    </div>
                    @if ($date->lessThan(today()->addDays(90)->startOfDay()))
                        <a href="{{ route('schedule.index', ['date' => $nextDate]) }}" class="grid h-11 w-11 place-items-center rounded-xl text-zinc-400 transition hover:bg-white/[0.05] hover:text-white" aria-label="Jadwal sehari berikutnya">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3.296 15.847a.75.75 0 0 1 .143-1.052l8-10.5a.75.75 0 0 1 1.127-.075l4.5 4.5a.75.75 0 1 1-1.06 1.06l-3.894-3.893-7.48 9.817a.75.75 0 0 1-1.05.143Z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span class="grid h-11 w-11 place-items-center text-zinc-800" aria-hidden="true">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3.296 15.847a.75.75 0 0 1 .143-1.052l8-10.5a.75.75 0 0 1 1.127-.075l4.5 4.5a.75.75 0 1 1-1.06 1.06l-3.894-3.893-7.48 9.817a.75.75 0 0 1-1.05.143Z" clip-rule="evenodd" /></svg>
                        </span>
                    @endif
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center gap-4 text-xs text-zinc-500">
                <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>Available</span>
                <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-sky-300"></span>Booked</span>
                <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-amber-200"></span>In Use</span>
                <span class="inline-flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-rose-300"></span>Maintenance</span>
                @if ($date->isToday())
                    <span class="rounded-full border border-amber-200/15 bg-amber-200/[0.06] px-2.5 py-1 text-amber-100">Hari ini</span>
                @endif
            </div>
        </div>
    </section>

    <section class="py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($units->isEmpty())
                <div class="rounded-3xl border border-dashed border-white/10 bg-white/[0.02] px-6 py-20 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-white/[0.08] bg-white/[0.03] text-zinc-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M6 8h12a4 4 0 0 1 3.8 2.8l1.2 4a3 3 0 0 1-5.5 2.2l-.7-1h-10l-.7 1A3 3 0 0 1 1 14.8l1.2-4A4 4 0 0 1 6 8Zm2 3v4m-2-2h4m7-1h.01M18.5 13h.01" stroke-linecap="round" /></svg>
                    </div>
                    <h2 class="mt-5 text-lg font-semibold text-white">Jadwal unit belum tersedia</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-500">Belum ada unit aktif untuk ditampilkan. Hubungi tim kami untuk menanyakan ketersediaan.</p>
                    <a href="{{ route('contact') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Hubungi Kami</a>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($units as $unit)
                        @php
                            $effective = $unit->effectiveStatus($date->copy()->setTimeFromTimeString('12:00'));
                            $daySchedules = $unit->relationLoaded('day_schedules')
                                ? $unit->day_schedules
                                : ($unit->relationLoaded('schedules') ? $unit->schedules : collect());
                            $effectiveValue = $effective instanceof \BackedEnum ? $effective->value : (string) $effective;
                            $effectiveLabel = $effective instanceof \App\Enums\UnitStatus
                                ? $effective->label()
                                : ucfirst(str_replace('_', ' ', $effectiveValue));
                            $statusClasses = match ($effectiveValue) {
                                'available' => 'border-emerald-300/20 bg-emerald-300/[0.08] text-emerald-200',
                                'booked' => 'border-sky-300/20 bg-sky-300/[0.08] text-sky-200',
                                'in_use' => 'border-amber-200/20 bg-amber-200/[0.08] text-amber-100',
                                'maintenance' => 'border-rose-300/20 bg-rose-300/[0.08] text-rose-200',
                                default => 'border-white/10 bg-white/[0.05] text-zinc-300',
                            };
                        @endphp

                        <article class="flex h-full flex-col rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-5 sm:p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-[0.65rem] font-semibold uppercase tracking-[0.17em] text-zinc-600">{{ $unit->code }} · {{ $unit->type?->name }}</p>
                                    <h2 class="mt-2 text-lg font-semibold text-white">{{ $unit->name }}</h2>
                                </div>
                                <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border px-2.5 py-1 text-[0.68rem] font-semibold {{ $statusClasses }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $effectiveLabel }}
                                </span>
                            </div>

                            <div class="mt-6 flex-1 border-t border-white/[0.07] pt-5">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">Sesi Tercatat</p>
                                    <p class="text-xs text-zinc-600">{{ $daySchedules->count() }} slot</p>
                                </div>

                                <div class="mt-4 space-y-2.5">
                                    @forelse ($daySchedules as $schedule)
                                        @php
                                            $scheduleStatus = $schedule->status;
                                            $scheduleValue = $scheduleStatus instanceof \BackedEnum ? $scheduleStatus->value : (string) $scheduleStatus;
                                            $scheduleLabel = $scheduleStatus instanceof \App\Enums\ScheduleStatus
                                                ? $scheduleStatus->label()
                                                : ucfirst(str_replace('_', ' ', $scheduleValue));
                                            $scheduleClasses = match ($scheduleValue) {
                                                'reserved' => 'border-sky-300/15 bg-sky-300/[0.05] text-sky-200',
                                                'in_progress' => 'border-amber-200/15 bg-amber-200/[0.05] text-amber-100',
                                                'released' => 'border-emerald-300/15 bg-emerald-300/[0.05] text-emerald-200',
                                                default => 'border-white/[0.07] bg-white/[0.025] text-zinc-400',
                                            };
                                        @endphp
                                        <div class="rounded-xl border border-white/[0.06] bg-zinc-950/45 p-3.5">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="font-mono text-sm font-semibold text-zinc-200">{{ $schedule->start_at->format('H:i') }}–{{ $schedule->end_at->format('H:i') }}</p>
                                                <span class="rounded-md border px-2 py-0.5 text-[0.62rem] font-semibold {{ $scheduleClasses }}">{{ $scheduleLabel }}</span>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-xl border border-dashed border-white/[0.08] px-4 py-5 text-center text-xs leading-5 text-zinc-600">Tidak ada reservasi tercatat pada tanggal ini.</div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-6 border-t border-white/[0.07] pt-5">
                                @if ($effectiveValue === 'available' && $unit->is_active)
                                    <a href="{{ route('booking.create', ['playstation_unit_id' => $unit->id, 'date' => $dateKey]) }}" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-amber-200 px-4 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Book Tanggal Ini</a>
                                @else
                                    <a href="{{ route('contact') }}" class="inline-flex h-11 w-full items-center justify-center rounded-xl border border-white/[0.08] bg-white/[0.025] px-4 text-sm font-medium text-zinc-400 transition hover:bg-white/[0.05]">Tanyakan Unit Ini</a>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="mt-10 flex flex-col items-center justify-between gap-5 rounded-3xl border border-white/[0.08] bg-zinc-900/55 p-6 text-center sm:flex-row sm:text-left">
                <div>
                    <h2 class="font-semibold text-white">Sudah menemukan slot yang sesuai?</h2>
                    <p class="mt-1 text-sm text-zinc-500">Kirim permintaan dan biarkan server melakukan pemeriksaan akhir.</p>
                </div>
                <a href="{{ route('booking.create') }}" class="inline-flex h-11 shrink-0 items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Mulai Booking</a>
            </div>
        </div>
    </section>
</x-public-layout>
