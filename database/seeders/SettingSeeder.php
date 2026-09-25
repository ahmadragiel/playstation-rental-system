<?php

namespace Database\Seeders;

use App\Services\SettingService;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        app(SettingService::class)->putMany([
            'rental_name' => 'NEXUS PLAY',
            'description' => 'Rental PlayStation premium dengan unit nyaman, game pilihan, dan harga transparan.',
            'facilities' => ['AC', 'TV / Monitor', 'WiFi', 'Controller', 'Ruang nyaman', 'Tempat duduk ergonomis', 'Banyak pilihan game', 'Parkir'],
            'logo' => 'images/brand/logo.svg',
            'banner' => 'images/brand/banner.svg',
            'address' => 'Jl. Contoh No. 21, Jakarta',
            'whatsapp' => '6281234567890',
            'instagram' => '@nexusplay',
            'opening_hours' => '10:00-02:00',
            'hero_title' => 'Main lebih seru. Duduk, relax, core!',
            'hero_subtitle' => 'Pilih unit PlayStation favoritmu, atur jadwal, lalu fokus bermain sampai match tanpa gangguan.',
            'payment_information' => 'Pembayaran dapat dilakukan melalui Cash, Transfer, atau QRIS. Booking dikonfirmasi setelah pembayaran diterima.',
            'map_url' => 'https://maps.google.com/?q=Jakarta',
        ], 'public');

        app(SettingService::class)->flush();
    }
}
