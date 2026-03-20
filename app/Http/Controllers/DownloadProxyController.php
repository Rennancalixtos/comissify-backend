<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DownloadProxyController extends Controller
{
    public function download(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            abort(400, 'URL inválida.');
        }

        // Only allow HTTPS
        if (!str_starts_with($url, 'https://')) {
            abort(403, 'Apenas URLs HTTPS são permitidas.');
        }

        $host = parse_url($url, PHP_URL_HOST);

        // Resolve host to IP and block private ranges
        $ip = gethostbyname($host);
        if ($this->isPrivateIp($ip)) {
            abort(403, 'Domínio não permitido.');
        }

        $filename = 'media-' . substr(md5($url), 0, 8);
        $path     = strtolower(parse_url($url, PHP_URL_PATH) ?? '');

        // Social media pages and HLS streams must use yt-dlp
        $ytdlpDomains = ['tiktok.com', 'pinterest.com', 'instagram.com', 'youtube.com', 'youtu.be'];
        $needsYtdlp   = str_ends_with($path, '.m3u8')
            || collect($ytdlpDomains)->contains(fn($d) => str_contains($host, $d));

        if ($needsYtdlp) {
            return $this->downloadWithYtdlp($url, $filename . '.mp4');
        }

        // Direct file proxy
        $response = Http::timeout(30)->get($url);

        if (!$response->successful()) {
            // Fallback to yt-dlp for video URLs that refuse direct download
            if (str_contains($response->header('Content-Type') ?? '', 'video') || str_ends_with($path, '.mp4')) {
                return $this->downloadWithYtdlp($url, $filename . '.mp4');
            }
            abort(502, 'Falha ao baixar o arquivo.');
        }

        $contentType = $response->header('Content-Type') ?? 'application/octet-stream';
        $ext         = str_contains($contentType, 'video') ? 'mp4' : 'jpg';

        return response($response->body(), 200, [
            'Content-Type'        => $contentType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '.' . $ext . '"',
            'Content-Length'      => strlen($response->body()),
        ]);
    }

    private function downloadWithYtdlp(string $url, string $filename): \Symfony\Component\HttpFoundation\Response
    {
        $ytdlp    = config('services.ytdlp.path');
        $tmpRaw   = sys_get_temp_dir() . '/' . uniqid('raw_') . '.mp4';
        $tmpFinal = sys_get_temp_dir() . '/' . uniqid('final_') . '.mp4';

        // 1. Download com yt-dlp (melhor qualidade disponível)
        $download = new Process([
            $ytdlp,
            $url,
            '--no-playlist',
            '-o', $tmpRaw,
            '--merge-output-format', 'mp4',
            '--quiet',
        ]);
        $download->setTimeout(120);

        try {
            $download->mustRun();
        } catch (\Exception $e) {
            Log::error('DownloadProxy yt-dlp failed', ['url' => $url, 'error' => $e->getMessage()]);
            abort(502, 'Falha ao baixar vídeo.');
        }

        if (!file_exists($tmpRaw)) {
            abort(502, 'Arquivo de vídeo não gerado.');
        }

        // 2. Transcodar para H.264 + AAC com FFmpeg (compatível com qualquer dispositivo)
        $transcode = new Process([
            'ffmpeg',
            '-i', $tmpRaw,
            '-c:v', 'libx264',
            '-preset', 'fast',
            '-crf', '23',
            '-c:a', 'aac',
            '-b:a', '128k',
            '-movflags', '+faststart',
            '-y',
            $tmpFinal,
        ]);
        $transcode->setTimeout(180);

        try {
            $transcode->mustRun();
        } catch (\Exception $e) {
            @unlink($tmpRaw);
            Log::error('DownloadProxy ffmpeg transcode failed', ['url' => $url, 'error' => $e->getMessage()]);
            abort(502, 'Falha ao processar vídeo.');
        }

        @unlink($tmpRaw);

        if (!file_exists($tmpFinal)) {
            abort(502, 'Arquivo transcoded não gerado.');
        }

        return response()->download($tmpFinal, $filename, [
            'Content-Type' => 'video/mp4',
        ])->deleteFileAfterSend(true);
    }

    private function isPrivateIp(string $ip): bool
    {
        if (in_array($ip, ['localhost', '::1'], true)) {
            return true;
        }

        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return true; // can't resolve = block
        }

        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
