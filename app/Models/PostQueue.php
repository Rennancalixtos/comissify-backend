<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostQueue extends Model
{
    use HasFactory;

    protected $table = 'post_queue';

    protected $fillable = [
        'video_id',
        'account_id',
        'title',
        'description',
        'hashtags',
        'affiliate_link',
        'priority',
        'status',
        'scheduled_at',
        'posted_at',
        'shopee_video_id',
        'error_message',
    ];

    protected $casts = [
        'hashtags'     => 'array',
        'priority'     => 'integer',
        'scheduled_at' => 'datetime',
        'posted_at'    => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReadyToPost($query)
    {
        return $query->pending()
            ->where(fn ($q) => $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now()))
            ->orderBy('priority')
            ->orderBy('created_at');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    public function getHashtagsStringAttribute(): string
    {
        return implode(' ', $this->hashtags ?? []);
    }

    public function markAsPosted(string $shopeeVideoId): void
    {
        $this->update([
            'status'          => 'posted',
            'posted_at'       => now(),
            'shopee_video_id' => $shopeeVideoId,
            'error_message'   => null,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status'        => 'failed',
            'error_message' => $reason,
        ]);
    }
}
