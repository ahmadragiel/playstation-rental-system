@props(['status'])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $label = $status instanceof \App\Enums\BookingStatus || $status instanceof \App\Enums\UnitStatus || $status instanceof \App\Enums\PaymentStatus
        ? $status->label()
        : ucwords(str_replace('_', ' ', $value));
    $tone = match ($value) {
        'available', 'completed', 'paid', 'emerald' => 'border-emerald-400/20 bg-emerald-400/10 text-emerald-300',
        'confirmed', 'booked', 'blue' => 'border-blue-400/20 bg-blue-400/10 text-blue-300',
        'ongoing', 'in_use', 'cyan' => 'border-cyan-400/20 bg-cyan-400/10 text-cyan-300',
        'pending', 'maintenance', 'amber' => 'border-amber-400/20 bg-amber-400/10 text-amber-300',
        'cancelled', 'failed', 'rose' => 'border-rose-400/20 bg-rose-400/10 text-rose-300',
        'refunded', 'violet' => 'border-violet-400/20 bg-violet-400/10 text-violet-300',
        default => 'border-slate-400/20 bg-slate-400/10 text-slate-300',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider {$tone}"]) }}>
    {{ $label }}
</span>
