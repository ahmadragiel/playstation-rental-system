<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    protected $model = Game::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('####'),
            'genre' => fake()->randomElement(['Sports', 'Action', 'Fighting']),
            'platform' => 'PS5',
            'player_count' => '1-4',
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
