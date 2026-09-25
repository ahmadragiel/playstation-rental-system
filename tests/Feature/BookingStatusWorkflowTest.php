<?php

namespace Tests\Feature;

use App\Enums\ScheduleStatus;
use App\Enums\UnitStatus;
use App\Models\Booking;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingStatusWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_progress_booking_through_operational_states(): void
    {
        $booking = Booking::factory()->create();
        $booking->schedule()->create([
            'playstation_unit_id' => $booking->playstation_unit_id,
            'start_at' => $booking->start_at,
            'end_at' => $booking->end_at,
            'status' => ScheduleStatus::Reserved,
        ]);

        $this->actingAs($this->admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'confirmed',
        ])->assertRedirect();

        $this->assertSame('confirmed', $booking->fresh()->status->value);
        $this->assertNotNull($booking->fresh()->confirmed_at);

        $this->actingAs($this->admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'ongoing',
        ])->assertRedirect();

        $this->assertSame('ongoing', $booking->fresh()->status->value);
        $this->assertNotNull($booking->fresh()->started_at);
        $this->assertSame(UnitStatus::InUse, $booking->unit->fresh()->status);
        $this->assertSame(ScheduleStatus::InProgress, $booking->schedule()->first()->status);

        $this->actingAs($this->admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'completed',
        ])->assertRedirect();

        $this->assertSame('completed', $booking->fresh()->status->value);
        $this->assertNotNull($booking->fresh()->completed_at);
        $this->assertSame(ScheduleStatus::Released, $booking->schedule()->first()->status);
        $this->assertSame(UnitStatus::Available, $booking->unit->fresh()->status);
    }

    public function test_completed_booking_cannot_return_to_confirmed(): void
    {
        $booking = Booking::factory()->create(['status' => 'completed']);

        $this->actingAs($this->admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'confirmed',
        ])->assertSessionHasErrors('status');

        $this->assertSame('completed', $booking->fresh()->status->value);
    }

    public function test_ongoing_booking_can_be_cancelled_with_reason(): void
    {
        $booking = Booking::factory()->create(['status' => 'ongoing']);
        $booking->schedule()->create([
            'playstation_unit_id' => $booking->playstation_unit_id,
            'start_at' => $booking->start_at,
            'end_at' => $booking->end_at,
            'status' => ScheduleStatus::InProgress,
        ]);

        $this->actingAs($this->admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'cancelled',
            'reason' => 'Pelanggan membatalkan sesi.',
        ])->assertRedirect();

        $booking->refresh();
        $this->assertSame('cancelled', $booking->status->value);
        $this->assertSame('Pelanggan membatalkan sesi.', $booking->cancellation_reason);
    }
}
