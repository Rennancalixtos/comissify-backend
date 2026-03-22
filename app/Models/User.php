<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // is_admin e is_active foram removidos do fillable para prevenir mass assignment.
    // Esses campos só devem ser alterados via métodos/queries explícitas com autorização.
    protected $fillable = ['name', 'email', 'phone', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'is_admin'          => 'boolean',
        'is_active'         => 'boolean',
    ];

    public function credential(): HasOne
    {
        return $this->hasOne(UserCredential::class);
    }

    public function token(): HasOne
    {
        return $this->hasOne(UserToken::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(TokenTransaction::class)->latest();
    }

    public function hasCredentials(): bool
    {
        return $this->credential()->exists();
    }

    public function isShopeeConnected(): bool
    {
        return $this->credential?->is_connected === true;
    }

    public function tokenBalance(): float
    {
        return (float) ($this->token?->balance ?? 0);
    }

    /** Garante que o UserToken existe, criando com balance=0 se necessário. */
    public function ensureTokenWallet(): UserToken
    {
        return $this->token ?? $this->token()->create(['balance' => 0]);
    }

    /** Relatórios liberados apenas para quem comprou pacote ≥ R$24 e ainda tem saldo. */
    public function hasReportAccess(): bool
    {
        return ($this->token?->reports_unlocked === true)
            && ($this->tokenBalance() > 0);
    }
}
