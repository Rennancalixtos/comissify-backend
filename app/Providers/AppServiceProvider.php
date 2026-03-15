<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Services\PinterestService::class);
        $this->app->singleton(\App\Services\FFmpegService::class);
        $this->app->singleton(\App\Services\AiTextService::class);
        $this->app->singleton(\App\Services\AppiumService::class);
    }

    public function boot(): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
    }
}
