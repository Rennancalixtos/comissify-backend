<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class FFmpegService
{
    // Shopee video requirements
    public const MIN_WIDTH      = 1280;
    public const MIN_HEIGHT     = 720;
    public const TARGET_FPS     = 30;
    public const MAX_DURATION   = 60;   // seconds
    public const MAX_SIZE_MB    = 50;
    public const VIDEO_CODEC    = 'h264';
    public const AUDIO_CODEC    = 'aac';

    private string $pythonBinary;
    private string $scriptPath;
    private string $ffmpegBin;
    private string $ffprobeBin;

    public function __construct()
    {
        $this->pythonBinary = env('PYTHON_PATH', '/usr/bin/python3');
        $this->scriptPath   = base_path('scripts/python/ffmpeg_validator.py');
        $this->ffmpegBin    = env('FFMPEG_PATH', '/usr/bin/ffmpeg');
        $this->ffprobeBin   = env('FFPROBE_PATH', '/usr/bin/ffprobe');
    }

    /**
     * Validate a video file against Shopee requirements via ffmpeg_validator.py.
     *
     * @return array{valid: bool, reason: string|null, metadata: array}
     */
    public function validate(string $path): array
    {
        $result = $this->runScript('validate', $path);

        return [
            'valid'    => (bool) ($result['success'] ?? false),
            'reason'   => ($result['rejection_reason'] ?? '') ?: null,
            'metadata' => $result['metadata'] ?? [],
        ];
    }

    /**
     * Normalize (re-encode) a video to meet all Shopee requirements.
     *
     * After a successful normalize, also generates a thumbnail from frame 1.
     *
     * @return array{success: bool, output_path: string, metadata: array, error: string|null}
     */
    public function normalize(string $inputPath, string $outputPath): array
    {
        $result = $this->runScript('normalize', $inputPath, $outputPath);

        $success = (bool) ($result['success'] ?? false);

        return [
            'success'     => $success,
            'output_path' => $result['output_path'] ?? '',
            'metadata'    => $result['metadata'] ?? [],
            'error'       => ($result['rejection_reason'] ?? '') ?: null,
        ];
    }

    /**
     * Extract metadata from a video file by running --action validate and
     * returning only the metadata portion of the response.
     *
     * @return array{duration: int, width: int, height: int, video_codec: string, audio_codec: string, fps: float, file_size_mb: float}|null
     */
    public function getMetadata(string $path): ?array
    {
        $result = $this->runScript('validate', $path);
        $meta   = $result['metadata'] ?? [];

        return ! empty($meta) ? $meta : null;
    }

    /**
     * Generate a thumbnail (JPEG) from the first second of a video file.
     *
     * @param  string  $videoPath      Absolute path to the MP4.
     * @param  string  $thumbnailPath  Absolute path for the output JPEG.
     * @return bool
     */
    public function generateThumbnail(string $videoPath, string $thumbnailPath): bool
    {
        $dir = dirname($thumbnailPath);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $process = new Process([
            $this->ffmpegBin,
            '-y',
            '-i', $videoPath,
            '-ss', '00:00:01',
            '-vframes', '1',
            '-q:v', '2',
            $thumbnailPath,
        ]);

        $process->setTimeout(30);

        try {
            $process->mustRun();

            return file_exists($thumbnailPath);
        } catch (\Exception $e) {
            Log::warning('FFmpegService::generateThumbnail failed', [
                'video'  => $videoPath,
                'error'  => $e->getMessage(),
            ]);

            return false;
        }
    }

    // ─── Private helpers ─────────────────────────────────────────────────

    private function runScript(string $action, string $input, string $output = ''): array
    {
        $cmd = [$this->pythonBinary, $this->scriptPath, '--action', $action, '--input', $input];
        if ($output) {
            $cmd = array_merge($cmd, ['--output', $output]);
        }

        $process = new Process($cmd);
        $process->setTimeout(300);

        try {
            $process->mustRun();
            return json_decode($process->getOutput(), true) ?? [];
        } catch (\Exception $e) {
            Log::error("FFmpegService::{$action} failed", [
                'input' => $input,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'output_path' => '', 'metadata' => [], 'rejection_reason' => $e->getMessage()];
        }
    }
}
