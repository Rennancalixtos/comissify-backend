<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserToken extends Model
{
    protected $fillable = ['user_id', 'balance', 'reports_unlocked'];

    protected $casts = [
        'balance'          => 'decimal:4',
        'reports_unlocked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function debit(float $amount, string $description, ?string $reference = null): bool
    {
        if ($this->balance < $amount) {
            return false;
        }

        $this->balance -= $amount;

        // Lock reports when balance reaches zero
        if ((float) $this->balance <= 0) {
            $this->reports_unlocked = false;
        }

        $this->save();

        TokenTransaction::create([
            'user_id'       => $this->user_id,
            'type'          => 'debit',
            'amount'        => $amount,
            'description'   => $description,
            'reference'     => $reference,
            'balance_after' => $this->balance,
        ]);

        return true;
    }

    public function credit(float $amount, string $description): void
    {
        $this->balance += $amount;

        // Unlock reports when a package of R$24 or more is purchased
        if ($amount >= 24) {
            $this->reports_unlocked = true;
        }

        $this->save();

        TokenTransaction::create([
            'user_id'       => $this->user_id,
            'type'          => 'credit',
            'amount'        => $amount,
            'description'   => $description,
            'reference'     => null,
            'balance_after' => $this->balance,
        ]);
    }
}
