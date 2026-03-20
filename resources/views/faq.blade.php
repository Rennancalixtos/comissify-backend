<!DOCTYPE html>
<html lang="pt-BR" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO --}}
    <title>Perguntas Frequentes — Comissify</title>
    <meta name="description" content="Tire suas dúvidas sobre o Comissify: como funciona, como conectar sua conta Shopee, créditos, cobrança e muito mais.">
    <meta name="keywords" content="faq comissify, dúvidas afiliado shopee, como usar comissify, programa afiliados shopee api, créditos comissify">
    <link rel="canonical" href="{{ route('faq') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('faq') }}">
    <meta property="og:title" content="Perguntas Frequentes — Comissify">
    <meta property="og:description" content="Tire suas dúvidas sobre o Comissify: créditos, conexão Shopee, cobrança e como começar a ganhar comissão.">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-comissify.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/og-comissify.png">

    {{-- FAQPage Schema --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [
            {
                "@type": "Question",
                "name": "O que são créditos?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Créditos são o saldo da sua conta no Comissify, usado para realizar buscas de produtos e gerar links afiliados. Cada busca consome R$ 0,02 em créditos, garantindo uso justo da plataforma. Novos usuários ganham R$ 5,00 de créditos grátis ao criar conta."
                }
            },
            {
                "@type": "Question",
                "name": "Preciso ser aprovado no programa de afiliados da Shopee?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Sim. Para usar o Comissify, você precisa já participar do Programa de Afiliados Shopee e ter solicitado a liberação de acesso à API diretamente no app da Shopee. Sem essa aprovação, não é possível conectar."
                }
            },
            {
                "@type": "Question",
                "name": "Como conecto minha conta Shopee ao Comissify?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Após criar sua conta no Comissify, você será direcionado para uma tela de conexão onde inserirá seu App ID e sua Chave de acesso, fornecidos pela Shopee ao liberar a API. A validação é feita automaticamente."
                }
            },
            {
                "@type": "Question",
                "name": "Como funciona a cobrança?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Você compra pacotes de créditos e usa conforme necessário. Não há assinatura mensal — você paga apenas o que usar. Seus créditos não expiram."
                }
            },
            {
                "@type": "Question",
                "name": "Posso cancelar a qualquer momento?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Sim. Não há fidelidade nem contrato. Seus créditos não expiram e você pode parar de usar quando quiser."
                }
            },
            {
                "@type": "Question",
                "name": "O Comissify funciona para qualquer afiliado Shopee?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Sim, desde que você tenha acesso à API de afiliados da Shopee. A plataforma usa a API oficial da Shopee, então qualquer afiliado aprovado no programa pode usar."
                }
            },
            {
                "@type": "Question",
                "name": "Quais redes sociais posso usar para compartilhar os links?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "O Comissify gera links curtos prontos para compartilhar em qualquer lugar: grupos de WhatsApp, canais do Telegram, TikTok, Instagram, Pinterest e muito mais."
                }
            },
            {
                "@type": "Question",
                "name": "Como o Comissify busca vídeos do TikTok e Pinterest?",
                "acceptedAnswer": {
                    "@type": "Answer",
                    "text": "Após encontrar um produto, o Comissify busca automaticamente vídeos e posts relacionados no TikTok, Pinterest e Instagram para que você possa compartilhá-los junto com seu link afiliado, sem precisar sair da plataforma."
                }
            }
        ]
    }
    </script>

    {{-- BreadcrumbList --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Início",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Perguntas Frequentes",
                "item": "{{ route('faq') }}"
            }
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
            <a href="{{ route('blog.index') }}" class="hover:text-d-text transition-colors">Blog</a>
            <a href="{{ route('faq') }}" class="transition-colors" style="color:var(--color-primary)">FAQ</a>
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
            Ajuda & Suporte
        </div>
        <h1 class="font-display text-4xl font-extrabold text-d-text mb-4 leading-tight">
            Perguntas <span style="color:var(--color-primary)">frequentes</span>
        </h1>
        <p class="text-d-muted text-lg leading-relaxed">
            Tudo que você precisa saber antes de começar a ganhar comissão com o Comissify.
        </p>
    </div>
</section>

{{-- FAQ --}}
<section class="py-20">
    <div class="max-w-2xl mx-auto px-6">
        <div class="space-y-3" x-data="{ open: null }">
            @foreach([
                [
                    'q' => 'O que são créditos?',
                    'a' => 'Créditos são o saldo da sua conta no Comissify, usado para realizar buscas de produtos e gerar links afiliados. Cada busca consome R$ 0,02 em créditos, garantindo uso justo da plataforma. Novos usuários ganham R$ 5,00 de créditos grátis ao criar conta — sem precisar de cartão.',
                ],
                [
                    'q' => 'Preciso ser aprovado no programa de afiliados da Shopee?',
                    'a' => 'Sim. Para usar o Comissify, você precisa já participar do Programa de Afiliados Shopee e ter solicitado a liberação de acesso à API diretamente no app da Shopee. Sem essa aprovação, não é possível conectar.',
                ],
                [
                    'q' => 'Como conecto minha conta Shopee ao Comissify?',
                    'a' => 'Após criar sua conta no Comissify, você será direcionado para uma tela de conexão onde inserirá seu App ID e sua Chave de acesso, fornecidos pela Shopee ao liberar a API. A validação é feita automaticamente.',
                ],
                [
                    'q' => 'Como funciona a cobrança?',
                    'a' => 'Você compra pacotes de créditos e usa conforme necessário. Não há assinatura mensal — você paga apenas o que usar. Seus créditos não expiram.',
                ],
                [
                    'q' => 'Posso cancelar a qualquer momento?',
                    'a' => 'Sim. Não há fidelidade nem contrato. Seus créditos não expiram e você pode parar de usar quando quiser.',
                ],
                [
                    'q' => 'O Comissify funciona para qualquer afiliado Shopee?',
                    'a' => 'Sim, desde que você tenha acesso à API de afiliados da Shopee. A plataforma usa a API oficial, então qualquer afiliado aprovado no programa pode usar.',
                ],
                [
                    'q' => 'Quais redes sociais posso usar para compartilhar os links?',
                    'a' => 'O Comissify gera links curtos prontos para compartilhar em qualquer lugar: grupos de WhatsApp, canais do Telegram, TikTok, Instagram, Pinterest e muito mais.',
                ],
                [
                    'q' => 'Como o Comissify busca vídeos do TikTok e Pinterest?',
                    'a' => 'Após encontrar um produto, o Comissify busca automaticamente vídeos e posts relacionados no TikTok, Pinterest e Instagram para que você possa compartilhá-los junto com seu link afiliado — sem precisar sair da plataforma.',
                ],
            ] as $i => $faq)
            <div class="rounded-xl overflow-hidden border border-d-border" style="background:#1A1A1A">
                <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                        class="w-full text-left px-5 py-4 flex items-center justify-between gap-4">
                    <span class="font-medium text-d-text text-sm font-display">{{ $faq['q'] }}</span>
                    <svg class="w-4 h-4 text-d-muted flex-shrink-0 transition-transform duration-200"
                         :class="open === {{ $i }} && 'rotate-180'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === {{ $i }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="display:none">
                    <div class="px-5 pb-4 text-sm text-d-muted border-t border-d-border pt-4 leading-relaxed">{{ $faq['a'] }}</div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="mt-16 rounded-2xl border border-d-border p-10 text-center" style="background:#111111">
            <h2 class="font-display text-xl font-bold text-d-text mb-3">Ainda tem dúvidas?</h2>
            <p class="text-d-muted text-sm mb-6">
                Crie sua conta grátis e ganhe <span style="color:var(--color-primary)">R$ 5,00 em créditos</span> para testar sem nenhum compromisso.
            </p>
            <a href="{{ route('register') }}" class="btn-primary !py-3 !px-10 !text-base font-display font-bold inline-flex items-center gap-2">
                Criar conta grátis
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
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

@livewireScripts
</body>
</html>
