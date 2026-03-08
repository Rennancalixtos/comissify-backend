<?php

use App\Jobs\DiscoverProductsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// ─── Scheduled Jobs ──────────────────────────────────────────────────────────

Schedule::job(new DiscoverProductsJob())->hourly()->withoutOverlapping();

// Reset daily post counters at midnight
Schedule::call(function () {
    \App\Models\Account::query()->update(['posts_today' => 0]);
})->dailyAt('00:05')->name('reset-post-counters');

// ─── Dev helpers ─────────────────────────────────────────────────────────────
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
