<?php

namespace App\Jobs;

use App\Models\Video;
use App\Services\FFmpegService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ValidateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 300;

    public function __construct(
        public readonly int $videoId,
    ) {}

    public function handle(FFmpegService $ffmpeg): void
    {
        $video = Video::with('product')->find($this->videoId);

        if (! $video) {
            Log::warning('ValidateVideoJob: vídeo não encontrado', ['id' => $this->videoId]);
            return;
        }

        $inputPath = storage_path('app/'.$video->local_path);

        // 1. Verificar se o arquivo existe
        if (! file_exists($inputPath)) {
            $this->reject($video, 'Arquivo não encontrado: '.$inputPath);
            return;
        }

        // 2. Ler metadata e atualizar registro
        $meta = $ffmpeg->getMetadata($inputPath);
        if ($meta) {
            $video->update([
                'duration_seconds' => $meta['duration'] ?? null,
                'resolution'       => isset($meta['width'], $meta['height'])
                    ? $meta['width'].'x'.$meta['height']
                    : null,
                'file_size_mb'     => $meta['file_size_mb'] ?? null,
                'codec'            => $meta['video_codec'] ?? null,
            ]);
        }

        // 3. Validar requisitos Shopee
        $validation = $ffmpeg->validate($inputPath);

        if ($validation['valid']) {
            // Já está dentro dos requisitos — mover para pasta "ready"
            $readyPath = $this->buildReadyPath($video);
            $this->ensureDir(dirname($readyPath));

            if (! rename($inputPath, $readyPath)) {
                $readyPath = $inputPath; // fallback: manter no temp
            }

            $video->update([
                'local_path'        => str_replace(storage_path('app/'), '', $readyPath),
                'validation_status' => 'valid',
                'validated_at'      => now(),
            ]);

            $this->generateThumbnail($ffmpeg, $video, $readyPath);

            Log::info('ValidateVideoJob: vídeo válido', ['video_id' => $video->id]);

            GenerateTextJob::dispatch($video->id);
            return;
        }

        // 4. Tentar normalizar (duração máxima 2× o limite para tentar)
        $duration = $meta['duration'] ?? 0;
        if ($duration > FFmpegService::MAX_DURATION * 2) {
            $this->reject($video, "Duração {$duration}s muito longa para normalizar.");
            @unlink($inputPath);
            return;
        }

        $outputPath = $this->buildReadyPath($video);
        $this->ensureDir(dirname($outputPath));

        $normalized = $ffmpeg->normalize($inputPath, $outputPath);

        if (! $normalized['success']) {
            $this->reject($video, 'Normalização falhou: '.($normalized['error'] ?? 'erro desconhecido'));
            @unlink($inputPath);
            return;
        }

        // Remover arquivo temp original
        if ($inputPath !== $outputPath) {
            @unlink($inputPath);
        }

        // Atualizar metadata com o arquivo normalizado
        $newMeta = $normalized['metadata'];
        $video->update([
            'local_path'        => str_replace(storage_path('app/'), '', $outputPath),
            'duration_seconds'  => $newMeta['duration'] ?? $video->duration_seconds,
            'resolution'        => isset($newMeta['width'], $newMeta['height'])
                ? $newMeta['width'].'x'.$newMeta['height']
                : $video->resolution,
            'file_size_mb'      => $newMeta['file_size_mb'] ?? $video->file_size_mb,
            'codec'             => $newMeta['video_codec'] ?? $video->codec,
            'validation_status' => 'normalized',
            'validated_at'      => now(),
        ]);

        $this->generateThumbnail($ffmpeg, $video, $outputPath);

        Log::info('ValidateVideoJob: vídeo normalizado', ['video_id' => $video->id]);

        GenerateTextJob::dispatch($video->id);
    }

    public function queue(): string
    {
        return 'videos';
    }


    private function reject(Video $video, string $reason): void
    {
        $video->update([
            'validation_status' => 'invalid',
            'rejection_reason'  => $reason,
            'validated_at'      => now(),
        ]);

        Log::warning('ValidateVideoJob: vídeo rejeitado', [
            'video_id' => $video->id,
            'reason'   => $reason,
        ]);
    }

    private function buildReadyPath(Video $video): string
    {
        $readyDir = env('VIDEO_READY_PATH', 'videos/ready');

        return storage_path("app/public/{$readyDir}/{$video->product_id}/{$video->id}.mp4");
    }

    private function generateThumbnail(FFmpegService $ffmpeg, Video $video, string $videoPath): void
    {
        $thumbDir  = env('THUMBNAIL_PATH', 'public/thumbnails');
        $thumbPath = storage_path("app/{$thumbDir}/{$video->id}.jpg");

        $ffmpeg->generateThumbnail($videoPath, $thumbPath);
    }

    private function ensureDir(string $dir): void
    {
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ValidateVideoJob failed', [
            'video_id'  => $this->videoId,
            'exception' => $exception->getMessage(),
        ]);

        Video::find($this->videoId)?->update([
            'validation_status' => 'invalid',
            'rejection_reason'  => 'Job failed: '.$exception->getMessage(),
        ]);
    }
}
