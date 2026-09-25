<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Game;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PlaystationType;
use App\Models\PlaystationUnit;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_core_admin_pages_render_with_real_relationships(): void
    {
        $this->seed(SettingSeeder::class);
        $admin = User::factory()->create(['role' => 'admin']);
        $type = PlaystationType::factory()->create(['name' => 'PS5 Page Test']);
        $unit = PlaystationUnit::factory()->for($type, 'type')->create();
        $package = Package::factory()->for($type, 'type')->create();
        $game = Game::factory()->create();
        $customer = Customer::factory()->create();
        $booking = Booking::factory()->create([
            'customer_id' => $customer->id,
            'playstation_unit_id' => $unit->id,
            'package_id' => $package->id,
            'status' => BookingStatus::Confirmed,
            'start_at' => now()->addDay()->setTime(14, 0),
            'end_at' => now()->addDay()->setTime(17, 0),
        ]);
        $booking->schedule()->create([
            'playstation_unit_id' => $unit->id,
            'start_at' => $booking->start_at,
            'end_at' => $booking->end_at,
            'status' => 'reserved',
        ]);
        Payment::query()->create([
            'transaction_number' => 'PAY-PAGE-001',
            'booking_id' => $booking->id,
            'processed_by' => $admin->id,
            'method' => 'cash',
            'amount' => $booking->total_price,
            'status' => 'pending',
        ]);

        $routes = [
            route('admin.dashboard'),
            route('admin.schedule', ['date' => $booking->start_at->toDateString()]),
            route('admin.bookings.show', $booking),
            route('admin.payments.index'),
            route('admin.customers.show', $customer),
            route('admin.units.edit', $unit),
            route('admin.packages.edit', $package),
            route('admin.games.edit', $game),
            route('admin.reports.index', ['period' => 'month']),
            route('admin.settings.edit'),
        ];

        foreach ($routes as $route) {
            $this->actingAs($admin)->get($route)->assertSuccessful();
        }
    }
}
