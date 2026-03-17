@php
    $toolsDef = [
        [
            'key'   => 'text',
            'label' => 'Texto AI',
            'desc'  => 'Gera um título chamativo e uma legenda completa otimizada para redes sociais, com o seu link de afiliado já incluso. Pronto para copiar e publicar.',
            'cost'  => $this->prices['text'],
            'color' => '#6366f1',
            'icon'  => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12',
        ],
        [
            'key'   => 'image',
            'label' => 'Imagem com IA',
            'desc'  => 'Cria uma imagem promocional do produto gerada por inteligência artificial. Ideal para feed do Instagram, stories e anúncios.',
            'cost'  => $this->prices['image'],
            'color' => '#ec4899',
            'icon'  => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z',
        ],
        [
            'key'   => 'video',
            'label' => 'Vídeo de Venda',
            'desc'  => 'Gera um vídeo de 20 a 30 segundos a partir da imagem do produto, com texto e música. Perfeito para Reels, Shorts e Stories.',
            'cost'  => $this->prices['video'],
            'color' => '#f59e0b',
            'icon'  => 'M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z',
        ],
        [
            'key'   => 'pinterest',
            'label' => 'Buscar no Pinterest',
            'desc'  => 'Encontra imagens e vídeos relacionados ao produto no Pinterest para usar como referência ou inspiração para seus posts.',
            'cost'  => $this->prices['pinterest'],
            'color' => '#e60023',
            'icon'  => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z',
        ],
    ];
@endphp

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ESTADO 1 — Selecionar produto                                         --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@if(! $selectedLinkId)

<div class="space-y-5">
    {{-- Hero --}}
    <div class="text-center py-8">
        <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4"
             style="background:color-mix(in srgb,var(--color-primary) 12%,transparent)">
            <svg class="w-6 h-6" style="color:var(--color-primary)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
            </svg>
        </div>
        <h2 class="text-base font-semibold text-d-text mb-1">Selecione um produto para começar</h2>
        <p class="text-xs text-d-muted">Escolha um dos seus produtos afiliados para criar conteúdo de divulgação</p>
    </div>

    @if($this->links->isEmpty())
    <div class="card text-center py-12">
        <p class="text-sm text-d-muted mb-4">Você ainda não tem produtos afiliados.</p>
        <a href="{{ route('dashboard') }}" class="btn-primary !text-xs !py-2 !px-4">Buscar produtos →</a>
    </div>
    @else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($this->links as $link)
        <button wire:click="selectLink({{ $link->id }})"
                class="card !p-0 overflow-hidden text-left group transition-all duration-200 hover:border-opacity-60 product-search-card"
                wire:key="link-{{ $link->id }}"
                style="animation-delay:{{ $loop->index * 40 }}ms">

            <div class="relative overflow-hidden" style="aspect-ratio:4/3;background:#111111">
                <img src="{{ $link->image_url ?? '' }}" alt="{{ $link->product_name }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'150\'%3E%3Crect width=\'200\' height=\'150\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M50 120l40-55 27 35 18-25 35 45H50z\' fill=\'%23333\'/%3E%3Ccircle cx=\'145\' cy=\'45\' r=\'18\' fill=\'%23333\'/%3E%3C/svg%3E'"/>

                @if($link->commission_rate)
                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-lg text-white text-xs font-bold"
                     style="background:var(--color-primary)">
                    {{ $link->commission_rate }}%
                </div>
                @endif

                @if($link->posts_count > 0)
                <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded-lg text-xs font-semibold"
                     style="background:rgba(0,0,0,0.7);color:#9CA3AF">
                    {{ $link->posts_count }} {{ $link->posts_count === 1 ? 'conteúdo' : 'conteúdos' }}
                </div>
                @endif
            </div>

            <div class="p-3 flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-d-text truncate">{{ $link->product_name }}</p>
                    <p class="text-xs text-d-muted truncate">{{ $link->shop_name }}</p>
                </div>
                <svg class="w-4 h-4 text-d-muted flex-shrink-0 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </button>
        @endforeach
    </div>
    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ESTADO 2 — Produto selecionado: escolher ferramenta                   --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@elseif(! $activeTab)

@php $link = $this->selectedLink; @endphp

