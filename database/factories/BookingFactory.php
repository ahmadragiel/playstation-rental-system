<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $start = now()->addDay()->setTime(11, 0);
        $duration = 180;

        return [
            'public_id' => (string) Str::uuid(),
            'booking_number' => 'RNT-TEST-'.Str::upper(Str::random(8)),
            'customer_id' => CustomerFactory::new(),
            'playstation_unit_id' => PlaystationUnitFactory::new(),
            'package_id' => PackageFactory::new(),
            'start_at' => $start,
            'end_at' => $start->copy()->addMinutes($duration),
            'duration_minutes' => $duration,
            'subtotal' => 65000,
            'total_price' => 65000,
            'status' => BookingStatus::Pending,
        ];
    }
}
