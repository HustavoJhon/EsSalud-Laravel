<?php

namespace Tests\Feature;

use App\Enums\ProcedureStatusEnum;
use App\Enums\RoleEnum;
use App\Models\Procedure;
use App\Models\ProcedureHistory;
use App\Models\ProcedureStatus;
use App\Models\ProcedureType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcedureTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected ProcedureType $procedureType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->seed(\Database\Seeders\ProcedureTypeStatusSeeder::class);

        $this->user = User::factory()->create();
        $this->user->assignRole('ASEG');

        $this->procedureType = ProcedureType::where('is_active', true)->first();
    }

    public function test_authenticated_user_can_create_procedure(): void
    {
        $response = $this->actingAs($this->user)->post(route('procedures.store'), [
            'procedure_type_id' => $this->procedureType->id,
            'data' => ['reason' => 'Test reason'],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('procedures', [
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);
    }

    public function test_procedure_starts_as_borrador(): void
    {
        $procedure = Procedure::factory()->create([
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);

        $borrador = ProcedureStatus::where('code', ProcedureStatusEnum::BORRADOR->value)->first();
        $this->assertEquals($borrador->id, $procedure->procedure_status_id);
    }

    public function test_user_can_submit_procedure(): void
    {
        $procedure = Procedure::factory()->create([
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('procedures.submit', $procedure));
        $response->assertRedirect();

        $procedure->refresh();
        $radicado = ProcedureStatus::where('code', ProcedureStatusEnum::RADICADO->value)->first();
        $this->assertEquals($radicado->id, $procedure->procedure_status_id);

        $this->assertDatabaseHas('procedure_histories', [
            'procedure_id' => $procedure->id,
            'to_status_id' => $radicado->id,
        ]);
    }

    public function test_operator_can_approve_procedure(): void
    {
        $operator = User::factory()->create();
        $operator->assignRole('OPER');

        $procedure = Procedure::factory()->create([
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);

        $radicado = ProcedureStatus::where('code', ProcedureStatusEnum::RADICADO->value)->first();
        $procedure->update(['procedure_status_id' => $radicado->id]);

        $response = $this->actingAs($operator)->post(route('procedures.approve', $procedure), [
            'comment' => 'Aprobado',
        ]);
        $response->assertRedirect();

        $procedure->refresh();
        $aprobado = ProcedureStatus::where('code', ProcedureStatusEnum::APROBADO->value)->first();
        $this->assertEquals($aprobado->id, $procedure->procedure_status_id);
    }

    public function test_user_cannot_approve_own_procedure(): void
    {
        $procedure = Procedure::factory()->create([
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('procedures.approve', $procedure));
        $response->assertStatus(403);
    }

    public function test_procedure_history_is_recorded_on_status_change(): void
    {
        $procedure = Procedure::factory()->create([
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);

        $this->actingAs($this->user)->post(route('procedures.submit', $procedure));

        $procedure->refresh();
        $this->assertEquals(1, $procedure->histories()->count());
    }

    public function test_procedure_list_respects_role_filter(): void
    {
        Procedure::factory(5)->create([
            'user_id' => $this->user->id,
            'procedure_type_id' => $this->procedureType->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('procedures.index'));
        $response->assertOk();
        $response->assertViewHas('procedures');
    }
}
