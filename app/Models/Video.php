<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'pinterest_url',
        'local_path',
        'duration_seconds',
        'resolution',
        'file_size_mb',
        'codec',
        'validation_status',
        'rejection_reason',
        'validated_at',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
        'file_size_mb'     => 'decimal:2',
        'validated_at'     => 'datetime',
    ];


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function postQueue(): HasMany
    {
        return $this->hasMany(PostQueue::class);
    }


    public function scopeValid($query)
    {
        return $query->whereIn('validation_status', ['valid', 'normalized']);
    }

    public function scopePendingValidation($query)
    {
        return $query->where('validation_status', 'pending');
    }


    public function isReadyToPost(): bool
    {
        return in_array($this->validation_status, ['valid', 'normalized'])
            && $this->local_path !== null;
    }

    public function getAbsolutePathAttribute(): ?string
    {
        return $this->local_path ? storage_path('app/'.$this->local_path) : null;
    }
}
