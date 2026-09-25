@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $waRaw = (string) ($settings['whatsapp'] ?? '');
    $waNumber = preg_match('/wa\.me\/(\d+)/i', $waRaw, $waMatches) ? $waMatches[1] : preg_replace('/\D+/', '', $waRaw);
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo, saya ingin bertanya tentang proses booking.') : null;
@endphp

<x-public.layout :settings="$settings" title="Cara Sewa" description="Pelajari langkah pemesanan PlayStation rental di {{ $siteName }} dari pilihan paket hingga konfirmasi.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-16 sm:py-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(245,158,11,0.075),transparent_32%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-10 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Cara Sewa</span>
            </nav>
            <x-public.section-heading
                eyebrow="Cara Sewa"
                title="Dari pilihan menuju sesi dalam sekali jalan."
                description="Ikuti enam tahap berikut. Harga dan ketersediaan selalu diperiksa server agar data yang Anda lihat tetap akurat."
            />
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('booking.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Mulai Booking</a>
                <a href="{{ route('schedule.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Cek Jadwal</a>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-28">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="relative">
                <div class="absolute bottom-10 left-[1.4rem] top-10 w-px bg-gradient-to-b from-amber-200/40 via-white/10 to-transparent sm:left-1/2"></div>
                <div class="space-y-12 sm:space-y-16">
                    @foreach ([
                        ['01', 'Pilih paket', 'Bandingkan jenis PlayStation, durasi, fasilitas, dan harga dari database.', 'Lihat Paket', 'packages.index'],
                        ['02', 'Pilih tanggal dan jam', 'Tentukan tanggal serta waktu mulai yang sesuai dengan jadwal Anda.', 'Cek Jadwal', 'schedule.index'],
                        ['03', 'Isi data pelanggan', 'Masukkan nama, WhatsApp, email opsional, pilihan unit, dan catatan.', 'Isi Formulir', 'booking.create'],
                        ['04', 'Kirim booking', 'Server memvalidasi harga, unit, jam operasional, dan konflik jadwal.', 'Mulai Booking', 'booking.create'],
                        ['05', 'Lakukan pembayaran', 'Konfirmasi melalui Cash, Transfer, atau QRIS sesuai instruksi tim.', 'Hubungi Kami', 'contact'],
                        ['06', 'Datang dan bermain', 'Tiba sesuai jadwal dan nikmati sesi bersama teman.', 'Lihat Unit', 'units.index'],
                    ] as [$number, $stepTitle, $stepDescription, $stepAction, $stepRoute])
                        <article class="relative grid gap-6 sm:grid-cols-[1fr_4rem_1fr] sm:items-center">
                            <div class="sm:col-start-1 sm:text-right">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-200/70">Langkah {{ $number }}</p>
                                <h2 class="mt-2 text-2xl font-semibold tracking-tight text-white">{{ $stepTitle }}</h2>
                                <p class="mt-3 text-sm leading-7 text-zinc-500">{{ $stepDescription }}</p>
                            </div>
                            <div class="absolute left-0 top-1 grid h-11 w-11 place-items-center rounded-2xl border border-amber-200/20 bg-zinc-900 font-mono text-sm font-semibold text-amber-100 shadow-lg shadow-black/20 sm:static sm:col-start-2 sm:mx-auto sm:row-start-1">{{ $number }}</div>
                            <div class="sm:col-start-3 sm:row-start-1">
                                <a href="{{ route($stepRoute) }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-white/10 px-4 text-sm font-semibold text-zinc-200 transition hover:border-white/20 hover:bg-white/[0.05]">{{ $stepAction }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="border-y border-white/[0.06] bg-white/[0.012] py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-public.section-heading
                eyebrow="Informasi Penting"
                title="Detail sebelum booking."
                description="Semua ketentuan aktif ditampilkan dari konfigurasi. Jika belum tersedia, hubungi tim untuk memastikan sebelum mengirim permintaan."
            />

            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['Waktu Minimum', $settings['minimum_booking_duration'] ?? $settings['minimum_booking'] ?? 'Mengikuti durasi paket', 'clock'],
                    ['Jarak Booking', $settings['minimum_booking_advance'] ?? $settings['booking_advance'] ?? 'Konfirmasi mengikuti ketersediaan', 'calendar'],
                    ['Pembayaran', $settings['payment_information'] ?? $settings['payment_method'] ?? 'Informasi diberikan saat konfirmasi', 'wallet'],
                    ['Perubahan Jadwal', $settings['reschedule_policy'] ?? $settings['cancellation_policy'] ?? 'Hubungi tim sebelum mengubah jadwal', 'refresh'],
                ] as [$policyTitle, $policyValue, $policyIcon])
                    @php
                        $policyIcons = [
                            'clock' => 'M12 7v5l3 2m6-2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                            'calendar' => 'M7 3v3m10-3v3M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z',
                            'wallet' => 'M4 7h14a2 2 0 0 1 2 2v9H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12M16 13h4',
                            'refresh' => 'M20 7v5h-5M4 17v-5h5m10.1-3A8 8 0 0 0 5.4 6M4.9 15A8 8 0 0 0 18.6 18',
                        ];
                    @endphp
                    <div class="rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6">
                        <div class="grid h-10 w-10 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-zinc-400">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $policyIcons[$policyIcon] }}" /></svg>
                        </div>
                        <h3 class="mt-5 text-sm font-semibold text-white">{{ $policyTitle }}</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $policyValue }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Siap Memulai?</p>
            <h2 class="mt-4 text-balance text-3xl font-semibold tracking-[-0.04em] text-white sm:text-4xl">Pilih waktu, lalu kami bantu sisanya.</h2>
            <p class="mx-auto mt-4 max-w-xl text-sm leading-7 text-zinc-400">Kirim permintaan melalui formulir yang sudah disiapkan. Data Anda akan kembali ke halaman konfirmasi setelah berhasil diproses.</p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ route('booking.create') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-amber-200 px-6 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Booking Sekarang</a>
                @if ($waLink)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Tanya via WhatsApp</a>
                @endif
            </div>
        </div>
    </section>
</x-public-layout>
