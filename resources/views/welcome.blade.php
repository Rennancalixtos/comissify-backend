<!DOCTYPE html>
<html lang="pt-BR" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO --}}
    <title>Comissify — Link Afiliado Shopee Automático | Ganhe Comissão em Segundos</title>
    <meta name="description" content="Encontre produtos com alta comissão na Shopee, gere link afiliado e ache vídeos prontos do TikTok e Pinterest para postar. Tudo em segundos. Comece com R$ 5,00 grátis.">
    <meta name="keywords" content="afiliado shopee, link afiliado shopee, programa afiliados shopee, ganhar dinheiro shopee, ferramenta afiliado shopee, comissão shopee">
    <link rel="canonical" href="{{ config('app.url') }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url') }}">
    <meta property="og:title" content="Comissify — Link Afiliado Shopee em 1 Clique">
    <meta property="og:description" content="Encontre produtos com alta comissão, gere links e compartilhe em segundos. Teste grátis com R$ 5,00 em créditos.">
    <meta property="og:image" content="{{ config('app.url') }}/images/og-comissify.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="pt_BR">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ config('app.url') }}/images/og-comissify.png">

    {{-- JSON-LD --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "Comissify",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "Web",
        "url": "{{ config('app.url') }}",
        "description": "Plataforma de automação para afiliados Shopee. Encontre produtos com alta comissão, gere links afiliados e compartilhe em grupos de WhatsApp e Telegram.",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "BRL",
            "description": "R$ 5,00 em créditos grátis ao criar conta"
        },
        "inLanguage": "pt-BR",
        "audience": {
            "@type": "Audience",
            "audienceType": "Afiliados Shopee"
        }
    }
    </script>

    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"></noscript>

    {{-- Preload LCP image --}}
    @php $firstShot = $screenshots->first(); @endphp
    @if($firstShot && $firstShot->hasWebp())
    <link rel="preload" as="image"
          href="{{ $firstShot->webpUrl(828) }}"
          imagesrcset="{{ $firstShot->webpUrl(640) }} 640w, {{ $firstShot->webpUrl(828) }} 828w, {{ $firstShot->webpUrl(1200) }} 1200w"
          imagesizes="(max-width: 768px) 100vw, (max-width: 1200px) 518px, 634px"
          fetchpriority="high">
    @elseif($firstShot)
    <link rel="preload" as="image" href="{{ Storage::url($firstShot->image_path) }}" fetchpriority="high">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        {!! \App\Services\ThemeService::cssVars() !!}

        .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Dot grid background */
        .bg-dots {
            background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 28px 28px;
        }

        /* Fade-up animation */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.7s ease both; }
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }

        /* Shimmer */
        @keyframes btn-shimmer {
            0%   { transform: translateX(-200%); }
            100% { transform: translateX(300%); }
        }

        /* Glow pulse */
        @keyframes glowPulse {
            0%, 100% { opacity: 0.15; }
            50%       { opacity: 0.30; }
        }
        .glow-pulse { animation: glowPulse 4s ease-in-out infinite; }

        /* Nav link glow pulse */
        @keyframes nav-glow-pulse {
            0%, 100% { opacity: 0.6; }
            50%       { opacity: 1; }
        }
        .nav-glow-pulse {
            color: #e5e7eb;
            animation: nav-glow-pulse 2.5s ease-in-out infinite;
        }

        /* Workflow connector line */
        .workflow-line::after {
            content: '';
            position: absolute;
            top: 24px;
            left: calc(50% + 3rem);
            width: calc(100% - 6rem);
            height: 1px;
            background: linear-gradient(to right, rgba(255,255,255,0.08), rgba(255,255,255,0.04));
        }

    </style>
</head>
<body class="bg-d-bg text-d-text antialiased">

{{-- ── Navbar ─────────────────────────────────────────────────────────── --}}
<nav class="border-b border-d-border sticky top-0 z-50 backdrop-blur-md" style="background:rgba(20,20,20,0.85)">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-sm font-display">C</span>
            </div>
            <span class="font-bold text-d-text font-display tracking-tight">Comissify</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('trending') }}"
               class="hidden sm:flex items-center gap-1 text-sm font-display font-semibold nav-glow-pulse">
                🔥 Mais vendidos
            </a>
            <a href="{{ route('commissions') }}"
               class="hidden sm:flex items-center gap-1 text-sm font-display font-semibold nav-glow-pulse">
                💰 Maior comissão
            </a>
            <a href="{{ route('blog.index') }}"
               class="hidden sm:flex items-center gap-1 text-sm font-display font-semibold text-d-muted hover:text-d-text transition-colors">
                Blog
            </a>
            <a href="{{ route('faq') }}"
               class="hidden sm:flex items-center gap-1 text-sm font-display font-semibold text-d-muted hover:text-d-text transition-colors">
                FAQ
            </a>
            @auth
                @php
                    $dashRoute = auth()->user()->is_admin
                        ? route('admin.dashboard')
                        : (auth()->user()->isShopeeConnected() ? route('dashboard') : route('connect'));
                @endphp
                <a href="{{ $dashRoute }}" class="btn-primary !py-1.5 !px-4 text-sm">Ir para o Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-secondary !py-1.5 !px-4 text-sm">Entrar</a>
                <a href="{{ route('register') }}" class="btn-primary !py-1.5 !px-4 text-sm">Começar grátis</a>
            @endauth
        </div>
    </div>
