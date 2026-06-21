<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'role',
        'content',
        'message_type',
        'sources',
        'confidence',
        'latency_ms',
        'feedback_helpful',
        'feedback_comment',
    ];

    protected function casts(): array
    {
        return [
            'sources' => 'json',
            'confidence' => 'float',
            'latency_ms' => 'integer',
            'feedback_helpful' => 'boolean',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class, 'session_id');
    }
}
