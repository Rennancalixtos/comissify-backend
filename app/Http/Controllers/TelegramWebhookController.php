<?php

namespace App\Http\Controllers;

use App\Models\UserTelegramGroup;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request, TelegramService $telegram): Response
    {
        // Validate secret token sent by Telegram (fail-safe: bloqueia se não configurado)
        $expected = config('services.telegram.webhook_secret');
        if (!$expected || $request->header('X-Telegram-Bot-Api-Secret-Token') !== $expected) {
            return response('Unauthorized', 401);
        }

        $update = $request->all();

        if (!isset($update['message'])) {
            return response('ok', 200);
        }

        $message   = $update['message'];
        $text      = trim($message['text'] ?? '');
        $chat      = $message['chat'];
        $chatId    = $chat['id'];
        $chatTitle = $chat['title'] ?? ($chat['first_name'] ?? "Chat {$chatId}");

        // /conectar CODE
        if (preg_match('/^\/conectar(?:@\S+)?\s+([a-z0-9]+)/i', $text, $matches)) {
            $code   = strtolower(trim($matches[1]));
            $userId = $telegram->validateConnectionCode($code);

            if (!$userId) {
                $telegram->sendMessage($chatId, '❌ Código inválido ou expirado. Gere um novo código no painel Comissify.');
                return response('ok', 200);
            }

            UserTelegramGroup::updateOrCreate(
                ['user_id' => $userId, 'chat_id' => $chatId],
                ['chat_title' => $chatTitle]
            );

            $telegram->sendMessage(
                $chatId,
                "✅ <b>{$chatTitle}</b> conectado ao Comissify!\n\nAgora você pode enviar links afiliados diretamente para cá a partir do painel."
            );

            return response('ok', 200);
        }

        return response('ok', 200);
    }
}
