<?php

namespace Database\Factories;

use App\Models\Procedure;
use App\Models\ProcedureStatus;
use App\Models\ProcedureType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProcedureFactory extends Factory
{
    protected $model = Procedure::class;

    public function definition(): array
    {
        $borrador = ProcedureStatus::where('code', 'BORRADOR')->first()
            ?? ProcedureStatus::factory()->create(['code' => 'BORRADOR']);

        return [
            'user_id' => User::factory(),
            'procedure_type_id' => ProcedureType::factory(),
            'procedure_status_id' => $borrador->id,
            'data' => ['reason' => fake()->sentence()],
        ];
    }
}
