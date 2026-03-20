<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO --}}
    <title>Blog — Guias para Afiliados Shopee | Comissify</title>
    <meta name="description" content="Dicas, guias e estratégias para afiliados Shopee. Aprenda como gerar links, encontrar produtos com alta comissão e ganhar mais.">
    <meta name="keywords" content="blog afiliados shopee, dicas afiliado shopee, como ganhar comissão shopee, guia afiliados shopee">
    <link rel="canonical" href="{{ route('blog.index') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('blog.index') }}">
    <meta property="og:title" content="Blog Comissify — Guias para Afiliados Shopee">
    <meta property="og:description" content="Dicas, guias e estratégias para afiliados Shopee. Aprenda como gerar links, encontrar produtos com alta comissão e ganhar mais.">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-comissify.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/og-comissify.png">

    {{-- BreadcrumbList --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            { "@type": "ListItem", "position": 1, "name": "Início", "item": "{{ url('/') }}" },
            { "@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ route('blog.index') }}" }
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

{{-- Header --}}
<section class="py-16 text-center border-b border-d-border" style="background:#0f0f0f">
    <div class="max-w-2xl mx-auto px-6">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-medium mb-5"
             style="border-color:color-mix(in srgb,var(--color-primary) 40%,transparent);background:color-mix(in srgb,var(--color-primary) 10%,transparent);color:var(--color-primary)">
            <span class="w-1.5 h-1.5 rounded-full" style="background:var(--color-primary)"></span>
            Guias & Estratégias
        </div>
        <h1 class="font-display text-4xl font-extrabold text-d-text mb-4 leading-tight">
            Blog para <span style="color:var(--color-primary)">afiliados Shopee</span>
        </h1>
        <p class="text-d-muted text-lg leading-relaxed">
            Guias práticos para gerar mais links, encontrar produtos rentáveis e ganhar mais comissão.
        </p>
    </div>
</section>

{{-- Article list --}}
<section class="py-20">
    <div class="max-w-3xl mx-auto px-6">
        <div class="space-y-6">
            @foreach($articles as $article)
            <a href="{{ route('blog.show', $article['slug']) }}"
               class="block rounded-2xl border border-d-border p-7 transition-all duration-200 hover:border-opacity-60 group"
               style="background:#1A1A1A">
                <div class="flex items-center gap-3 mb-3 text-xs text-d-muted font-display">
                    <span>{{ \Carbon\Carbon::parse($article['published_at'])->format('d/m/Y') }}</span>
                    <span>·</span>
                    <span>{{ $article['reading_time'] }} min de leitura</span>
                </div>
                <h2 class="font-display text-xl font-bold text-d-text mb-2 group-hover:opacity-80 transition-opacity leading-snug">
                    {{ $article['title'] }}
                </h2>
                <p class="text-d-muted text-sm leading-relaxed mb-4">{{ $article['description'] }}</p>
                <span class="inline-flex items-center gap-1.5 text-sm font-display font-semibold transition-colors"
                      style="color:var(--color-primary)">
                    Ler artigo
                    <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>
            @endforeach
        </div>
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
