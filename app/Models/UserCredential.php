<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredential extends Model
{
    protected $fillable = [
        'user_id',
        'app_id',
        'app_secret',
        'affiliate_id',
        'is_connected',
        'connected_at',
    ];

    protected $casts = [
        'app_id'       => 'encrypted',
        'app_secret'   => 'encrypted',
        'is_connected' => 'boolean',
        'connected_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isConnected(): bool
    {
        return $this->is_connected === true;
    }
}