</nav>

{{-- ── Hero ────────────────────────────────────────────────────────────── --}}
<section class="relative overflow-hidden bg-dots">
    {{-- Ambient glow --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] -z-0 glow-pulse"
         style="background:radial-gradient(ellipse at center,color-mix(in srgb,var(--color-primary) 30%,transparent),transparent 70%)">
    </div>

    <div class="relative max-w-6xl mx-auto px-6 py-24 z-10">
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            {{-- Left: copy --}}
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-medium mb-6 animate-fade-up"
                     style="border-color:color-mix(in srgb,var(--color-primary) 40%,transparent);background:color-mix(in srgb,var(--color-primary) 10%,transparent);color:var(--color-primary)">
                    <span class="w-1.5 h-1.5 rounded-full" style="background:var(--color-primary)"></span>
                    Ferramenta de afiliados Shopee
                </div>

                <h1 class="font-display text-5xl font-extrabold text-d-text mb-6 leading-[1.1] tracking-tight animate-fade-up delay-100">
                    Produto, link e<br>
                    <span style="color:var(--color-primary)">vídeo pra postar</span><br>
                    em segundos.
                </h1>

                <p class="text-lg text-d-muted mb-8 leading-relaxed animate-fade-up delay-200">
                    Encontre produtos com alta comissão na Shopee, gere seu link afiliado e ache vídeos prontos do TikTok, Pinterest e Instagram para postar — tudo em um lugar só.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-6 animate-fade-up delay-300">
                    <a href="{{ route('register') }}" class="btn-primary btn-shimmer !py-3 !px-8 !text-base font-display font-semibold">
                        Começar grátis agora
                    </a>
                    <a href="{{ route('login') }}" class="btn-secondary !py-3 !px-8 !text-base">
                        Já tenho conta
                    </a>
                </div>

                <p class="text-xs text-d-muted mb-8 animate-fade-up delay-300" style="color:var(--color-primary)">
                    ✦ Ganhe <strong>R$ 5,00 em créditos</strong> só por criar sua conta — sem precisar de cartão.
                </p>

                {{-- Social proof bullets --}}
                <div class="flex flex-col gap-2.5 animate-fade-up delay-400">
                    @foreach([
                        'Busca produtos com comissão alta em segundos',
                        'Gera link afiliado encurtado com 1 clique',
                        'Acha vídeos prontos no TikTok, Pinterest e Instagram',
                        'Compartilha direto no WhatsApp e Telegram',
                    ] as $bullet)
                    <div class="flex items-center gap-2.5 text-sm text-d-muted">
                        <span class="w-4 h-4 rounded-full flex items-center justify-center text-white flex-shrink-0 text-[10px]"
                              style="background:var(--color-primary)">✓</span>
                        {{ $bullet }}
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Screenshot slideshow --}}
            @php $screenshotCount = $screenshots->count(); @endphp
            <div class="animate-fade-up delay-500"
                 x-data="{
                    current: 0,
                    total: {{ $screenshotCount }},
                    init() {
                        if (this.total > 1) {
                            setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
                        }
                    }
                 }">

                @if($screenshots->isNotEmpty())
                    <div class="relative rounded-2xl overflow-hidden border border-d-border shadow-2xl"
                         style="background:#111111;aspect-ratio:40/21;">
                        @foreach($screenshots as $i => $s)
                        <div x-show="current === {{ $i }}"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute inset-0"
                             style="display:none">
                            <picture>
                                @if($s->hasWebp())
                                <source type="image/webp"
                                        srcset="{{ $s->webpUrl(640) }} 640w, {{ $s->webpUrl(828) }} 828w, {{ $s->webpUrl(1200) }} 1200w"
                                        sizes="(max-width: 768px) 100vw, (max-width: 1200px) 518px, 634px">
                                @endif
                                <img src="{{ Storage::url($s->image_path) }}"
                                     alt="{{ $s->title ?? 'Comissify - Plataforma de afiliados Shopee' }}"
                                     class="w-full h-full object-contain"
                                     width="634" height="309"
                                     @if($i === 0) fetchpriority="high" loading="eager" @else loading="lazy" @endif>
                            </picture>
                            @if($s->title)
                            <div class="absolute bottom-0 left-0 right-0 px-4 py-3"
                                 style="background:linear-gradient(to top,rgba(0,0,0,0.7),transparent)">
                                <p class="text-xs text-white font-medium">{{ $s->title }}</p>
                            </div>
                            @endif
                        </div>
                        @endforeach

                        @if($screenshotCount > 1)
                        <button type="button" @click="current = (current - 1 + total) % total"
                                aria-label="Imagem anterior"
                                class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-white"
                                style="background:rgba(0,0,0,0.5)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button type="button" @click="current = (current + 1) % total"
                                aria-label="Próxima imagem"
                                class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-white"
                                style="background:rgba(0,0,0,0.5)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        @endif
                    </div>

                    @if($screenshotCount > 1)
                    <div class="flex justify-center gap-1.5 mt-4">
                        @foreach($screenshots as $i => $s)
                        <button type="button" @click="current = {{ $i }}"
                                aria-label="Ir para imagem {{ $i + 1 }}"
                                class="h-1.5 rounded-full transition-all duration-300"
                                :style="current === {{ $i }} ? 'background:var(--color-primary);width:1.5rem;' : 'background:#2E2E2E;width:0.5rem'">
                        </button>
                        @endforeach
                    </div>
                    @endif
                @else
                    {{-- Fallback mock --}}
                    <div class="rounded-2xl border border-d-border p-6 shadow-2xl" style="background:#111111;aspect-ratio:16/10;">
                        <div class="h-full flex flex-col gap-3">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="w-3 h-3 rounded-full bg-green-500"></div>
                                <div class="flex-1 h-5 rounded" style="background:#1A1A1A"></div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 flex-1">
                                @for($r = 0; $r < 4; $r++)
                                <div class="h-8 rounded" style="background:#1A1A1A;opacity:{{ 1 - $r * 0.15 }}"></div>
                                <div class="h-8 rounded col-span-2" style="background:#1A1A1A;opacity:{{ 1 - $r * 0.15 }}"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Glow --}}
                <div class="absolute -inset-6 -z-10 rounded-3xl opacity-15 blur-3xl glow-pulse"
                     style="background:var(--color-primary)"></div>
            </div>

        </div>
    </div>
