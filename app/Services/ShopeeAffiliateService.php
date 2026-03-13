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
        $this->endpoint = config('services.shopee.api_base_url', 'https://open-api.affiliate.shopee.com.br/graphql');
    }


    public function getProducts(UserCredential $cred, array $params = []): array
    {
        $keyword    = $params['keyword'] ?? null;
        $categoryId = isset($params['productCatId']) ? (int) $params['productCatId'] : null;

        $nodeFields = <<<'FIELDS'
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
        FIELDS;

        if ($keyword) {
            // Keyword com ou sem categoria
            $query = <<<GQL
            query GetProducts(\$keyword: String, \$productCatId: Int, \$sortType: Int, \$page: Int, \$limit: Int) {
                productOfferV2(keyword: \$keyword, productCatId: \$productCatId, sortType: \$sortType, page: \$page, limit: \$limit) {
                    $nodeFields
                }
            }
            GQL;
            $variables = array_filter([
                'keyword'      => $keyword,
                'productCatId' => $categoryId,
                'sortType'     => (int) ($params['sortType'] ?? 2),
                'page'         => (int) ($params['page'] ?? 1),
                'limit'        => (int) ($params['limit'] ?? 20),
            ], fn ($v) => $v !== null);
        } elseif ($categoryId) {
            // Categoria sem keyword: listType: 4 + matchId
            $query = <<<GQL
            query GetProducts(\$matchId: Int64, \$sortType: Int, \$page: Int, \$limit: Int) {
                productOfferV2(listType: 4, matchId: \$matchId, sortType: \$sortType, page: \$page, limit: \$limit) {
                    $nodeFields
                }
            }
            GQL;
            $variables = array_filter([
                'matchId'  => (string) $categoryId, // Int64 scalar expects string in JSON
                'sortType' => (int) ($params['sortType'] ?? 2),
                'page'     => (int) ($params['page'] ?? 1),
                'limit'    => (int) ($params['limit'] ?? 20),
            ], fn ($v) => $v !== null);
        } else {
            // Sem filtros: produtos recomendados
            $query = <<<GQL
            query GetProducts(\$sortType: Int, \$page: Int, \$limit: Int) {
                productOfferV2(listType: 0, sortType: \$sortType, page: \$page, limit: \$limit) {
                    $nodeFields
                }
            }
            GQL;
            $variables = array_filter([
                'sortType' => (int) ($params['sortType'] ?? 2),
                'page'     => (int) ($params['page'] ?? 1),
                'limit'    => (int) ($params['limit'] ?? 20),
            ], fn ($v) => $v !== null);
        }

        $response = $this->request($cred, $query, $variables);

        if (isset($response['error'])) {
            return ['nodes' => [], 'pageInfo' => [], 'error' => $response['error']];
        }

        $nodes = $response['data']['productOfferV2']['nodes'] ?? [];

        return [
            'nodes'    => $nodes,
            'pageInfo' => $response['data']['productOfferV2']['pageInfo'] ?? [],
        ];
    }

    public function generateShortLink(UserCredential $cred, string $originUrl, array $subIds = []): string
    {
        $mutation = <<<'GQL'
        mutation GenerateShortLink($originUrl: String!, $subIds: [String!]) {
            generateShortLink(input: {
                originUrl: $originUrl
                subIds: $subIds
            }) {
                shortLink
            }
        }
        GQL;

        $variables = array_filter([
            'originUrl' => $originUrl,
            'subIds'    => $subIds ?: null,
        ]);

        $response = $this->request($cred, $mutation, $variables);

        if (isset($response['error'])) {
            throw new \RuntimeException($response['error']);
        }

        return $response['data']['generateShortLink']['shortLink'] ?? $originUrl;
    }

    public function getConversionReport(UserCredential $cred, array $params = []): array
    {
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
            ['id' => 100017, 'name' => 'Roupas Femininas'],
            ['id' => 100011, 'name' => 'Roupas Masculinas'],
            ['id' => 100630, 'name' => 'Beleza'],
            ['id' => 100001, 'name' => 'Saúde'],
            ['id' => 100009, 'name' => 'Acessórios de Moda'],
            ['id' => 100010, 'name' => 'Eletrodomésticos'],
            ['id' => 100012, 'name' => 'Sapatos Masculinos'],
            ['id' => 100013, 'name' => 'Celulares e Dispositivos'],
            ['id' => 100015, 'name' => 'Viagens e Bagagens'],
            ['id' => 100016, 'name' => 'Bolsas Femininas'],
            ['id' => 100532, 'name' => 'Sapatos Femininos'],
            ['id' => 100533, 'name' => 'Bolsas Masculinas'],
            ['id' => 100534, 'name' => 'Relógios'],
            ['id' => 100535, 'name' => 'Áudio'],
            ['id' => 100631, 'name' => 'Animais Domésticos'],
            ['id' => 100632, 'name' => 'Mãe e Bebê'],
            ['id' => 100633, 'name' => 'Moda Infantil'],
            ['id' => 100634, 'name' => 'Jogos e Consoles'],
            ['id' => 100635, 'name' => 'Câmeras e Drones'],
            ['id' => 100636, 'name' => 'Casa e Decoração'],
            ['id' => 100637, 'name' => 'Esportes e Atividades ao Ar Livre'],
            ['id' => 100638, 'name' => 'Papelaria'],
            ['id' => 100639, 'name' => 'Hobbies e Coleções'],
            ['id' => 100640, 'name' => 'Automóveis'],
            ['id' => 100641, 'name' => 'Motocicletas'],
            ['id' => 100643, 'name' => 'Livros e Revistas'],
            ['id' => 100644, 'name' => 'Computadores e Acessórios'],
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
