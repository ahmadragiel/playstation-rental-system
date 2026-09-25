@php
    $status = $booking->status;
    $statusValue = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $scheduleDate = ($booking->start_at ?? $booking->created_at)?->toDateString();
@endphp

<x-public.layout :settings="$settings" title="Booking Berhasil" description="Permintaan booking PlayStation berhasil dikirim dan sedang menunggu konfirmasi.">
    <section class="relative overflow-hidden border-b border-white/[0.06] py-14 sm:py-20">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_50%_0%,rgba(52,211,153,0.07),transparent_32%)]"></div>
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl border border-emerald-300/20 bg-emerald-300/[0.08] text-emerald-200 shadow-xl shadow-emerald-950/20">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m5 12.5 4.2 4.2L19 7" />
                </svg>
            </div>
            <p class="mt-7 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-200/70">Permintaan Diterima</p>
            <h1 class="mt-4 text-balance text-4xl font-semibold tracking-[-0.045em] text-white sm:text-5xl">Booking Anda sudah tercatat.</h1>
            <p class="mx-auto mt-5 max-w-2xl text-sm leading-7 text-zinc-400 sm:text-base">Simpan nomor booking di bawah. Tim kami akan memeriksa ketersediaan dan menghubungi Anda melalui WhatsApp untuk langkah konfirmasi berikutnya.</p>
        </div>
    </section>

    <section class="py-12 sm:py-16 lg:py-20">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <x-public.booking-summary :booking="$booking" />

            <div class="mt-8 rounded-3xl border border-white/[0.08] bg-zinc-900/50 p-6 sm:p-7">
                <div class="flex items-start gap-4">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-amber-200/15 bg-amber-200/[0.06] text-amber-100">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16Zm.75 4a.75.75 0 0 0-1.5 0v3.5c0 .27.14.52.38.64l2.5 1.25a.75.75 0 1 0 .67-1.34L10.75 9.38V6Z" clip-rule="evenodd" /></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-white">Status saat ini: {{ ucfirst(str_replace('_', ' ', $statusValue)) }}</h2>
                        <p class="mt-1.5 text-sm leading-6 text-zinc-500">Status dapat berubah setelah tim memverifikasi unit, jadwal, pembayaran, atau detail lainnya. Simpan halaman ini sebagai bukti permintaan.</p>
                    </div>
                </div>
            </div>

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                <a href="{{ route('home') }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.025] px-4 text-sm font-semibold text-zinc-300 transition hover:bg-white/[0.05] hover:text-white">Kembali ke Beranda</a>
                <a href="{{ route('schedule.index', array_filter(['date' => $scheduleDate])) }}" class="inline-flex h-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.025] px-4 text-sm font-semibold text-zinc-300 transition hover:bg-white/[0.05] hover:text-white">Lihat Jadwal</a>
                <a href="{{ route('booking.create') }}" class="inline-flex h-12 items-center justify-center rounded-xl bg-amber-200 px-4 text-sm font-bold text-zinc-950 transition hover:bg-amber-100">Booking Lain</a>
            </div>

            <p class="mt-7 text-center text-xs leading-5 text-zinc-600">Jika ada pertanyaan, hubungi tim dan sertakan nomor booking Anda.</p>
        </div>
    </section>
</x-public-layout>
