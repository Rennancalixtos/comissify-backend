<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LandingScreenshot extends Model
{
    protected $fillable = ['title', 'image_path', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    public function webpUrl(int $width): string
    {
        $stem = pathinfo($this->image_path, PATHINFO_FILENAME);
        return '/storage/landing/webp/' . $stem . '-' . $width . 'w.webp';
    }

    public function hasWebp(): bool
    {
        $stem = pathinfo($this->image_path, PATHINFO_FILENAME);
        return Storage::disk('public')->exists("landing/webp/{$stem}-640w.webp");
    }
}
