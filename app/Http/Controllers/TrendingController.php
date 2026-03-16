<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class TrendingController extends Controller
{
    public function trending()
    {
        $products = collect(Cache::get('trending_products', []))
            ->sortByDesc(fn ($p) => (int) ($p['sales'] ?? 0))  // mais vendidos = mais vendas
            ->values()
            ->all();

        $updatedAt = Cache::get('trending_products_updated_at');

        return view('trending', [
            'products'   => $products,
            'updatedAt'  => $updatedAt,
            'pageTitle'  => 'Produtos Mais Vendidos Shopee Hoje',
            'metaTitle'  => 'Produtos Mais Vendidos na Shopee Hoje | Comissify',
            'metaDesc'   => 'Veja os produtos mais vendidos na Shopee agora. Lista atualizada automaticamente — ideal para afiliados que querem divulgar o que já está bombando.',
            'metaKeywords' => 'produtos mais vendidos shopee, o que está bombando shopee, shopee mais vendidos hoje, afiliado shopee tendencia',
            'heading'    => 'Produtos mais vendidos<br><span style="color:var(--color-primary)">na Shopee agora</span>',
            'subheading' => 'O que os brasileiros estão comprando agora na Shopee — atualizado a cada 6 horas.',
            'badge'      => '🔥 Mais vendidos',
            'routeName'  => 'trending',
        ]);
    }

    public function commissions()
    {
        $products = collect(Cache::get('commission_products', []))
            ->sortByDesc(fn ($p) => (float) ($p['commission'] ?? 0))
            ->take(20)
            ->values()
            ->all();

        $updatedAt = Cache::get('trending_products_updated_at');

        return view('trending', [
            'products'   => $products,
            'updatedAt'  => $updatedAt,
            'pageTitle'  => 'Produtos com Maior Comissão Shopee para Afiliados',
            'metaTitle'  => 'Maior Comissão Shopee para Afiliados | Comissify',
            'metaDesc'   => 'Os produtos Shopee com maior percentual de comissão para afiliados. Divulgue o que paga mais e maximize seus ganhos.',
            'metaKeywords' => 'produtos maior comissão shopee, melhor produto divulgar shopee afiliado, shopee afiliado maior ganho, comissão shopee afiliados',
            'heading'    => 'Maior comissão para<br><span style="color:var(--color-primary)">afiliados Shopee</span>',
            'subheading' => 'Produtos com maior percentual de comissão disponíveis agora — calcule quanto você pode ganhar por venda.',
            'badge'      => '💰 Maior comissão',
            'routeName'  => 'commissions',
        ]);
    }
}
