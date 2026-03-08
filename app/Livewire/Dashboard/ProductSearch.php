<?php

namespace App\Livewire\Dashboard;

use App\Models\SystemPrice;
use App\Models\UserAffiliateLink;
use App\Services\ShopeeAffiliateService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductSearch extends Component
{
    public string  $keyword     = '';
    public ?int    $categoryId  = null;
    public int     $sortType    = 2;  // default: mais vendidos
    public int     $page        = 1;

    public array   $products    = [];
    public array   $pageInfo    = [];
    public array   $selected    = [];
    public bool    $selectAll   = false;

    public bool    $loading     = false;
    public ?string $error       = null;
    public ?string $flashMsg    = null;

    // Modal de link gerado (single)
    public bool    $showLinkModal   = false;
    public ?string $generatedLink   = null;
    public ?string $linkProductName = null;

    // Modal de links em bulk
    public bool    $showBulkModal = false;
    public array   $bulkLinks     = [];

    // Modal de detalhes do produto
    public bool    $showDetailModal = false;
    public array   $detailProduct   = [];

    public function getSearchCostProperty(): float
    {
        return SystemPrice::get('search_cost');
    }

    public function getLinkCostProperty(): float
    {
        return SystemPrice::get('link_generation_cost');
    }

    public function search(): void
    {
        $user = Auth::user();
        $cost = $this->searchCost;

        if ($user->tokenBalance() < $cost) {
            $this->error = "Saldo insuficiente. Você precisa de R\$ " . number_format($cost, 4, ',', '.') . " em tokens.";
            return;
        }

        $this->loading  = true;
        $this->error    = null;
        $this->products = [];
        $this->selected = [];
        $this->page     = 1;

        $wallet = $user->ensureTokenWallet();
        $cred   = $user->credential;

        /** @var ShopeeAffiliateService $service */
        $service = app(ShopeeAffiliateService::class);

        $result = $service->getProducts($cred, [
            'keyword'      => $this->keyword ?: null,
            'productCatId' => $this->categoryId,
            'sortType'     => $this->sortType,
            'page'         => $this->page,
            'limit'        => 20,
        ]);

        if (isset($result['error'])) {
            $this->error = $result['error'];
        } else {
            // Debitar somente após sucesso
            $wallet->debit($cost, 'Busca de produtos', 'search:' . ($this->keyword ?: 'categoria'));
            $this->products = $result['nodes'];
            $this->pageInfo = $result['pageInfo'];
        }

        $this->loading = false;
    }

    public function loadPage(int $page): void
    {
        $this->page = $page;
        $this->doSearch(debit: false);
    }

    public function sortBy(int $sortType): void
    {
        $this->sortType = $sortType;

        if (empty($this->products)) return;

        // Sort locally — no new API call needed
        $products = $this->products;

        usort($products, match ($sortType) {
            2 => fn ($a, $b) => ($b['soldCount']      ?? 0) <=> ($a['soldCount']      ?? 0),
            5 => fn ($a, $b) => ($b['commissionRate']  ?? 0) <=> ($a['commissionRate']  ?? 0),
            4 => fn ($a, $b) => ($a['priceMin']        ?? 0) <=> ($b['priceMin']        ?? 0),
            3 => fn ($a, $b) => ($b['priceMax']        ?? 0) <=> ($a['priceMax']        ?? 0),
            default => fn () => 0,
        });

        $this->products = $products;
    }

    private function doSearch(bool $debit = true): void
    {
        $user  = Auth::user();
        $cost  = $this->searchCost;
        $cred  = $user->credential;

        /** @var ShopeeAffiliateService $service */
        $service = app(ShopeeAffiliateService::class);

        $result = $service->getProducts($cred, [
            'keyword'      => $this->keyword ?: null,
            'productCatId' => $this->categoryId,
            'sortType'     => $this->sortType,
            'page'         => $this->page,
            'limit'        => 20,
        ]);

        if (isset($result['error'])) {
            $this->error = $result['error'];
            return;
        }

        if ($debit) {
            $user->ensureTokenWallet()->debit($cost, 'Busca de produtos', 'search:page');
        }

        $this->products = $result['nodes'];
        $this->pageInfo = $result['pageInfo'];
        $this->selected = [];
    }

    public function generateLink(string $productLink, string $productName): void
    {
        $user = Auth::user();
        $cost = $this->linkCost;

        if ($user->tokenBalance() < $cost) {
            $this->error = "Saldo insuficiente para gerar link.";
            return;
        }

        /** @var ShopeeAffiliateService $service */
        $service = app(ShopeeAffiliateService::class);

        $product = collect($this->products)->firstWhere('productLink', $productLink)
                ?? collect($this->products)->firstWhere('offerLink', $productLink);

        try {
            $shortLink = $service->generateShortLink($user->credential, $productLink);
            $user->ensureTokenWallet()->debit($cost, 'Geração de link afiliado', 'link:' . substr($productLink, -20));

            // Persist the affiliate link for the Posts page
            UserAffiliateLink::updateOrCreate(
                ['user_id' => $user->id, 'item_id' => (string) ($product['itemId'] ?? md5($productLink))],
                [
                    'product_name'    => $productName,
                    'image_url'       => $product['imageUrl'] ?? null,
                    'product_link'    => $productLink,
                    'affiliate_link'  => $shortLink,
                    'price_min'       => $product['priceMin'] ?? null,
                    'commission_rate' => $product['commissionRate'] ?? null,
                    'shop_name'       => $product['shopName'] ?? null,
                ]
            );

            $this->generatedLink   = $shortLink;
            $this->linkProductName = $productName;
            $this->showLinkModal   = true;
        } catch (\Throwable $e) {
            $this->error = 'Erro ao gerar link: ' . $e->getMessage();
        }
    }

    public function generateBulkLinks(): void
    {
        if (empty($this->selected)) return;

        $user  = Auth::user();
        $cost  = $this->linkCost * count($this->selected);
        $wallet = $user->ensureTokenWallet();

        if ($user->tokenBalance() < $cost) {
            $this->error = "Saldo insuficiente. Necessário R\$ " . number_format($cost, 4, ',', '.');
            return;
        }

        /** @var ShopeeAffiliateService $service */
        $service = app(ShopeeAffiliateService::class);
        $results = [];

        foreach ($this->selected as $itemId) {
            $product = collect($this->products)->firstWhere('itemId', $itemId);
            if (!$product) continue;

            try {
                $originUrl = $product['offerLink'] ?? $product['productLink'];
                $shortLink = $service->generateShortLink($user->credential, $originUrl);
                $wallet->debit($this->linkCost, 'Geração de link afiliado (bulk)', 'link:' . $itemId);

                UserAffiliateLink::updateOrCreate(
                    ['user_id' => $user->id, 'item_id' => (string) $itemId],
                    [
                        'product_name'    => $product['productName'],
                        'image_url'       => $product['imageUrl'] ?? null,
                        'product_link'    => $originUrl,
                        'affiliate_link'  => $shortLink,
                        'price_min'       => $product['priceMin'] ?? null,
                        'commission_rate' => $product['commissionRate'] ?? null,
                        'shop_name'       => $product['shopName'] ?? null,
                    ]
                );

                $results[] = ['name' => $product['productName'], 'link' => $shortLink, 'ok' => true];
            } catch (\Throwable $e) {
                $results[] = ['name' => $product['productName'], 'link' => null, 'ok' => false, 'error' => $e->getMessage()];
            }
        }

        $this->bulkLinks      = $results;
        $this->showBulkModal  = true;
        $this->selected       = [];
    }

    public function showDetail(string $itemId): void
    {
        $product = collect($this->products)->firstWhere('itemId', $itemId);
        if ($product) {
            $this->detailProduct   = $product;
            $this->showDetailModal = true;
        }
    }

    public function toggleSelect(string $itemId): void
    {
        if (in_array($itemId, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$itemId]));
        } else {
            $this->selected[] = $itemId;
        }
        $this->selectAll = count($this->selected) === count($this->products);
    }

    public function toggleSelectAll(): void
    {
        $this->selectAll = !$this->selectAll;
        $this->selected  = $this->selectAll
            ? array_column($this->products, 'itemId')
            : [];
    }

    public function getCategories(): array
    {
        return app(ShopeeAffiliateService::class)->getCategories();
    }

    public function render()
    {
        $user = Auth::user();

        // Show blocked view if Shopee not connected
        if (!$user->isShopeeConnected()) {
            return view('livewire.dashboard.locked')
                ->layout('layouts.app', ['title' => 'Dashboard']);
        }

        return view('livewire.dashboard.product-search', [
            'categories'  => $this->getCategories(),
            'searchCost'  => $this->searchCost,
            'linkCost'    => $this->linkCost,
            'balance'     => $user->tokenBalance(),
        ])->layout('layouts.app', ['title' => 'Busca de Produtos']);
    }
}
