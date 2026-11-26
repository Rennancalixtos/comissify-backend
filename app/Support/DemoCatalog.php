<?php

namespace App\Support;

/**
 * Catálogo fictício usado no modo demonstração.
 * Os campos seguem o formato do productOfferV2 da API de afiliados da Shopee.
 */
class DemoCatalog
{
    /** [itemId, nome, categoria, preço, desconto %, comissão %, vendas, nota, loja, emoji, cor] */
    private const PRODUCTS = [
        [9100001, 'Fone de Ouvido Bluetooth TWS com Cancelamento de Ruído', 100535, 89.90, 35, 12, 18420, 4.8, 'SomTech Oficial', '🎧', '#6366f1'],
        [9100002, 'Smartwatch Esportivo à Prova d\'Água com Monitor Cardíaco', 100534, 149.90, 40, 10, 9310, 4.7, 'Relógios Prime', '⌚', '#0ea5e9'],
        [9100003, 'Garrafa Térmica Inox 1L Mantém Gelado 24h', 100636, 54.90, 20, 15, 25110, 4.9, 'Casa Bonita', '🧊', '#14b8a6'],
        [9100004, 'Kit Skincare Vitamina C Sérum + Hidratante', 100630, 79.90, 30, 18, 12840, 4.8, 'Beleza Pura', '✨', '#f472b6'],
        [9100005, 'Tênis Casual Masculino Confortável Leve', 100012, 129.90, 25, 9, 7650, 4.6, 'Passo Certo', '👟', '#f97316'],
        [9100006, 'Bolsa Feminina Transversal Couro Sintético', 100016, 69.90, 45, 14, 15230, 4.7, 'Moda Dela', '👜', '#a855f7'],
        [9100007, 'Carregador Turbo 33W USB-C com Cabo', 100013, 39.90, 50, 8, 31200, 4.8, 'Mega Cell', '🔌', '#22c55e'],
        [9100008, 'Luminária de Mesa LED Recarregável Touch', 100636, 59.90, 30, 13, 8920, 4.7, 'Casa Bonita', '💡', '#eab308'],
        [9100009, 'Mochila Notebook Antifurto com USB', 100533, 119.90, 35, 11, 6410, 4.8, 'Urban Bags', '🎒', '#64748b'],
        [9100010, 'Mouse Gamer RGB 7200 DPI', 100644, 49.90, 40, 10, 14780, 4.7, 'GameZone', '🖱️', '#ef4444'],
        [9100011, 'Teclado Mecânico Compacto Switch Blue', 100644, 179.90, 30, 9, 5320, 4.8, 'GameZone', '⌨️', '#8b5cf6'],
        [9100012, 'Câmera de Segurança Wi-Fi 360° Visão Noturna', 100635, 139.90, 45, 12, 11040, 4.6, 'Smart Home BR', '📷', '#0f766e'],
        [9100013, 'Air Fryer 4L Digital Antiaderente', 100010, 329.90, 28, 7, 4210, 4.9, 'EletroLar', '🍟', '#dc2626'],
        [9100014, 'Vestido Midi Floral Manga Bufante', 100017, 89.90, 40, 16, 9870, 4.6, 'Moda Dela', '👗', '#ec4899'],
        [9100015, 'Camiseta Masculina Básica Algodão Kit 3', 100011, 79.90, 33, 15, 20150, 4.7, 'Básicos Co.', '👕', '#3b82f6'],
        [9100016, 'Brinquedo Educativo Montessori Madeira', 100632, 64.90, 20, 17, 6730, 4.9, 'Pequeno Gênio', '🧸', '#f59e0b'],
        [9100017, 'Cama Pet Redonda Pelúcia Antiestresse', 100631, 74.90, 35, 14, 10330, 4.8, 'Pet Feliz', '🐶', '#a16207'],
        [9100018, 'Tapete de Yoga Antiderrapante 6mm', 100637, 59.90, 30, 13, 7480, 4.7, 'Vida Ativa', '🧘', '#10b981'],
        [9100019, 'Caixa de Som Bluetooth Portátil 20W', 100535, 119.90, 38, 11, 13560, 4.7, 'SomTech Oficial', '🔊', '#4f46e5'],
        [9100020, 'Kit Organizador de Gavetas 6 Peças', 100636, 34.90, 25, 19, 17620, 4.8, 'Casa Bonita', '🗂️', '#06b6d4'],
        [9100021, 'Controle Sem Fio para PC e Celular', 100634, 99.90, 30, 10, 8810, 4.6, 'GameZone', '🎮', '#7c3aed'],
        [9100022, 'Óculos de Sol Polarizado UV400', 100009, 44.90, 50, 20, 16240, 4.6, 'Visual Style', '🕶️', '#334155'],
        [9100023, 'Suporte Veicular Magnético para Celular', 100640, 29.90, 40, 16, 22890, 4.7, 'Auto Fácil', '🚗', '#475569'],
        [9100024, 'Planner 2027 Capa Dura Personalizado', 100638, 49.90, 15, 18, 5940, 4.9, 'Papel & Arte', '📒', '#e11d48'],
    ];

