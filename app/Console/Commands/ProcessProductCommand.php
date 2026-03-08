<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Video;
use App\Services\AiTextService;
use App\Services\FFmpegService;
use App\Services\PinterestService;
use App\Services\ShopeeApiService;
use Illuminate\Console\Command;

class ProcessProductCommand extends Command
{
    protected $signature   = 'shopee:process-product {product_id : ID do produto na tabela products}';
    protected $description = 'Executa o pipeline completo (busca → download → validação → IA) de forma síncrona para um produto.';

    public function handle(
        AiTextService    $ai,
        PinterestService $pinterest,
        FFmpegService    $ffmpeg,
        ShopeeApiService $shopee,
    ): int {
        $productId = (int) $this->argument('product_id');
        $product   = Product::find($productId);

        if (! $product) {
            $this->error("Produto #{$productId} não encontrado.");
            return self::FAILURE;
        }

        $this->info("Produto: {$product->name}");
        $this->newLine();

        // ─── Passo 1: Gerar termos de busca ──────────────────────────────────
        $this->line('<fg=blue>[1/4]</> Gerando termos de busca via IA...');

        $terms = $ai->generateSearchTerms($product);

        if (empty($terms)) {
            $this->warn('  → IA não retornou termos. Usando nome do produto como fallback.');
            $terms = [mb_substr($product->name, 0, 30), $product->category ?? $product->name];
        }

        $this->line('  → '.implode(', ', $terms));
        $this->newLine();

        // ─── Passo 2: Buscar e baixar vídeos ─────────────────────────────────
        $this->line('<fg=blue>[2/4]</> Buscando e baixando vídeos no Pinterest...');

        $candidates = $pinterest->searchVideos($terms, 5);

        if (empty($candidates)) {
            $this->warn('  → Nenhum candidato encontrado no Pinterest.');
            $this->warn('  → Verifique a conexão com a internet e o yt-dlp.');
            return self::FAILURE;
        }

        $downloadedVideos = [];
        $outputDir        = storage_path('app/'.env('VIDEO_TEMP_PATH', 'videos/temp').'/'.$product->id);

        foreach (array_slice($candidates, 0, 3) as $i => $candidate) {
            $num = $i + 1;

            // Verificar duração antes de baixar
            if ($candidate['duration'] > 55) {
                $this->line("  → Candidato {$num}: rejeitado na busca (duração {$candidate['duration']}s — fora do limite)");
                continue;
            }

            $this->line("  → Candidato {$num}: baixando ({$candidate['duration']}s)...");

            $result = $pinterest->downloadVideo($candidate['url'], $outputDir);

            if (! $result['success']) {
                $this->line("  → Candidato {$num}: <fg=red>falhou</> — {$result['error']}");
                continue;
            }

            $video = Video::create([
                'product_id'        => $product->id,
                'pinterest_url'     => $candidate['url'],
                'local_path'        => str_replace(storage_path('app/'), '', $result['filepath']),
                'file_size_mb'      => $result['size_mb'] ?? null,
                'validation_status' => 'pending',
            ]);

            $sizeMb  = number_format($result['size_mb'] ?? 0, 1);
            $this->line("  → Candidato {$num}: <fg=green>baixado</> ({$candidate['duration']}s, {$sizeMb}MB) — ID #{$video->id}");

            $downloadedVideos[] = $video;
        }

        if (empty($downloadedVideos)) {
            $this->error('Nenhum vídeo foi baixado com sucesso.');
            return self::FAILURE;
        }

        $this->newLine();

        // ─── Passo 3: Validar e normalizar ───────────────────────────────────
        $this->line('<fg=blue>[3/4]</> Validando e normalizando vídeos...');

        $approvedVideos = [];

        foreach ($downloadedVideos as $video) {
            $inputPath = storage_path('app/'.$video->local_path);

            // Ler metadata
            $meta = $ffmpeg->getMetadata($inputPath);
            if ($meta) {
                $video->update([
                    'duration_seconds' => $meta['duration'] ?? null,
                    'resolution'       => isset($meta['width'], $meta['height']) ? $meta['width'].'x'.$meta['height'] : null,
                    'file_size_mb'     => $meta['file_size_mb'] ?? $video->file_size_mb,
                    'codec'            => $meta['video_codec'] ?? null,
                ]);
            }

            $validation = $ffmpeg->validate($inputPath);

            if ($validation['valid']) {
                $readyPath = $this->buildReadyPath($video);
                $this->ensureDir(dirname($readyPath));
                @rename($inputPath, $readyPath);

                $video->update([
                    'local_path'        => str_replace(storage_path('app/'), '', $readyPath),
                    'validation_status' => 'valid',
                    'validated_at'      => now(),
                ]);

                $this->generateThumbnail($ffmpeg, $video, $readyPath);
                $this->line("  → Vídeo #{$video->id}: <fg=green>aprovado</> → salvo em /videos/ready/{$product->id}/");
                $approvedVideos[] = $video->fresh();
                continue;
            }

            // Tentar normalizar
            $outputPath = $this->buildReadyPath($video);
            $this->ensureDir(dirname($outputPath));

            $this->line("  → Vídeo #{$video->id}: fora do padrão ({$validation['reason']}), normalizando...");

            $normalized = $ffmpeg->normalize($inputPath, $outputPath);

            if (! $normalized['success']) {
                $video->update([
                    'validation_status' => 'invalid',
                    'rejection_reason'  => $normalized['error'] ?? 'Normalização falhou',
                    'validated_at'      => now(),
                ]);
                @unlink($inputPath);
                $this->line("  → Vídeo #{$video->id}: <fg=red>rejeitado</> — {$normalized['error']}");
                continue;
            }

            if ($inputPath !== $outputPath) {
                @unlink($inputPath);
            }

            $nm = $normalized['metadata'];
            $video->update([
                'local_path'        => str_replace(storage_path('app/'), '', $outputPath),
                'duration_seconds'  => $nm['duration'] ?? $video->duration_seconds,
                'resolution'        => isset($nm['width'], $nm['height']) ? $nm['width'].'x'.$nm['height'] : $video->resolution,
                'file_size_mb'      => $nm['file_size_mb'] ?? $video->file_size_mb,
                'codec'             => $nm['video_codec'] ?? $video->codec,
                'validation_status' => 'normalized',
                'validated_at'      => now(),
            ]);

            $this->generateThumbnail($ffmpeg, $video, $outputPath);
            $this->line("  → Vídeo #{$video->id}: <fg=green>normalizado</> → salvo em /videos/ready/{$product->id}/");
            $approvedVideos[] = $video->fresh();
        }

        if (empty($approvedVideos)) {
            $this->error('Nenhum vídeo passou na validação.');
            return self::FAILURE;
        }

        $this->newLine();

        // ─── Passo 4: Gerar conteúdo com IA ──────────────────────────────────
        $this->line('<fg=blue>[4/4]</> Gerando conteúdo via IA...');

        $account = \App\Models\Account::available()->first()
            ?? \App\Models\Account::active()->first();

        if (! $account) {
            $this->warn('  → Nenhuma conta ativa. Itens criados sem conta — edite manualmente no painel.');
        }

        foreach ($approvedVideos as $video) {
            $title         = $ai->generateTitle($product);
            $description   = $ai->generateDescription($product);
            $hashtags      = $ai->generateHashtags($product);
            $affiliateLink = $shopee->generateAffiliateLink($product->shopee_product_id);

            $this->line("  → Título: <fg=yellow>\"{$title}\"</>");
            $this->line('  → Descrição: gerada ('.mb_strlen($description).' chars)');
            $this->line('  → Hashtags: '.implode(' ', $hashtags));

            if ($account) {
                $intervalMinutes = (int) \App\Models\Setting::get('posting.interval_minutes', 90);
                $scheduledAt     = $account->last_post_at
                    ? $account->last_post_at->addMinutes($intervalMinutes)
                    : now();
                if ($scheduledAt->isPast()) {
                    $scheduledAt = now();
                }

                $priority = max(1, min(10, (int) round(10 - ($product->opportunity_score / 10))));

                \App\Models\PostQueue::create([
                    'video_id'       => $video->id,
                    'account_id'     => $account->id,
                    'title'          => $title,
                    'description'    => $description,
                    'hashtags'       => $hashtags,
                    'affiliate_link' => $affiliateLink,
                    'priority'       => $priority,
                    'status'         => 'pending',
                    'scheduled_at'   => $scheduledAt,
                ]);

                $video->update(['validation_status' => 'queued']);
            }
        }

        $this->newLine();
        $count = count($approvedVideos);
        $this->line("<fg=green>✓</> Pipeline concluído. {$count} vídeo(s) prontos em <href=".url('/admin/queue').">admin/queue</>");
        $this->newLine();

        return self::SUCCESS;
    }

    private function buildReadyPath(Video $video): string
    {
        return storage_path('app/public/'.env('VIDEO_READY_PATH', 'videos/ready').'/'.$video->product_id.'/'.$video->id.'.mp4');
    }

    private function ensureDir(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function generateThumbnail(FFmpegService $ffmpeg, Video $video, string $videoPath): void
    {
        $thumbDir  = env('THUMBNAIL_PATH', 'public/thumbnails');
        $thumbPath = storage_path("app/{$thumbDir}/{$video->id}.jpg");
        $ffmpeg->generateThumbnail($videoPath, $thumbPath);
    }
}
