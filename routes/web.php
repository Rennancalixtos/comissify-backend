<?php

use Illuminate\Support\Facades\Route;

// ─── Landing ──────────────────────────────────────────────────────────────────
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('home');
Route::get('/tendencias',      [\App\Http\Controllers\TrendingController::class, 'trending'])->name('trending');
Route::get('/maiores-comissoes', [\App\Http\Controllers\TrendingController::class, 'commissions'])->name('commissions');
Route::get('/privacidade',     fn() => view('privacy'))->name('privacy');
Route::get('/termos',          fn() => view('terms'))->name('terms');

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',   [\App\Http\Controllers\Auth\AuthController::class, 'show'])->name('login');
Route::post('/login',  [\App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

// Multi-step registration — Livewire component
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');

// ─── Conectar Shopee ──────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/connect',  [\App\Http\Controllers\ConnectController::class, 'show'])->name('connect');
    Route::post('/connect', [\App\Http\Controllers\ConnectController::class, 'store'])->name('connect.store');
});

// ─── Proxy de download (imagens/vídeos externos) ──────────────────────────────
Route::middleware('auth')->get('/download-proxy', [\App\Http\Controllers\DownloadProxyController::class, 'download'])->name('download.proxy');

// ─── Dashboard do cliente (auth only — mostra bloqueado se sem Shopee) ────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard\ProductSearch::class)->name('dashboard');
    Route::get('/posts',     \App\Livewire\Dashboard\Posts::class)->name('posts');
    Route::get('/canais',    \App\Livewire\Dashboard\TelegramChannels::class)->name('channels');
    Route::get('/relatorio', \App\Livewire\Dashboard\ConversionReport::class)->name('report');
    Route::get('/creditos',  \App\Livewire\Dashboard\Credits::class)->name('credits');
});

// ─── Pagamentos (Mercado Pago) ────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/checkout/create',    [\App\Http\Controllers\PaymentController::class, 'create'])->name('payment.create');
    Route::get('/checkout/success',    [\App\Http\Controllers\PaymentController::class, 'success'])->name('payment.success');
    Route::get('/checkout/failure',    [\App\Http\Controllers\PaymentController::class, 'failure'])->name('payment.failure');
    Route::get('/checkout/pending',    [\App\Http\Controllers\PaymentController::class, 'pending'])->name('payment.pending');
});

// Webhooks — sem auth, sem CSRF (exceções em bootstrap/app.php)
Route::post('/webhook/mercadopago', [\App\Http\Controllers\MercadoPagoWebhookController::class, 'handle'])
    ->name('payment.webhook');

Route::post('/webhook/telegram', [\App\Http\Controllers\TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/',            \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/queue',       \App\Livewire\Admin\Queue::class)->name('queue.index');
    Route::get('/settings',    \App\Livewire\Admin\Settings::class)->name('settings.index');

    // Fase 3
    Route::get('/tokens',      \App\Livewire\Admin\Tokens::class)->name('tokens.index');
    Route::get('/users',       \App\Livewire\Admin\Users::class)->name('users.index');

    // Fase 5
    Route::get('/screenshots', \App\Livewire\Admin\Screenshots::class)->name('screenshots.index');

    // Fase 8
    Route::get('/emails',      \App\Livewire\Admin\Emails::class)->name('emails.index');
});
