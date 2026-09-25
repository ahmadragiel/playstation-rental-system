@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $canBook = $packages->isNotEmpty() && $units->isNotEmpty();
    $requestedBookingDate = request()->input('booking_date', request()->input('date'));
    $defaultBookingDate = is_string($requestedBookingDate) && $requestedBookingDate !== ''
        ? $requestedBookingDate
        : now()->addDay()->toDateString();
    $requestedPackageId = request()->input('package_id');
    $requestedUnitId = request()->input('playstation_unit_id');
    $requestedPackageId = is_scalar($requestedPackageId) ? (string) $requestedPackageId : null;
    $requestedUnitId = is_scalar($requestedUnitId) ? (string) $requestedUnitId : null;

    if (! $requestedPackageId && $requestedUnitId) {
        $requestedUnit = $units->first(fn ($unit) => (string) $unit->id === $requestedUnitId);
        $matchingPackage = $packages->first(
            fn ($package) => $package->playstation_type_id === $requestedUnit?->playstation_type_id,
        );
        $requestedPackageId = $matchingPackage ? (string) $matchingPackage->id : null;
    }

    $bookingConfig = [
        'initial' => [
            'packageId' => $requestedPackageId,
            'unitId' => $requestedUnitId,
            'bookingDate' => $defaultBookingDate,
            'startTime' => old('start_time', '19:00'),
        ],
        'quoteUrl' => route('booking.quote'),
        'availabilityUrl' => route('booking.availability'),
        'packages' => $packages->map(fn ($package) => [
            'id' => $package->id,
            'typeId' => $package->playstation_type_id,
            'name' => $package->name,
            'type' => $package->type?->name,
            'duration' => $package->duration_label,
            'price' => (float) $package->price,
        ])->values(),
        'units' => $units->map(fn ($unit) => [
            'id' => $unit->id,
            'typeId' => $unit->playstation_type_id,
            'code' => $unit->code,
            'name' => $unit->name,
            'type' => $unit->type?->name ?? 'PlayStation',
        ])->values(),
    ];
@endphp

