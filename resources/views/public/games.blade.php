<x-public.layout :settings="$settings" title="Daftar Game" description="Katalog game PlayStation rental, genre, jumlah pemain, dan unit yang tersedia.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-16 sm:py-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(196,181,253,0.06),transparent_34%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-10 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Game</span>
            </nav>
            <x-public.section-heading
                eyebrow="Game Library"
                title="Cari judul, cari companion."
                description="Telusuri game yang tersedia beserta genre, jumlah pemain, dan unit yang dapat menanganinya." />
            <div class="mt-7 flex justify-center">
                <span class="rounded-full border border-white/[0.08] bg-white/[0.03] px-4 py-2 text-xs font-medium text-zinc-500">{{ $games->count() }} game dalam katalog</span>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($games->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($games as $game)
                        <x-public.game-card :game="$game" />
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-white/10 bg-white/[0.02] px-6 py-20 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-white/[0.08] bg-white/[0.03] text-zinc-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M7 8h10a4 4 0 0 1 3.8 2.8l1.4 4.6a3.5 3.5 0 0 1-6.4 2.3L15 16H9l-.8 1.7a3.5 3.5 0 0 1-6.4-2.3l1.4-4.6A4 4 0 0 1 7 8Z" stroke-linejoin="round" /><path d="M8 11v4M6 13h4M15.5 11.5h.01M18 14h.01" stroke-linecap="round" /></svg>
                    </div>
                    <h2 class="mt-5 text-lg font-semibold text-white">Katalog game belum tersedia</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-500">Daftar game sedang disiapkan. Silakan hubungi tim kami untuk menanyakan judul yang Anda cari.</p>
                    <a href="{{ route('contact') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Tanya Game</a>
                </div>
            @endif

            <div class="mt-12 flex flex-col items-center justify-between gap-5 rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6 text-center sm:flex-row sm:text-left">
                <div>
                    <h2 class="font-semibold text-white">Sudah menemukan game yang ingin dimainkan?</h2>
                    <p class="mt-1 text-sm text-zinc-500">Lihat unit kompatibel dan lanjutkan ke booking.</p>
                </div>
                <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                    <a href="{{ route('units.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Lihat Unit</a>
                    <a href="{{ route('booking.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Booking</a>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
