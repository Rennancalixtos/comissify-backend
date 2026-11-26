<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookAdLibraryService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = (string) config('services.scrapecreators.key');
    }

    public function searchAds(string $query, int $limit = 12): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeader('x-api-key', $this->apiKey)
                ->get('https://api.scrapecreators.com/v1/facebook/adLibrary/search/ads', [
                    'query'      => $query,
                    'country'    => 'BR',
                    'status'     => 'ACTIVE',
                    'sort_by'    => 'total_impressions',
                    'media_type' => 'ALL',
                    'trim'       => 'true',
                ]);

            $body = $response->json();

            if (empty($body['searchResults'])) {
                return ['ads' => [], 'cursor' => null, 'error' => 'Nenhum anúncio encontrado.'];
            }

            $ads = collect($body['searchResults'])
                ->map(function ($ad) {
                    $snapshot = $ad['snapshot'] ?? [];
                    $images   = $snapshot['images'] ?? [];
                    $image    = $images[0]['resized_image_url'] ?? $images[0]['original_image_url'] ?? null;
                    $text     = $snapshot['body']['text'] ?? '';
                    $cta      = $snapshot['cta_text'] ?? null;
                    $pageName = $ad['page_name'] ?? $snapshot['page_name'] ?? '';

                    if (!$image && !$text) {
                        return null;
                    }

                    return [
                        'id'        => $ad['ad_archive_id'] ?? null,
                        'page_name' => $pageName,
                        'image'     => $image,
                        'text'      => $text,
                        'cta'       => ($cta && $cta !== 'No button') ? $cta : null,
                        'platforms' => $ad['publisher_platform'] ?? [],
                        'is_active' => $ad['is_active'] ?? true,
                        'page_url'  => $snapshot['page_profile_uri'] ?? null,
                    ];
                })
                ->filter()
                ->take($limit)
                ->values()
                ->all();

            return ['ads' => $ads, 'cursor' => $body['cursor'] ?? null];

        } catch (\Throwable $e) {
            Log::error('FacebookAdLibraryService::searchAds failed', ['message' => $e->getMessage()]);
            return ['ads' => [], 'cursor' => null, 'error' => 'Falha ao buscar na Ad Library.'];
        }
    }
}
