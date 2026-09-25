@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $heroTitle = $settings['hero_title'] ?? 'Main lebih baik. Booking lebih mudah.';
    $heroSubtitle = $settings['hero_subtitle'] ?? 'Pilih unit dan paket yang sesuai, lihat ketersediaan jadwal, lalu kirim permintaan booking tanpa proses yang rumit.';
    $waRaw = (string) ($settings['whatsapp'] ?? '');
    $waNumber = preg_match('/wa\.me\/(\d+)/i', $waRaw, $waMatches) ? $waMatches[1] : preg_replace('/\D+/', '', $waRaw);
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo, saya ingin membuat booking PlayStation.') : null;
    $facilityItems = $settings['facilities'] ?? $settings['amenities'] ?? [];
    if (is_string($facilityItems)) {
        $decodedFacilities = json_decode($facilityItems, true);
        $facilityItems = is_array($decodedFacilities) ? $decodedFacilities : array_filter(array_map('trim', explode(',', $facilityItems)));
    }
    $facilityItems = collect(is_array($facilityItems) ? $facilityItems : [])->filter(fn ($item) => is_scalar($item))->map(fn ($item) => (string) $item);
    if ($facilityItems->isEmpty()) {
        $facilityItems = collect(['AC', 'TV / Monitor', 'WiFi', 'Controller', 'Ruang nyaman', 'Kursi ergonomis', 'Parkir']);
    }
    $startingPrice = (float) ($packages->min(fn ($package) => (float) $package->price) ?? 0);
    $openingHours = $settings['opening_hours'] ?? '10:00-02:00';
    $bannerPath = trim((string) ($settings['banner'] ?? ''));
    $bannerUrl = match (true) {
        $bannerPath === '' => null,
        str_starts_with($bannerPath, 'http://'), str_starts_with($bannerPath, 'https://'), str_starts_with($bannerPath, 'data:') => $bannerPath,
        str_starts_with($bannerPath, 'images/') => asset($bannerPath),
        str_starts_with($bannerPath, '/') => url($bannerPath),
        default => \Illuminate\Support\Facades\Storage::disk('public')->url($bannerPath),
    };
@endphp

