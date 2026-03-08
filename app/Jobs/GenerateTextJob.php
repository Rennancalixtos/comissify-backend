<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\PostQueue;
use App\Models\Video;
use App\Services\AiTextService;
use App\Services\ShopeeApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly int $videoId,
    ) {}

    public function handle(AiTextService $ai, ShopeeApiService $shopee): void
    {
        $video = Video::with('product')->find($this->videoId);

        if (! $video || ! $video->product) {
            Log::warning('GenerateTextJob: vídeo ou produto não encontrado', ['video_id' => $this->videoId]);
            return;
        }

        if (! in_array($video->validation_status, ['valid', 'normalized'])) {
            Log::warning('GenerateTextJob: vídeo não está aprovado', [
                'video_id' => $this->videoId,
                'status'   => $video->validation_status,
            ]);
            return;
        }

        $product = $video->product;

        // 1. Gerar conteúdo via IA
        $title         = $ai->generateTitle($product);
        $description   = $ai->generateDescription($product);
        $hashtags      = $ai->generateHashtags($product);
        $affiliateLink = $shopee->generateAffiliateLink($product->shopee_product_id);

        Log::info('GenerateTextJob: conteúdo gerado', [
            'video_id' => $video->id,
            'title'    => $title,
            'hashtags' => $hashtags,
        ]);

        // 2. Buscar conta disponível
        $account = Account::available()->first();

        if (! $account) {
            Log::warning('GenerateTextJob: nenhuma conta disponível, criando fila sem conta');
            // Pegar qualquer conta ativa para manter FK
            $account = Account::active()->first();
        }

        if (! $account) {
            Log::error('GenerateTextJob: nenhuma conta ativa no sistema');
            return;
        }

        // 3. Calcular scheduled_at
        $intervalMinutes = (int) \App\Models\Setting::get('posting.interval_minutes', 90);
        $scheduledAt     = $account->last_post_at
            ? $account->last_post_at->addMinutes($intervalMinutes)
            : now();

        if ($scheduledAt->isPast()) {
            $scheduledAt = now();
        }

        // 4. Criar na fila
        $priority = (int) round(10 - ($product->opportunity_score / 10));
        $priority = max(1, min(10, $priority));

        PostQueue::create([
            'video_id'      => $video->id,
            'account_id'    => $account->id,
            'title'         => $title,
            'description'   => $description,
            'hashtags'      => $hashtags,
            'affiliate_link' => $affiliateLink,
            'priority'      => $priority,
            'status'        => 'pending',
            'scheduled_at'  => $scheduledAt,
        ]);

        // 5. Marcar vídeo como na fila
        $video->update(['validation_status' => 'queued']);

        Log::info('GenerateTextJob: item criado na fila', ['video_id' => $video->id]);
    }

    public function queue(): string
    {
        return 'ai';
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateTextJob failed', [
            'video_id'  => $this->videoId,
            'exception' => $exception->getMessage(),
        ]);
    }
}
