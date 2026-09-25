<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'whatsapp' => '62'.fake()->unique()->numerify('8##########'),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
        ];
    }
}
