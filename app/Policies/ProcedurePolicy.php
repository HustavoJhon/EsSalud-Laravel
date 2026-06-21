<?php

namespace App\Policies;

use App\Models\Procedure;
use App\Models\User;

class ProcedurePolicy
{
    public function view(User $user, Procedure $procedure): bool
    {
        if ($user->hasRole('SADM')) return true;
        return $user->id === $procedure->user_id
            || $user->id === $procedure->current_assignee_id
            || $user->hasAnyRole(['OPER', 'SUPV', 'GESDOC']);
    }

    public function update(User $user, Procedure $procedure): bool
    {
        return $user->id === $procedure->user_id && $procedure->status->code === 'BORRADOR';
    }

    public function delete(User $user, Procedure $procedure): bool
    {
        return $user->id === $procedure->user_id && $procedure->status->code === 'BORRADOR';
    }

    public function submit(User $user, Procedure $procedure): bool
    {
        return $user->id === $procedure->user_id && $procedure->status->code === 'BORRADOR';
    }

    public function subsanar(User $user, Procedure $procedure): bool
    {
        return $user->id === $procedure->user_id && $procedure->status->code === 'SUBSANACION';
    }

    public function approve(User $user, Procedure $procedure): bool
    {
        return $user->hasAnyRole(['OPER', 'SUPV', 'SADM'])
            && in_array($procedure->status->code, ['RADICADO', 'EVALUACION']);
    }

    public function reject(User $user, Procedure $procedure): bool
    {
        return $user->hasAnyRole(['OPER', 'SUPV', 'SADM'])
            && in_array($procedure->status->code, ['RADICADO', 'EVALUACION']);
    }

    public function requestSubsanacion(User $user, Procedure $procedure): bool
    {
        return $user->hasAnyRole(['OPER', 'SUPV', 'SADM'])
            && in_array($procedure->status->code, ['RADICADO', 'EVALUACION']);
    }

    public function assign(User $user, Procedure $procedure): bool
    {
        return $user->hasAnyRole(['SUPV', 'SADM']);
    }
}
