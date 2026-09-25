<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>@yield('title', isset($title) ? (string) $title : 'Dashboard') · {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-200 antialiased">
<div x-data="adminShell" class="min-h-screen">
    <div x-cloak x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden" aria-hidden="true" x-on:click="sidebarOpen = false"></div>

    <aside
        class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-white/10 bg-slate-950/95 backdrop-blur-xl transition-transform duration-300 lg:translate-x-0"
        :class="sidebarOpen && '!translate-x-0'"
        aria-label="Sidebar admin"
    >
        <div class="flex h-20 items-center justify-between border-b border-white/10 px-6">
            <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3">
                <span class="grid size-10 place-items-center rounded-xl bg-cyan-400 font-black text-slate-950 shadow-lg shadow-cyan-500/10">NP</span>
                <span>
                    <span class="block text-sm font-bold tracking-wide text-white">{{ $rentalSettings['rental_name'] ?? config('app.name') }}</span>
                    <span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-cyan-300">Control Center</span>
                </span>
            </a>
            <button type="button" class="icon-button lg:hidden" @click="sidebarOpen = false" aria-label="Tutup menu">&times;</button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-5" aria-label="Navigasi utama">
            @php
                $navigation = [
                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'grid'],
                    ['route' => 'admin.bookings.index', 'label' => 'Booking', 'icon' => 'calendar'],
                    ['route' => 'admin.schedule', 'label' => 'Jadwal', 'icon' => 'clock'],
                    ['route' => 'admin.customers.index', 'label' => 'Pelanggan', 'icon' => 'users'],
                    ['route' => 'admin.units.index', 'label' => 'Unit PlayStation', 'icon' => 'device'],
                    ['route' => 'admin.packages.index', 'label' => 'Paket Sewa', 'icon' => 'tag'],
                    ['route' => 'admin.games.index', 'label' => 'Game', 'icon' => 'game'],
                    ['route' => 'admin.payments.index', 'label' => 'Pembayaran', 'icon' => 'wallet'],
                    ['route' => 'admin.reports.index', 'label' => 'Laporan', 'icon' => 'chart'],
                    ['route' => 'admin.settings.edit', 'label' => 'Pengaturan', 'icon' => 'settings'],
                ];
                $icons = [
                    'grid' => '<path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/>',
                    'calendar' => '<path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/>',
                    'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
                    'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
                    'device' => '<rect x="3" y="5" width="18" height="12" rx="2"/><path d="M7 21h10M12 17v4"/>',
                    'tag' => '<path d="M20.6 13.6 11 4H4v7l9.6 9.6a2 2 0 0 0 2.8 0l4.2-4.2a2 2 0 0 0 0-2.8Z"/><circle cx="7.5" cy="7.5" r="1"/>',
                    'game' => '<path d="M7 8h10a5 5 0 0 1 4.6 6.9l-1.2 3a2 2 0 0 1-3.7.3L15 16H9l-1.7 2.2a2 2 0 0 1-3.7-.3l-1.2-3A5 5 0 0 1 7 8Z"/><path d="M7 11v4M5 13h4M16 12h.01M19 14h.01"/>',
                    'wallet' => '<path d="M3 6a2 2 0 0 1 2-2h14v16H5a2 2 0 0 1-2-2V6Z"/><path d="M3 8h16M16 14h.01"/>',
                    'chart' => '<path d="M4 19V9M10 19V5M16 19v-7M22 19H2"/>',
                    'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6 1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>',
                ];
            @endphp

            @foreach($navigation as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold transition {{ $active ? 'bg-cyan-400/10 text-cyan-300 ring-1 ring-inset ring-cyan-400/20' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                    @if($active) aria-current="page" @endif
                >
                    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$item['icon']] !!}</svg>
                    <span>{{ $item['label'] }}</span>
                    @if($active)<span class="ml-auto size-1.5 rounded-full bg-cyan-300"></span>@endif
                </a>
            @endforeach
        </nav>

        <div class="border-t border-white/10 p-4">
            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-400 transition hover:bg-white/5 hover:text-white">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3h7v7M10 14 21 3M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
                Lihat Website
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-rose-300 transition hover:bg-rose-500/10" type="submit">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17l5-5-5-5M15 12H3M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 flex h-20 items-center border-b border-white/10 bg-slate-950/85 px-4 backdrop-blur-xl sm:px-6 lg:px-8">
            <button type="button" class="icon-button mr-3 lg:hidden" @click="sidebarOpen = true" aria-label="Buka menu">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Admin Workspace</p>
                <h1 class="mt-0.5 text-lg font-bold text-white">@yield('title', isset($title) ? (string) $title : 'Dashboard')</h1>
            </div>
            <div class="ml-auto flex items-center gap-3">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-white">{{ auth()->user()?->name }}</p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
                <div class="grid size-10 place-items-center rounded-xl border border-white/10 bg-white/5 text-sm font-bold text-cyan-300">
                    {{ str(auth()->user()?->name ?? 'A')->substr(0, 1)->upper() }}
                </div>
            </div>
        </header>

        <main class="min-h-[calc(100vh-5rem)] p-4 sm:p-6 lg:p-8">
            <div class="mx-auto max-w-[1600px] space-y-6">
                <x-admin.flash />
                @yield('content')
                @isset($slot)
                    {!! $slot !!}
                @endisset
            </div>
        </main>
    </div>
</div>
</body>
</html>
