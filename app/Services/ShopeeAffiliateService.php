<?php

namespace App\Services;

use App\Models\UserCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShopeeAffiliateService
{
    private string $endpoint;

    public function __construct()
    {
        $this->endpoint = env('SHOPEE_AFFILIATE_API_URL', 'https://open-api.affiliate.shopee.com.br/graphql');
    }


    public function getProducts(UserCredential $cred, array $params = []): array
    {
        if (env('SHOPEE_MOCK', false)) {
            return $this->mockProducts($params);
        }

        $query = <<<'GQL'
        query GetProducts(
            $keyword: String
            $productCatId: Int
            $sortType: Int
            $page: Int
            $limit: Int
        ) {
            productOfferV2(
                listType: 0
                keyword: $keyword
                productCatId: $productCatId
                sortType: $sortType
                page: $page
                limit: $limit
            ) {
                nodes {
                    itemId
                    productName
                    imageUrl
                    priceMin
                    priceMax
                    priceDiscountRate
                    commissionRate
                    sellerCommissionRate
                    shopeeCommissionRate
                    commission
                    sales
                    ratingStar
                    shopId
                    shopName
                    offerLink
                    productLink
                }
                pageInfo {
                    page
                    limit
                    hasNextPage
                }
            }
        }
        GQL;

        $variables = array_filter([
            'keyword'       => $params['keyword'] ?? null,
            'productCatId'  => isset($params['productCatId']) ? (int) $params['productCatId'] : null,
            'sortType'      => isset($params['sortType']) ? (int) $params['sortType'] : 2,
            'page'          => (int) ($params['page'] ?? 1),
            'limit'         => (int) ($params['limit'] ?? 20),
        ], fn ($v) => $v !== null);

        $response = $this->request($cred, $query, $variables);

        if (isset($response['error'])) {
            return ['nodes' => [], 'pageInfo' => [], 'error' => $response['error']];
        }

        return [
            'nodes'    => $response['data']['productOfferV2']['nodes'] ?? [],
            'pageInfo' => $response['data']['productOfferV2']['pageInfo'] ?? [],
        ];
    }

    public function generateShortLink(UserCredential $cred, string $originUrl, array $subIds = []): string
    {
        if (env('SHOPEE_MOCK', false)) {
            return 'https://s.shopee.com.br/mock_' . substr(md5($originUrl), 0, 8);
        }

        $mutation = <<<'GQL'
        mutation GenerateShortLink($input: GenerateShortLinkInput!) {
            generateShortLink(input: $input) {
                shortLink
            }
        }
        GQL;

        $variables = [
            'input' => array_filter([
                'originUrl' => $originUrl,
                'subIds'    => $subIds ?: null,
            ]),
        ];

        $response = $this->request($cred, $mutation, $variables);

        if (isset($response['error'])) {
            throw new \RuntimeException($response['error']);
        }

        return $response['data']['generateShortLink']['shortLink'] ?? $originUrl;
    }

    public function getConversionReport(UserCredential $cred, array $params = []): array
    {
        if (env('SHOPEE_MOCK', false)) {
            return $this->mockConversionReport($params);
        }

        $query = <<<'GQL'
        query GetConversionReport(
            $purchaseTimeStart: Int
            $purchaseTimeEnd: Int
            $orderStatus: String
            $limit: Int
            $scrollId: String
        ) {
            conversionReport(
                purchaseTimeStart: $purchaseTimeStart
                purchaseTimeEnd: $purchaseTimeEnd
                orderStatus: $orderStatus
                limit: $limit
                scrollId: $scrollId
            ) {
                nodes {
                    purchaseTime
                    conversionId
                    totalCommission
                    netCommission
                    buyerType
                    utmContent
                    device
                    orders {
                        orderId
                        orderStatus
                        items {
                            shopName
                            itemName
                            itemPrice
                            actualAmount
                            qty
                            imageUrl
                            itemTotalCommission
                            displayItemStatus
                            fraudStatus
                        }
                    }
                }
                pageInfo {
                    limit
                    hasNextPage
                    scrollId
                }
            }
        }
        GQL;

        $variables = array_filter([
            'purchaseTimeStart' => $params['purchaseTimeStart'] ?? null,
            'purchaseTimeEnd'   => $params['purchaseTimeEnd'] ?? null,
            'orderStatus'       => $params['orderStatus'] ?? null,
            'limit'             => (int) ($params['limit'] ?? 50),
            'scrollId'          => $params['scrollId'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->request($cred, $query, $variables);

        if (isset($response['error'])) {
            return ['nodes' => [], 'pageInfo' => [], 'error' => $response['error']];
        }

        return [
            'nodes'    => $response['data']['conversionReport']['nodes'] ?? [],
            'pageInfo' => $response['data']['conversionReport']['pageInfo'] ?? [],
        ];
    }

    public function getCategories(): array
    {
        return [
            ['id' => 100001, 'name' => 'Celulares e Telecomunicações'],
            ['id' => 100006, 'name' => 'Computadores e Acessórios'],
            ['id' => 100013, 'name' => 'Eletrônicos'],
            ['id' => 100633, 'name' => 'Eletrodomésticos'],
            ['id' => 100018, 'name' => 'Moda Feminina'],
            ['id' => 100019, 'name' => 'Moda Masculina'],
            ['id' => 100040, 'name' => 'Moda Infantil e Bebês'],
            ['id' => 100016, 'name' => 'Casa e Decoração'],
            ['id' => 100034, 'name' => 'Beleza e Cuidado Pessoal'],
            ['id' => 100035, 'name' => 'Esporte e Lazer'],
            ['id' => 100039, 'name' => 'Brinquedos e Jogos'],
            ['id' => 100046, 'name' => 'Alimentos e Bebidas'],
            ['id' => 100012, 'name' => 'Livros e Papelaria'],
            ['id' => 100017, 'name' => 'Ferramentas e Indústria'],
            ['id' => 100032, 'name' => 'Pet Shop'],
            ['id' => 100033, 'name' => 'Saúde'],
            ['id' => 100015, 'name' => 'Automotivo'],
            ['id' => 100041, 'name' => 'Viagem e Bagagens'],
            ['id' => 100047, 'name' => 'Jogos e Colecionáveis'],
        ];
    }


    private function request(UserCredential $cred, string $query, array $variables = []): array
    {
        $appId   = $cred->app_id;
        $secret  = $cred->app_secret;
        $payload = json_encode(['query' => $query, 'variables' => $variables]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type'  => 'application/json',
                    'Authorization' => $this->buildAuthHeader($appId, $secret, $payload),
                ])
                ->withBody($payload, 'application/json')
                ->post($this->endpoint);

            $body = $response->json();

            // GraphQL errors
            if (!empty($body['errors'])) {
                $err  = $body['errors'][0];
                $code = $err['extensions']['code'] ?? 0;
                return ['error' => $this->mapErrorCode((int) $code, $err['message'] ?? 'Erro desconhecido')];
            }

            return $body;
        } catch (\Throwable $e) {
            Log::error('ShopeeAffiliateService request failed', ['message' => $e->getMessage()]);
            return ['error' => 'Falha na conexão com a API Shopee.'];
        }
    }

    private function buildAuthHeader(string $appId, string $secret, string $payload): string
    {
        $timestamp = time();
        $factor    = $appId . $timestamp . $payload . $secret;
        $signature = hash('sha256', $factor); // lowercase hex, SHA256 simples (não HMAC)

        return "SHA256 Credential={$appId}, Timestamp={$timestamp}, Signature={$signature}";
    }

    private function mockProducts(array $params): array
    {
        $keyword = $params['keyword'] ?? 'produto';
        $page    = (int) ($params['page'] ?? 1);
        $limit   = (int) ($params['limit'] ?? 20);

        $fakeNames = [
            'Tênis Feminino Plataforma Slip On',
            'Kit Skincare Vitamina C Sérum 30ml',
            'Suporte Celular Veicular Magnético',
            'Fone Bluetooth TWS Pro 5.0',
            'Câmera IP Wi-Fi Full HD 1080p',
            'Relógio Smartwatch HW67 Ultra',
            'Conjunto Moletom Feminino Inverno',
            'Mochila Escolar Impermeável USB',
            'Máscara LED Photon Facial Anti-idade',
            'Aspirador Robô Inteligente Wi-Fi',
            'Fritadeira Air Fryer Digital 5.5L',
            'Luminária LED Ring Light 18"',
            'Cadeira Gamer Ergonômica Reclinável',
            'Teclado Mecânico Gamer RGB',
            'Mouse Sem Fio Recarregável',
            'Capa Capinha iPhone 15 Pro Max',
            'Perfume Importado 100ml Masculino',
            'Protetor Solar FPS 70 Hidratante',
            'Organizador Gaveta Roupas Íntimas',
            'Colchão Casal Mola Ensacada',
        ];

        // Filter by keyword (simulate real API behavior, accent-insensitive)
        if ($keyword && $keyword !== 'produto') {
            $normalize = fn(string $s): string => mb_strtolower(
                transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $s) ?? $s
            );
            $needle = $normalize($keyword);
            $fakeNames = array_values(array_filter($fakeNames, fn($n) =>
                str_contains($normalize($n), $needle)
            ));
        }

        $nodes = [];
        foreach (array_slice($fakeNames, ($page - 1) * min($limit, 10), min($limit, 10)) as $i => $name) {
            $price      = rand(2990, 59900);
            $commission = rand(8, 25);
            $sales      = rand(150, 12000);
            $itemId     = 'MOCK_' . ($page * 100 + $i);

            $nodes[] = [
                'itemId'               => $itemId,
                'productName'          => $name,
                'imageUrl'             => "https://via.placeholder.com/200x200/EE4D2D/ffffff?text=" . urlencode(substr($name, 0, 10)),
                'priceMin'             => $price * 100000,
                'priceMax'             => (int) ($price * 1.3 * 100000),
                'priceDiscountRate'    => rand(0, 40),
                'commissionRate'       => $commission,
                'sellerCommissionRate' => (int) ($commission * 0.6),
                'shopeeCommissionRate' => (int) ($commission * 0.4),
                'commission'           => round($price / 100 * $commission / 100, 2),
                'sales'                => $sales,
                'ratingStar'           => round(rand(38, 50) / 10, 1),
                'shopId'               => rand(10000, 99999),
                'shopName'             => 'Loja Teste ' . rand(1, 99),
                'offerLink'            => 'https://shopee.com.br/produto/' . $itemId,
                'productLink'          => 'https://shopee.com.br/produto/' . $itemId,
            ];
        }

        $total = count($fakeNames);

        return [
            'nodes'    => $nodes,
            'pageInfo' => [
                'page'        => $page,
                'limit'       => $limit,
                'hasNextPage' => ($page * min($limit, 10)) < $total,
            ],
        ];
    }

    private function mockConversionReport(array $params): array
    {
        $statuses  = ['COMPLETED', 'COMPLETED', 'COMPLETED', 'PENDING', 'CANCELLED'];
        $itemNames = [
            'Fone Bluetooth TWS Pro 5.0', 'Tênis Feminino Plataforma', 'Kit Skincare Vitamina C',
            'Relógio Smartwatch Ultra', 'Fritadeira Air Fryer 5.5L', 'Câmera IP Wi-Fi Full HD',
            'Cadeira Gamer Ergonômica', 'Mochila Escolar Impermeável', 'Protetor Solar FPS 70',
            'Suporte Celular Magnético',
        ];

        $nodes = [];
        for ($i = 0; $i < 10; $i++) {
            $amount     = round(rand(3000, 30000) / 100, 2);
            $commission = round($amount * (rand(8, 18) / 100), 2);
            $status     = $statuses[array_rand($statuses)];

            $nodes[] = [
                'purchaseTime'    => time() - rand(0, 30 * 86400),
                'conversionId'    => 900000000 + $i,
                'totalCommission' => number_format($commission, 2, '.', ''),
                'netCommission'   => number_format($commission * 0.9, 2, '.', ''),
                'buyerType'       => rand(0, 1) ? 'NEW' : 'EXISTING',
                'utmContent'      => '',
                'device'          => rand(0, 1) ? 'APP' : 'WEB',
                'orders'          => [[
                    'orderId'     => 'ORD' . rand(100000, 999999),
                    'orderStatus' => $status,
                    'items'       => [[
                        'shopName'           => 'Loja Teste ' . rand(1, 50),
                        'itemName'           => $itemNames[array_rand($itemNames)],
                        'itemPrice'          => number_format($amount, 2, '.', ''),
                        'actualAmount'       => number_format($amount, 2, '.', ''),
                        'qty'                => rand(1, 3),
                        'imageUrl'           => '',
                        'itemTotalCommission'=> number_format($commission, 2, '.', ''),
                        'displayItemStatus'  => $status,
                        'fraudStatus'        => 'UNVERIFIED',
                    ]],
                ]],
            ];
        }

        return [
            'nodes'    => $nodes,
            'pageInfo' => ['limit' => 50, 'hasNextPage' => false, 'scrollId' => ''],
        ];
    }

    private function mapErrorCode(int $code, string $fallback): string
    {
        return match ($code) {
            10020 => 'Credenciais inválidas. Verifique seu App ID e Senha.',
            10030 => 'Limite de requisições atingido. Aguarde alguns segundos.',
            10032 => 'Affiliate ID inválido.',
            10033 => 'Sua conta está congelada. Entre em contato com a Shopee.',
            10034 => 'Sua conta está na lista negra.',
            10035 => 'Você não tem acesso à API de Afiliados. Solicite acesso em affiliate.shopee.com.br.',
            default => $fallback ?: 'Erro ao conectar. Tente novamente.',
        };
    }
}
