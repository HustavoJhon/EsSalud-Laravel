<?php

namespace App\Console\Commands;

use App\Models\Procedure;
use App\Models\ProcedureHistory;
use App\Models\ProcedureStatus;
use Illuminate\Console\Command;

class AutoCancelProcedures extends Command
{
    protected $signature = 'procedures:auto-cancel';
    protected $description = 'Automatically cancel expired draft and subsanacion procedures';

    public function handle(): void
    {
        $cancelado = ProcedureStatus::where('code', 'CANCELADO')->first();
        $borrador = ProcedureStatus::where('code', 'BORRADOR')->first();

        if (!$cancelado) {
            $this->error('Status CANCELADO not found.');
            return;
        }

        $expiredDrafts = Procedure::where('procedure_status_id', $borrador->id ?? 0)
            ->where('created_at', '<', now()->subDays(30))
            ->get();

        foreach ($expiredDrafts as $procedure) {
            $procedure->update([
                'procedure_status_id' => $cancelado->id,
                'completed_at' => now(),
            ]);

            ProcedureHistory::create([
                'procedure_id' => $procedure->id,
                'from_status_id' => $borrador->id,
                'to_status_id' => $cancelado->id,
                'changed_by' => 1,
                'comment' => 'Cancelado automáticamente por expiración (borrador > 30 días).',
            ]);

            $this->line("Cancelled draft procedure #{$procedure->id}");
        }

        $subsanacion = ProcedureStatus::where('code', 'SUBSANACION')->first();
        if ($subsanacion) {
            $expiredSubsanaciones = Procedure::where('procedure_status_id', $subsanacion->id)
                ->whereHas('subsanaciones', function ($q) {
                    $q->where('is_fulfilled', false)
                      ->where('deadline', '<', now());
                })
                ->get();

            foreach ($expiredSubsanaciones as $procedure) {
                $procedure->update([
                    'procedure_status_id' => $cancelado->id,
                    'completed_at' => now(),
                ]);

                ProcedureHistory::create([
                    'procedure_id' => $procedure->id,
                    'from_status_id' => $subsanacion->id,
                    'to_status_id' => $cancelado->id,
                    'changed_by' => 1,
                    'comment' => 'Cancelado automáticamente por expiración de plazo de subsanación.',
                ]);

                $this->line("Cancelled subsanacion procedure #{$procedure->id}");
            }
        }

        $total = $expiredDrafts->count() + ($expiredSubsanaciones->count() ?? 0);
        $this->info("{$total} procedures auto-cancelled.");
    }
}
