<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopee_product_id',
        'name',
        'category',
        'price',
        'commission_rate',
        'rating',
        'sales_count',
        'opportunity_score',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'price'             => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'rating'            => 'decimal:2',
        'sales_count'       => 'integer',
        'opportunity_score' => 'decimal:2',
        'last_synced_at'    => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────────

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTopOpportunity($query, int $limit = 20)
    {
        return $query->active()->orderByDesc('opportunity_score')->limit($limit);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    public function getFormattedPriceAttribute(): string
    {
        return 'R$ '.number_format((float) $this->price, 2, ',', '.');
    }

    public function getFormattedCommissionAttribute(): string
    {
        return $this->commission_rate.'%';
    }
}
