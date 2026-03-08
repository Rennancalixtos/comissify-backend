<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopeeApiService
{
    private string $baseUrl;
    private string $appId;
    private string $secret;
    private string $affiliateId;
    private int    $timeout;

    public function __construct()
    {
        $this->baseUrl     = config('services.shopee.api_base_url', env('SHOPEE_API_BASE_URL'));
        $this->appId       = config('services.shopee.app_id', env('SHOPEE_APP_ID'));
        $this->secret      = config('services.shopee.secret', env('SHOPEE_SECRET'));
        $this->affiliateId = config('services.shopee.affiliate_id', env('SHOPEE_AFFILIATE_ID'));
        $this->timeout     = (int) env('SHOPEE_REQUEST_TIMEOUT', 30);
    }

    /**
     * Fetch a ranked list of top-performing products from the Shopee Affiliate API.
     *
     * @param  string  $category  Optional category slug to filter results.
     * @param  int     $limit     Maximum number of products to return (default 50).
     * @return array<int, array{
     *     shopee_product_id: string,
     *     name: string,
     *     category: string,
     *     price: float,
     *     commission_rate: float,
     *     rating: float,
     *     sales_count: int,
     * }>
     *
     * @throws \RuntimeException When the API returns an unexpected response.
     */
    public function getTopProducts(string $category = '', int $limit = 50): array
    {
        // TODO:
        // 1. Build GraphQL query (or REST request) for the Shopee Affiliate Open API.
        // 2. Sign the request with HMAC-SHA256 using $this->appId and $this->secret.
        // 3. Send POST to $this->baseUrl with the signed payload.
        // 4. Parse the response and return a normalised array of product data.
        // 5. Handle rate-limit errors with exponential backoff (max 3 retries).
        return [];
    }

    /**
     * Retrieve detailed information for a single product by its Shopee product ID.
     *
     * @param  string  $shopeeProductId  The unique product identifier on Shopee.
     * @return array{
     *     shopee_product_id: string,
     *     name: string,
     *     category: string,
     *     price: float,
     *     commission_rate: float,
     *     rating: float,
     *     sales_count: int,
     *     description: string,
     *     images: string[],
     * }|null  Null if the product is not found or the API returns an error.
     */
    public function getProductDetails(string $shopeeProductId): ?array
    {
        // TODO:
        // 1. Send a product detail request to the API endpoint.
        // 2. Return null gracefully if the product is unavailable / delisted.
        return null;
    }

    /**
     * Generate a trackable Shopee affiliate deep-link for the given product.
     *
     * @param  string  $shopeeProductId  The unique product identifier on Shopee.
     * @return string  The shortened affiliate URL (bit.ly-style or shopee.link).
     *
     * @throws \RuntimeException When affiliate link generation fails.
     */
    public function generateAffiliateLink(string $shopeeProductId): string
    {
        // Se a API não estiver configurada, retorna um placeholder navegável
        if (empty($this->appId) || empty($this->secret)) {
            return "https://shopee.com.br/product/{$shopeeProductId}?af_id={$this->affiliateId}";
        }

        return \Illuminate\Support\Facades\Cache::remember(
            "affiliate_link:{$shopeeProductId}",
            now()->addHours(24),
            function () use ($shopeeProductId) {
                // TODO: implementar chamada real à API Shopee Affiliate
                // Por enquanto retorna URL direta como placeholder
                return "https://shopee.com.br/product/{$shopeeProductId}?af_id={$this->affiliateId}";
            }
        );
    }

    // ─── Private helpers ─────────────────────────────────────────────────

    private function buildSignature(array $params): string
    {
        // TODO: Implement HMAC-SHA256 signature as per Shopee Open API docs.
        return '';
    }

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$this->appId,
            ]);
    }
}
