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

    /**
     * Search Pinterest for videos matching any of the given terms.
     *
     * Runs yt-dlp for each term extracting metadata without downloading.
     * Filters by duration 10s–55s (safety margin under the 60s Shopee limit).
     *
     * @param  array<string>  $terms  Search terms from AiTextService::generateSearchTerms.
     * @param  int            $limit  Max results per term.
     * @return array<int, array{url: string, title: string, duration: int, term: string}>
     */
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

    /**
     * Download a Pinterest video to the given output directory.
     *
     * Calls pinterest_downloader.py and parses JSON output.
     * The filename is generated automatically (UUID.mp4).
     *
     * @param  string  $url        Pinterest pin or video URL.
     * @param  string  $outputDir  Absolute directory path.
     * @return array{success: bool, filepath: string, error: string, size_mb: float}
     */
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

    // ─── Pinterest API v5 — Pin Search ────────────────────────────────────────

    /**
     * Search public pins via Pinterest API v5.
     * Returns array of pin objects for display/download in the Posts page.
     *
     * @return array{pins: array, bookmark: string|null, error?: string}
     */
    public function searchPins(string $query, int $pageSize = 12): array
    {
        if (env('PINTEREST_MOCK', true)) {
            return $this->mockSearchPins($query, $pageSize);
        }

        $token = env('PINTEREST_ACCESS_TOKEN');

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->get('https://api.pinterest.com/v5/search/pins', [
                    'query'     => $query,
                    'page_size' => $pageSize,
                ]);

            $body = $response->json();

            if (!isset($body['items'])) {
                return ['pins' => [], 'bookmark' => null, 'error' => 'Nenhum resultado.'];
            }

            $pins = collect($body['items'])->map(fn ($item) => [
                'id'         => $item['id'],
                'title'      => $item['title'] ?? '',
                'link'       => $item['link'] ?? null,
                'image_url'  => $item['media']['images']['orig']['url']
                             ?? $item['media']['images']['600x']['url']
                             ?? null,
                'media_type' => $item['media']['media_type'] ?? 'image',
                'video_url'  => $item['media']['videos']['V_720P']['url']
                             ?? $item['media']['videos']['V_HLS']['url']
                             ?? null,
            ])->values()->all();

            return ['pins' => $pins, 'bookmark' => $body['bookmark'] ?? null];

        } catch (\Throwable $e) {
            Log::error('PinterestService::searchPins failed', ['message' => $e->getMessage()]);
            return ['pins' => [], 'bookmark' => null, 'error' => 'Falha ao buscar no Pinterest.'];
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

    // ─── Private ─────────────────────────────────────────────────────────────

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
