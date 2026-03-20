<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    private static array $articles = [
        'como-gerar-link-afiliado-shopee' => [
            'slug'         => 'como-gerar-link-afiliado-shopee',
            'title'        => 'Como gerar link de afiliado Shopee automaticamente',
            'description'  => 'Aprenda a gerar links de afiliado Shopee com 1 clique usando o Comissify — sem copiar produto por produto nem ficar no app da Shopee.',
            'keywords'     => 'gerar link afiliado shopee, link afiliado shopee automatico, como criar link afiliado shopee, shopee afiliados link, encurtar link afiliado shopee',
            'published_at' => '2025-03-10',
            'reading_time' => 5,
        ],
        'como-encontrar-produtos-maior-comissao-shopee' => [
            'slug'         => 'como-encontrar-produtos-maior-comissao-shopee',
            'title'        => 'Como encontrar produtos com maior comissão na Shopee',
            'description'  => 'Descubra como identificar os produtos Shopee que pagam mais comissão e como usar o Comissify para filtrá-los automaticamente.',
            'keywords'     => 'produtos maior comissao shopee, melhor produto divulgar shopee, shopee comissao alta, como aumentar comissao shopee afiliado, produtos shopee afiliados',
            'published_at' => '2025-03-17',
            'reading_time' => 6,
        ],
    ];

    public function index()
    {
        $articles = array_values(self::$articles);

        usort($articles, fn ($a, $b) => strcmp($b['published_at'], $a['published_at']));

        return view('blog.index', compact('articles'));
    }

    public function show(string $slug)
    {
        abort_unless(isset(self::$articles[$slug]), 404);

        $article = self::$articles[$slug];

        $otherSlugs = array_filter(
            array_keys(self::$articles),
            fn ($s) => $s !== $slug
        );
        $related = array_map(fn ($s) => self::$articles[$s], $otherSlugs);

        return view("blog.articles.{$slug}", compact('article', 'related'));
    }
}