<x-public.layout :settings="$settings" title="Form Booking" description="Kirim permintaan booking PlayStation rental di {{ $siteName }}. Isi paket, unit, jadwal, dan data kontak Anda.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-14 sm:py-20">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(245,158,11,0.075),transparent_32%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-9 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Booking</span>
            </nav>
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Form Booking</p>
                <h1 class="mt-4 text-balance text-4xl font-semibold tracking-[-0.045em] text-white sm:text-5xl">Lengkapi detail sesi Anda.</h1>
                <p class="mt-5 text-sm leading-7 text-zinc-400 sm:text-base">Harga dan ketersediaan akan divalidasi kembali oleh server. Form ini tidak mengirim atau mengubah harga secara mandiri.</p>
            </div>
        </div>
    </section>

    <section class="py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($canBook)
                <div class="mb-8 rounded-3xl border border-white/[0.08] bg-white/[0.02] px-5 py-4 text-sm text-zinc-400">
                    Pastikan tanggal dan waktu yang dipilih sudah sesuai. Ketersediaan tetap diperiksa ulang ketika permintaan dikirim.
                </div>
            @else
                <div class="mb-8 rounded-3xl border border-amber-300/20 bg-amber-300/[0.06] px-5 py-5 text-amber-100" role="status">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.168 2.625-1.515 2.625H3.72c-1.347 0-2.188-1.458-1.515-2.625l6.28-10.875ZM10 6a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                        <div>
                            <p class="text-sm font-semibold">Booking belum dapat dikirim</p>
                            <p class="mt-1 text-sm text-amber-100/75">Paket atau unit yang dapat dipesan belum tersedia. Anda dapat menghubungi tim untuk mendapatkan informasi langsung.</p>
                            <a href="{{ route('contact') }}" class="mt-3 inline-flex text-sm font-semibold text-amber-50 underline underline-offset-4">Hubungi Kami</a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1fr)_22rem] xl:gap-10">
                <form action="{{ route('booking.store') }}"
                      method="POST"
                      x-data="bookingForm(@js($bookingConfig))"
                      @submit="submitting = true"
                      class="overflow-hidden rounded-3xl border border-white/[0.09] bg-zinc-900/65">
                    @csrf

                    <div class="border-b border-white/[0.07] px-5 py-5 sm:px-7 sm:py-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-600">Lengkapi Formulir</p>
                        <h2 class="mt-2 text-xl font-semibold text-white">Data Pemesan</h2>
                        <p class="mt-1 text-sm text-zinc-500">Gunakan WhatsApp aktif agar proses konfirmasi dapat dilakukan dengan cepat.</p>
                    </div>

                    @if ($errors->any())
                        <div class="border-b border-rose-300/15 bg-rose-300/[0.055] px-5 py-5 sm:px-7" role="alert">
                            <div class="flex items-start gap-3">
                                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" /></svg>
                                <div>
                                    <p class="text-sm font-semibold text-rose-100">Ada {{ $errors->count() }} field yang perlu diperbaiki</p>
                                    <ul class="mt-2 list-inside list-disc space-y-1 text-xs leading-5 text-rose-100/75">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-7 px-5 py-6 sm:px-7 sm:py-8">
                        <fieldset>
                            <legend class="text-sm font-semibold text-white">Informasi pelanggan</legend>
                            <p class="mt-1 text-xs text-zinc-600">Kolom bertanda bintang wajib diisi.</p>

                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="customer_name" class="block text-sm font-medium text-zinc-300">Nama lengkap <span class="text-amber-200" aria-hidden="true">*</span></label>
                                    <input id="customer_name"
                                           name="customer_name"
                                           type="text"
                                           value="{{ old('customer_name') }}"
                                           autocomplete="name"
                                           maxlength="100"
                                           required
                                           @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white placeholder:text-zinc-700 transition focus:outline-none focus:ring-2', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('customer_name'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('customer_name')])
                                           placeholder="Nama sesuai identitas">
                                    @error('customer_name')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="customer_whatsapp" class="block text-sm font-medium text-zinc-300">Nomor WhatsApp <span class="text-amber-200" aria-hidden="true">*</span></label>
                                    <input id="customer_whatsapp"
                                           name="customer_whatsapp"
                                           type="tel"
                                           value="{{ old('customer_whatsapp') }}"
                                           autocomplete="tel"
                                           inputmode="tel"
                                           maxlength="32"
                                           required
                                           @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white placeholder:text-zinc-700 transition focus:outline-none focus:ring-2', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('customer_whatsapp'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('customer_whatsapp')])
                                           placeholder="08xxxxxxxxxx">
                                    @error('customer_whatsapp')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="customer_email" class="block text-sm font-medium text-zinc-300">Email <span class="text-zinc-600">(opsional)</span></label>
                                    <input id="customer_email"
                                           name="customer_email"
                                           type="email"
                                           value="{{ old('customer_email') }}"
                                           autocomplete="email"
                                           maxlength="150"
                                           @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white placeholder:text-zinc-700 transition focus:outline-none focus:ring-2', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('customer_email'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('customer_email')])
                                           placeholder="nama@email.com">
                                    @error('customer_email')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        <div class="h-px bg-white/[0.07]"></div>

                        <fieldset>
                            <legend class="text-sm font-semibold text-white">Pilihan rental</legend>
                            <p class="mt-1 text-xs text-zinc-600">Pilih satu paket dan satu unit yang akan dikirim ke server.</p>

                            <div class="mt-5 space-y-5">
                                <div>
                                    <label for="package_id" class="block text-sm font-medium text-zinc-300">Paket <span class="text-amber-200" aria-hidden="true">*</span></label>
                                    <select id="package_id"
                                            name="package_id"
                                            x-model="packageId"
                                            @change="refreshAvailability()"
                                            required
                                            @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white transition focus:outline-none focus:ring-2', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('package_id'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('package_id')])
                                            @disabled(! $packages->isNotEmpty())>
                                        <option value="">Pilih paket rental</option>
                                        @foreach ($packages as $package)
                                            <option value="{{ $package->id }}" @selected((string) old('package_id', $requestedPackageId) === (string) $package->id)>
                                                {{ $package->name }} — {{ $package->type?->name }} — {{ $package->duration_label }} — Rp {{ number_format((float) $package->price, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('package_id')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="playstation_unit_id" class="block text-sm font-medium text-zinc-300">Unit PlayStation <span class="text-amber-200" aria-hidden="true">*</span></label>
                                    <select id="playstation_unit_id"
                                            name="playstation_unit_id"
                                            x-model="unitId"
                                            required
                                            @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white transition focus:outline-none focus:ring-2', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('playstation_unit_id'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('playstation_unit_id')])
                                            @disabled(! $units->isNotEmpty())>
                                        <option value="">Pilih unit</option>
                                        <template x-for="unit in units.filter(item => isUnitAvailable(item.id) || String(item.id) === String(unitId))" :key="unit.id">
                                            <option :value="unit.id" x-text="`${unit.code} — ${unit.name} — ${unit.type}`"></option>
                                        </template>
                                    </select>
                                    <p x-show="availabilityLoading" x-cloak class="mt-2 text-xs text-zinc-500">Memeriksa ketersediaan unit…</p>
                                    <p x-show="availabilityError" x-cloak x-text="availabilityError" class="mt-2 text-xs font-medium text-rose-300"></p>
                                    @error('playstation_unit_id')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>

                        <div class="h-px bg-white/[0.07]"></div>

                        <fieldset>
                            <legend class="text-sm font-semibold text-white">Jadwal dan catatan</legend>
                            <p class="mt-1 text-xs text-zinc-600">Waktu akhir dihitung dari durasi paket oleh server.</p>

                            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="booking_date" class="block text-sm font-medium text-zinc-300">Tanggal booking <span class="text-amber-200" aria-hidden="true">*</span></label>
                                    <input id="booking_date"
                                           name="booking_date"
                                           type="date"
                                           value="{{ old('booking_date', $defaultBookingDate) }}"
                                           x-model="bookingDate"
                                           @change="refreshAvailability()"
                                           min="{{ now()->toDateString() }}"
                                           required
                                           @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white transition focus:outline-none focus:ring-2 [color-scheme:dark]', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('booking_date'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('booking_date')])>
                                    @error('booking_date')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-zinc-300">Waktu mulai <span class="text-amber-200" aria-hidden="true">*</span></label>
                                    <input id="start_time"
                                           name="start_time"
                                           type="time"
                                           value="{{ old('start_time', '19:00') }}"
                                           x-model="startTime"
                                           @change="refreshAvailability()"
                                           step="900"
                                           required
                                           @class(['mt-2 block h-12 w-full rounded-xl border bg-zinc-950/70 px-4 text-sm text-white transition focus:outline-none focus:ring-2 [color-scheme:dark]', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('start_time'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('start_time')])>
                                    @error('start_time')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-zinc-300">Catatan <span class="text-zinc-600">(opsional)</span></label>
                                    <textarea id="notes"
                                              name="notes"
                                              rows="4"
                                              maxlength="1000"
                                              @class(['mt-2 block w-full resize-y rounded-xl border bg-zinc-950/70 px-4 py-3 text-sm leading-6 text-white placeholder:text-zinc-700 transition focus:outline-none focus:ring-2', 'border-rose-300/40 focus:border-rose-300/60 focus:ring-rose-300/20' => $errors->has('notes'), 'border-white/[0.09] focus:border-amber-200/50 focus:ring-amber-200/15' => !$errors->has('notes')])
                                              placeholder="Contoh: preferensi pemain, request tertentu, atau informasi lain.">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <p class="mt-2 text-xs font-medium text-rose-300">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="flex flex-col gap-4 border-t border-white/[0.07] bg-white/[0.015] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-7">
                        <p class="text-xs leading-5 text-zinc-600">Dengan mengirim, Anda memastikan data kontak dan jadwal sudah benar.</p>
                        <button type="submit"
                                class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-xl px-6 text-sm font-bold transition focus:outline-none focus:ring-2 focus:ring-amber-200/60 focus:ring-offset-2 focus:ring-offset-zinc-900 disabled:cursor-not-allowed disabled:opacity-55"
                                :class="submitting ? 'bg-zinc-700 text-zinc-400' : 'bg-amber-200 text-zinc-950 hover:bg-amber-100'"
                                x-bind:disabled="submitting || availabilityLoading || availabilityError || !packageId || !unitId"
                                @disabled(! $canBook)>
                            <svg x-show="!submitting" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 3.5a.75.75 0 0 1 .75.75v6.5h6.5a.75.75 0 0 1 0 1.5h-6.5v6.5a.75.75 0 0 1-1.5 0v-6.5h-6.5a.75.75 0 0 1 0-1.5h6.5V4.25A.75.75 0 0 1 10 3.5Z" /></svg>
                            <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" /><path class="opacity-80" d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
                            <span x-show="!submitting">Kirim Permintaan</span>
                            <span x-show="submitting" x-cloak>Mengirim...</span>
                        </button>
                    </div>
                </form>

                <aside class="space-y-5 lg:sticky lg:top-28">
                    <div class="rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-600">Ringkasan Pilihan</p>
                        <h2 class="mt-3 text-lg font-semibold text-white">Harga dari server</h2>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">Ringkasan dan ketersediaan di bawah diambil melalui endpoint server, bukan dihitung dari input browser.</p>

                        <div x-show="!selectedPackage" class="mt-5 rounded-xl border border-dashed border-white/10 px-4 py-5 text-center text-sm text-zinc-600">
                            Pilih paket untuk melihat ringkasan.
                        </div>

                        <div x-show="selectedPackage" x-cloak class="mt-5 space-y-3">
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.025] p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-zinc-200" x-text="selectedPackage?.name"></p>
                                        <p class="mt-1 text-xs text-zinc-600" x-text="`${selectedPackage?.type} · ${selectedPackage?.duration}`"></p>
                                    </div>
                                    <span class="rounded-lg border border-emerald-300/15 bg-emerald-300/[0.06] px-2 py-1 text-[10px] font-semibold text-emerald-200">Aktif</span>
                                </div>
                            </div>

                            <dl class="space-y-3 rounded-xl border border-white/[0.06] bg-white/[0.02] p-4 text-sm">
                                <div class="flex items-center justify-between gap-4"><dt class="text-zinc-600">Tanggal</dt><dd class="font-medium text-zinc-300" x-text="bookingDate"></dd></div>
                                <div class="flex items-center justify-between gap-4"><dt class="text-zinc-600">Mulai</dt><dd class="font-medium text-zinc-300" x-text="startTime"></dd></div>
                                <div class="flex items-center justify-between gap-4"><dt class="text-zinc-600">Selesai</dt><dd class="font-medium text-zinc-300" x-text="quote?.end_time || '—'"></dd></div>
                                <div class="flex items-center justify-between gap-4"><dt class="text-zinc-600">Unit</dt><dd class="max-w-[65%] truncate font-medium text-zinc-300" x-text="selectedUnit ? `${selectedUnit.code} · ${selectedUnit.name}` : 'Belum dipilih'"></dd></div>
                                <div class="flex items-center justify-between gap-4"><dt class="text-zinc-600">Unit tersedia</dt><dd class="font-medium text-zinc-300" x-text="availabilityLoaded ? availableUnitIds.length : '—'"></dd></div>
                            </dl>

                            <div class="flex items-end justify-between gap-4 rounded-xl border border-amber-200/15 bg-amber-200/[0.05] p-4">
                                <div>
                                    <p class="text-xs text-zinc-500">Total sementara</p>
                                    <p class="mt-1 text-xs text-zinc-600">Divalidasi lagi saat submit</p>
                                </div>
                                <p class="text-lg font-semibold text-white" x-text="quote ? formatCurrency(quote.total_price) : '—'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-white/[0.08] bg-white/[0.018] p-6">
                        <h2 class="text-sm font-semibold text-white">Setelah form dikirim</h2>
                        <ol class="mt-4 space-y-4 text-sm text-zinc-500">
                            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full border border-white/[0.08] text-[0.65rem] font-semibold text-zinc-400">1</span><span>Data booking disimpan dengan status awal.</span></li>
                            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full border border-white/[0.08] text-[0.65rem] font-semibold text-zinc-400">2</span><span>Ketersediaan dan detail diverifikasi.</span></li>
                            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full border border-white/[0.08] text-[0.65rem] font-semibold text-zinc-400">3</span><span>Tim menghubungi Anda untuk konfirmasi.</span></li>
                        </ol>
                    </div>

                    <a href="{{ route('schedule.index') }}" class="flex items-center justify-between rounded-3xl border border-white/[0.08] bg-zinc-900/50 p-5 text-sm font-semibold text-zinc-300 transition hover:border-white/[0.14] hover:text-white">
                        Ingin mengecek jadwal dulu?
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69l-2.22-2.22a.75.75 0 1 1 1.06-1.06l3.5 3.5a.75.75 0 0 1 0 1.06l-3.5 3.5a.75.75 0 1 1-1.06-1.06l2.22-2.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
                    </a>
                </aside>
            </div>
        </div>
    </section>
</x-public-layout>
