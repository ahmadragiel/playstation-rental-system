@php
    $siteName = $rentalSettings['rental_name'] ?? config('app.name', 'Nexus Play');
    $logoPath = trim((string) ($rentalSettings['logo'] ?? ''));
    $logoUrl = match (true) {
        $logoPath === '' => null,
        str_starts_with($logoPath, 'http://'), str_starts_with($logoPath, 'https://'), str_starts_with($logoPath, 'data:') => $logoPath,
        str_starts_with($logoPath, 'images/') => asset($logoPath),
        default => \Illuminate\Support\Facades\Storage::disk('public')->url($logoPath),
    };
@endphp
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <title>Masuk Admin · {{ $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative grid min-h-screen place-items-center overflow-hidden bg-slate-950 px-4 py-10 text-slate-200 antialiased">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute left-1/2 top-[-18rem] h-[36rem] w-[36rem] -translate-x-1/2 rounded-full bg-cyan-400/10 blur-3xl"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(148,163,184,0.035)_1px,transparent_1px),linear-gradient(90deg,rgba(148,163,184,0.035)_1px,transparent_1px)] bg-[size:34px_34px] [mask-image:linear-gradient(to_bottom,black,transparent)]"></div>
    </div>

    <main class="relative w-full max-w-md">
        <a href="{{ route('home') }}" class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-cyan-300">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke website
        </a>

        <section class="panel p-6 shadow-2xl shadow-slate-950/60 sm:p-8">
            <div class="flex items-center gap-4">
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-12 w-auto max-w-44 object-contain">
                @else
                    <span class="grid size-12 place-items-center rounded-2xl bg-cyan-400 font-black text-slate-950 shadow-lg shadow-cyan-500/10">NP</span>
                @endif
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-300">Secure Access</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-white">Masuk dashboard</h1>
                </div>
            </div>

            <p class="mt-6 text-sm leading-6 text-slate-500">Gunakan akun admin untuk mengelola booking, unit, pembayaran, dan laporan rental.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5" data-loading="true">
                @csrf

                <div>
                    <label for="email" class="form-label">Email admin</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="admin@nexusplay.test" class="form-input">
                    @error('email')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="form-label">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan password" class="form-input">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-400">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember')) class="size-4 rounded border-white/20 bg-slate-950 text-cyan-400 focus:ring-cyan-400/30">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit" class="btn-primary w-full">Masuk ke Dashboard</button>
            </form>

            <div class="mt-7 border-t border-white/10 pt-5 text-center">
                <p class="text-xs leading-5 text-slate-600">Akses dilindungi oleh session encryption, CSRF, dan login rate limit.</p>
            </div>
        </section>
    </main>
</body>
</html>
