<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;

class PinterestService
{
    private string $pythonBinary;
    private string $scriptPath;
    private string $ytdlpBin;
    private int    $downloadTimeout;
    private int    $searchTimeout;

    public function __construct()
    {
        $this->pythonBinary    = env('PYTHON_PATH', '/usr/bin/python3');
        $this->scriptPath      = base_path('scripts/python/pinterest_downloader.py');
        $this->ytdlpBin        = env('YTDLP_PATH', '/usr/local/bin/yt-dlp');
        $this->downloadTimeout = (int) env('VIDEO_DOWNLOAD_TIMEOUT', 120);
        $this->searchTimeout   = 60;
    }

    public function searchVideos(array $terms, int $limit = 5): array
    {
        $candidates = [];

        foreach ($terms as $term) {
            $results    = $this->searchTerm($term, $limit);
            $candidates = array_merge($candidates, $results);
        }

        // Deduplicate by URL
        $seen   = [];
        $unique = [];
        foreach ($candidates as $c) {
            if (! isset($seen[$c['url']])) {
                $seen[$c['url']] = true;
                $unique[]        = $c;
            }
        }

        return $unique;
    }

    public function downloadVideo(string $url, string $outputDir): array
    {
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = rtrim($outputDir, '/').'/'.Str::uuid().'.mp4';

        $process = new Process([
            $this->pythonBinary,
            $this->scriptPath,
            '--url',    $url,
            '--output', $outputPath,
        ]);

        $process->setTimeout($this->downloadTimeout);

        Log::info('PinterestService::downloadVideo start', ['url' => $url]);

        try {
            $process->mustRun();
            $result = json_decode($process->getOutput(), true);

            if (! is_array($result)) {
                return ['success' => false, 'filepath' => '', 'error' => 'Invalid JSON from downloader', 'size_mb' => 0.0];
            }

            Log::info('PinterestService::downloadVideo done', [
                'success' => $result['success'] ?? false,
                'size_mb' => $result['size_mb'] ?? 0,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('PinterestService::downloadVideo failed', [
                'url'    => $url,
                'error'  => $e->getMessage(),
                'stderr' => substr($process->getErrorOutput(), 0, 500),
            ]);

            return ['success' => false, 'filepath' => '', 'error' => $e->getMessage(), 'size_mb' => 0.0];
        }
    }


    public function searchPins(string $query, int $pageSize = 12, ?string $cursor = null): array
    {
        $apiKey = config('services.pinterest.scraper_key');

        try {
            $params = ['query' => $query];
            if ($cursor) {
                $params['cursor'] = $cursor;
            }

            $response = Http::timeout(15)
                ->withHeader('x-api-key', $apiKey)
                ->get('https://api.scrapecreators.com/v1/pinterest/search', $params);

            $body = $response->json();

            if (empty($body['success']) || !isset($body['pins'])) {
                return ['pins' => [], 'cursor' => null, 'error' => 'Nenhum resultado.'];
            }

            // Debug: log total pins and video structure
            $totalPins  = count($body['pins']);
            $pinsWVideo = collect($body['pins'])->filter(fn($p) => !empty($p['videos']))->count();
            Log::info('Pinterest search result', ['query' => $query, 'total_pins' => $totalPins, 'pins_with_video' => $pinsWVideo]);
            $firstVideo = collect($body['pins'])->first(fn($p) => !empty($p['videos']));
            if ($firstVideo) {
                Log::info('Pinterest video_list keys', [
                    'keys'   => array_keys($firstVideo['videos']['video_list'] ?? []),
                    'sample' => array_map(fn($v) => $v['url'] ?? null, $firstVideo['videos']['video_list'] ?? []),
                ]);
            }

            $pins = collect($body['pins'])
                ->map(function ($pin) {
                    $videos    = $pin['videos'] ?? null;
                    $videoList = $videos['video_list'] ?? [];
                    // Pinterest only provides HLS streams — yt-dlp handles download/conversion
                    $videoUrl = $videoList['V_720P']['url']
                             ?? $videoList['V_480P']['url']
                             ?? $videoList['V_360P']['url']
                             ?? $videoList['V_HLSV4']['url']
                             ?? $videoList['V_HLSV3_MOBILE']['url']
                             ?? null;
                    return [
                        'id'         => $pin['id'],
                        'title'      => $pin['grid_title'] ?? $pin['title'] ?? $pin['description'] ?? '',
                        'link'       => $pin['link'] ?? $pin['url'],
                        'pin_url'    => $pin['url'],
                        'image_url'  => $pin['images']['orig']['url'] ?? null,
                        'media_type' => ($videos && $videoUrl) ? 'video' : 'image',
                        'video_url'  => $videoUrl,
                    ];
                })
                ->filter(function ($pin) {
                    // Only videos with a direct MP4 URL; exclude Instagram content
                    return $pin['media_type'] === 'video'
                        && $pin['video_url']
                        && !str_contains($pin['link'] ?? '', 'instagram.com');
                })
                ->take($pageSize)
                ->values()
                ->all();

            return ['pins' => $pins, 'cursor' => $body['cursor'] ?? null];

        } catch (\Throwable $e) {
            Log::error('PinterestService::searchPins failed', ['message' => $e->getMessage()]);
            return ['pins' => [], 'cursor' => null, 'error' => 'Falha ao buscar no Pinterest.'];
        }
    }

    private function mockSearchPins(string $query, int $pageSize): array
    {
        $titles = [
            "{$query} - Veja como usar",
            "Melhor {$query} do mercado",
            "{$query} que todo mundo quer",
            "Review completo: {$query}",
            "{$query} vale a pena?",
            "Como escolher o melhor {$query}",
            "Top {$query} em 2025",
            "{$query} em promoção",
            "{$query} - Guia completo",
            "Tendências de {$query}",
            "{$query} para iniciantes",
            "{$query} premium",
        ];

        $colors = ['EE4D2D', 'E60023', 'BD081C', 'FF6B6B', 'C0392B', 'E74C3C'];
        $pins   = [];

        for ($i = 0; $i < min($pageSize, count($titles)); $i++) {
            $color    = $colors[$i % count($colors)];
            $isVideo  = $i % 4 === 0;
            $pins[]   = [
                'id'         => 'MOCK_PIN_' . ($i + 1),
                'title'      => $titles[$i],
                'link'       => 'https://pinterest.com/pin/mock_' . ($i + 1),
                'image_url'  => "https://via.placeholder.com/600x400/{$color}/ffffff?text=" . urlencode(substr($query, 0, 14)),
                'media_type' => $isVideo ? 'video' : 'image',
                'video_url'  => $isVideo ? 'https://sample-videos.com/video123/mp4/720/big_buck_bunny_720p_1mb.mp4' : null,
            ];
        }

        return ['pins' => $pins, 'bookmark' => null];
    }


    private function searchTerm(string $term, int $limit): array
    {
        $searchUrl = 'https://www.pinterest.com/search/pins/?q='.urlencode($term).'&rs=typed';

        $process = new Process([
            $this->ytdlpBin,
            $searchUrl,
            '--flat-playlist',
            '-j',
            '--no-download',
            '--no-warnings',
            '--playlist-end', (string) ($limit * 3), // fetch extra to allow duration filtering
        ]);

        $process->setTimeout($this->searchTimeout);

        try {
            $process->run(); // don't use mustRun — partial output is still useful

            $candidates = [];
            $count      = 0;

            foreach (explode("\n", trim($process->getOutput())) as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                $data = json_decode($line, true);
                if (! is_array($data)) {
                    continue;
                }

                $duration = (int) ($data['duration'] ?? 0);
                $url      = $data['webpage_url'] ?? $data['url'] ?? null;

                if (! $url) {
                    continue;
                }

                // Filter: 10s ≤ duration ≤ 55s
                if ($duration < 10 || $duration > 55) {
                    continue;
                }

                $candidates[] = [
                    'url'      => $url,
                    'title'    => $data['title'] ?? $data['description'] ?? '',
                    'duration' => $duration,
                    'term'     => $term,
                ];

                if (++$count >= $limit) {
                    break;
                }
            }

            return $candidates;
        } catch (\Exception $e) {
            Log::error('PinterestService::searchTerm failed', [
                'term'  => $term,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
