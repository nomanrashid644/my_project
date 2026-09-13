<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['ai_chat_session_id', 'role', 'message', 'provider', 'tokens_used'])]
class AiChatMessage extends Model
{
    public function session(): BelongsTo { return $this->belongsTo(AiChatSession::class, 'ai_chat_session_id'); }

    public function getFormattedMessageAttribute(): string
    {
        return $this->role === 'assistant' ? Str::markdown($this->message) : e($this->message);
    }
}