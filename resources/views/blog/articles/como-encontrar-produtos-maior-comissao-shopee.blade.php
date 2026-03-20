<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $article['title'] }} | Comissify</title>
    <meta name="description" content="{{ $article['description'] }}">
    <meta name="keywords" content="{{ $article['keywords'] }}">
    <link rel="canonical" href="{{ route('blog.show', $article['slug']) }}">

    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('blog.show', $article['slug']) }}">
    <meta property="og:title" content="{{ $article['title'] }}">
    <meta property="og:description" content="{{ $article['description'] }}">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-comissify.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta property="article:published_time" content="{{ $article['published_at'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/og-comissify.png">

    {{-- Article Schema --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "{{ $article['title'] }}",
        "description": "{{ $article['description'] }}",
        "url": "{{ route('blog.show', $article['slug']) }}",
        "datePublished": "{{ $article['published_at'] }}",
        "dateModified": "{{ $article['published_at'] }}",
        "inLanguage": "pt-BR",
        "publisher": {
            "@type": "Organization",
            "name": "Comissify",
            "url": "{{ url('/') }}"
        },
        "author": {
            "@type": "Organization",
            "name": "Comissify"
        },
        "image": "{{ config('app.url') }}/images/og-comissify.png"
    }
    </script>

    {{-- BreadcrumbList --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Início", "item": "{{ url('/') }}" },
            { "@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ route('blog.index') }}" },
            { "@type": "ListItem", "position": 3, "name": "{{ $article['title'] }}", "item": "{{ route('blog.show', $article['slug']) }}" }
        ]
    }
    </script>

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        {!! \App\Services\ThemeService::cssVars() !!}
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .prose h2 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.375rem; font-weight: 700; color: #F3F4F6; margin-top: 2.5rem; margin-bottom: 1rem; line-height: 1.3; }
        .prose h3 { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.1rem; font-weight: 600; color: #E5E7EB; margin-top: 2rem; margin-bottom: 0.75rem; }
        .prose p { color: #9CA3AF; line-height: 1.8; margin-bottom: 1.25rem; font-size: 0.9375rem; }
        .prose ul { list-style: none; padding: 0; margin-bottom: 1.25rem; }
        .prose ul li { color: #9CA3AF; font-size: 0.9375rem; line-height: 1.8; padding-left: 1.5rem; position: relative; margin-bottom: 0.5rem; }
        .prose ul li::before { content: ''; position: absolute; left: 0; top: 0.65rem; width: 0.375rem; height: 0.375rem; border-radius: 50%; background: var(--color-primary); }
        .prose ol { counter-reset: item; padding: 0; margin-bottom: 1.25rem; }
        .prose ol li { color: #9CA3AF; font-size: 0.9375rem; line-height: 1.8; padding-left: 2rem; position: relative; margin-bottom: 0.5rem; counter-increment: item; }
        .prose ol li::before { content: counter(item); position: absolute; left: 0; top: 0.1rem; width: 1.4rem; height: 1.4rem; border-radius: 50%; background: color-mix(in srgb, var(--color-primary) 15%, transparent); color: var(--color-primary); font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; justify-content: center; font-family: 'Plus Jakarta Sans', sans-serif; }
        .prose strong { color: #F3F4F6; font-weight: 600; }
        .prose a { color: var(--color-primary); text-decoration: underline; text-underline-offset: 3px; }
        .tip-box { background: color-mix(in srgb, var(--color-primary) 8%, transparent); border: 1px solid color-mix(in srgb, var(--color-primary) 25%, transparent); border-radius: 0.75rem; padding: 1rem 1.25rem; margin: 1.5rem 0; }
        .tip-box p { margin-bottom: 0; }
    </style>
</head>
<body class="bg-d-bg text-d-text antialiased">

{{-- Navbar --}}
<nav class="border-b border-d-border sticky top-0 z-50 backdrop-blur-md" style="background:rgba(20,20,20,0.85)">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-sm font-display">C</span>
            </div>
            <span class="font-bold text-d-text font-display tracking-tight">Comissify</span>
        </a>
        <div class="hidden sm:flex items-center gap-4 text-sm font-display font-semibold text-d-muted">
            <a href="{{ route('trending') }}" class="hover:text-d-text transition-colors">🔥 Mais vendidos</a>
            <a href="{{ route('commissions') }}" class="hover:text-d-text transition-colors">💰 Maior comissão</a>
            <a href="{{ route('blog.index') }}" class="transition-colors" style="color:var(--color-primary)">Blog</a>
            <a href="{{ route('faq') }}" class="hover:text-d-text transition-colors font-normal">FAQ</a>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="btn-secondary !py-1.5 !px-4 text-sm">Entrar</a>
            <a href="{{ route('register') }}" class="btn-primary !py-1.5 !px-4 text-sm">Começar grátis</a>
        </div>
    </div>
</nav>

{{-- Article header --}}
<section class="py-16 border-b border-d-border" style="background:#0f0f0f">
    <div class="max-w-2xl mx-auto px-6">
        <div class="flex items-center gap-2 mb-5 text-xs text-d-muted font-display">
            <a href="{{ route('blog.index') }}" class="hover:text-d-text transition-colors">Blog</a>
            <span>/</span>
            <span>Estratégias</span>
        </div>
        <h1 class="font-display text-3xl sm:text-4xl font-extrabold text-d-text mb-5 leading-tight">
            {{ $article['title'] }}
        </h1>
        <p class="text-d-muted text-lg leading-relaxed mb-6">{{ $article['description'] }}</p>
        <div class="flex items-center gap-4 text-xs text-d-muted font-display">
            <span>{{ \Carbon\Carbon::parse($article['published_at'])->format('d \d\e F \d\e Y') }}</span>
            <span>·</span>
            <span>{{ $article['reading_time'] }} min de leitura</span>
        </div>
    </div>
</section>

{{-- Article content --}}
<article class="py-16">
    <div class="max-w-2xl mx-auto px-6 prose">

        <h2>Como funciona a comissão na Shopee?</h2>
        <p>
            No Programa de Afiliados da Shopee, cada produto tem uma <strong>taxa de comissão individual</strong>
            definida pelo vendedor. Isso significa que dois produtos no mesmo nicho podem pagar comissões
            completamente diferentes — um pode pagar 3% e outro, 15%.
        </p>
        <p>
            A comissão é calculada sobre o valor da venda. Num produto de R$&nbsp;100,00 com 10% de comissão,
            você recebe R$&nbsp;10,00 por cada venda gerada pelo seu link. Parece pouco? Multiplique por 50 vendas
            por mês.
        </p>

        <h2>Por que a maioria dos afiliados ignora isso</h2>
        <p>
            O problema é que a plataforma de afiliados da Shopee não mostra a comissão de cada produto de forma
            clara e comparável. Para descobrir qual produto paga mais, você teria que:
        </p>
        <ol>
            <li>Abrir produto por produto na plataforma</li>
            <li>Anotar a taxa de cada um</li>
            <li>Comparar manualmente</li>
        </ol>
        <p>
            Na prática, a maioria dos afiliados <strong>não faz essa análise</strong> e acaba divulgando
            produtos com comissões abaixo do potencial — deixando dinheiro na mesa sem saber.
        </p>

        <div class="tip-box">
            <p><strong>Exemplo real:</strong> divulgar um produto de R$&nbsp;50,00 com 12% de comissão
            (R$&nbsp;6,00/venda) é mais lucrativo do que um de R$&nbsp;200,00 com 2% (R$&nbsp;4,00/venda) —
            mesmo que o segundo pareça mais caro e "premium".</p>
        </div>

        <h2>Como filtrar por comissão no Comissify</h2>
        <p>
            O Comissify resolve esse problema mostrando a <strong>taxa de comissão de cada produto em
            destaque</strong> nos resultados de busca. Você não precisa clicar produto por produto para descobrir:
            o percentual aparece direto no card, antes mesmo de gerar o link.
        </p>
        <p>
            Isso muda a lógica do trabalho: em vez de buscar um produto e tomar como certo que vale a pena
            divulgar, você <strong>compara as opções antes de decidir</strong>.
        </p>
        <ul>
            <li>Busque por categoria ou palavra-chave</li>
            <li>Compare a comissão entre os produtos listados</li>
            <li>Escolha os que têm maior porcentagem</li>
            <li>Gere o link afiliado com 1 clique</li>
        </ul>

        <h2>A lista de maiores comissões — atualizada automaticamente</h2>
        <p>
            Além da busca manual, o Comissify mantém uma
            <a href="{{ route('commissions') }}">lista pública de produtos com maior comissão na Shopee</a>,
            atualizada a cada 6 horas automaticamente. É um ponto de partida rápido para quem quer começar
            a divulgar produtos rentáveis sem nem precisar buscar.
        </p>
        <p>
            A mesma lógica vale para tendências: há também uma
            <a href="{{ route('trending') }}">lista dos produtos mais vendidos no momento</a> —
            útil para combinar alta comissão com alta demanda.
        </p>

        <h2>A estratégia ideal: comissão alta + produto em tendência</h2>
        <p>
            O ponto ideal é a intersecção entre <strong>comissão alta</strong> e <strong>produto em demanda</strong>.
            Um produto que paga bem mas ninguém compra não gera resultado. Um produto que todo mundo quer mas
            paga 1% também não vale o esforço.
        </p>
        <p>
            Use o filtro de comissão do Comissify para identificar os produtos com percentual acima de 8%,
            e cruze com a lista de mais vendidos para ver quais desses produtos também têm volume de vendas.
            Esse é o portfólio que você quer divulgar.
        </p>

        <div class="tip-box">
            <p>
                <strong>Dica:</strong> verifique também o campo de ganho estimado por venda que aparece
                nos resultados. Ele mostra em reais quanto você ganha por conversão, considerando o preço
                e a taxa — mais fácil de comparar do que percentual puro.
            </p>
        </div>

        <h2>Conclusão</h2>
        <p>
            Encontrar produtos com maior comissão na Shopee manualmente é impraticável em escala. Com o
            Comissify, essa informação aparece na sua frente antes de você decidir o que divulgar —
            transformando uma decisão às cegas num processo baseado em dados.
        </p>

    </div>
</article>

{{-- CTA --}}
<section class="py-16 border-t border-d-border" style="background:#0f0f0f">
    <div class="max-w-2xl mx-auto px-6">
        <div class="rounded-2xl border border-d-border p-10 text-center" style="background:#111111">
            <h2 class="font-display text-2xl font-bold text-d-text mb-3">
                Comece a divulgar os produtos que mais pagam.
            </h2>
            <p class="text-d-muted text-sm mb-6">
                Crie sua conta grátis e ganhe <span style="color:var(--color-primary)">R$&nbsp;5,00 em créditos</span>
                para testar agora — sem cartão, sem compromisso.
            </p>
            <a href="{{ route('register') }}" class="btn-primary !py-3 !px-10 !text-base font-display font-bold inline-flex items-center gap-2">
                Criar conta grátis
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        {{-- Related --}}
        @if(!empty($related))
        <div class="mt-12">
            <p class="text-xs uppercase tracking-widest font-display mb-5" style="color:var(--color-primary)">Leia também</p>
            @foreach($related as $rel)
            <a href="{{ route('blog.show', $rel['slug']) }}"
               class="flex items-center justify-between gap-4 rounded-xl border border-d-border p-5 transition-all duration-200 hover:border-opacity-60 group"
               style="background:#1A1A1A">
                <div>
                    <p class="font-display font-semibold text-d-text text-sm group-hover:opacity-80 transition-opacity">{{ $rel['title'] }}</p>
                    <p class="text-xs text-d-muted mt-1">{{ $rel['reading_time'] }} min de leitura</p>
                </div>
                <svg class="w-4 h-4 flex-shrink-0 text-d-muted group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- Footer --}}
<footer class="py-8 border-t border-d-border" style="background:#0a0a0a">
    <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-xs font-display">C</span>
            </div>
            <span class="text-sm font-semibold text-d-text font-display">Comissify</span>
            <span class="text-xs text-d-muted ml-1">— Link afiliado Shopee automático</span>
        </div>
        <div class="flex items-center gap-5 text-xs text-d-muted">
            <a href="{{ route('home') }}" class="hover:text-d-text transition-colors">Início</a>
            <a href="{{ route('blog.index') }}" class="hover:text-d-text transition-colors">Blog</a>
            <a href="{{ route('faq') }}" class="hover:text-d-text transition-colors">FAQ</a>
            <a href="{{ route('terms') }}" class="hover:text-d-text transition-colors">Termos de uso</a>
            <a href="{{ route('privacy') }}" class="hover:text-d-text transition-colors">Privacidade</a>
        </div>
    </div>
</footer>

</body>
</html>
