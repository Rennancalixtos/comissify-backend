<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LandingScreenshot;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class GenerateLandingWebp extends Command
{
    protected $signature = 'landing:generate-webp {--force : Regera mesmo que já existam}';
    protected $description = 'Gera versões WebP responsivas (640w, 828w, 1200w) das screenshots da landing page';

    public function handle(): int
    {
        $manager     = new ImageManager(new Driver());
        $screenshots = LandingScreenshot::all();
        $outputDir   = storage_path('app/public/landing/webp');

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        foreach ($screenshots as $screenshot) {
            $sourcePath = storage_path('app/public/' . $screenshot->image_path);

            if (!file_exists($sourcePath)) {
                $this->warn("Não encontrado: {$screenshot->image_path}");
                continue;
            }

            $stem = pathinfo($sourcePath, PATHINFO_FILENAME);

            foreach ([640, 828, 1200] as $width) {
                $outputPath = "{$outputDir}/{$stem}-{$width}w.webp";

                if (!$this->option('force') && file_exists($outputPath)) {
                    $this->line("  Já existe: {$stem}-{$width}w.webp (use --force para reger)");
                    continue;
                }

                $encoded = $manager->read($sourcePath)
                    ->scaleDown(width: $width)
                    ->encode(new WebpEncoder(quality: 82));

                file_put_contents($outputPath, $encoded);
                $this->info("  Gerado: {$stem}-{$width}w.webp");
            }
        }

        $this->info('Concluído.');
        return 0;
    }
}
