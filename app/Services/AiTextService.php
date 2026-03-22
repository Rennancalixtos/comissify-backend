<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTextService
{
    private string $provider;
    private string $model;
    private int    $maxTokens;
    private float  $temperature;

    public function __construct()
    {
        $this->provider    = config('services.ai.provider', 'anthropic');
        $this->model       = $this->resolveModel();
        $this->maxTokens   = (int) config('services.ai.max_tokens', 512);
        $this->temperature = (float) config('services.ai.temperature', 0.9);
    }

    public function generateSearchTerms(Product $product): array
    {
        $prompt = "Dado o produto: '{$product->name}' da categoria '{$product->category}', "
            ."retorne exatamente 3 termos de busca curtos (2 a 4 palavras cada) "
            ."para encontrar vídeos relacionados no Pinterest. "
            ."Retorne 2 termos em português e 1 em inglês. "
            .'Responda APENAS com JSON: {"terms": ["...", "...", "..."]}';

        $raw = $this->chat('Você é um especialista em marketing de afiliados.', $prompt);

        $data = $this->parseJson($raw);

        if (isset($data['terms']) && is_array($data['terms'])) {
            return array_slice($data['terms'], 0, 3);
        }

        // Fallback: derive from product name
        $name = strtolower($product->name);
        return [
            substr($name, 0, 30),
            ($product->category ?? 'produto').' '.substr($name, 0, 20),
            $product->name,
        ];
    }

    public function generateTitle(Product $product): string
    {
        $prompt = "Crie um título criativo e direto para um vídeo do produto '{$product->name}'. "
            ."Máximo 100 caracteres. Apenas o título, sem aspas.";

        $title = trim($this->chat('Você é um especialista em marketing de afiliados na Shopee.', $prompt));

        return mb_substr($title, 0, 100);
    }

    public function generateDescription(Product $product): string
    {
        $prompt = "Crie uma descrição curta e envolvente para um vídeo do produto '{$product->name}' "
            ."da categoria '{$product->category}', preço R$ ".number_format((float) $product->price, 2, ',', '.').".\n"
            ."Mencione 2 benefícios principais e inclua um CTA sutil. "
            ."Máximo 500 caracteres. Apenas a descrição, sem aspas.";

        return trim($this->chat('Você é um especialista em marketing de afiliados na Shopee.', $prompt));
    }

    public function generateHashtags(Product $product): array
    {
        $prompt = "Gere 5 hashtags relevantes para um vídeo do produto '{$product->name}' "
            ."da categoria '{$product->category}'. "
            .'Responda APENAS com JSON: {"hashtags": ["#...", "#...", "#...", "#...", "#..."]}';

        $raw  = $this->chat('Você é um especialista em marketing digital.', $prompt);
        $data = $this->parseJson($raw);

        if (isset($data['hashtags']) && is_array($data['hashtags'])) {
            return array_map(
                fn ($h) => str_starts_with($h, '#') ? $h : '#'.$h,
                array_slice($data['hashtags'], 0, 5)
            );
        }

        return ['#shopee', '#shopeeafiliados', '#oferta', '#desconto', '#compras'];
    }


    private function chat(string $systemPrompt, string $userPrompt): string
    {
        try {
            return match ($this->provider) {
                'anthropic' => $this->anthropic($systemPrompt, $userPrompt),
                'openai'    => $this->openai($systemPrompt, $userPrompt),
                'local'     => $this->ollama($systemPrompt, $userPrompt),
                default     => $this->anthropic($systemPrompt, $userPrompt),
            };
        } catch (\Exception $e) {
            Log::error('AiTextService::chat failed', [
                'provider' => $this->provider,
                'error'    => $e->getMessage(),
            ]);

            return '';
        }
    }

    private function anthropic(string $system, string $user): string
    {
        $response = Http::withHeaders([
            'x-api-key'         => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model'       => $this->model,
            'max_tokens'  => $this->maxTokens,
            'temperature' => $this->temperature,
            'system'      => $system,
            'messages'    => [['role' => 'user', 'content' => $user]],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Anthropic API error: '.$response->body());
        }

        return $response->json('content.0.text', '');
    }

    private function openai(string $system, string $user): string
    {
        $response = Http::withToken(config('services.openai.api_key'))
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'       => $this->model,
                'max_tokens'  => $this->maxTokens,
                'temperature' => $this->temperature,
                'messages'    => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user',   'content' => $user],
                ],
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('OpenAI API error: '.$response->body());
        }

        return $response->json('choices.0.message.content', '');
    }

    private function ollama(string $system, string $user): string
    {
        $response = Http::post(config('services.ai.ollama_url', 'http://localhost:11434').'/api/chat', [
            'model'  => $this->model,
            'stream' => false,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Ollama error: '.$response->body());
        }

        return $response->json('message.content', '');
    }

    private function parseJson(string $raw): array
    {
        // Strip ```json ... ``` fences if present
        $cleaned = preg_replace('/^```(?:json)?\s*/m', '', $raw);
        $cleaned = preg_replace('/```\s*$/m', '', $cleaned ?? $raw);

        $data = json_decode(trim($cleaned ?? $raw), true);

        return is_array($data) ? $data : [];
    }

    private function resolveModel(): string
    {
        return match (config('services.ai.provider', 'anthropic')) {
            'openai'    => config('services.openai.model', 'gpt-4o-mini'),
            'local'     => config('services.ai.ollama_model', 'llama3'),
            default     => config('services.anthropic.model', 'claude-sonnet-4-20250514'),
        };
    }
}
