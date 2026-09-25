<?php

namespace Database\Factories;

use App\Enums\UnitStatus;
use App\Models\PlaystationUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlaystationUnitFactory extends Factory
{
    protected $model = PlaystationUnit::class;

    public function definition(): array
    {
        return [
            'playstation_type_id' => PlaystationTypeFactory::new(),
            'code' => 'PS5-'.fake()->unique()->numerify('###'),
            'name' => 'PS5 Reguler',
            'condition' => 'Excellent',
            'status' => UnitStatus::Available,
            'location' => 'Studio A',
            'is_active' => true,
        ];
    }

    public function maintenance(): static
    {
        return $this->state(fn (): array => ['status' => UnitStatus::Maintenance]);
    }
}
