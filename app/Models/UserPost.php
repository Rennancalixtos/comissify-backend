<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPost extends Model
{
    protected $fillable = [
        'user_id',
        'affiliate_link_id',
        'type',
        'status',
        'credits_charged',
        'result',
        'error_message',
    ];

    protected $casts = [
        'credits_charged' => 'decimal:4',
        'result'          => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function affiliateLink(): BelongsTo
    {
        return $this->belongsTo(UserAffiliateLink::class, 'affiliate_link_id');
    }

    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isFailed(): bool    { return $this->status === 'failed'; }
}
