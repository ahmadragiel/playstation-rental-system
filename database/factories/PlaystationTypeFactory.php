<?php

namespace Database\Factories;

use App\Models\PlaystationType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PlaystationTypeFactory extends Factory
{
    protected $model = PlaystationType::class;

    public function definition(): array
    {
        $name = 'PlayStation '.fake()->randomElement([4, 5, 5, 4]).' '.fake()->unique()->numerify('####');

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'specifications' => ['Resolusi' => 'HD'],
            'is_active' => true,
        ];
    }
}
