<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\UnitStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        $this->admin = User::factory()->create();
    }

    public function test_admin_can_record_exact_paid_transaction(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::Confirmed, 'total_price' => 85000]);

        $this->actingAs($this->admin)->post(route('admin.payments.store'), [
            'booking_id' => $booking->id,
            'method' => 'qris',
            'amount' => 85000,
            'status' => 'paid',
            'reference' => 'QRIS-12345',
        ])->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'method' => 'qris',
            'amount' => 85000,
            'status' => 'paid',
        ]);
        $this->assertSame(BookingStatus::Paid, $booking->fresh()->status);
    }

    public function test_mismatched_payment_amount_is_rejected_without_creating_transaction(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::Confirmed, 'total_price' => 85000]);

        $this->actingAs($this->admin)->post(route('admin.payments.store'), [
            'booking_id' => $booking->id,
            'method' => 'cash',
            'amount' => 80000,
            'status' => 'pending',
        ])->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_pending_payment_can_be_marked_paid(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::Confirmed, 'total_price' => 85000]);
        $payment = Payment::query()->create([
            'transaction_number' => 'PAY-TEST-001',
            'booking_id' => $booking->id,
            'processed_by' => $this->admin->id,
            'method' => 'transfer',
            'amount' => 85000,
            'status' => PaymentStatus::Pending,
        ]);

        $this->actingAs($this->admin)->patch(route('admin.payments.status', $payment), [
            'status' => 'paid',
        ])->assertRedirect();

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(BookingStatus::Paid, $booking->fresh()->status);
        $this->assertNotNull($payment->fresh()->paid_at);
    }

    public function test_booking_cannot_be_marked_paid_without_payment_record(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::Confirmed]);

        $this->actingAs($this->admin)->patch(route('admin.bookings.status', $booking), [
            'status' => 'paid',
        ])->assertSessionHasErrors('status');

        $this->assertSame(BookingStatus::Confirmed, $booking->fresh()->status);
    }

    public function test_refunded_payment_cancels_active_booking_and_releases_schedule(): void
    {
        $booking = Booking::factory()->create(['status' => BookingStatus::Paid, 'total_price' => 85000]);
        $booking->unit->update(['status' => UnitStatus::Maintenance]);
        $booking->schedule()->create([
            'playstation_unit_id' => $booking->playstation_unit_id,
            'start_at' => $booking->start_at,
            'end_at' => $booking->end_at,
            'status' => 'reserved',
        ]);
        $payment = Payment::query()->create([
            'transaction_number' => 'PAY-TEST-REFUND',
            'booking_id' => $booking->id,
            'processed_by' => $this->admin->id,
            'method' => 'transfer',
            'amount' => 85000,
            'status' => PaymentStatus::Paid,
            'paid_at' => now(),
        ]);

        $this->actingAs($this->admin)->patch(route('admin.payments.status', $payment), [
            'status' => 'refunded',
        ])->assertRedirect();

        $this->assertSame(PaymentStatus::Refunded, $payment->fresh()->status);
        $this->assertSame(BookingStatus::Cancelled, $booking->fresh()->status);
        $this->assertSame(UnitStatus::Maintenance, $booking->unit->fresh()->status);
        $this->assertSame('released', $booking->schedule()->first()->status->value);
    }
}
