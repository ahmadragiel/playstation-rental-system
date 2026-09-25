@props([
    'title' => null,
    'description' => null,
    'settings' => [],
    'errors' => null,
])

@php
    $siteName = $settings['rental_name'] ?? $settings['site_name'] ?? $settings['business_name'] ?? config('app.name', 'PlayStation Rental');
    $metaTitle = filled($title) ? $title . ' — ' . $siteName : $siteName;
    $metaDescription = $description ?? ($settings['description'] ?? $settings['site_description'] ?? $settings['tagline'] ?? 'Informasi unit, paket, jadwal, dan booking PlayStation rental.');
    $errors = $errors instanceof \Illuminate\Support\ViewErrorBag
        ? $errors
        : new \Illuminate\Support\ViewErrorBag();
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth bg-zinc-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="theme-color" content="#09090b">
    <meta name="color-scheme" content="dark">

    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('head')
</head>
<body class="min-h-screen overflow-x-hidden bg-zinc-950 font-sans text-zinc-100 antialiased selection:bg-amber-300 selection:text-zinc-950">
    <a href="#main-content" class="fixed left-4 top-4 z-[100] -translate-y-24 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-zinc-950 transition focus:translate-y-0">
        Lewati ke konten
    </a>

    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden" aria-hidden="true">
        <div class="absolute left-1/2 top-[-18rem] h-[36rem] w-[70rem] -translate-x-1/2 rounded-full bg-amber-300/[0.035] blur-3xl"></div>
        <div class="absolute -right-48 top-1/3 h-96 w-96 rounded-full bg-sky-300/[0.025] blur-3xl"></div>
        <div class="absolute -left-48 bottom-0 h-96 w-96 rounded-full bg-violet-300/[0.025] blur-3xl"></div>
    </div>

    <x-public.navbar :settings="$settings" />

    @if (session('success') || session('error'))
        <div class="relative z-40 mx-auto w-full max-w-7xl px-4 pt-5 sm:px-6 lg:px-8" aria-live="polite">
            @if (session('success'))
                <div class="flex items-start gap-3 rounded-2xl border border-emerald-400/20 bg-emerald-400/[0.08] px-4 py-3 text-sm text-emerald-100 shadow-sm backdrop-blur">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-start gap-3 rounded-2xl border border-rose-400/20 bg-rose-400/[0.08] px-4 py-3 text-sm text-rose-100 shadow-sm backdrop-blur">
                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10A8 8 0 1 1 2 10a8 8 0 0 1 16 0Zm-8-4a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 6Zm0 8a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                    </svg>
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif
        </div>
    @endif

    @if ($errors->any())
        <div class="relative z-40 mx-auto mt-5 w-full max-w-7xl px-4 sm:px-6 lg:px-8" role="alert">
            <div class="rounded-2xl border border-amber-300/20 bg-amber-300/[0.07] px-4 py-3 text-sm text-amber-100">
                <p class="font-semibold">Periksa kembali data yang Anda isi.</p>
                <p class="mt-0.5 text-amber-100/70">Periksa kembali kolom yang ditandai sebelum melanjutkan.</p>
            </div>
        </div>
    @endif

    <main id="main-content" class="relative">
        {{ $slot }}
    </main>

    <x-public.footer :settings="$settings" />

    @stack('scripts')
</body>
</html>
