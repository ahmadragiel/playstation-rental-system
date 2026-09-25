<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Package;
use App\Models\PlaystationType;
use App\Models\PlaystationUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RentalCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $types = collect([
            [
                'name' => 'PlayStation 4',
                'slug' => 'playstation-4',
                'description' => 'Konsol PS4 yang andal untuk mode single player, co-op, dan bermain bersama teman.',
                'specifications' => ['Resolusi' => 'HD', 'Storage' => '500 GB', 'Output' => 'HDMI'],
            ],
            [
                'name' => 'PlayStation 5',
                'slug' => 'playstation-5',
                'description' => 'PS5 dengan performa cepat dan dukungan game generasi terbaru.',
                'specifications' => ['Resolusi' => '4K-ready', 'Storage' => '825 GB', 'Output' => 'HDMI'],
            ],
            [
                'name' => 'PlayStation 5 VIP',
                'slug' => 'playstation-5-vip',
                'description' => 'Pengalaman premium dengan monitor terbaik dan area bermain yang lebih private.',
                'specifications' => ['Resolusi' => '4K', 'Storage' => '1 TB', 'Output' => 'HDMI 2.1'],
            ],
        ])->mapWithKeys(function (array $data): array {
            $type = PlaystationType::query()->updateOrCreate(['slug' => $data['slug']], $data);

            return [$type->name => $type];
        });

        $units = [
            ['PS4-01', 'PS4 Reguler 01', 'PlayStation 4', 'Excellent', 'available', 'Studio A', 'images/units/ps4.svg'],
            ['PS4-02', 'PS4 Reguler 02', 'PlayStation 4', 'Good', 'available', 'Studio A', 'images/units/ps4.svg'],
            ['PS4-03', 'PS4 Reguler 03', 'PlayStation 4', 'Fair', 'maintenance', 'Studio A', 'images/units/ps4.svg'],
            ['PS5-01', 'PS5 Reguler 01', 'PlayStation 5', 'Excellent', 'available', 'Studio B', 'images/units/ps5.svg'],
            ['PS5-02', 'PS5 Reguler 02', 'PlayStation 5', 'Good', 'available', 'Studio B', 'images/units/ps5.svg'],
            ['PS5-VIP-01', 'PS5 VIP 01', 'PlayStation 5 VIP', 'Excellent', 'available', 'VIP Room', 'images/units/ps5-vip.svg'],
        ];

        $unitModels = collect($units)->mapWithKeys(function (array $data) use ($types): array {
            $unit = PlaystationUnit::query()->updateOrCreate(
                ['code' => $data[0]],
                [
                    'playstation_type_id' => $types[$data[2]]->id,
                    'name' => $data[1],
                    'condition' => $data[3],
                    'status' => $data[4],
                    'location' => $data[5],
                    'photo' => $data[6],
                    'is_active' => true,
                    'notes' => null,
                ],
            );

            return [$unit->code => $unit];
        });

        $games = collect([
            ['EA Sports FC', 'Sports', 'PS4, PS5', '1-4', 'Game sepak bola kompetitif dengan mode multiplayer.'],
            ['GTA V', 'Action', 'PS4, PS5', '1-2', 'Petualangan open world untuk solo maupun co-op.'],
            ['Tekken 8', 'Fighting', 'PS4, PS5', '1-8', 'Pertarungan fighting dengan karakter ikonik.'],
            ['Mortal Kombat', 'Fighting', 'PS4, PS5', '1-2', 'Game fighting intense untuk duel caroten.'],
            ['Marvel’s Spider-Man', 'Action', 'PS4, PS5', '1-1', 'Petualangan Spider-Man dengan cerita sinematik.'],
            ['NBA 2K', 'Sports', 'PS4, PS5', '1-10', 'Simulasi basket dengan kontrol dan gameplay mendalam.'],
            ['Call of Duty', 'Shooter', 'PS4, PS5', '1-8', 'Aksi tembak-menembak kompetitif untuk solo dan multiplayer.'],
            ['eFootball', 'Sports', 'PS4, PS5', '1-8', 'Sepak bola gratis dengan gameplay cepat.'],
        ])->mapWithKeys(function (array $data): array {
            $game = Game::query()->updateOrCreate(
                ['slug' => Str::slug($data[0])],
                [
                    'name' => $data[0],
                    'genre' => $data[1],
                    'platform' => $data[2],
                    'player_count' => $data[3],
                    'description' => $data[4],
                    'cover' => 'images/games/'.Str::slug($data[0]).'.svg',
                    'is_active' => true,
                ],
            );

            return [$game->name => $game];
        });

        foreach ($unitModels as $unit) {
            $unit->games()->sync($games->pluck('id'));
        }

        $packages = [
            ['PS4 Reguler', 'playstation-4', 180, 45000, true, true, 'Cocok untuk sesi santai dan multiplayer ringan.', ['TV 42 inch', 'DualShock 4', 'WiFi', 'AC']],
            ['PS5 Reguler', 'playstation-5', 180, 65000, true, true, 'Grafis lebih mulus untuk game single player dan kompetitif.', ['TV 50 inch', 'DualSense', 'WiFi', 'AC']],
            ['PS5 VIP', 'playstation-5-vip', 180, 85000, true, true, 'Ruang private dengan monitor dan amenities premium.', ['Monitor 27 inch', 'DualSense', 'WiFi ekstra', 'AC', 'Sofa premium']],
            ['Paket 5 Jam', 'playstation-5', 300, 95000, true, false, 'Lima jam bermain tanpa perlu pindah tempat.', ['TV 50 inch', 'DualSense', 'WiFi', 'AC', 'Snack']],
            ['Paket Malam', 'playstation-5', 300, 110000, true, false, 'Sesi malam dengan atmosfer yang lebih private.', ['TV 50 inch', 'DualSense', 'WiFi', 'AC', 'Minuman']],
            ['Paket Weekend', 'playstation-5', 600, 185000, true, false, 'Sesi panjang untuk kompetisi dan nonton bersama.', ['TV 50 inch', 'DualSense', 'WiFi', 'AC', 'Snack', 'Minuman']],
            ['PS4 Weekend', 'playstation-4', 600, 135000, true, false, 'Sesi panjang bersama teman atau keluarga.', ['TV 42 inch', 'DualShock 4', 'WiFi', 'AC', 'Snack']],
        ];

        foreach ($packages as [$name, $typeSlug, $duration, $price, $active, $featured, $description, $facilities]) {
            $type = $types->firstWhere('slug', $typeSlug);

            Package::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'playstation_type_id' => $type->id,
                    'name' => $name,
                    'duration_minutes' => $duration,
                    'price' => $price,
                    'description' => $description,
                    'facilities' => $facilities,
                    'is_active' => $active,
                    'is_featured' => $featured,
                ],
            );
        }
    }
}
