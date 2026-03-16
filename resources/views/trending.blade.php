<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDesc }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <link rel="canonical" href="{{ route($routeName) }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route($routeName) }}">
    <meta property="og:title" content="{{ $metaTitle }}">
    <meta property="og:description" content="{{ $metaDesc }}">
    <meta property="og:locale" content="pt_BR">

    @if($products)
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ItemList",
        "name": "{{ $pageTitle }}",
        "description": "{{ $metaDesc }}",
        "url": "{{ route($routeName) }}",
        "numberOfItems": {{ count($products) }},
        "itemListElement": [
            @foreach($products as $i => $p)
            {
                "@type": "ListItem",
                "position": {{ $i + 1 }},
                "item": {
                    "@type": "Product",
                    "name": "{{ addslashes($p['productName'] ?? '') }}",
                    "image": "{{ $p['imageUrl'] ?? '' }}",
                    "url": "{{ $p['productLink'] ?? '' }}",
                    "offers": {
                        "@type": "Offer",
                        "price": "{{ ($p['priceMin'] ?? 0) / 100000 }}",
                        "priceCurrency": "BRL",
                        "availability": "https://schema.org/InStock"
                    }
                }
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    }
    </script>
    @endif

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        {!! \App\Services\ThemeService::cssVars() !!}
        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .product-card {
            animation: fadeUp 0.4s ease both;
        }

        @keyframes nav-shimmer {
            0%   { background-position: 200% center; }
            100% { background-position: -200% center; }
        }
        .nav-shimmer {
            background: linear-gradient(90deg, #9CA3AF 0%, #9CA3AF 30%, #ffffff 50%, #9CA3AF 70%, #9CA3AF 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: nav-shimmer 3s linear infinite;
        }
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
        <div class="hidden sm:flex items-center gap-4 text-sm font-display font-semibold">
            <a href="{{ route('trending') }}"
               class="transition-colors {{ $routeName === 'trending' ? '' : 'nav-shimmer' }}"
               style="{{ $routeName === 'trending' ? 'color:var(--color-primary)' : '' }}">
                🔥 Mais vendidos
            </a>
            <a href="{{ route('commissions') }}"
               class="transition-colors {{ $routeName === 'commissions' ? '' : 'nav-shimmer' }}"
               style="{{ $routeName === 'commissions' ? 'color:var(--color-primary)' : '' }}">
                💰 Maior comissão
            </a>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="btn-secondary !py-1.5 !px-4 text-sm">Entrar</a>
            <a href="{{ route('register') }}" class="btn-primary !py-1.5 !px-4 text-sm">Começar grátis</a>
        </div>
    </div>
</nav>

{{-- Header --}}
<section class="py-16 text-center border-b border-d-border" style="background:#0f0f0f">
    <div class="max-w-3xl mx-auto px-6">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-medium mb-5"
             style="border-color:color-mix(in srgb,var(--color-primary) 40%,transparent);background:color-mix(in srgb,var(--color-primary) 10%,transparent);color:var(--color-primary)">
            <span class="w-1.5 h-1.5 rounded-full animate-pulse" style="background:var(--color-primary)"></span>
            {{ $badge }} · Atualizado a cada 6 horas
        </div>
        <h1 class="font-display text-4xl font-extrabold text-d-text mb-4 leading-tight">
            {!! $heading !!}
        </h1>
        <p class="text-d-muted text-lg leading-relaxed">{{ $subheading }}</p>
        @if($updatedAt)
        <p class="text-xs text-d-muted mt-4">
            Última atualização: {{ \Carbon\Carbon::parse($updatedAt)->setTimezone('America/Sao_Paulo')->format('d/m/Y \à\s H:i') }}
        </p>
        @endif
    </div>
</section>

{{-- Products Grid --}}
<section class="py-16">
    <div class="max-w-6xl mx-auto px-6">
        @if($products && count($products) > 0)

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($products as $i => $product)
            @php
                $price      = isset($product['priceMin']) && $product['priceMin'] > 0 ? (float) $product['priceMin'] : null;
                $commission = isset($product['commissionRate']) ? (float) $product['commissionRate'] * 100 : null;
                $rating     = $product['ratingStar'] ?? null;
                $sales      = $product['sales'] ?? null;
            @endphp
            <article class="product-card card flex flex-col gap-3 hover:border-opacity-60 transition-all duration-200 group"
                     style="animation-delay: {{ $i * 40 }}ms">

                {{-- Image --}}
                <div class="relative rounded-xl overflow-hidden aspect-square" style="background:#111">
                    @if($product['imageUrl'] ?? null)
                    <img src="{{ $product['imageUrl'] }}"
                         alt="{{ $product['productName'] ?? 'Produto Shopee' }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         loading="lazy">
                    @else
                    <div class="w-full h-full flex items-center justify-center text-d-muted text-xs">Sem imagem</div>
                    @endif

                    {{-- Commission badge --}}
                    @if($commission)
                    <div class="absolute top-2 right-2 px-2 py-1 rounded-lg text-white text-xs font-bold font-display"
                         style="background:var(--color-primary)">
                        {{ number_format($commission, 1) }}% comissão
                    </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex flex-col gap-1 flex-1">
                    <h2 class="text-sm font-medium text-d-text leading-snug line-clamp-2">
                        {{ $product['productName'] ?? 'Produto Shopee' }}
                    </h2>

                    @if($product['shopName'] ?? null)
                    <p class="text-xs text-d-muted">{{ $product['shopName'] }}</p>
                    @endif

                    <div class="flex items-center gap-3 mt-1">
                        @if($price)
                        <span class="font-display font-bold text-sm" style="color:var(--color-primary)">
                            R$ {{ number_format($price, 2, ',', '.') }}
                        </span>
                        @endif

                        @if($rating)
                        <span class="text-xs text-d-muted flex items-center gap-1">
                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ number_format($rating, 1) }}
                        </span>
                        @endif
                    </div>

                    @if(!empty($product['commission']) && (float)$product['commission'] > 0)
                    <p class="text-xs text-d-muted mt-1">
                        Ganhe ~<strong class="text-d-text">R$ {{ number_format((float)$product['commission'], 2, ',', '.') }}</strong> por venda
                    </p>
                    @endif
                </div>

                {{-- CTA --}}
                <a href="{{ route('register') }}"
                   class="btn-primary w-full justify-center !py-2 !text-xs font-display font-semibold mt-auto">
                    Gerar meu link afiliado
                </a>
            </article>
            @endforeach
        </div>

        {{-- CTA banner --}}
        <div class="mt-16 rounded-2xl border border-d-border p-10 text-center" style="background:#111111">
            <h2 class="font-display text-2xl font-bold text-d-text mb-3">
                Quer gerar links para esses produtos?
            </h2>
            <p class="text-d-muted mb-6 text-sm">
                Crie sua conta grátis no Comissify e gere links afiliados Shopee com 1 clique — com R$ 5,00 de bônus para testar.
            </p>
            <a href="{{ route('register') }}" class="btn-primary !py-3 !px-10 !text-base font-display font-bold inline-flex items-center gap-2">
                Criar conta grátis
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        @else
        {{-- Empty state --}}
        <div class="text-center py-24">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4"
                 style="background:color-mix(in srgb,var(--color-primary) 15%,transparent)">
                <svg class="w-8 h-8" style="color:var(--color-primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="font-display text-xl font-bold text-d-text mb-2">Carregando tendências...</h2>
            <p class="text-d-muted text-sm">Os produtos serão atualizados em breve. Volte em alguns minutos.</p>
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
            <a href="{{ route('terms') }}" class="hover:text-d-text transition-colors">Termos de uso</a>
            <a href="{{ route('privacy') }}" class="hover:text-d-text transition-colors">Privacidade</a>
        </div>
    </div>
</footer>

</body>
</html>
