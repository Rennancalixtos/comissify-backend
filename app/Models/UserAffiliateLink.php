<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserAffiliateLink extends Model
{
    protected $fillable = [
        'user_id',
        'item_id',
        'product_name',
        'image_url',
        'product_link',
        'affiliate_link',
        'price_min',
        'commission_rate',
        'shop_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(UserPost::class, 'affiliate_link_id');
    }
}
