<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TokenPackage extends Model
{
    protected $fillable = [
        'name',
        'tokens_amount',
        'price',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'tokens_amount' => 'decimal:4',
        'price'         => 'decimal:2',
        'is_active'     => 'boolean',
        'is_featured'   => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
