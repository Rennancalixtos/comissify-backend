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

        // HLS streams (.m3u8) must be processed via yt-dlp
        if (str_ends_with($path, '.m3u8')) {
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
        $ytdlp  = config('services.ytdlp.path');
        $tmpOut = sys_get_temp_dir() . '/' . uniqid('media_') . '.mp4';

        $process = new Process([
            $ytdlp,
            $url,
            '--no-playlist',
            '-o', $tmpOut,
            '--merge-output-format', 'mp4',
            '--quiet',
        ]);
        $process->setTimeout(120);

        try {
            $process->mustRun();
        } catch (\Exception $e) {
            Log::error('DownloadProxy yt-dlp failed', ['url' => $url, 'error' => $e->getMessage()]);
            abort(502, 'Falha ao processar vídeo.');
        }

        if (!file_exists($tmpOut)) {
            abort(502, 'Arquivo de vídeo não gerado.');
        }

        return response()->download($tmpOut, $filename, [
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
