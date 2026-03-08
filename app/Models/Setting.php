<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];


    /**
     * Get a setting value by key, with an optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting?->value ?? $default;
        });
    }

    /**
     * Set (upsert) a setting value and bust the cache.
     */
    public static function set(string $key, mixed $value, ?string $description = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            array_filter([
                'value'       => (string) $value,
                'description' => $description,
            ], fn ($v) => $v !== null)
        );

        Cache::forget("setting:{$key}");
    }

    /**
     * Bust all cached settings (useful after bulk edits).
     */
    public static function flushCache(): void
    {
        static::all()->each(fn ($s) => Cache::forget("setting:{$s->key}"));
    }
}
