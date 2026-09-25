@props([
    'settings' => [],
])

@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $waRaw = (string) ($settings['whatsapp'] ?? '');
    $waNumber = preg_match('/wa\.me\/(\d+)/i', $waRaw, $waMatches) ? $waMatches[1] : preg_replace('/\D+/', '', $waRaw);
    $waLink = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode('Halo, saya ingin memesan sesi rental.') : null;
    $logoPath = trim((string) ($settings['logo'] ?? ''));
    $logoUrl = match (true) {
        $logoPath === '' => null,
        str_starts_with($logoPath, 'http://'), str_starts_with($logoPath, 'https://'), str_starts_with($logoPath, 'data:') => $logoPath,
        str_starts_with($logoPath, 'images/') => asset($logoPath),
        str_starts_with($logoPath, '/') => url($logoPath),
        default => \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath),
    };

    $links = [
        ['label' => 'Home', 'route' => 'home', 'pattern' => 'home'],
        ['label' => 'Paket Sewa', 'route' => 'packages.index', 'pattern' => 'packages.index'],
        ['label' => 'PlayStation', 'route' => 'units.index', 'pattern' => 'units.index'],
        ['label' => 'Game', 'route' => 'games.index', 'pattern' => 'games.index'],
        ['label' => 'Fasilitas', 'route' => 'facilities', 'pattern' => 'facilities'],
        ['label' => 'Cara Sewa', 'route' => 'how-it-works', 'pattern' => 'how-it-works'],
        ['label' => 'Jadwal', 'route' => 'schedule.index', 'pattern' => 'schedule.index'],
        ['label' => 'Kontak', 'route' => 'contact', 'pattern' => 'contact'],
    ];
@endphp

<header x-data="{ open: false }" @keydown.escape.window="open = false" class="sticky top-0 z-50 border-b border-white/[0.07] bg-zinc-950/85 backdrop-blur-xl">
    <nav class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
        <a href="{{ route('home') }}" class="group flex min-w-0 items-center gap-3" aria-label="{{ $siteName }} — Beranda">
            @if ($logoUrl)
                <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-10 w-auto max-w-32 shrink-0 object-contain sm:max-w-40">
            @else
                <span class="relative grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-xl border border-white/10 bg-zinc-900 shadow-inner shadow-white/[0.04]">
                    <span class="absolute inset-0 bg-gradient-to-br from-amber-200/10 to-transparent"></span>
                    <svg class="relative h-5 w-5 text-amber-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path d="M8.3 7.2h7.4a4.3 4.3 0 0 1 4.18 3.43l1.03 4.6a3.55 3.55 0 0 1-6.54 2.36l-.75-1.2H10.38l-.75 1.2a3.55 3.55 0 0 1-6.54-2.36l1.03-4.6A4.3 4.3 0 0 1 8.3 7.2Z" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.2 10.4v4M6.2 12.4h4M16 11.2h.01M18 13.2h.01" stroke-linecap="round" />
                    </svg>
                </span>
            @endif
            <span class="min-w-0">
                <span class="block max-w-40 truncate text-sm font-semibold tracking-tight text-white sm:max-w-56">{{ $siteName }}</span>
                <span class="block text-[0.65rem] font-medium uppercase tracking-[0.2em] text-zinc-500">PlayStation Rental</span>
            </span>
        </a>

        <div class="hidden items-center gap-0.5 xl:flex">
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['pattern']); @endphp
                <a href="{{ route($link['route']) }}"
                   @if ($isActive) aria-current="page" @endif
                   class="rounded-lg px-3 py-2 text-sm font-medium transition {{ $isActive ? 'bg-white/[0.07] text-white' : 'text-zinc-400 hover:bg-white/[0.04] hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="hidden items-center gap-2 xl:flex">
            @if ($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="grid h-10 w-10 place-items-center rounded-xl border border-white/10 text-zinc-400 transition hover:border-white/20 hover:bg-white/[0.05] hover:text-white" aria-label="Hubungi kami melalui WhatsApp">
                    <svg class="h-[1.05rem] w-[1.05rem]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M20.52 3.45A19.78 19.78 0 0 0 12.04 0C5.46 0 .1 5.36.1 11.94c0 2.1.55 4.16 1.6 5.97L.05 24l6.23-1.63a11.9 11.9 0 0 0 5.76 1.47h.01c6.58 0 11.94-5.36 11.94-11.94a11.85 11.85 0 0 0-3.47-8.45ZM12.05 21.8h-.01a9.9 9.9 0 0 1-5.04-1.38l-.36-.21-3.74.98.99-3.65-.23-.38a9.88 9.88 0 0 1-1.52-5.3c0-5.48 4.46-9.94 9.95-9.94 2.65 0 5.14 1.04 7.03 2.92a9.87 9.87 0 0 1 2.91 7.04c0 5.48-4.46 9.93-9.98 9.93Zm5.48-7.44c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.94 1.18-.17.2-.35.22-.65.08-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.5-1.78-1.67-2.08-.18-.3-.02-.46.13-.61.14-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.8.38-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2-1.41.25-.7.25-1.29.18-1.42-.08-.13-.28-.2-.58-.35Z" />
                    </svg>
                </a>
            @endif

            <a href="{{ route('booking.create') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-amber-200 px-4 text-sm font-bold text-zinc-950 shadow-lg shadow-amber-950/20 transition hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-200/70 focus:ring-offset-2 focus:ring-offset-zinc-950">
                Booking Sekarang
            </a>
        </div>

        <button type="button"
                class="grid h-11 w-11 place-items-center rounded-xl border border-white/10 text-zinc-300 transition hover:bg-white/[0.05] xl:hidden"
                @click="open = !open"
                :aria-expanded="open.toString()"
                aria-controls="mobile-navigation"
                aria-label="Buka atau tutup menu navigasi">
            <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="M4 7h16M4 12h16M4 17h16" stroke-linecap="round" />
            </svg>
            <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path d="m6 6 12 12M18 6 6 18" stroke-linecap="round" />
            </svg>
        </button>
    </nav>

    <div id="mobile-navigation"
         x-show="open"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="open = false"
         class="absolute inset-x-0 top-full max-h-[calc(100vh-4.5rem)] overflow-y-auto border-b border-white/[0.08] bg-zinc-950/98 px-4 pb-5 pt-3 shadow-2xl backdrop-blur-xl xl:hidden">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-2">
            @foreach ($links as $link)
                @php $isActive = request()->routeIs($link['pattern']); @endphp
                <a href="{{ route($link['route']) }}"
                   @click="open = false"
                   @if ($isActive) aria-current="page" @endif
                   class="rounded-xl border px-3 py-3 text-sm font-medium transition {{ $isActive ? 'border-amber-200/20 bg-amber-200/[0.08] text-amber-100' : 'border-white/[0.06] bg-white/[0.025] text-zinc-300 hover:bg-white/[0.05] hover:text-white' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>
        <div class="mx-auto mt-3 flex max-w-7xl gap-2">
            @if ($waLink)
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="inline-flex h-11 flex-1 items-center justify-center rounded-xl border border-white/10 text-sm font-semibold text-zinc-200">WhatsApp</a>
            @endif
            <a href="{{ route('booking.create') }}" @click="open = false" class="inline-flex h-11 flex-[1.5] items-center justify-center rounded-xl bg-amber-200 px-4 text-sm font-bold text-zinc-950">Booking Sekarang</a>
        </div>
    </div>
</header>
