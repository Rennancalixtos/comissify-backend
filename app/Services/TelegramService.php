<?php

namespace App\Services;

use App\Models\UserAffiliateLink;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TelegramService
{
    private string $token;
    private string $apiBase;

    public function __construct()
    {
        $this->token   = config('services.telegram.bot_token', '');
        $this->apiBase = "https://api.telegram.org/bot{$this->token}";
    }

    public function isConfigured(): bool
    {
        return !empty($this->token);
    }

    // ── Connection codes ─────────────────────────────────────────────────────

    public function generateConnectionCode(int $userId): string
    {
        // Revoke any previous code for this user
        $old = Cache::get("telegram_user_code:{$userId}");
        if ($old) {
            Cache::forget("telegram_connect:{$old}");
            Cache::forget("telegram_code_expiry:{$old}");
        }

        $code      = strtolower(Str::random(8));
        $expiresAt = now()->addMinutes(10)->timestamp;

        Cache::put("telegram_connect:{$code}",      $userId,    now()->addMinutes(10));
        Cache::put("telegram_user_code:{$userId}",  $code,      now()->addMinutes(10));
        Cache::put("telegram_code_expiry:{$code}",  $expiresAt, now()->addMinutes(11));

        return $code;
    }

    public function getActiveCode(int $userId): ?string
    {
        return Cache::get("telegram_user_code:{$userId}");
    }

    public function getCodeExpiresAt(string $code): ?int
    {
        return Cache::get("telegram_code_expiry:{$code}");
    }

    public function validateConnectionCode(string $code): ?int
    {
        $userId = Cache::get("telegram_connect:{$code}");
        if (!$userId) return null;

        Cache::forget("telegram_connect:{$code}");
        Cache::forget("telegram_user_code:{$userId}");
        Cache::forget("telegram_code_expiry:{$code}");

        return (int) $userId;
    }

    // ── Sending ──────────────────────────────────────────────────────────────

    public function sendMessage(int|string $chatId, string $text, string $parseMode = 'HTML'): bool
    {
        if (!$this->isConfigured()) return false;

        $response = Http::post("{$this->apiBase}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $text,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => false,
        ]);

        if (!$response->successful() || !$response->json('ok')) {
            Log::warning('Telegram sendMessage failed', [
                'chat_id' => $chatId,
                'error'   => $response->json('description'),
            ]);
            return false;
        }

        return true;
    }

    public function sendPhoto(int|string $chatId, string $photoUrl, string $caption, string $parseMode = 'HTML'): bool
    {
        if (!$this->isConfigured()) return false;

        $response = Http::post("{$this->apiBase}/sendPhoto", [
            'chat_id'    => $chatId,
            'photo'      => $photoUrl,
            'caption'    => $caption,
            'parse_mode' => $parseMode,
        ]);

        if (!$response->successful() || !$response->json('ok')) {
            Log::warning('Telegram sendPhoto failed', [
                'chat_id' => $chatId,
                'error'   => $response->json('description'),
            ]);
            return false;
        }

        return true;
    }

    // ── Template ─────────────────────────────────────────────────────────────

    public function defaultTemplate(): string
    {
        return "🔥 <b>{{nome}}</b>\n\n💰 Comissão: {{comissao}}\n💵 Preço: {{preco}}\n\n🛒 <a href=\"{{link}}\">Comprar agora →</a>";
    }

    public function resolveTemplate(string $template, UserAffiliateLink $link): string
    {
        $preco    = $link->price_min ? 'R$ ' . number_format((float) $link->price_min, 2, ',', '.') : '';
        $comissao = $link->commission_rate ? $link->commission_rate . '%' : '';

        return str_replace(
            ['{{nome}}', '{{link}}', '{{preco}}', '{{comissao}}'],
            [$link->product_name, $link->affiliate_link, $preco, $comissao],
            $template
        );
    }

    public function botUsername(): string
    {
        return config('services.telegram.bot_username', '');
    }
}
