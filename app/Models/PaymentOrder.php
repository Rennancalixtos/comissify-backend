<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentOrder extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'amount',
        'tokens_amount',
        'status',
        'mp_preference_id',
        'mp_payment_id',
        'paid_at',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'tokens_amount' => 'decimal:4',
        'paid_at'       => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TokenPackage::class, 'package_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