</section>

{{-- ── Barra de confiança ───────────────────────────────────────────────── --}}
<div class="border-y border-d-border py-5" style="background:#111111">
    <div class="max-w-4xl mx-auto px-6">
        <p class="text-center text-xs text-d-muted uppercase tracking-widest font-display mb-4">Compatível com</p>
        <div class="flex flex-wrap justify-center items-center gap-8 text-d-muted">
            <div class="flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                WhatsApp
            </div>
            <div class="flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                Telegram
            </div>
            <div class="flex items-center gap-2 text-sm font-medium">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Shopee API Oficial
            </div>
        </div>
    </div>
</div>

{{-- ── Como funciona ──────────────────────────────────────────────────── --}}
<section class="py-24" style="background:#0f0f0f">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <p class="text-xs uppercase tracking-widest font-display mb-3" style="color:var(--color-primary)">Workflow</p>
            <h2 class="font-display text-3xl font-bold text-d-text">Do produto ao post em 4 passos</h2>
            <p class="text-d-muted mt-3 max-w-lg mx-auto text-sm">O que outros afiliados fazem em horas, você faz em menos de 1 minuto.</p>
        </div>

        <div class="grid md:grid-cols-4 gap-6 relative">
            @foreach([
                [
                    'num'  => '01',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>',
                    'title' => 'Busque o produto',
                    'desc'  => 'Filtre por categoria ou palavra-chave e encontre os produtos Shopee com maior porcentagem de comissão.',
                ],
                [
                    'num'  => '02',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>',
                    'title' => 'Gere o link',
                    'desc'  => 'Um clique gera seu link afiliado encurtado com seu ID — rastreado e pronto para compartilhar.',
                ],
                [
                    'num'  => '03',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>',
                    'title' => 'Ache vídeos prontos',
                    'desc'  => 'Busque vídeos e posts sobre o produto no TikTok, Pinterest e Instagram — sem sair da plataforma.',
                ],
                [
                    'num'  => '04',
                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>',
                    'title' => 'Cole e lucre',
                    'desc'  => 'Envie o vídeo + link para grupos de WhatsApp, Telegram ou poste nas suas redes. Comissão garantida.',
                ],
            ] as $i => $step)
            <div class="relative text-center p-6 rounded-2xl border border-d-border group transition-all duration-300 hover:border-opacity-50"
                 style="background:#1A1A1A;hover:border-color:var(--color-primary)">
                {{-- Number + icon --}}
                <div class="relative w-12 h-12 mx-auto mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                         style="background:color-mix(in srgb,var(--color-primary) 15%,transparent);border:1px solid color-mix(in srgb,var(--color-primary) 30%,transparent)">
                        <svg class="w-5 h-5" style="color:var(--color-primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $step['icon'] !!}
                        </svg>
                    </div>
                    <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full text-white text-[9px] font-bold font-display flex items-center justify-center"
                          style="background:var(--color-primary)">{{ $step['num'] }}</span>
                </div>
                <h3 class="font-display font-semibold text-d-text mb-2">{{ $step['title'] }}</h3>
                <p class="text-d-muted text-sm leading-relaxed">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Pacotes de tokens ───────────────────────────────────────────────── --}}