<div class="space-y-5">
    {{-- Barra do produto selecionado --}}
    <div class="card !p-3 flex items-center gap-3">
        <img src="{{ $link->image_url ?? '' }}" alt=""
             class="w-10 h-10 rounded-lg object-cover flex-shrink-0"
             style="background:#272727"
             onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'%3E%3Crect width=\'40\' height=\'40\' rx=\'8\' fill=\'%231A1A1A\'/%3E%3C/svg%3E'"/>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-d-text truncate">{{ $link->product_name }}</p>
            <div class="flex items-center gap-2 mt-0.5">
                @if($link->shop_name)<span class="text-xs text-d-muted">{{ $link->shop_name }}</span>@endif
                @if($link->commission_rate)<span class="text-xs font-bold" style="color:var(--color-primary)">· {{ $link->commission_rate }}% comissão</span>@endif
            </div>
        </div>
        <button wire:click="clearProduct"
                class="flex items-center gap-1.5 text-xs text-d-muted hover:text-d-text transition-colors flex-shrink-0 px-2 py-1 rounded-lg hover:bg-d-input">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Trocar produto
        </button>
    </div>

    {{-- Título --}}
    <div>
        <h2 class="text-sm font-semibold text-d-text">O que você quer criar?</h2>
        <p class="text-xs text-d-muted mt-0.5">Escolha uma das ferramentas abaixo para gerar conteúdo de divulgação</p>
    </div>

    {{-- Grid de ferramentas --}}
    <div class="grid sm:grid-cols-2 gap-4">
        @foreach($toolsDef as $tool)
        <button wire:click="selectTab('{{ $tool['key'] }}')"
                class="card text-left flex flex-col gap-4 transition-all duration-200 group hover:border-opacity-60"
                style="min-height:180px">

            <div class="flex items-start justify-between">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center"
                     style="background:color-mix(in srgb,{{ $tool['color'] }} 15%,transparent)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                         style="color:{{ $tool['color'] }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tool['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-xs font-bold" style="color:{{ $tool['color'] }}">
                    R$ {{ number_format($tool['cost'], 2, ',', '.') }}
                </span>
            </div>

            <div class="flex-1">
                <p class="text-sm font-semibold text-d-text mb-1.5">{{ $tool['label'] }}</p>
                <p class="text-xs text-d-muted leading-relaxed">{{ $tool['desc'] }}</p>
            </div>

            <div class="flex items-center gap-1.5 text-xs font-semibold transition-colors"
                 style="color:{{ $tool['color'] }}">
                Selecionar
                <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </button>
        @endforeach
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ESTADO 3 — Ferramenta selecionada: workspace completo                 --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}
@else

@php
    $link       = $this->selectedLink;
    $activeTool = collect($toolsDef)->firstWhere('key', $activeTab);
    $history    = collect($this->postsForLink)->where('type', $activeTab)->values();
@endphp

<div class="space-y-4">

    {{-- Header de navegação --}}
    <div class="flex items-center gap-3">
        <button wire:click="clearTab"
                class="flex items-center gap-1.5 text-xs text-d-muted hover:text-d-text transition-colors px-2 py-1.5 rounded-lg hover:bg-d-input">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Ferramentas
        </button>
        <span class="text-d-muted text-xs">/</span>
        <div class="flex items-center gap-2">
            <div class="w-5 h-5 rounded-md flex items-center justify-center"
                 style="background:color-mix(in srgb,{{ $activeTool['color'] }} 15%,transparent)">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                     style="color:{{ $activeTool['color'] }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $activeTool['icon'] }}"/>
                </svg>
            </div>
            <span class="text-xs font-semibold text-d-text">{{ $activeTool['label'] }}</span>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <img src="{{ $link->image_url ?? '' }}" alt=""
                 class="w-5 h-5 rounded object-cover"
                 style="background:#272727"
                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'20\' height=\'20\'%3E%3Crect width=\'20\' height=\'20\' rx=\'4\' fill=\'%231A1A1A\'/%3E%3C/svg%3E'"/>
            <span class="text-xs text-d-muted truncate max-w-[140px]">{{ $link->product_name }}</span>
        </div>
    </div>

    {{-- Flash / erro --}}
    @if($flashMsg)
    <div class="rounded-xl px-4 py-3 text-sm flex items-center gap-2"
         style="background:rgba(34,197,94,.08);color:#4ade80;border:1px solid rgba(34,197,94,.2)">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $flashMsg }}
    </div>
    @endif
    @if($error)
    <div class="alert-error">{{ $error }}</div>
    @endif

    {{-- Workspace da ferramenta --}}
    <div class="card space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:color-mix(in srgb,{{ $activeTool['color'] }} 15%,transparent)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                         style="color:{{ $activeTool['color'] }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $activeTool['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-d-text">{{ $activeTool['label'] }}</p>
                    <p class="text-xs text-d-muted">R$ {{ number_format($activeTool['cost'], 2, ',', '.') }} por geração</p>
                </div>
            </div>
        </div>

        <p class="text-xs text-d-muted leading-relaxed">{{ $activeTool['desc'] }}</p>

        {{-- Ação específica da ferramenta --}}
        @if($activeTab === 'pinterest')
        <div class="flex gap-2">
            <input wire:model="pinterestQuery" type="text"
                   class="form-input flex-1"
                   placeholder="Palavra-chave para buscar..."
                   @keydown.enter="$wire.searchPinterest()"/>
            <button wire:click="searchPinterest" wire:loading.attr="disabled"
                    class="btn-primary !px-5 flex-shrink-0">
                <span wire:loading.remove wire:target="searchPinterest">Buscar</span>
                <span wire:loading wire:target="searchPinterest">Buscando...</span>
            </button>
        </div>
        @if($pinterestError)
        <p class="text-xs text-red-400">{{ $pinterestError }}</p>
        @endif
        @else
        <button wire:click="createPost('{{ $activeTab }}')" wire:loading.attr="disabled"
                class="btn-primary w-full justify-center">
            <span wire:loading.remove wire:target="createPost">
                Gerar {{ $activeTool['label'] }}
            </span>
            <span wire:loading wire:target="createPost">Gerando...</span>
        </button>
        @endif
    </div>

    {{-- Resultado Pinterest --}}
    @once
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    @endonce
    @if(!empty($pinterestPins))
    <div class="card !p-0 overflow-hidden">
        <div class="px-4 py-3 border-b border-d-border flex items-center justify-between">
            <p class="text-sm font-semibold text-d-text">{{ count($pinterestPins) }} pins encontrados</p>
            <span class="text-xs text-d-muted">Clique para abrir ou baixar</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-px" style="background:rgba(255,255,255,0.04)">
            @foreach($pinterestPins as $pin)
            @php
                $isVideo = $pin['media_type'] === 'video' && $pin['video_url'];
                $dlAsset = $pin['video_url'] ?? $pin['image_url'];
                $dlUrl   = $dlAsset ? route('download.proxy', ['url' => $dlAsset]) : null;
            @endphp
            <div class="relative group" style="background:#1C1C1C"
                 x-data="{ playing: false,
                            play() {
                                this.playing = true;
                                let v = this.$refs.vid;
                                if (window.Hls && Hls.isSupported()) {
                                    let h = new Hls(); h.loadSource(v.dataset.src); h.attachMedia(v); v.play();
                                } else { v.src = v.dataset.src; v.play(); }
                            },
                            stop() { this.playing = false; this.$refs.vid.pause(); }
                          }">
                <div class="aspect-square overflow-hidden relative">
                    <img x-show="!playing" src="{{ $pin['image_url'] }}" alt="{{ $pin['title'] }}"
                         class="w-full h-full object-cover" :class="!playing ? 'group-hover:opacity-70 transition-opacity' : ''">
                    @if($isVideo)
                    <video x-ref="vid" x-show="playing" class="w-full h-full object-cover bg-black"
                           data-src="{{ $pin['video_url'] }}" controls playsinline @click.stop></video>
                    <button x-show="!playing" @click.stop="play()"
                            class="absolute inset-0 flex items-center justify-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background:rgba(0,0,0,0.65)">
                            <svg class="w-4 h-4 text-white ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>
                    </button>
                    <button x-show="playing" @click.stop="stop()"
                            class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full flex items-center justify-center z-10"
                            style="background:rgba(0,0,0,0.7)">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    @endif
                </div>
                <div x-show="!playing"
                     class="absolute inset-0 flex items-end opacity-0 group-hover:opacity-100 transition-opacity p-2 pointer-events-none"
                     style="background:linear-gradient(to top, rgba(0,0,0,0.85), transparent)">
                    <div class="w-full flex gap-1.5 pointer-events-auto">
                        <a href="{{ $pin['link'] ?? '#' }}" target="_blank" rel="noopener"
                           class="flex-1 text-center text-xs font-medium text-white py-1 rounded-lg"
                           style="background:rgba(255,255,255,0.15)" @click.stop>Abrir</a>
                        @if($dlUrl)
                        <a href="{{ $dlUrl }}"
                           class="flex-1 text-center text-xs font-medium text-white py-1 rounded-lg"
                           style="background:var(--color-primary)" @click.stop>Baixar</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Histórico filtrado por esta ferramenta --}}
    @if($history->isNotEmpty())
    <div class="space-y-3">
        <p class="text-xs font-semibold text-d-muted uppercase tracking-wider">
            Gerados anteriormente — {{ $history->count() }} {{ $history->count() === 1 ? 'item' : 'itens' }}
        </p>

        @foreach($history as $post)
        @php $result = $post['result'] ?? []; @endphp
        <div class="card !p-0 overflow-hidden">
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-d-border"
                 style="background:#272727">
                <span class="text-xs font-semibold" style="color:{{ $activeTool['color'] }}">{{ $activeTool['label'] }}</span>
                <span class="text-xs text-d-muted">{{ \Carbon\Carbon::parse($post['created_at'])->format('d/m H:i') }}</span>
            </div>

            <div class="p-4">
                @if($activeTab === 'text' && $post['status'] === 'completed')
                @php $caption = ($result['title'] ?? '') . "\n\n" . ($result['description'] ?? '') . "\n\n" . ($link->affiliate_link ?? ''); @endphp
                <div x-data="{ copied: false }" class="space-y-3">
                    <div class="rounded-xl p-4 space-y-2" style="background:#111111;border:1px solid rgba(255,255,255,0.05)">
                        <p class="text-sm font-semibold text-d-text leading-snug">{{ $result['title'] ?? '' }}</p>
                        <p class="text-xs text-d-muted leading-relaxed">{{ $result['description'] ?? '' }}</p>
                        <p class="text-xs font-mono truncate" style="color:var(--color-primary)">{{ $link->affiliate_link ?? '' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <button @click="navigator.clipboard.writeText('{{ addslashes($caption) }}'); copied = true; setTimeout(() => copied = false, 2500)"
                                class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl border text-xs font-semibold transition-all"
                                style="border-color:#2A2A2A;color:#9CA3AF"
                                x-bind:style="copied ? 'border-color:rgba(34,197,94,.4);color:#4ade80;background:rgba(34,197,94,.05)' : ''">
                            <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="copied ? 'Copiado!' : 'Copiar legenda'"></span>
                        </button>
                        @if($this->telegramGroups->isNotEmpty())
                        @include('livewire.dashboard.partials.telegram-send-btn', ['postId' => $post['id']])
                        @endif
                    </div>
                </div>

                @elseif($activeTab === 'image' && $post['status'] === 'completed')
                <div class="space-y-3">
                    <img src="{{ $result['image_url'] ?? '' }}" alt="" class="rounded-xl w-full object-cover" style="max-height:300px">
                    <div class="flex gap-2">
                        <a href="{{ $result['image_url'] ?? '#' }}" download
                           class="flex-1 flex items-center justify-center gap-2 py-2 rounded-xl border text-xs font-semibold transition-colors hover:bg-d-input"
                           style="border-color:#2A2A2A;color:#9CA3AF">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Baixar imagem
                        </a>
                        @if($this->telegramGroups->isNotEmpty())
                        @include('livewire.dashboard.partials.telegram-send-btn', ['postId' => $post['id']])
                        @endif
                    </div>
                </div>

                @elseif($activeTab === 'video' && $post['status'] === 'completed')
                <div class="space-y-3">
                    <video src="{{ $result['video_url'] ?? '' }}" controls class="rounded-xl w-full" style="max-height:300px;background:#111"></video>
                    <a href="{{ $result['video_url'] ?? '#' }}" download
                       class="w-full flex items-center justify-center gap-2 py-2 rounded-xl border text-xs font-semibold transition-colors hover:bg-d-input"
                       style="border-color:#2A2A2A;color:#9CA3AF">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Baixar vídeo
                    </a>
                </div>

                @elseif($activeTab === 'pinterest' && $post['status'] === 'completed')
                <p class="text-xs text-d-muted">{{ count($result['pins'] ?? []) }} pins para <span class="text-d-text">"{{ $result['query'] ?? '' }}"</span></p>

                @elseif(in_array($post['status'], ['pending', 'processing']))
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full animate-pulse bg-yellow-400"></span>
                    <p class="text-sm text-yellow-400">Processando...</p>
                </div>

                @elseif($post['status'] === 'failed')
                <p class="text-sm text-red-400">{{ $post['error_message'] ?? 'Falhou' }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endif
