<?php

namespace App\Services\Demo;

use App\Models\UserCredential;
use App\Services\ShopeeAffiliateService;
use App\Support\DemoCatalog;

/**
 * Substitui a API de afiliados da Shopee no modo demonstração.
 * Mesmo contrato do serviço real, mas sem chamadas externas.
 */
class DemoShopeeAffiliateService extends ShopeeAffiliateService
{
    public function getProducts(object $cred, array $params = []): array
    {
        $page  = max(1, (int) ($params['page'] ?? 1));
        $limit = (int) ($params['limit'] ?? 20);

        $all = DemoCatalog::search(
            $params['keyword'] ?? null,
            isset($params['productCatId']) ? (int) $params['productCatId'] : null,
            (int) ($params['sortType'] ?? 2),
        );

        // Busca sem resultado: mostra o catálogo inteiro para a demo nunca ficar vazia.
        if (empty($all)) {
            $all = DemoCatalog::search(null, null, (int) ($params['sortType'] ?? 2));
        }

        $nodes = array_slice($all, ($page - 1) * $limit, $limit);

        return [
            'nodes'    => $nodes,
            'pageInfo' => ['page' => $page, 'limit' => $limit, 'hasNextPage' => count($all) > $page * $limit],
        ];
    }

    public function generateShortLink(UserCredential $cred, string $originUrl, array $subIds = []): string
    {
        return 'https://s.shopee.com.br/demo' . substr(md5($originUrl), 0, 7);
    }

    public function getConversionReport(UserCredential $cred, array $params = []): array
    {
        $start  = (int) ($params['purchaseTimeStart'] ?? strtotime('-30 days'));
        $end    = (int) ($params['purchaseTimeEnd'] ?? time());
        $status = $params['orderStatus'] ?? 'ALL';

        $products = DemoCatalog::all();
        $statuses = ['COMPLETED', 'COMPLETED', 'COMPLETED', 'PENDING', 'PENDING', 'CANCELLED'];
        $nodes    = [];

        // Gera conversões determinísticas (mesma lista a cada visita), uma a cada ~14h.
        mt_srand(42);
        for ($t = time(); $t > strtotime('-90 days'); $t -= mt_rand(6, 22) * 3600) {
            $product = $products[mt_rand(0, count($products) - 1)];
            $st      = $statuses[mt_rand(0, count($statuses) - 1)];
            $qty     = mt_rand(1, 3);

            if ($t < $start || $t > $end) continue;
            if ($status !== 'ALL' && $st !== $status) continue;

            $amount     = round($product['priceMin'] * $qty, 2);
            $commission = $st === 'CANCELLED' ? 0 : round($amount * $product['commissionRate'] / 100, 2);

            $nodes[] = [
                'clickTime'       => $t - mt_rand(600, 7200),
                'purchaseTime'    => $t,
                'conversionId'    => (string) (5000000 + $t % 1000000),
                'totalCommission' => $commission,
                'netCommission'   => round($commission * 0.9, 2),
                'buyerType'       => mt_rand(0, 1) ? 'NEW' : 'EXISTING',
                'device'          => mt_rand(0, 3) ? 'APP' : 'WEB',
                'orders'          => [[
                    'orderId'     => 'DEMO' . strtoupper(substr(md5((string) $t), 0, 10)),
                    'orderStatus' => $st,
                    'items'       => [[
                        'itemId'              => $product['itemId'],
                        'itemName'            => $product['productName'],
                        'shopName'            => $product['shopName'],
                        'imageUrl'            => $product['imageUrl'],
                        'itemPrice'           => $product['priceMin'],
                        'actualAmount'        => $amount,
                        'qty'                 => $qty,
                        'itemTotalCommission' => $commission,
                    ]],
                ]],
            ];
        }
        mt_srand();

        return [
            'nodes'    => array_slice($nodes, 0, (int) ($params['limit'] ?? 50)),
            'pageInfo' => ['limit' => 50, 'hasNextPage' => false, 'scrollId' => null],
        ];
    }
}
