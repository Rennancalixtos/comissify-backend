<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Video;
use App\Services\AiTextService;
use App\Services\PinterestService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DownloadVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 600;

    public function __construct(
        public readonly int $productId,
        public readonly int $maxCandidates = 3,
    ) {}

    public function handle(PinterestService $pinterest, AiTextService $ai): void
    {
        $product = Product::find($this->productId);

        if (! $product || $product->status !== 'active') {
            Log::warning('DownloadVideoJob: produto não encontrado ou inativo', ['id' => $this->productId]);
            return;
        }

        // 1. Gerar termos de busca via IA
        $terms = $ai->generateSearchTerms($product);

        Log::info('DownloadVideoJob: termos gerados', ['product' => $product->name, 'terms' => $terms]);

        // 2. Buscar candidatos no Pinterest
        $candidates = $pinterest->searchVideos($terms, 5);

        if (empty($candidates)) {
            Log::warning('DownloadVideoJob: nenhum candidato encontrado', ['product' => $product->name]);
            return;
        }

        // 3. Baixar os N primeiros candidatos válidos
        $downloaded = 0;
        $outputDir  = storage_path('app/'.env('VIDEO_TEMP_PATH', 'videos/temp').'/'.$product->id);

        foreach (array_slice($candidates, 0, $this->maxCandidates) as $candidate) {
            // Criar registro pendente
            $video = Video::create([
                'product_id'       => $product->id,
                'pinterest_url'    => $candidate['url'],
                'validation_status' => 'pending',
            ]);

            $result = $pinterest->downloadVideo($candidate['url'], $outputDir);

            if (! $result['success']) {
                $video->update([
                    'validation_status' => 'invalid',
                    'rejection_reason'  => 'Download falhou: '.($result['error'] ?? 'erro desconhecido'),
                ]);

                Log::warning('DownloadVideoJob: download falhou', [
                    'url'   => $candidate['url'],
                    'error' => $result['error'] ?? '',
                ]);
                continue;
            }

            $video->update([
                'local_path'   => str_replace(storage_path('app/'), '', $result['filepath']),
                'file_size_mb' => $result['size_mb'] ?? null,
            ]);

            Log::info('DownloadVideoJob: vídeo baixado', [
                'video_id' => $video->id,
                'path'     => $result['filepath'],
                'size_mb'  => $result['size_mb'] ?? 0,
            ]);

            ValidateVideoJob::dispatch($video->id);
            $downloaded++;
        }

        Log::info('DownloadVideoJob: concluído', [
            'product'     => $product->name,
            'candidates'  => count($candidates),
            'downloaded'  => $downloaded,
        ]);
    }

    public function queue(): string
    {
        return 'videos';
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('DownloadVideoJob failed', [
            'product_id' => $this->productId,
            'exception'  => $exception->getMessage(),
        ]);
    }
}
