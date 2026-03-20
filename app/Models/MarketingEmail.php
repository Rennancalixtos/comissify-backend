<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingEmail extends Model
{
    protected $fillable = [
        'subject',
        'body',
        'audience',
        'recipients_count',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function audienceLabel(): string
    {
        return match ($this->audience) {
            'connected'    => 'Com Shopee conectada',
            'zero_balance' => 'Saldo zerado',
            default        => 'Todos os usuários',
        };
    }
}
