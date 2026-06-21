<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'procedure_type_id',
        'procedure_status_id',
        'current_assignee_id',
        'data',
        'idempotency_key',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'json',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function procedureType(): BelongsTo
    {
        return $this->belongsTo(ProcedureType::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProcedureStatus::class, 'procedure_status_id');
    }

    public function currentAssignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_assignee_id');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ProcedureHistory::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ProcedureComment::class);
    }

    public function subsanaciones(): HasMany
    {
        return $this->hasMany(Subsanacion::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
