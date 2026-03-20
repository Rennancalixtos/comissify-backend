<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.scrapecreators.key');
    }

    public function searchVideos(string $query, int $limit = 12): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeader('x-api-key', $this->apiKey)
                ->get('https://api.scrapecreators.com/v1/tiktok/search/keyword', [
                    'query'   => $query,
                    'region'  => 'BR',
                    'sort_by' => 'most-liked',
                ]);

            $body = $response->json();

            if (empty($body['search_item_list'])) {
                return ['videos' => [], 'cursor' => null, 'error' => 'Nenhum resultado encontrado.'];
            }

            $videos = collect($body['search_item_list'])
                ->map(function ($item) {
                    $info    = $item['aweme_info'] ?? $item;
                    $stats   = $info['statistics'] ?? [];
                    $author  = $info['author'] ?? [];
                    $video   = $info['video'] ?? [];
                    $awemeId = $info['aweme_id'] ?? null;
                    $handle  = $author['unique_id'] ?? null;

                    $thumbnail = $video['cover']['url_list'][0]
                        ?? $video['ai_dynamic_cover']['url_list'][0]
                        ?? $video['origin_cover']['url_list'][0]
                        ?? null;

                    // URL direto do vídeo para preview (CDN TikTok)
                    $videoUrl = $video['play_addr']['url_list'][0]
                        ?? $video['download_addr']['url_list'][0]
                        ?? null;

                    $shareUrl = $info['share_url']
                        ?? ($handle && $awemeId ? "https://www.tiktok.com/@{$handle}/video/{$awemeId}" : null);

                    if (!$thumbnail || !$shareUrl) {
                        return null;
                    }

                    return [
                        'id'        => $awemeId,
                        'desc'      => $info['desc'] ?? '',
                        'author'    => $author['nickname'] ?? $handle ?? '',
                        'handle'    => $handle ?? '',
                        'thumbnail' => $thumbnail,
                        'video_url' => $videoUrl,
                        'share_url' => $shareUrl,
                        'likes'     => $stats['digg_count'] ?? 0,
                        'plays'     => $stats['play_count'] ?? 0,
                        'comments'  => $stats['comment_count'] ?? 0,
                    ];
                })
                ->filter()
                ->take($limit)
                ->values()
                ->all();

            return ['videos' => $videos, 'cursor' => $body['cursor'] ?? null];

        } catch (\Throwable $e) {
            Log::error('TikTokService::searchVideos failed', ['message' => $e->getMessage()]);
            return ['videos' => [], 'cursor' => null, 'error' => 'Falha ao buscar no TikTok.'];
        }
    }
}
