<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true).' Package';

        return [
            'playstation_type_id' => PlaystationTypeFactory::new(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'duration_minutes' => 180,
            'price' => 65000,
            'description' => fake()->sentence(),
            'facilities' => ['WiFi', 'AC', 'DualSense'],
            'is_active' => true,
            'is_featured' => true,
        ];
    }
}
