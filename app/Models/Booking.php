<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'booking_number',
        'customer_id',
        'playstation_unit_id',
        'package_id',
        'start_at',
        'end_at',
        'duration_minutes',
        'subtotal',
        'total_price',
        'status',
        'notes',
        'confirmed_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'duration_minutes' => 'integer',
            'subtotal' => 'decimal:2',
            'total_price' => 'decimal:2',
            'status' => BookingStatus::class,
            'confirmed_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(PlaystationUnit::class, 'playstation_unit_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(BookingDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            BookingStatus::Pending->value,
            BookingStatus::Confirmed->value,
            BookingStatus::Paid->value,
            BookingStatus::Ongoing->value,
        ]);
    }

    public function scopeOccupiesSchedule(Builder $query): Builder
    {
        return $query->whereIn('status', [
            BookingStatus::Pending->value,
            BookingStatus::Confirmed->value,
            BookingStatus::Paid->value,
            BookingStatus::Ongoing->value,
        ]);
    }

    public function paidPayment(): ?Payment
    {
        return $this->payments()->where('status', 'paid')->latest('paid_at')->first();
    }
}
