<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
        return DB::transaction(function () use ($amount, $description, $reference) {
            // Lock pessimista: garante que apenas uma requisição por vez debita este wallet
            $locked = UserToken::where('id', $this->id)->lockForUpdate()->first();

            if ((float) $locked->balance < $amount) {
                return false;
            }

            $locked->balance -= $amount;

            if ((float) $locked->balance <= 0) {
                $locked->reports_unlocked = false;
            }

            $locked->save();

            // Sincroniza o estado local para que o chamador veja o saldo atualizado
            $this->balance          = $locked->balance;
            $this->reports_unlocked = $locked->reports_unlocked;

            TokenTransaction::create([
                'user_id'       => $locked->user_id,
                'type'          => 'debit',
                'amount'        => $amount,
                'description'   => $description,
                'reference'     => $reference,
                'balance_after' => $locked->balance,
            ]);

            return true;
        });
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
