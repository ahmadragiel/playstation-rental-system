<x-public.layout :settings="$settings" title="Unit PlayStation" description="Daftar unit PlayStation, kondisi, lokasi, status, dan game yang tersedia.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-16 sm:py-24">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_75%_0%,rgba(125,211,252,0.055),transparent_30%),radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.025),transparent_25%)]"></div>
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-10 flex items-center gap-2 text-xs font-medium text-zinc-600" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition hover:text-zinc-300">Beranda</a>
                <span aria-hidden="true">/</span>
                <span class="text-zinc-400">Unit</span>
            </nav>
            <x-public.section-heading
                eyebrow="Unit PlayStation"
                title="Pilih ruang yang tepat."
                description="Setiap unit menampilkan tipe, kondisi, lokasi, status, serta game terkait agar pilihan Anda lebih terinformasi."
            />
            <div class="mt-7 flex flex-wrap justify-center gap-2 text-xs text-zinc-500">
                <span class="rounded-full border border-white/[0.08] bg-white/[0.03] px-3 py-1.5">{{ $units->count() }} unit</span>
                <span class="rounded-full border border-emerald-300/15 bg-emerald-300/[0.05] px-3 py-1.5 text-emerald-200">Available</span>
                <span class="rounded-full border border-sky-300/15 bg-sky-300/[0.05] px-3 py-1.5 text-sky-200">Booked</span>
                <span class="rounded-full border border-amber-200/15 bg-amber-200/[0.05] px-3 py-1.5 text-amber-100">In Use</span>
            </div>
        </div>
    </section>

    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if ($units->isNotEmpty())
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($units as $unit)
                        <x-public.unit-card :unit="$unit" />
                    @endforeach
                </div>
            @else
                <div class="rounded-3xl border border-dashed border-white/10 bg-white/[0.02] px-6 py-20 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl border border-white/[0.08] bg-white/[0.03] text-zinc-600">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><rect x="3" y="7" width="18" height="11" rx="4" /><path d="M8 10v5M5.5 12.5h5M16 11.5h.01M18.5 14h.01" stroke-linecap="round" /></svg>
                    </div>
                    <h2 class="mt-5 text-lg font-semibold text-white">Belum ada unit aktif</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-zinc-500">Ketersediaan unit sedang diperbarui. Hubungi kami untuk menanyakan waktu yang sesuai.</p>
                    <a href="{{ route('contact') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05]">Tanya Ketersediaan</a>
                </div>
            @endif

            <div class="mt-12 rounded-3xl border border-white/[0.08] bg-zinc-900/55 p-6 sm:flex sm:items-center sm:justify-between sm:gap-6 sm:p-8">
                <div>
                    <h2 class="font-semibold text-white">Ingin memastikan waktu yang tersedia?</h2>
                    <p class="mt-1 text-sm leading-6 text-zinc-500">Lihat jadwal per tanggal sebelum mengirim permintaan booking.</p>
                </div>
                <a href="{{ route('schedule.index') }}" class="mt-5 inline-flex h-11 shrink-0 items-center justify-center rounded-xl border border-white/10 px-5 text-sm font-semibold text-zinc-200 transition hover:bg-white/[0.05] sm:mt-0">Lihat Jadwal</a>
            </div>
        </div>
    </section>
</x-public-layout>
