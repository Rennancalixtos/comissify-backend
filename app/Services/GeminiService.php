<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private const MODEL = 'gemini-flash-latest';

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateSalesText(
        string  $productName,
        ?string $price          = null,
        ?string $commissionRate = null
    ): array {
        if (!$this->isConfigured()) {
            return ['error' => 'Serviço de IA não configurado.'];
        }

        $context = "Produto: {$productName}";
        if ($price)          $context .= "\nPreço: R$ {$price}";
        if ($commissionRate) $context .= "\nComissão: {$commissionRate}%";

        $prompt = <<<PROMPT
Você é um especialista em marketing de afiliados Shopee.
Crie um título chamativo e um texto de venda curto para divulgar o produto abaixo via WhatsApp, Telegram e Instagram.
O texto deve ter no máximo 120 palavras, ser persuasivo e usar emojis com moderação.
NÃO inclua URLs, links, placeholders de link ou qualquer menção a "clique aqui" no texto — o link será adicionado automaticamente pelo sistema.

{$context}

Responda APENAS com JSON válido contendo os campos "titulo" (string) e "texto" (string).
PROMPT;

        $response = Http::timeout(30)->post(
            'https://generativelanguage.googleapis.com/v1beta/models/' . self::MODEL . ':generateContent?key=' . $this->apiKey,
            [
                'contents'         => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['responseMimeType' => 'application/json'],
            ]
        );

        if (!$response->successful()) {
            Log::error('Gemini API error', ['status' => $response->status(), 'body' => $response->body()]);
            return ['error' => 'Falha ao contactar a IA. Tente novamente.'];
        }

        try {
            $raw  = $response->json('candidates.0.content.parts.0.text');
            $data = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);

            $description = $data['texto'] ?? '';
            $description = $this->stripLinkPlaceholders($description);

            return [
                'title'       => $data['titulo'] ?? $productName,
                'description' => trim($description),
                'image_url'   => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Gemini parse error', ['error' => $e->getMessage(), 'raw' => $raw ?? null]);
            return ['error' => 'Resposta da IA inválida. Tente novamente.'];
        }
    }

    private function stripLinkPlaceholders(string $text): string
    {
        $patterns = [
            '/\[INSERIR[^\]]*LINK[^\]]*\]/ui',
            '/\[LINK[^\]]*\]/ui',
            '/\[URL[^\]]*\]/ui',
            '/\[COLOQUE[^\]]*LINK[^\]]*\]/ui',
            '/\[SEU LINK[^\]]*\]/ui',
            '/https?:\/\/\[[^\]]*\]/ui',
        ];

        $text = preg_replace($patterns, '', $text);

        // Remove linhas que ficaram vazias após a remoção
        $lines = explode("\n", $text);
        $lines = array_filter($lines, fn($l) => trim($l) !== '');
        return implode("\n", $lines);
    }
}
