<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemPrice extends Model
{
    protected $fillable = ['key', 'value', 'description'];

    protected $casts = [
        'value' => 'decimal:6',
    ];

    public static function get(string $key): float
    {
        try {
            return (float) Cache::remember(
                "system_price:{$key}",
                now()->addMinutes(60),
                fn () => static::where('key', $key)->value('value') ?? 0
            );
        } catch (\Throwable) {
            // Cache indisponível — buscar direto do banco
            return (float) (static::where('key', $key)->value('value') ?? 0);
        }
    }

    public static function set(string $key, float $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
        try { Cache::forget("system_price:{$key}"); } catch (\Throwable) {}
    }
}
