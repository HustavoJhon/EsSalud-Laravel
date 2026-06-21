<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcedureType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'requirements',
        'max_days_resolution',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'json',
            'is_active' => 'boolean',
        ];
    }

    public function procedures(): HasMany
    {
        return $this->hasMany(Procedure::class);
    }
}
