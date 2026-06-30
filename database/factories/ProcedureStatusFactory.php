<?php

namespace Database\Factories;

use App\Models\ProcedureStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcedureStatusFactory extends Factory
{
    protected $model = ProcedureStatus::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->word(),
            'name' => fake()->word(),
            'is_terminal' => false,
        ];
    }
}
