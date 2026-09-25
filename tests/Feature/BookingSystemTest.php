<?php

namespace Tests\Feature;

use App\Enums\UnitStatus;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PlaystationType;
use App\Models\PlaystationUnit;
use Carbon\CarbonImmutable;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
    }

    public function test_customer_can_create_booking_with_server_calculated_price(): void
    {
        [$package, $unit] = $this->compatibleCatalog();
        $start = CarbonImmutable::tomorrow()->setTime(11, 0);

        $response = $this->post(route('booking.store'), $this->payload($package, $unit, $start) + [
            'total_price' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'package_id' => $package->id,
            'playstation_unit_id' => $unit->id,
            'total_price' => 65000,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('booking_details', [
            'package_name' => $package->name,
            'total_price' => 65000,
        ]);
        $this->assertDatabaseHas('schedules', [
            'playstation_unit_id' => $unit->id,
            'status' => 'reserved',
        ]);
    }

    public function test_double_booking_is_rejected_but_adjacent_booking_is_allowed(): void
    {
        [$package, $unit] = $this->compatibleCatalog();
        $firstStart = CarbonImmutable::tomorrow()->setTime(11, 0);
        $secondStart = CarbonImmutable::tomorrow()->setTime(14, 0);

        $this->post(route('booking.store'), $this->payload($package, $unit, $firstStart))->assertRedirect();

        $this->post(route('booking.store'), $this->payload($package, $unit, $firstStart))
            ->assertSessionHasErrors('playstation_unit_id');

        $this->assertDatabaseCount('bookings', 1);

        $this->post(route('booking.store'), $this->payload($package, $unit, $secondStart))
            ->assertRedirect();
        $this->assertDatabaseCount('bookings', 2);
    }

    public function test_maintenance_unit_cannot_be_booked(): void
    {
        $type = PlaystationType::factory()->create();
        $unit = PlaystationUnit::factory()->for($type, 'type')->create(['status' => UnitStatus::Maintenance]);
        $package = Package::factory()->for($type, 'type')->create();

        $this->post(route('booking.store'), $this->payload(
            $package,
            $unit,
            CarbonImmutable::tomorrow()->setTime(11, 0),
        ))->assertSessionHasErrors('playstation_unit_id');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_booking_outside_operating_hours_is_rejected(): void
    {
        [$package, $unit] = $this->compatibleCatalog();

        $this->post(route('booking.store'), $this->payload(
            $package,
            $unit,
            CarbonImmutable::tomorrow()->setTime(8, 0),
        ))->assertSessionHasErrors('start_time');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_overnight_booking_until_closing_time_is_accepted(): void
    {
        [$package, $unit] = $this->compatibleCatalog();
        $start = CarbonImmutable::tomorrow()->setTime(23, 0);

        $this->post(route('booking.store'), $this->payload($package, $unit, $start))
            ->assertRedirect();

        $booking = Booking::query()->firstOrFail();
        $this->assertSame('02:00', $booking->end_at->format('H:i'));
    }

    public function test_duration_cannot_be_tampered(): void
    {
        [$package, $unit] = $this->compatibleCatalog();

        $this->post(route('booking.store'), array_merge($this->payload(
            $package,
            $unit,
            CarbonImmutable::tomorrow()->setTime(11, 0),
        ), ['duration_minutes' => 60]))->assertSessionHasErrors('duration_minutes');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_availability_endpoint_only_returns_compatible_non_conflicting_units(): void
    {
        [$package, $unit] = $this->compatibleCatalog();
        $start = CarbonImmutable::tomorrow()->setTime(11, 0);

        $this->post(route('booking.availability'), [
            'package_id' => $package->id,
            'booking_date' => $start->toDateString(),
            'start_time' => '11:00',
        ])->assertOk()->assertJsonPath('meta.available', 1);

        $this->post(route('booking.store'), $this->payload($package, $unit, $start));

        $this->post(route('booking.availability'), [
            'package_id' => $package->id,
            'booking_date' => $start->toDateString(),
            'start_time' => '11:00',
        ])->assertOk()->assertJsonPath('meta.available', 0);
    }

    /** @return array{Package, PlaystationUnit} */
    private function compatibleCatalog(): array
    {
        $type = PlaystationType::factory()->create(['name' => 'PlayStation 5 Test']);
        $package = Package::factory()->for($type, 'type')->create(['price' => 65000, 'duration_minutes' => 180]);
        $unit = PlaystationUnit::factory()->for($type, 'type')->create(['status' => UnitStatus::Available]);

        return [$package, $unit];
    }

    /** @return array<string, mixed> */
    private function payload(Package $package, PlaystationUnit $unit, CarbonImmutable $start): array
    {
        return [
            'customer_name' => 'Raka Pratama',
            'customer_whatsapp' => '081234567890',
            'customer_email' => 'raka@example.com',
            'package_id' => $package->id,
            'playstation_unit_id' => $unit->id,
            'booking_date' => $start->toDateString(),
            'start_time' => $start->format('H:i'),
            'duration_minutes' => $package->duration_minutes,
            'notes' => 'Datang dua orang.',
        ];
    }
}
