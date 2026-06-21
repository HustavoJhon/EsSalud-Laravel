<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RagSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_id',
        'title',
        'source_url',
        'category',
        'is_active',
        'total_chunks',
        'last_indexed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_chunks' => 'integer',
            'last_indexed_at' => 'datetime',
        ];
    }
}
