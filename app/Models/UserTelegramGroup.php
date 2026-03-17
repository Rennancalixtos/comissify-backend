<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTelegramGroup extends Model
{
    protected $fillable = ['user_id', 'chat_id', 'chat_title', 'message_template'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
