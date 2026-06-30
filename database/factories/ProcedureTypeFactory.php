<?php

namespace Database\Factories;

use App\Models\ProcedureType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcedureTypeFactory extends Factory
{
    protected $model = ProcedureType::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->lexify('TYPE_???'),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'requirements' => ['dni_vigente'],
            'max_days_resolution' => fake()->numberBetween(5, 30),
            'is_active' => true,
        ];
    }
}