@if($packages->isNotEmpty())
<section id="pacotes" class="py-24">
    <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
            <p class="text-xs uppercase tracking-widest font-display mb-3" style="color:var(--color-primary)">Preços</p>
            <h2 class="font-display text-3xl font-bold text-d-text">Pague só o que usar</h2>
            <p class="text-d-muted mt-3 text-sm">Sem assinatura mensal. Seus créditos não expiram. Pare quando quiser.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            @foreach($packages as $pkg)
            <div class="card relative flex flex-col {{ $pkg->is_featured ? 'border-2 shadow-lg' : '' }}"
                 style="{{ $pkg->is_featured ? 'border-color:var(--color-primary)' : '' }}">
                @if($pkg->is_featured)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-xs font-bold text-white font-display"
                     style="background:var(--color-primary)">Mais popular</div>
                @endif
                <h3 class="font-display text-lg font-bold text-d-text mb-1">{{ $pkg->name }}</h3>
                <div class="flex items-baseline gap-1 mb-1">
                    <span class="font-display text-3xl font-extrabold" style="color:var(--color-primary)">R$ {{ number_format($pkg->price, 2, ',', '.') }}</span>
                </div>
                <p class="text-d-muted text-xs mb-4">= <strong class="text-d-text">R$ {{ number_format($pkg->tokens_amount, 2, ',', '.') }}</strong> em créditos para usar</p>
                <div class="border-t border-d-border mb-5"></div>
                <a href="{{ route('register') }}"
                   class="btn-primary w-full justify-center !py-2.5 mt-auto font-display font-semibold">
                    Escolher pacote
                </a>
            </div>
            @endforeach
        </div>
        <p class="text-center text-xs text-d-muted mt-8">
            Novos usuários ganham <span style="color:var(--color-primary)">R$ 5,00 em créditos grátis</span> para testar sem precisar comprar.
        </p>
    </div>
</section>
@endif

{{-- ── Dúvidas / FAQ teaser ─────────────────────────────────────────────── --}}
<section class="py-14 border-t border-d-border" style="background:#0f0f0f">
    <div class="max-w-2xl mx-auto px-6 text-center">
        <p class="text-d-muted text-sm mb-3">Ainda tem dúvidas sobre como funciona?</p>
        <a href="{{ route('faq') }}"
           class="inline-flex items-center gap-2 font-display font-semibold text-sm transition-colors hover:opacity-80"
           style="color:var(--color-primary)">
            Veja as perguntas frequentes
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</section>

{{-- ── CTA Final ───────────────────────────────────────────────────────── --}}
<section class="py-28 relative overflow-hidden">
    <div class="absolute inset-0 bg-dots opacity-50"></div>
    <div class="absolute inset-0 glow-pulse" style="background:radial-gradient(ellipse at center,color-mix(in srgb,var(--color-primary) 12%,transparent),transparent 70%)"></div>
    <div class="relative max-w-2xl mx-auto px-6 text-center z-10">
        <h2 class="font-display text-4xl font-extrabold text-d-text mb-4 leading-tight">
            Comece a ganhar<br>comissão hoje mesmo.
        </h2>
        <p class="text-d-muted mb-8 leading-relaxed">
            Junte-se a afiliados que já usam o Comissify para encontrar produtos, gerar links e compartilhar — tudo em segundos.
        </p>
        <a href="{{ route('register') }}" class="btn-primary btn-shimmer !py-4 !px-12 !text-base font-display font-bold inline-flex items-center gap-2">
            Criar conta grátis
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
        <p class="text-xs text-d-muted mt-4">Sem cartão de crédito · R$ 5,00 de bônus · Cancele quando quiser</p>
    </div>
</section>

{{-- ── Footer ─────────────────────────────────────────────────────────── --}}
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
            <a href="{{ route('blog.index') }}" class="hover:text-d-text transition-colors">Blog</a>
            <a href="{{ route('faq') }}" class="hover:text-d-text transition-colors">FAQ</a>
            <a href="{{ route('terms') }}" class="hover:text-d-text transition-colors">Termos de uso</a>
            <a href="{{ route('privacy') }}" class="hover:text-d-text transition-colors">Privacidade</a>
        </div>
    </div>
</footer>

@vite(['resources/js/alpine.js'])
</body>
</html>
