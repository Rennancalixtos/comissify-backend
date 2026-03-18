<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PollinationsService
{
    private const BASE_URL = 'https://image.pollinations.ai/prompt';

    /**
     * Gera imagem server-side (sem Referer do browser) e salva em storage público.
     * Retorna a URL local ou null em caso de falha.
     */
    public function generate(string $productName, int $userId): ?string
    {
        $prompt = "professional product promotional photo, {$productName}, white background, studio lighting, high quality, e-commerce style, clean";
        $seed   = rand(1, 999999);
        $apiUrl = self::BASE_URL . '/' . rawurlencode($prompt) . "?width=800&height=800&nologo=true&seed={$seed}";

        try {
            $response = Http::timeout(60)
                ->withoutRedirecting()
                ->withHeaders(['Referer' => ''])
                ->get($apiUrl);

            // Pollinations pode redirecionar para a imagem final
            if ($response->status() === 301 || $response->status() === 302) {
                $location = $response->header('Location');
                $response = Http::timeout(60)->withHeaders(['Referer' => ''])->get($location);
            }

            $contentType = $response->header('Content-Type', '');
            if (!$response->successful() || !str_contains($contentType, 'image')) {
                Log::warning('Pollinations generation failed', [
                    'status' => $response->status(),
                    'type'   => $contentType,
                    'body'   => substr($response->body(), 0, 200),
                ]);
                return null;
            }

            $path = "ai-images/{$userId}/" . uniqid('img_') . '.jpg';
            Storage::disk('public')->put($path, $response->body());

            return Storage::disk('public')->url($path);

        } catch (\Throwable $e) {
            Log::error('Pollinations exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