    public static function all(): array
    {
        return array_map(fn ($p) => self::toNode($p), self::PRODUCTS);
    }

    public static function find(string $itemId): ?array
    {
        foreach (self::all() as $node) {
            if ((string) $node['itemId'] === (string) $itemId) {
                return $node;
            }
        }
        return null;
    }

    public static function search(?string $keyword, ?int $categoryId, int $sortType): array
    {
        $items = collect(self::all())->map(function ($n) {
            $n['_cat'] = self::categoryOf($n['itemId']);
            return $n;
        });

        if ($keyword) {
            $words = preg_split('/\s+/', mb_strtolower(trim($keyword)));
            $items = $items->filter(function ($n) use ($words) {
                $haystack = mb_strtolower($n['productName'] . ' ' . $n['shopName']);
                foreach ($words as $w) {
                    if ($w !== '' && str_contains($haystack, $w)) return true;
                }
                return false;
            });
        }

        if ($categoryId) {
            $items = $items->where('_cat', $categoryId);
        }

        // sortType da API: 2 = mais vendidos, 4 = preço maior, 5 = maior comissão
        $items = match ($sortType) {
            4       => $items->sortByDesc('priceMin'),
            5       => $items->sortByDesc('commission'),
            default => $items->sortByDesc('sales'),
        };

        return $items->map(fn ($n) => collect($n)->except('_cat')->all())->values()->all();
    }

    public static function svg(int $itemId): ?string
    {
        foreach (self::PRODUCTS as $p) {
            if ($p[0] === $itemId) {
                [, , , , , , , , , $emoji, $color] = $p;
                return <<<SVG
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300">
                  <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0" stop-color="{$color}" stop-opacity=".95"/>
                    <stop offset="1" stop-color="{$color}" stop-opacity=".55"/>
                  </linearGradient></defs>
                  <rect width="300" height="300" fill="url(#g)"/>
                  <circle cx="150" cy="150" r="92" fill="#fff" fill-opacity=".16"/>
                  <text x="150" y="150" font-size="110" text-anchor="middle" dominant-baseline="central">{$emoji}</text>
                </svg>
                SVG;
            }
        }
        return null;
    }

    private static function categoryOf(int|string $itemId): ?int
    {
        foreach (self::PRODUCTS as $p) {
            if ((string) $p[0] === (string) $itemId) return $p[2];
        }
        return null;
    }

    private static function toNode(array $p): array
    {
        [$id, $name, , $price, $discount, $rate, $sales, $rating, $shop] = $p;
        $commission = round($price * $rate / 100, 2);
        $slug       = \Illuminate\Support\Str::slug($name);

        return [
            'itemId'               => $id,
            'productName'          => $name,
            'imageUrl'             => url("/demo/product-image/{$id}"),
            'priceMin'             => $price,
            'priceMax'             => $price,
            'priceDiscountRate'    => $discount,
            'commissionRate'       => $rate,
            'sellerCommissionRate' => round($rate * 0.6, 1),
            'shopeeCommissionRate' => round($rate * 0.4, 1),
            'commission'           => $commission,
            'sales'                => $sales,
            'ratingStar'           => $rating,
            'shopId'               => 70000 + ($id % 1000),
            'shopName'             => $shop,
            'offerLink'            => "https://shopee.com.br/{$slug}-i.70000.{$id}",
            'productLink'          => "https://shopee.com.br/{$slug}-i.70000.{$id}",
        ];
    }
}
