<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TikTokShopService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.scrapecreators.key');
    }

    public function searchProducts(string $query, int $page = 1): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeader('x-api-key', $this->apiKey)
                ->get('https://api.scrapecreators.com/v1/tiktok/shop/search', [
                    'query'  => $query,
                    'region' => 'BR',
                    'page'   => $page,
                ]);

            $body = $response->json();

            if (empty($body['success']) || empty($body['products'])) {
                return ['products' => [], 'error' => 'Nenhum produto encontrado.'];
            }

            $products = collect($body['products'])
                ->map(function ($p) {
                    $price     = $p['product_price_info'] ?? [];
                    $rate      = $p['rate_info'] ?? [];
                    $sold      = $p['sold_info'] ?? [];
                    $image     = $p['image']['url_list'][0] ?? null;
                    $productId = $p['product_id'] ?? null;

                    // TikTok Shop BR não permite acesso externo a páginas de produto individual.
                    // Redireciona para busca no TikTok Shop BR com o título do produto.
                    $title = $p['title'] ?? '';
                    $url   = 'https://www.tiktok.com/shop/br?q=' . urlencode($title);

                    if (!$image) {
                        return null;
                    }

                    // Para região BR a API retorna o preço em centavos sem ponto decimal (ex: "4658" = R$46,58)
                    // Para outras regiões retorna com decimal (ex: "49.99")
                    $rawPrice = $price['sale_price_decimal'] ?? $price['sale_price_format'] ?? null;
                    $priceDisplay = null;
                    if ($rawPrice !== null) {
                        $floatPrice = (float) $rawPrice;
                        // Se não há ponto decimal na string original, está em centavos
                        if (!str_contains((string) $rawPrice, '.')) {
                            $floatPrice /= 100;
                        }
                        $priceDisplay = number_format($floatPrice, 2, ',', '.');
                    }

                    return [
                        'id'       => $productId,
                        'title'    => $p['title'] ?? '',
                        'image'    => $image,
                        'price'    => $priceDisplay,
                        'currency' => 'R$',
                        'discount' => $price['discount_format'] ?? null,
                        'rating'   => $rate['score'] ?? null,
                        'reviews'  => $rate['review_count'] ?? null,
                        'sold'     => $sold['sold_count'] ?? null,
                        'shop'     => $p['seller_info']['shop_name'] ?? '',
                        'url'      => $url,
                    ];
                })
                ->filter()
                ->values()
                ->all();

            return ['products' => $products];

        } catch (\Throwable $e) {
            Log::error('TikTokShopService::searchProducts failed', ['message' => $e->getMessage()]);
            return ['products' => [], 'error' => 'Falha ao buscar no TikTok Shop.'];
        }
    }
}
