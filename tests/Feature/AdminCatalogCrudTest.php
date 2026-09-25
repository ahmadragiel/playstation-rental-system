<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Game;
use App\Models\Package;
use App\Models\PlaystationType;
use App\Models\PlaystationUnit;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_update_and_delete_unit(): void
    {
        $type = PlaystationType::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.units.store'), [
            'playstation_type_id' => $type->id,
            'code' => 'PS5-TEST-01',
            'name' => 'PS5 Test Unit',
            'condition' => 'Excellent',
            'status' => 'available',
            'location' => 'Studio Test',
            'is_active' => '1',
        ])->assertRedirect(route('admin.units.index'));

        $unit = PlaystationUnit::query()->where('code', 'PS5-TEST-01')->firstOrFail();

        $this->actingAs($this->admin)->put(route('admin.units.update', $unit), [
            'playstation_type_id' => $type->id,
            'code' => 'PS5-TEST-01',
            'name' => 'PS5 Updated Unit',
            'condition' => 'Good',
            'status' => 'maintenance',
            'location' => 'Studio Test',
            'is_active' => '1',
        ])->assertRedirect(route('admin.units.index'));

        $this->assertSame('maintenance', $unit->fresh()->status->value);

        $this->actingAs($this->admin)
            ->delete(route('admin.units.destroy', $unit))
            ->assertRedirect(route('admin.units.index'));
        $this->assertModelMissing($unit);
    }

    public function test_admin_can_create_and_update_package_with_facilities(): void
    {
        $type = PlaystationType::factory()->create();

        $this->actingAs($this->admin)->post(route('admin.packages.store'), [
            'playstation_type_id' => $type->id,
            'name' => 'Paket Competitive',
            'slug' => 'paket-competitive',
            'duration_minutes' => 180,
            'price' => 75000,
            'description' => 'Paket untuk tim kompetitif.',
            'facilities' => "WiFi\nAC, DualSense",
            'is_active' => '1',
            'is_featured' => '1',
        ])->assertRedirect(route('admin.packages.index'));

        $package = Package::query()->where('slug', 'paket-competitive')->firstOrFail();
        $this->assertSame(['WiFi', 'AC', 'DualSense'], $package->facilities);

        $this->actingAs($this->admin)->put(route('admin.packages.update', $package), [
            'playstation_type_id' => $type->id,
            'name' => 'Paket Competitive Updated',
            'slug' => 'paket-competitive',
            'duration_minutes' => 300,
            'price' => 110000,
            'facilities' => 'WiFi, AC',
            'is_active' => '1',
        ])->assertRedirect(route('admin.packages.index'));

        $this->assertSame(300, $package->fresh()->duration_minutes);
    }

    public function test_admin_can_create_and_update_game(): void
    {
        $type = PlaystationType::factory()->create(['name' => 'PS5 Game Test']);
        $unit = PlaystationUnit::factory()->for($type, 'type')->create();

        $this->actingAs($this->admin)->post(route('admin.games.store'), [
            'name' => 'Game Baru',
            'slug' => 'game-baru',
            'genre' => 'Action',
            'platform' => 'PS5',
            'player_count' => '1-2',
            'description' => 'Deskripsi game.',
            'unit_ids' => [$unit->id],
            'is_active' => '1',
        ])->assertRedirect(route('admin.games.index'));

        $game = Game::query()->where('slug', 'game-baru')->firstOrFail();
        $this->assertTrue($game->units()->whereKey($unit->id)->exists());

        $this->actingAs($this->admin)->put(route('admin.games.update', $game), [
            'name' => 'Game Baru Update',
            'slug' => 'game-baru',
            'genre' => 'Sports',
            'platform' => 'PS4, PS5',
            'player_count' => '1-4',
            'is_active' => '0',
        ])->assertRedirect(route('admin.games.index'));

        $this->assertFalse($game->fresh()->is_active);
    }

    public function test_customer_management_pages_are_available(): void
    {
        $customer = Customer::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.customers.index'))
            ->assertSuccessful()
            ->assertSee($customer->name);

        $this->actingAs($this->admin)
            ->get(route('admin.customers.show', $customer))
            ->assertSuccessful()
            ->assertSee($customer->whatsapp);
    }
}
