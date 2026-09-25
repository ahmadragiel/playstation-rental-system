<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_view_and_update_public_settings(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.settings.edit'))
            ->assertSuccessful()
            ->assertSee('NEXUS PLAY');

        $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'rental_name' => 'Nexus Play Updated',
            'description' => 'Deskripsi rental terbaru.',
            'address' => 'Jl. Updated No. 10',
            'whatsapp' => '628999888777',
            'instagram' => '@nexusupdated',
            'opening_hours' => '11:00-01:00',
            'hero_title' => 'Judul Hero Baru',
            'hero_subtitle' => 'Subjudul hero baru.',
            'payment_information' => 'Pembayaran melalui QRIS.',
        ])->assertRedirect();

        $this->assertDatabaseHas('settings', [
            'key' => 'rental_name',
            'value' => 'Nexus Play Updated',
        ]);
        $this->assertSame('11:00-01:00', Setting::query()->where('key', 'opening_hours')->value('value'));
    }

    public function test_invalid_opening_hours_is_rejected(): void
    {
        $this->actingAs($this->admin)->put(route('admin.settings.update'), [
            'rental_name' => 'Nexus Play',
            'opening_hours' => '25:99-30:88',
        ])->assertSessionHasErrors('opening_hours');

        $this->assertSame('10:00-02:00', Setting::query()->where('key', 'opening_hours')->value('value'));
    }
}
