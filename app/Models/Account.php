<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'shopee_username',
        'session_data',
        'daily_post_limit',
        'posts_today',
        'last_post_at',
        'status',
    ];

    protected $casts = [
        'daily_post_limit' => 'integer',
        'posts_today'      => 'integer',
        'last_post_at'     => 'datetime',
    ];


    public function postQueue(): HasMany
    {
        return $this->hasMany(PostQueue::class);
    }


    public function getSessionDataAttribute(?string $value): ?array
    {
        if ($value === null) {
            return null;
        }

        try {
            return json_decode(Crypt::decryptString($value), true);
        } catch (\Exception) {
            return null;
        }
    }

    public function setSessionDataAttribute(?array $value): void
    {
        $this->attributes['session_data'] = $value !== null
            ? Crypt::encryptString(json_encode($value))
            : null;
    }


    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->active()
            ->whereColumn('posts_today', '<', 'daily_post_limit');
    }


    public function canPost(): bool
    {
        return $this->status === 'active'
            && $this->posts_today < $this->daily_post_limit;
    }

    public function getRemainingPostsAttribute(): int
    {
        return max(0, $this->daily_post_limit - $this->posts_today);
    }
}
