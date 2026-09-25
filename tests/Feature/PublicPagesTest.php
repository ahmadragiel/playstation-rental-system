<?php

namespace Tests\Feature;

use Database\Seeders\RentalCatalogSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SettingSeeder::class);
        $this->seed(RentalCatalogSeeder::class);
    }

    public function test_public_pages_are_available(): void
    {
        $routes = [
            route('home'),
            route('packages.index'),
            route('units.index'),
            route('games.index'),
            route('facilities'),
            route('how-it-works'),
            route('contact'),
            route('booking.create'),
            route('schedule.index'),
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertSuccessful();
        }

        $this->get(route('home'))
            ->assertSee('NEXUS PLAY')
            ->assertSee('Paket Sewa')
            ->assertSee('Lihat Paket');
    }

    public function test_public_schedule_does_not_expose_customer_information(): void
    {
        $this->get(route('schedule.index'))
            ->assertSuccessful()
            ->assertDontSee('RNT-DEMO')
            ->assertDontSee('Pelanggan');
    }
}
