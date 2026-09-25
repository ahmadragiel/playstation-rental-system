<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'package_name',
        'playstation_type',
        'unit_code',
        'unit_name',
        'customer_name',
        'customer_whatsapp',
        'customer_email',
        'package_price',
        'duration_minutes',
        'total_price',
        'start_at',
        'end_at',
        'facilities',
    ];

    protected function casts(): array
    {
        return [
            'package_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'facilities' => 'array',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