<x-public.layout :settings="$settings" title="Beranda" description="Temukan paket, unit, game, dan jadwal PlayStation rental terbaik di {{ $siteName }}.">
    <section class="relative isolate overflow-hidden border-b border-white/[0.06]">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_78%_38%,rgba(245,158,11,0.08),transparent_28%),radial-gradient(circle_at_22%_12%,rgba(255,255,255,0.035),transparent_25%)]"></div>
        @if ($bannerUrl)
            <img src="{{ $bannerUrl }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-[0.08] mix-blend-screen" aria-hidden="true">
        @endif
        <div class="absolute inset-x-0 top-0 -z-10 h-px bg-gradient-to-r from-transparent via-amber-200/20 to-transparent"></div>
        <div class="mx-auto grid min-h-[calc(100svh-4.5rem)] max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 sm:py-20 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-24">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/[0.09] bg-white/[0.035] px-3 py-1.5 text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-zinc-400 backdrop-blur">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-200 opacity-30"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-amber-200"></span>
                    </span>
                    PlayStation Rental
                </div>

                <h1 class="mt-7 text-balance text-5xl font-semibold leading-[0.98] tracking-[-0.055em] text-white sm:text-6xl lg:text-7xl xl:text-[5.4rem]">
                    {{ $heroTitle }}
                </h1>
                <p class="mt-7 max-w-2xl text-pretty text-base leading-8 text-zinc-400 sm:text-lg">
                    {{ $heroSubtitle }}
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('booking.create') }}" class="inline-flex h-13 items-center justify-center gap-2 rounded-2xl bg-amber-200 px-6 text-sm font-bold text-zinc-950 shadow-xl shadow-amber-950/20 transition hover:-translate-y-0.5 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-200/70 focus:ring-offset-2 focus:ring-offset-zinc-950">
                        Mulai Booking
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69l-2.22-2.22a.75.75 0 1 1 1.06-1.06l3.5 3.5a.75.75 0 0 1 0 1.06l-3.5 3.5a.75.75 0 1 1-1.06-1.06l2.22-2.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
                    </a>
                    <a href="#packages" class="inline-flex h-13 items-center justify-center rounded-2xl border border-white/[0.1] bg-white/[0.035] px-6 text-sm font-semibold text-zinc-200 transition hover:border-white/20 hover:bg-white/[0.07] focus:outline-none focus:ring-2 focus:ring-white/20">
                        Lihat Paket
                    </a>
                </div>

                <div class="mt-11 grid max-w-2xl grid-cols-3 divide-x divide-white/[0.08] border-y border-white/[0.07] py-5">
                    <div class="pr-4 sm:pr-6">
                        <p class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">{{ $availableUnitCount }}</p>
                        <p class="mt-1 text-[0.68rem] uppercase tracking-[0.14em] text-zinc-600">Unit tersedia</p>
                    </div>
                    <div class="px-4 sm:px-6">
                        <p class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">Rp {{ number_format($startingPrice, 0, ',', '.') }}</p>
                        <p class="mt-1 text-[0.68rem] uppercase tracking-[0.14em] text-zinc-600">Mulai dari</p>
                    </div>
                    <div class="pl-4 sm:pl-6">
                        <p class="text-base font-semibold tracking-tight text-white sm:text-xl">{{ $openingHours }}</p>
                        <p class="mt-1 text-[0.68rem] uppercase tracking-[0.14em] text-zinc-600">Jam operasional</p>
                    </div>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-xl lg:mx-0 lg:ml-auto" aria-label="Ilustrasi konsol PlayStation">
                <div class="absolute -inset-10 -z-10 rounded-full bg-amber-300/[0.04] blur-3xl"></div>
                <div class="relative overflow-hidden rounded-[2rem] border border-white/[0.09] bg-zinc-900/70 p-3 shadow-2xl shadow-black/30 backdrop-blur">
                    <div class="rounded-[1.45rem] border border-white/[0.06] bg-zinc-950 px-5 pb-7 pt-5 sm:px-8 sm:pb-9 sm:pt-7">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-emerald-300/80"></span>
                                <span class="text-[0.65rem] font-semibold uppercase tracking-[0.2em] text-zinc-500">Rental Console</span>
                            </div>
                            <span class="rounded-lg border border-white/[0.07] bg-white/[0.035] px-2 py-1 text-[0.62rem] font-medium text-zinc-500">PREMIUM</span>
                        </div>

                        <div class="relative mt-7 overflow-hidden rounded-2xl border border-white/[0.07] bg-gradient-to-b from-zinc-800/90 to-zinc-950 px-5 py-8 sm:px-8">
                            <div class="absolute inset-0 opacity-25 [background-image:linear-gradient(rgba(255,255,255,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.04)_1px,transparent_1px)] [background-size:26px_26px]"></div>
                            <div class="absolute left-1/2 top-1/2 h-52 w-52 -translate-x-1/2 -translate-y-1/2 rounded-full bg-amber-200/[0.055] blur-3xl"></div>
                            <svg class="relative mx-auto h-44 w-full max-w-sm" viewBox="0 0 420 220" fill="none" aria-hidden="true">
                                <defs>
                                    <linearGradient id="console-body" x1="80" y1="45" x2="335" y2="180" gradientUnits="userSpaceOnUse">
                                        <stop stop-color="#3f3f46" />
                                        <stop offset="1" stop-color="#18181b" />
                                    </linearGradient>
                                </defs>
                                <path d="M78 59h226c24 0 44 18 47 42l12 91c3 22-14 38-33 33l-50-13H133l-50 13c-19 5-36-11-33-33l12-91c3-24 23-42 47-42H78Z" fill="url(#console-body)" stroke="white" stroke-opacity=".12" stroke-width="2" />
                                <path d="M92 48h198c32 0 59 23 64 55l5 31H23l5-31c5-32 32-55 64-55Z" fill="#27272a" stroke="white" stroke-opacity=".12" stroke-width="2" />
                                <path d="M85 79h207" stroke="white" stroke-opacity=".12" />
                                <path d="M122 91v39M102.5 110.5h39" stroke="#fbbf24" stroke-width="7" stroke-linecap="round" opacity=".9" />
                                <circle cx="288" cy="102" r="5" fill="#71717a" />
                                <circle cx="308" cy="114" r="5" fill="#71717a" />
                                <path d="M276 121v18M267 130h18" stroke="#a1a1aa" stroke-width="2" stroke-linecap="round" />
                                <path d="M173 141h76" stroke="#09090b" stroke-width="22" stroke-linecap="round" />
                                <path d="M173 141h76" stroke="#3f3f46" stroke-width="17" stroke-linecap="round" />
                                <path d="M157 170c14 14 35 20 54 20s40-6 54-20" stroke="white" stroke-opacity=".09" stroke-width="2" />
                            </svg>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.025] p-4">
                                <p class="text-[0.62rem] uppercase tracking-[0.16em] text-zinc-600">Status</p>
                                <div class="mt-2 flex items-center gap-2 text-sm font-semibold text-zinc-200"><span class="h-2 w-2 rounded-full bg-emerald-300"></span>Sistem online</div>
                            </div>
                            <div class="rounded-xl border border-white/[0.07] bg-white/[0.025] p-4">
                                <p class="text-[0.62rem] uppercase tracking-[0.16em] text-zinc-600">Ketersediaan</p>
                                <p class="mt-2 text-sm font-semibold text-zinc-200">{{ $availableUnitCount }} unit tersedia</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-6 -left-3 hidden rounded-2xl border border-white/[0.1] bg-zinc-900/90 p-4 shadow-xl backdrop-blur sm:block lg:-left-8">
                    <p class="text-[0.62rem] uppercase tracking-[0.18em] text-zinc-600">Pilihan pemain</p>
                    <p class="mt-1.5 text-sm font-semibold text-white">Konsol bersama, fokus penuh.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="packages" class="scroll-mt-24 border-b border-white/[0.06] py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <x-public.section-heading
                    eyebrow="Paket Rental"
                    title="Pilih durasi yang pas."
                    description="Harga dan durasi ditampilkan langsung dari data paket. Detail final akan dikonfirmasi kembali oleh sistem."
                    align="left"
                />
                <a href="{{ route('packages.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-zinc-300 transition hover:text-amber-100">
                    Lihat semua paket
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69l-2.22-2.22a.75.75 0 1 1 1.06-1.06l3.5 3.5a.75.75 0 0 1 0 1.06l-3.5 3.5a.75.75 0 1 1-1.06-1.06l2.22-2.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
                </a>
            </div>

            <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @forelse ($packages->take(3) as $package)
                    <x-public.package-card :package="$package" />
                @empty
                    <div class="md:col-span-2 lg:col-span-3">
                        <div class="rounded-3xl border border-dashed border-white/10 bg-white/[0.02] px-6 py-16 text-center">
                            <p class="font-semibold text-zinc-300">Belum ada paket aktif.</p>
                            <p class="mt-2 text-sm text-zinc-600">Hubungi tim kami untuk informasi ketersediaan terbaru.</p>
                            <a href="{{ route('contact') }}" class="mt-5 inline-flex text-sm font-semibold text-amber-100 hover:text-amber-50">Hubungi kami</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="playstation" class="scroll-mt-24 border-b border-white/[0.06] bg-white/[0.012] py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-public.section-heading
                eyebrow="Unit PlayStation"
                title="Ruang yang siap untuk Anda."
                description="Kenali tipe, kondisi, lokasi, serta game yang tersedia pada setiap unit sebelum memilih."
            />

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($units->take(4) as $unit)
                    <x-public.unit-card :unit="$unit" />
                @empty
                    <div class="sm:col-span-2 lg:col-span-4">
                        <div class="rounded-3xl border border-dashed border-white/10 bg-zinc-950/50 px-6 py-16 text-center">
                            <p class="font-semibold text-zinc-300">Belum ada unit aktif.</p>
                            <p class="mt-2 text-sm text-zinc-600">Informasi unit akan diperbarui setelah tersedia.</p>
                            <a href="{{ route('contact') }}" class="mt-5 inline-flex text-sm font-semibold text-amber-100 hover:text-amber-50">Tanya ketersediaan</a>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-9 text-center">
                <a href="{{ route('units.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-300 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white">Lihat semua unit</a>
            </div>
        </div>
    </section>

    <section id="games" class="scroll-mt-24 border-b border-white/[0.06] py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
                <x-public.section-heading
                    eyebrow="Game Library"
                    title="Main bersama teman favorit."
                    description="Koleksi game dikelompokkan agar lebih mudah menemukan judul yang cocok untuk gaya main Anda."
                    align="left"
                />
                <a href="{{ route('games.index') }}" class="inline-flex w-fit items-center gap-2 text-sm font-semibold text-zinc-300 transition hover:text-amber-100">
                    Jelajahi semua game
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h9.69l-2.22-2.22a.75.75 0 1 1 1.06-1.06l3.5 3.5a.75.75 0 0 1 0 1.06l-3.5 3.5a.75.75 0 1 1-1.06-1.06l2.22-2.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" /></svg>
                </a>
            </div>

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($games->take(6) as $game)
                    <x-public.game-card :game="$game" />
                @empty
                    <div class="sm:col-span-2 lg:col-span-3">
                        <div class="rounded-3xl border border-dashed border-white/10 bg-white/[0.02] px-6 py-16 text-center">
                            <p class="font-semibold text-zinc-300">Daftar game sedang disiapkan.</p>
                            <p class="mt-2 text-sm text-zinc-600">Silakan cek kembali atau hubungi tim kami.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="facilities" class="scroll-mt-24 border-b border-white/[0.06] py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-public.section-heading
                eyebrow="Fasilitas"
                title="Detail yang terasa sejak awal."
                description="Ruang rental disiapkan agar Anda dapat fokus pada ritme permainan dan berbagi waktu bersama."
            />

            <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Lounge Nyaman', 'Area duduk dan ruang mampir yang lebih private untuk Anda dan teman.'],
                    ['Konsol Terawat', 'Unit dengan kondisi dan spesifikasi yang ditampilkan secara transparan.'],
                    ['Game Terpilih', 'Katalog game dan kompatibilitas unit dapat dilihat sebelum booking.'],
                    ['Informasi Jadwal', 'Status unit dan sesi membantu Anda memilih waktu dengan lebih pasti.'],
                ] as [$facilityTitle, $facilityDescription])
                    <div class="rounded-3xl border border-white/[0.08] bg-zinc-900/55 p-6">
                        <div class="grid h-10 w-10 place-items-center rounded-xl border border-amber-200/15 bg-amber-200/[0.06] text-amber-100">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 2.5a7.5 7.5 0 1 0 0 15 7.5 7.5 0 0 0 0-15Zm0 3a1.25 1.25 0 1 1 0 2.5A1.25 1.25 0 0 1 10 5.5Zm3.75 8.75a.75.75 0 0 1-1.06.06L10 12.44l-2.69 1.87a.75.75 0 1 1-.87-1.26l3.31-2.3a.75.75 0 0 1 .87 0l3.31 2.3a.75.75 0 0 1-.18 1.4Z" /></svg>
                        </div>
                        <h3 class="mt-5 font-semibold text-white">{{ $facilityTitle }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $facilityDescription }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-2">
                @foreach ($facilityItems->take(8) as $facility)
                    <span class="rounded-full border border-white/[0.07] bg-white/[0.025] px-3 py-1.5 text-xs text-zinc-500">{{ $facility }}</span>
                @endforeach
            </div>

            <div class="mt-9 text-center">
                <a href="{{ route('facilities') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-300 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white">Detail fasilitas</a>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="scroll-mt-24 border-b border-white/[0.06] bg-white/[0.012] py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-public.section-heading
                eyebrow="Cara Sewa"
                title="Enam langkah, lalu main."
                description="Alur rental dibuat jelas agar Anda dapat memilih paket, waktu, dan unit dengan lebih cepat."
            />

            <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['01', 'Pilih Paket', 'Pilih jenis konsol, durasi, dan harga yang sesuai.'],
                    ['02', 'Pilih Tanggal & Jam', 'Tentukan waktu mulai yang Anda inginkan.'],
                    ['03', 'Isi Data', 'Masukkan nama, WhatsApp, email, dan catatan.'],
                    ['04', 'Booking', 'Kirim permintaan dan dapatkan nomor booking.'],
                    ['05', 'Pembayaran', 'Lunasi melalui Cash, Transfer, atau QRIS.'],
                    ['06', 'Datang & Bermain', 'Tiba sesuai jadwal dan siapkan sesi terbaik Anda.'],
                ] as [$stepNumber, $stepTitle, $stepDescription])
                    <div class="relative overflow-hidden rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6">
                        <span class="absolute -right-2 -top-4 text-7xl font-semibold tracking-[-0.08em] text-white/[0.025]">{{ $stepNumber }}</span>
                        <p class="relative text-xs font-semibold tracking-[0.18em] text-amber-200/70">{{ $stepNumber }}</p>
                        <h3 class="relative mt-6 text-lg font-semibold text-white">{{ $stepTitle }}</h3>
                        <p class="relative mt-2 text-sm leading-6 text-zinc-500">{{ $stepDescription }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-9 text-center">
                <a href="{{ route('how-it-works') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-300 transition hover:border-white/20 hover:bg-white/[0.04] hover:text-white">Pelajari alurnya</a>
            </div>
        </div>
    </section>

    <section id="contact" class="scroll-mt-24 py-20 sm:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-[2rem] border border-white/[0.09] bg-zinc-900/70 px-6 py-12 shadow-2xl shadow-black/10 sm:px-10 sm:py-14 lg:px-14">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-amber-200/[0.055] blur-3xl"></div>
                <div class="absolute inset-0 opacity-20 [background-image:linear-gradient(rgba(255,255,255,0.035)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.035)_1px,transparent_1px)] [background-size:30px_30px]"></div>
                <div class="relative flex flex-col gap-9 lg:flex-row lg:items-center lg:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Siap bermain?</p>
                        <h2 class="mt-4 text-balance text-3xl font-semibold tracking-[-0.04em] text-white sm:text-4xl">Sesi bermain Anda berikutnya tinggal satu langkah.</h2>
                        <p class="mt-4 max-w-xl text-sm leading-7 text-zinc-400 sm:text-base">Pilih jadwal yang sesuai atau hubungi tim kami jika Anda membutuhkan rekomendasi unit.</p>
                    </div>
                    <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:flex-col xl:flex-row">
                        <a href="{{ route('booking.create') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-amber-200 px-6 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Booking Sekarang</a>
                        @if ($waLink)
                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-6 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.08]">Hubungi WhatsApp</a>
                        @else
                            <a href="{{ route('contact') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] px-6 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.08]">Kontak Kami</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
