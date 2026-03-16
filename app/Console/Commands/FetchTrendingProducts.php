<?php

namespace App\Console\Commands;

use App\Services\ShopeeAffiliateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class FetchTrendingProducts extends Command
{
    protected $signature   = 'trending:fetch';
    protected $description = 'Fetch top Shopee products by commission and cache for the public trending page';

    public function handle(ShopeeAffiliateService $shopee): int
    {
        $appId  = config('services.shopee.trending_app_id');
        $secret = config('services.shopee.trending_app_secret');

        if (!$appId || !$secret) {
            $this->error('SHOPEE_TRENDING_APP_ID or SHOPEE_TRENDING_APP_SECRET not set.');
            return self::FAILURE;
        }

        // Anonymous object — same interface as UserCredential but no Eloquent casts
        $cred = new class($appId, $secret) {
            public int    $user_id    = 0;
            public string $app_id;
            public string $app_secret;
            public function __construct(string $appId, string $secret) {
                $this->app_id     = $appId;
                $this->app_secret = $secret;
            }
        };

        $this->info('Fetching trending products from Shopee API...');

        // Mais vendidos (sortType: 1)
        $resultSales = $shopee->getProducts($cred, ['sortType' => 1, 'limit' => 50]);
        if (!empty($resultSales['error'])) {
            $this->error('Shopee API error (mais vendidos): ' . $resultSales['error']);
            return self::FAILURE;
        }

        // Maior comissão — busca 50 (máximo da API) e ordena por valor absoluto no nosso lado
        $resultComm = $shopee->getProducts($cred, ['sortType' => 2, 'limit' => 50]);
        if (!empty($resultComm['error'])) {
            $this->error('Shopee API error (maior comissão): ' . $resultComm['error']);
            return self::FAILURE;
        }

        $salesProducts = $resultSales['nodes'] ?? [];
        $commProducts  = $resultComm['nodes'] ?? [];

        if (empty($salesProducts) && empty($commProducts)) {
            $this->warn('No products returned.');
            return self::SUCCESS;
        }

        $ttl = now()->addHours(6);
        Cache::put('trending_products',            $salesProducts, $ttl);
        Cache::put('commission_products',          $commProducts,  $ttl);
        Cache::put('trending_products_updated_at', now()->toDateTimeString(), $ttl);

        $this->info('Cached ' . count($salesProducts) . ' trending + ' . count($commProducts) . ' commission products.');

        return self::SUCCESS;
    }
}
