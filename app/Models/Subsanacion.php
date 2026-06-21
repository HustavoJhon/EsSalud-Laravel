<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subsanacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'procedure_id',
        'attempt_number',
        'requested_by',
        'requested_comment',
        'responded_at',
        'response_comment',
        'deadline',
        'is_fulfilled',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'responded_at' => 'datetime',
            'deadline' => 'datetime',
            'is_fulfilled' => 'boolean',
        ];
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
