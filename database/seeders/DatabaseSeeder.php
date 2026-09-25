<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('rental.admin.email');
        $password = config('rental.admin.password');

        if (! $email || ! $password || strlen($password) < 12) {
            throw new RuntimeException('ADMIN_EMAIL dan ADMIN_PASSWORD minimal 12 karakter harus dikonfigurasi sebelum seeding.');
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => config('rental.admin.name'),
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        $this->call([
            SettingSeeder::class,
            RentalCatalogSeeder::class,
        ]);

        if (config('rental.seed_demo_data')) {
            $this->call(DemoRentalSeeder::class);
        }
    }
}
