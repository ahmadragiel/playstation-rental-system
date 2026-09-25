<x-public.layout :settings="$settings" title="Paket Rental" description="Daftar paket PlayStation rental beserta durasi, harga, dan fasilitas dari {{ $settings['rental_name'] ?? $settings['site_name'] ?? config('app.name') }}.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-16 sm:py-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(245,158,11,0.075),transparent_32%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-10 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Paket</span>
            </nav>
            <x-public.section-heading
                eyebrow="Paket Rental"
                title="Pilih ritme main Anda."
                description="Semua durasi dan harga di halaman ini berasal dari data paket terbaru. Harga serta total akhir tetap divalidasi oleh server saat booking diproses."
            />
            <div class="mt-7 flex justify-center">
                <span class="rounded-full border border-white/[0.08] bg-white/[0.03] px-4 py-2 text-xs font-medium text-zinc-500">
                    {{ $packages->count() }} paket tersedia
                </span>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @forelse ($packages as $package)
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <x-public.package-card :package="$package" />
                </div>
            @empty
                <div class="rounded-3xl border border-dashed border-white/10 bg-white/[0.02] px-6 py-20 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-white/[0.08] bg-white/[0.03] text-zinc-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M4 7.5 12 3l8 4.5v9L12 21l-8-4.5v-9Z" stroke-linejoin="round" /><path d="m4 7.5 8 4.5 8-4.5M12 12v9" /></svg>
                    </div>
                    <h2 class="mt-5 text-lg font-semibold text-white">Belum ada paket aktif</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-500">Paket baru akan tampil setelah diperbarui. Anda dapat menghubungi tim kami untuk informasi langsung.</p>
                    <a href="{{ route('contact') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Hubungi Kami</a>
                </div>
            @endforelse

            @if ($packages->isNotEmpty())
                <div class="mt-12 flex flex-col items-center justify-between gap-5 rounded-3xl border border-white/[0.08] bg-zinc-900/60 p-6 text-center sm:flex-row sm:text-left">
                    <div>
                        <h2 class="font-semibold text-white">Sudah menemukan paket yang sesuai?</h2>
                        <p class="mt-1 text-sm text-zinc-500">Lanjutkan ke formulir dan pilih unit serta jadwal.</p>
                    </div>
                    <a href="{{ route('booking.create') }}" class="inline-flex h-11 shrink-0 items-center justify-center rounded-xl bg-amber-200 px-5 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Mulai Booking</a>
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
