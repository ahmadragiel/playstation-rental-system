@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $waRaw = (string) ($settings['whatsapp'] ?? '');
    $waNumber = preg_match('/wa\.me\/(\d+)/i', $waRaw, $waMatches) ? $waMatches[1] : preg_replace('/\D+/', '', $waRaw);
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo, saya ingin menanyakan fasilitas rental.') : null;
    $address = $settings['address'] ?? $settings['location'] ?? null;
    if (is_array($address)) { $address = implode(', ', $address); }
    $hours = $settings['opening_hours'] ?? $settings['business_hours'] ?? null;
    if (is_array($hours)) { $hours = implode(' • ', $hours); }
    $facilityItems = $settings['facilities'] ?? $settings['amenities'] ?? [];
    if (is_string($facilityItems)) {
        $decodedFacilities = json_decode($facilityItems, true);
        $facilityItems = is_array($decodedFacilities) ? $decodedFacilities : array_filter(array_map('trim', explode(',', $facilityItems)));
    }
    $facilityItems = collect(is_array($facilityItems) ? $facilityItems : [])->filter(fn ($item) => is_scalar($item))->map(fn ($item) => (string) $item);
@endphp

<x-public.layout :settings="$settings" title="Fasilitas" description="Pelajari fasilitas, lokasi, jam operasional, dan informasi {{ $siteName }}.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-16 sm:py-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(245,158,11,0.075),transparent_32%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-10 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Fasilitas</span>
            </nav>
            <x-public.section-heading
                eyebrow="Fasilitas"
                title="Nyaman dari detik pertama."
                description="Ruang yang tertata, unit yang informatif, dan informasi operasional yang transparan untuk mendukung sesi bermain Anda."
            />
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('booking.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Booking Sekarang</a>
                <a href="{{ route('units.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Lihat Unit</a>
            </div>
        </div>
    </section>

    <section class="border-b border-white/[0.06] py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['AC', 'Ruang playback tetap sejuk dan nyaman selama sesi panjang.', 'M12 2v20M5 6h14M7 10h10a3 3 0 0 1 0 6H9a3 3 0 0 1 0-6ZM9 10v10M15 10v10'],
                    ['TV / Monitor', 'Layar yang sesuai untuk PS4 dan PS5, termasuk monitor premium untuk unit VIP.', 'M4 5h16a2 2 0 0 1 2 2v10H2V7a2 2 0 0 1 2-2ZM8 21h8M12 17v4'],
                    ['WiFi', 'Koneksi stabil untuk update game, fitur online, dan luciditasthi grind.', 'M5 12.5a10 10 0 0 1 14 0M8.5 16a5 5 0 0 1 7 0M12 20h.01'],
                    ['Controller', 'Controller original dan tambahan yang siap digunakan untuk unit yang Anda pesan.', 'M7 8h10a5 5 0 0 1 4.6 6.9l-1.2 3a2 2 0 0 1-3.7.3L15 16H9l-1.7 2.2a2 2 0 0 1-3.7-.3l-1.2-3A5 5 0 0 1 7 8Z M7 11v4M5 13h4M16 12h.01M19 14h.01'],
                    ['Ruang Nyaman', 'Area mampir dan ruang tunggal yang rapi untuk menunggu atau beristirahat.', 'M4 11V8a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v3M3 11h18v7H3zM6 18v3M18 18v3'],
                    ['Tempat Duduk', 'Kursi ergonomis dan sofa premium pada area VIP untuk sesi yang lebih santai.', 'M6 11V8a3 3 0 0 1 3-3h6a3 3 0 0 1 3 3v3M4 11h16v6H4zM7 17v4M17 17v4'],
                    ['Banyak Pilihan Game', 'Katalog aktif dan kompatibilitas game dapat dilihat sebelum memilih unit.', 'M7 8h10a4 4 0 0 1 3.8 2.8l1.4 4.6a3.5 3.5 0 0 1-6.4 2.3L15 16H9l-.8 1.7a3.5 3.5 0 0 1-6.4-2.3l1.4-4.6A4 4 0 0 1 7 8Z'],
                    ['Parkir', 'Tersedia area parkir untuk memudahkan proses datang dan pulang.', 'M5 20V4h10a4 4 0 0 1 0 8H5M8 20v-4M8 12h7'],
                ] as [$title, $description, $iconPath])
                    <div class="rounded-3xl border border-white/[0.08] bg-zinc-900/55 p-6 transition hover:border-white/[0.13] sm:p-7">
                        <div class="grid h-11 w-11 place-items-center rounded-2xl border border-amber-200/15 bg-amber-200/[0.055] text-amber-100">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $iconPath }}" /></svg>
                        </div>
                        <h2 class="mt-5 text-lg font-semibold text-white">{{ $title }}</h2>
                        <p class="mt-2 text-sm leading-7 text-zinc-500">{{ $description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white/[0.012] py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:gap-16">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Informasi Praktis</p>
                    <h2 class="mt-4 text-3xl font-semibold tracking-[-0.04em] text-white sm:text-4xl">Sebelum Anda datang.</h2>
                    <p class="mt-4 text-sm leading-7 text-zinc-400">Pastikan tanggal, unit, dan detail kontak Anda benar. Tim kami akan menggunakan informasi tersebut untuk proses konfirmasi.</p>
                    <a href="{{ route('how-it-works') }}" class="mt-7 inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Cara Booking</a>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6">
                        <div class="grid h-10 w-10 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-zinc-400">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a5.5 5.5 0 0 0-5.5 5.5c0 3.75 5.5 10.5 5.5 10.5S15.5 11.25 15.5 7.5A5.5 5.5 0 0 0 10 2Zm0 7.75a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-600">Lokasi</p>
                        <p class="mt-2 text-sm leading-7 text-zinc-300">{{ $address ?: 'Alamat lengkap akan ditampilkan pada konfirmasi.' }}</p>
                    </div>
                    <div class="rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6">
                        <div class="grid h-10 w-10 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-zinc-400">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4a.75.75 0 0 0-1.5 0v3.5c0 .27.14.52.38.64l2.5 1.25a.75.75 0 1 0 .67-1.34L10.75 9.38V6Z" clip-rule="evenodd" /></svg>
                        </div>
                        <p class="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-600">Jam Operasional</p>
                        <p class="mt-2 text-sm leading-7 text-zinc-300">{{ $hours ?: 'Hubungi kami untuk memastikan jam operasional pada tanggal booking.' }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 rounded-3xl border border-white/[0.08] bg-zinc-900/55 p-6 sm:p-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-white">Fasilitas tambahan</h2>
                        <p class="mt-1 text-sm text-zinc-500">Daftar yang dikonfigurasi untuk {{ $siteName }}.</p>
                    </div>
                    @if ($waLink)
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-fit items-center justify-center rounded-xl border border-white/10 px-4 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Tanya Fasilitas</a>
                    @endif
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    @forelse ($facilityItems as $facility)
                        <span class="rounded-xl border border-white/[0.07] bg-white/[0.025] px-3 py-2 text-sm text-zinc-400">{{ $facility }}</span>
                    @empty
                        <span class="text-sm text-zinc-600">Daftar fasilitas tambahan sedang dikonfigurasi.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
