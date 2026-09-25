@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $waRaw = (string) ($settings['whatsapp'] ?? '');
    $waNumber = preg_match('/wa\.me\/(\d+)/i', $waRaw, $waMatches) ? $waMatches[1] : preg_replace('/\D+/', '', $waRaw);
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo, saya ingin menanyakan ketersediaan rental.') : null;
    $email = $settings['email'] ?? $settings['contact_email'] ?? null;
    $instagram = $settings['instagram'] ?? null;
    $mapUrl = $settings['map_url'] ?? null;
    $address = $settings['address'] ?? $settings['location'] ?? null;
    if (is_array($address)) { $address = implode(', ', $address); }
    $hours = $settings['opening_hours'] ?? $settings['business_hours'] ?? null;
    if (is_array($hours)) { $hours = implode(' • ', $hours); }
@endphp

<x-public.layout :settings="$settings" title="Kontak" description="Hubungi {{ $siteName }} melalui WhatsApp atau email untuk informasi ketersediaan rental.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-16 sm:py-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(245,158,11,0.075),transparent_32%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-10 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Kontak</span>
            </nav>
            <x-public.section-heading
                eyebrow="Kontak"
                title="Mari mulai percakapan."
                description="Tanyakan rekomendasi unit, paket yang sesuai, atau waktu yang kosong. Untuk permintaan langsung, formulir booking adalah pilihan paling lengkap."
            />
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                @if ($waLink)
                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-11 items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Chat via WhatsApp</a>
                @endif
                <a href="{{ route('booking.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Form Booking</a>
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:gap-16">
                <div class="rounded-[2rem] border border-white/[0.08] bg-zinc-900/60 p-6 sm:p-9">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Informasi Kontak</p>
                    <h2 class="mt-4 text-2xl font-semibold tracking-tight text-white sm:text-3xl">Informasi yang dapat kami bagikan.</h2>
                    <p class="mt-3 text-sm leading-7 text-zinc-500">Gunakan kanal yang paling nyaman. Tim kami akan membantu menjelaskan pilihan unit, paket, dan ketersediaan jadwal.</p>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/[0.07] bg-white/[0.025] p-5">
                            <div class="grid h-10 w-10 place-items-center rounded-xl border border-emerald-300/15 bg-emerald-300/[0.06] text-emerald-200">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38A10.9 10.9 0 0 1 5.62 2.53 1.8 1.8 0 0 0 2.5 4.02C2.5 12.01 7.99 17.5 15.98 17.5a1.8 1.8 0 0 0 1.49-3.12 1.8 1.8 0 0 0-2.3-.27 3.02 3.02 0 0 1-1.3.64 11.87 11.87 0 0 1-7.82-7.82c.17-.4.42-.78.64-1.3a1.8 1.8 0 0 0-.27-2.3 1.8 1.8 0 0 0-1.15-.37Z" /></svg>
                            </div>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">WhatsApp</p>
                            @if ($waLink)
                                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="mt-2 block break-all text-sm font-semibold text-zinc-200 transition hover:text-amber-100">{{ $waRaw }}</a>
                            @else
                                <p class="mt-2 text-sm text-zinc-600">Nomor WhatsApp sedang dikonfigurasi.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-white/[0.07] bg-white/[0.025] p-5">
                            <div class="grid h-10 w-10 place-items-center rounded-xl border border-pink-300/15 bg-pink-300/[0.06] text-pink-200">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                            </div>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">Instagram</p>
                            @if ($instagram)
                                <a href="https://instagram.com/{{ ltrim((string) $instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="mt-2 block break-all text-sm font-semibold text-zinc-200 transition hover:text-pink-100">{{ $instagram }}</a>
                            @else
                                <p class="mt-2 text-sm text-zinc-600">Instagram belum dikonfigurasi.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-white/[0.07] bg-white/[0.025] p-5">
                            <div class="grid h-10 w-10 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-zinc-400">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2.5 4.5A2.5 2.5 0 0 1 5 2h10a2.5 2.5 0 0 1 2.5 2.5v.76l-7.5 3.57L2.5 5.26v-.76Zm0 2.38 7.24 3.45a.75.75 0 0 0 .62 0l7.14-3.4v5.57A2.5 2.5 0 0 1 15 15H5a2.5 2.5 0 0 1-2.5-2.55V6.88Z" /></svg>
                            </div>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">Email</p>
                            @if ($email)
                                <a href="mailto:{{ $email }}" class="mt-2 block break-all text-sm font-semibold text-zinc-200 transition hover:text-white">{{ $email }}</a>
                            @else
                                <p class="mt-2 text-sm text-zinc-600">Email belum dikonfigurasi.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-white/[0.07] bg-white/[0.025] p-5">
                            <div class="grid h-10 w-10 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-zinc-400">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a5.5 5.5 0 0 0-5.5 5.5c0 3.75 5.5 10.5 5.5 10.5S15.5 11.25 15.5 7.5A5.5 5.5 0 0 0 10 2Zm0 7.75a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">Lokasi</p>
                            @if ($address)
                                @if ($mapUrl)
                                    <a href="{{ $mapUrl }}" target="_blank" rel="noopener noreferrer" class="mt-2 block text-sm leading-6 text-zinc-300 transition hover:text-amber-100">{{ $address }} · Lihat peta ↗</a>
                                @else
                                    <p class="mt-2 text-sm leading-6 text-zinc-300">{{ $address }}</p>
                                @endif
                            @else
                                <p class="mt-2 text-sm text-zinc-600">Alamat lengkap sedang dikonfigurasi.</p>
                            @endif
                        </div>

                        <div class="rounded-2xl border border-white/[0.07] bg-white/[0.025] p-5">
                            <div class="grid h-10 w-10 place-items-center rounded-xl border border-white/[0.08] bg-white/[0.035] text-zinc-400">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4a.75.75 0 0 0-1.5 0v3.5c0 .27.14.52.38.64l2.5 1.25a.75.75 0 1 0 .67-1.34L10.75 9.38V6Z" clip-rule="evenodd" /></svg>
                            </div>
                            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-zinc-600">Jam Operasional</p>
                            <p class="mt-2 text-sm leading-6 text-zinc-300">{{ $hours ?: 'Silakan konfirmasi jam operasional melalui WhatsApp.' }}</p>
                        </div>
                    </div>

                    @if (! $waLink && ! $instagram && ! $email && ! $address && ! $hours)
                        <div class="mt-6 rounded-2xl border border-dashed border-white/10 px-5 py-8 text-center text-sm text-zinc-500">Informasi kontak sedang dikonfigurasi. Anda tetap dapat mengirim permintaan melalui formulir booking.</div>
                    @endif
                </div>

                <aside class="lg:sticky lg:top-28 lg:self-start">
                    <div class="overflow-hidden rounded-[2rem] border border-white/[0.09] bg-zinc-900/70 p-6 sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-100/70">Jalur Tercepat</p>
                        <h2 class="mt-4 text-2xl font-semibold tracking-tight text-white">Form booking telah menyiapkan semua detail.</h2>
                        <p class="mt-3 text-sm leading-7 text-zinc-500">Isi paket, unit, jadwal, dan kontak WhatsApp. Data yang berhasil dikirim akan memiliki nomor booking serta status terbaru.</p>
                        <a href="{{ route('booking.create') }}" class="mt-7 inline-flex h-12 w-full items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Buka Form Booking</a>
                        <a href="{{ route('schedule.index') }}" class="mt-3 inline-flex h-12 w-full items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Cek Jadwal Dulu</a>
                    </div>

                    <div class="mt-5 rounded-3xl border border-white/[0.08] bg-white/[0.02] p-6">
                        <h3 class="text-sm font-semibold text-white">Pertanyaan yang sering diajukan</h3>
                        <div class="mt-4 space-y-4 text-sm leading-6 text-zinc-500">
                            <p><span class="font-medium text-zinc-300">Apakah unit langsung dipesan?</span><br>Permintaan akan diperiksa ketersediaan sebelum dikonfirmasi.</p>
                            <p><span class="font-medium text-zinc-300">Kapan pembayaran dilakukan?</span><br>Informasi pembayaran diberikan dalam proses konfirmasi.</p>
                            <p><span class="font-medium text-zinc-300">Bisa mengubah jadwal?</span><br>Hubungi tim melalui WhatsApp untuk mengecek perubahan.</p>
                        </div>
                        <a href="{{ route('how-it-works') }}" class="mt-5 inline-flex text-sm font-semibold text-amber-100 transition hover:text-amber-50">Lihat cara booking</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-public-layout>
