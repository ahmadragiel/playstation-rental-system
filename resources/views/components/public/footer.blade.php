@props([
    'settings' => [],
])

@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $waRaw = (string) ($settings['whatsapp'] ?? '');
    $waNumber = preg_match('/wa\.me\/(\d+)/i', $waRaw, $waMatches) ? $waMatches[1] : preg_replace('/\D+/', '', $waRaw);
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo, saya ingin konsultasi memesan sesi rental.') : null;
    $logoPath = trim((string) ($settings['logo'] ?? ''));
    $logoUrl = match (true) {
        $logoPath === '' => null,
        str_starts_with($logoPath, 'http://'), str_starts_with($logoPath, 'https://'), str_starts_with($logoPath, 'data:') => $logoPath,
        str_starts_with($logoPath, 'images/') => asset($logoPath),
        str_starts_with($logoPath, '/') => url($logoPath),
        default => \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath),
    };
    $email = $settings['email'] ?? $settings['contact_email'] ?? null;
    $instagram = $settings['instagram'] ?? null;
    $address = $settings['address'] ?? $settings['location'] ?? null;
    $hours = $settings['opening_hours'] ?? $settings['business_hours'] ?? null;
    if (is_array($address)) { $address = implode(', ', $address); }
    if (is_array($hours)) { $hours = implode(' • ', $hours); }
@endphp

<footer class="relative border-t border-white/[0.07] bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-11 w-auto max-w-44 object-contain">
                    @else
                        <span class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 bg-zinc-900">
                            <svg class="h-5 w-5 text-amber-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M8.3 7.2h7.4a4.3 4.3 0 0 1 4.18 3.43l1.03 4.6a3.55 3.55 0 0 1-6.54 2.36l-.75-1.2H10.38l-.75 1.2a3.55 3.55 0 0 1-6.54-2.36l1.03-4.6A4.3 4.3 0 0 1 8.3 7.2Z" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.2 10.4v4M6.2 12.4h4M16 11.2h.01M18 13.2h.01" stroke-linecap="round" />
                            </svg>
                        </span>
                    @endif
                    <span class="max-w-64 truncate font-semibold tracking-tight text-white">{{ $siteName }}</span>
                </a>
                <p class="mt-5 max-w-sm text-sm leading-7 text-zinc-500">
                    {{ $settings['footer_description'] ?? $settings['description'] ?? $settings['site_description'] ?? 'Informasi rental yang jelas, jadwal yang transparan, dan proses booking yang ringkas untuk setiap sesi permainan Anda.' }}
                </p>
            </div>

            <div class="grid grid-cols-2 gap-8 sm:grid-cols-3 lg:col-span-5">
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Explore</h2>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-500">
                        <li><a href="{{ route('packages.index') }}" class="transition hover:text-white">Paket Rental</a></li>
                        <li><a href="{{ route('units.index') }}" class="transition hover:text-white">Unit PlayStation</a></li>
                        <li><a href="{{ route('games.index') }}" class="transition hover:text-white">Daftar Game</a></li>
                        <li><a href="{{ route('schedule.index') }}" class="transition hover:text-white">Jadwal</a></li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Informasi</h2>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-500">
                        <li><a href="{{ route('facilities') }}" class="transition hover:text-white">Fasilitas</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="transition hover:text-white">Cara Sewa</a></li>
                        <li><a href="{{ route('contact') }}" class="transition hover:text-white">Hubungi Kami</a></li>
                        <li><a href="{{ route('booking.create') }}" class="transition hover:text-white">Booking</a></li>
                    </ul>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Akses</h2>
                    <ul class="mt-4 space-y-3 text-sm text-zinc-500">
                        @auth
                            @if (auth()->user()->isAdmin())
                                <li><a href="{{ route('admin.dashboard') }}" class="transition hover:text-white">Dashboard Admin</a></li>
                            @endif
                        @else
                            <li><a href="{{ route('login') }}" class="transition hover:text-white">Login Admin</a></li>
                        @endauth
                        @if ($waLink)
                            <li><a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-white">WhatsApp</a></li>
                        @endif
                        @if ($instagram)
                            <li><a href="https://instagram.com/{{ ltrim((string) $instagram, '@') }}" target="_blank" rel="noopener noreferrer" class="transition hover:text-white">Instagram</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-300">Kontak</h2>
                <div class="mt-4 space-y-4 text-sm text-zinc-500">
                    @if ($address)
                        <p class="flex items-start gap-3 leading-6">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a5.5 5.5 0 0 0-5.5 5.5c0 3.75 5.5 10.5 5.5 10.5S15.5 11.25 15.5 7.5A5.5 5.5 0 0 0 10 2Zm0 7.75a2.25 2.25 0 1 1 0-4.5 2.25 2.25 0 0 1 0 4.5Z" clip-rule="evenodd" /></svg>
                            <span>{{ $address }}</span>
                        </p>
                    @endif
                    @if ($email)
                        <a href="mailto:{{ $email }}" class="flex items-center gap-3 transition hover:text-white">
                            <svg class="h-4 w-4 shrink-0 text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2.5 4.5A2.5 2.5 0 0 1 5 2h10a2.5 2.5 0 0 1 2.5 2.5v.76l-7.5 3.57L2.5 5.26v-.76Zm0 2.38 7.24 3.45a.75.75 0 0 0 .62 0l7.14-3.4v5.57A2.5 2.5 0 0 1 15 15H5a2.5 2.5 0 0 1-2.5-2.55V6.88Z" /></svg>
                            <span class="break-all">{{ $email }}</span>
                        </a>
                    @endif
                    @if ($hours)
                        <p class="flex items-start gap-3 leading-6">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-zinc-600" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4a.75.75 0 0 0-1.5 0v3.5c0 .27.14.52.38.64l2.5 1.25a.75.75 0 1 0 .67-1.34L10.75 9.38V6Z" clip-rule="evenodd" /></svg>
                            <span>{{ $hours }}</span>
                        </p>
                    @endif
                    @if (! $address && ! $email && ! $hours && ! $waLink)
                        <p>Informasi kontak sedang dikonfigurasi.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-white/[0.07] pt-6 text-xs text-zinc-600 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $siteName }}. Seluruh hak dilindungi.</p>
            <p>Booking dan harga akhir diproses oleh sistem.</p>
        </div>
    </div>
</footer>
