<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ShopeeApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DiscoverProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(
        public readonly string $category = '',
        public readonly int    $limit    = 50,
    ) {}

    public function handle(ShopeeApiService $shopee): void
    {
        // TODO: Implementation steps
        // 1. Call $shopee->getTopProducts($this->category, $this->limit)
        //    to retrieve a list of trending/high-commission products from the API.
        //
        // 2. For each product returned by the API:
        //    a. Upsert into the `products` table using shopee_product_id as the unique key.
        //    b. Calculate `opportunity_score` based on commission_rate, rating, and sales_count.
        //    c. Set status to 'active' if it meets minimum thresholds from Settings.
        //
        // 3. For each newly discovered product, dispatch DownloadVideoJob to find
        //    relevant Pinterest videos for that product.
        //
        // 4. Log discovery summary (products found, new vs updated, skipped).
    }

    public function queue(): string
    {
        return 'products';
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('DiscoverProductsJob failed', [
            'category'  => $this->category,
            'exception' => $exception->getMessage(),
        ]);
    }
}
