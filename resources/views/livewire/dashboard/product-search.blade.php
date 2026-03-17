<div class="space-y-4"
     x-data="{
         confirmLink: false,
         pendingLink: '',
         pendingName: '',
         pendingImage: '',
         pendingPrice: '',
         pendingCommission: '',
         open(link, name, image, price, commission) {
             this.pendingLink       = link;
             this.pendingName       = name;
             this.pendingImage      = image;
             this.pendingPrice      = price;
             this.pendingCommission = commission;
             this.confirmLink       = true;
         }
     }">

    {{-- Filters --}}
    <div class="card !p-4">
        <div class="flex flex-col sm:flex-row gap-3">

            {{-- Category select --}}
            <div class="flex-1">
                <select wire:model="categoryId" class="form-select w-full">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Keyword --}}
            <div class="flex-1">
                <input wire:model="keyword" type="text" placeholder="Pesquisar produto..."
                       class="form-input w-full" @keydown.enter="$wire.search()"/>
            </div>

            <button wire:click="search" wire:loading.attr="disabled"
                    class="btn-primary px-6 py-2.5 whitespace-nowrap">
                <span wire:loading.remove wire:target="search">Buscar</span>
                <span wire:loading wire:target="search">Buscando...</span>
            </button>
        </div>

        <p class="mt-3 text-xs text-d-muted">
            Cada busca consome <strong class="text-d-text">R$ {{ number_format($searchCost, 2, ',', '.') }}</strong> em créditos
            &nbsp;·&nbsp; Seu saldo: <strong style="color:var(--color-primary)">R$ {{ number_format($balance, 2, ',', '.') }}</strong>
        </p>
    </div>

    {{-- Error --}}
    @if($error)
    @if(str_contains($error, 'affiliate.shopee.com.br'))
    {{-- Sem acesso à API de Afiliados --}}
    <div class="rounded-xl border p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4"
         style="background:color-mix(in srgb,#f97316 8%,transparent);border-color:color-mix(in srgb,#f97316 30%,transparent)">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:color-mix(in srgb,#f97316 20%,transparent)">
            <svg class="w-5 h-5" style="color:#f97316" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-sm" style="color:#f97316">Conta sem acesso à API de Afiliados</p>
            <p class="text-xs text-d-muted mt-0.5">
                Sua conta Shopee ainda não tem permissão para usar a API de Afiliados.
                Solicite acesso no portal oficial — normalmente é aprovado em poucos dias.
            </p>
        </div>
        <a href="https://affiliate.shopee.com.br" target="_blank" rel="noopener noreferrer"
           class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold text-white transition-opacity hover:opacity-80"
           style="background:#f97316">
            Solicitar acesso
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    </div>
    @else
    <div class="alert-error">{{ $error }}</div>
    @endif
    @endif

    {{-- Bulk action bar --}}
    @if(!empty($selected))
    <div class="flex items-center justify-between px-4 py-3 rounded-xl border"
         style="background:color-mix(in srgb,var(--color-primary) 10%,transparent);border-color:color-mix(in srgb,var(--color-primary) 30%,transparent)">
        <span class="text-sm font-medium" style="color:var(--color-primary)">{{ count($selected) }} produto(s) selecionado(s)</span>
        <button wire:click="generateBulkLinks"
                wire:confirm="Gerar links para {{ count($selected) }} produto(s)? Consumirá R$ {{ number_format($linkCost * count($selected), 2, ',', '.') }} em créditos."
                class="btn-primary text-sm py-1.5 px-4">
            Gerar Links
        </button>
    </div>
    @endif

    {{-- Products --}}
    @if(!empty($products))

    {{-- Sort + select all controls --}}
    <div class="flex items-center gap-2 flex-wrap">
        <span class="text-xs text-d-muted flex-shrink-0">Ordenar:</span>

        @php
            $sortOptions = [
                'sales'             => 'Mais vendidos',
                'commission'        => 'Maior ganho',
                'commissionRate'    => 'Maior taxa',
                'priceMin'          => 'Menor preço',
                'priceDiscountRate' => 'Maior desconto',
            ];
        @endphp

        @foreach($sortOptions as $field => $label)
        <button wire:click="sortBy('{{ $field }}')"
                class="px-3 py-1 rounded-lg text-xs font-medium border transition-all duration-150 flex items-center gap-1"
                style="{{ $sortField === $field
                    ? 'background:var(--color-primary);border-color:var(--color-primary);color:#fff'
                    : 'background:transparent;border-color:#2A2A2A;color:#9CA3AF' }}">
            {{ $label }}
            @if($sortField === $field)
                {{ $sortDir === 'asc' ? '↑' : '↓' }}
            @endif
        </button>
        @endforeach

        <label class="ml-auto flex items-center gap-1.5 text-xs text-d-muted cursor-pointer flex-shrink-0 select-none">
            <input type="checkbox" wire:click="toggleSelectAll"
                   :checked="{{ json_encode($selectAll) }}"
                   class="rounded border-d-border bg-d-input"/>
            Selecionar todos
        </label>
    </div>

    {{-- Card grid --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($products as $i => $product)
        @php $isSelected = in_array($product['itemId'], $selected); @endphp

        <article class="product-search-card card !p-0 overflow-hidden flex flex-col group transition-all duration-200"
                 style="animation-delay:{{ $i * 45 }}ms;{{ $isSelected ? 'border-color:var(--color-primary)' : '' }}"
                 wire:key="product-{{ $product['itemId'] }}">

            {{-- Image --}}
            <div class="relative overflow-hidden" style="aspect-ratio:1/1;background:#111111">
                <img src="{{ $product['imageUrl'] ?? '' }}"
                     alt="{{ $product['productName'] }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\' viewBox=\'0 0 200 200\'%3E%3Crect width=\'200\' height=\'200\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M60 140l40-55 27 35 18-25 35 45H60z\' fill=\'%23333\'/%3E%3Ccircle cx=\'145\' cy=\'65\' r=\'18\' fill=\'%23333\'/%3E%3C/svg%3E'"/>

                {{-- Checkbox --}}
                <div class="absolute top-2 left-2">
                    <input type="checkbox"
                           wire:click="toggleSelect('{{ $product['itemId'] }}')"
                           @checked($isSelected)
                           class="w-4 h-4 rounded cursor-pointer"
                           style="accent-color:var(--color-primary)"/>
                </div>

                {{-- Commission rate badge --}}
                @if(!empty($product['commissionRate']) && $product['commissionRate'] > 0)
                <div class="absolute top-2 right-2 px-2 py-0.5 rounded-lg text-white text-xs font-bold"
                     style="background:var(--color-primary)">
                    {{ $product['commissionRate'] }}%
                </div>
                @endif

                {{-- Discount badge --}}
                @if(!empty($product['priceDiscountRate']) && $product['priceDiscountRate'] > 0)
                <div class="absolute bottom-2 left-2 px-2 py-0.5 rounded-lg text-white text-xs font-bold"
                     style="background:#dc2626">
                    -{{ $product['priceDiscountRate'] }}%
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="flex flex-col gap-1.5 p-3 flex-1">
                <h3 class="text-sm font-medium text-d-text leading-snug line-clamp-2">
                    {{ $product['productName'] }}
                </h3>

                @if(!empty($product['shopName']))
                <p class="text-xs text-d-muted truncate">{{ $product['shopName'] }}</p>
                @endif

                <div class="flex items-end justify-between mt-auto pt-1">
                    <div>
                        @if(!empty($product['priceMin']) && $product['priceMin'] > 0)
                        <p class="text-sm font-bold" style="color:var(--color-primary)">
                            R$ {{ number_format($product['priceMin'], 2, ',', '.') }}
                        </p>
                        @endif
                        @if(!empty($product['sales']) && $product['sales'] > 0)
                        <p class="text-xs text-d-muted">{{ number_format($product['sales']) }} vendidos</p>
                        @endif
                    </div>

                    @if(!empty($product['commission']) && $product['commission'] > 0)
                    <div class="text-right">
                        <p class="text-xs text-d-muted leading-none">Ganhe</p>
                        <p class="text-sm font-semibold text-green-400">R$ {{ number_format($product['commission'], 2, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="flex border-t divide-x" style="border-color:#2A2A2A">
                <button wire:click="showDetail('{{ $product['itemId'] }}')"
                        class="flex-1 py-2.5 text-xs font-medium text-d-muted hover:text-d-text hover:bg-d-input transition-colors flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Detalhes
                </button>
                <button @click="open('{{ addslashes($product['offerLink'] ?? $product['productLink']) }}', '{{ addslashes($product['productName']) }}', '{{ $product['imageUrl'] ?? '' }}', 'R$ {{ number_format($product['priceMin'] ?? 0, 2, ',', '.') }}', 'R$ {{ number_format($product['commission'] ?? 0, 2, ',', '.') }}')"
                        class="flex-1 py-2.5 text-xs font-medium hover:bg-d-input transition-colors flex items-center justify-center gap-1.5"
                        style="color:var(--color-primary)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Gerar link
                </button>
            </div>
        </article>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if(!empty($pageInfo))
    <div class="flex items-center justify-between text-sm text-d-muted pt-1">
        <span>Página {{ $pageInfo['page'] ?? 1 }}</span>
        <div class="flex gap-2">
            @if(($pageInfo['page'] ?? 1) > 1)
            <button wire:click="loadPage({{ ($pageInfo['page'] - 1) }})" class="px-3 py-1.5 border border-d-border rounded-lg hover:bg-d-input transition-colors text-xs">← Anterior</button>
            @endif
            @if(!empty($pageInfo['hasNextPage']))
            <button wire:click="loadPage({{ ($pageInfo['page'] ?? 1) + 1 }})" class="px-3 py-1.5 border border-d-border rounded-lg hover:bg-d-input transition-colors text-xs">Próxima →</button>
            @endif
        </div>
    </div>
    @endif

    @else
    {{-- Empty state --}}
    <div class="card text-center py-16">
        <svg class="w-12 h-12 text-d-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <p class="text-d-muted text-sm">Selecione uma categoria ou pesquise um produto e clique em <strong class="text-d-text">Buscar</strong></p>
    </div>
    @endif

    {{-- Modal: link gerado (single) --}}
    @if($showLinkModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Link gerado</h3>
                <button wire:click="$set('showLinkModal', false)" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>
            <p class="text-sm text-d-muted mb-3 truncate">{{ $linkProductName }}</p>
            <div class="flex gap-2">
                <input type="text" value="{{ $generatedLink }}" readonly
                       class="form-input flex-1 text-xs font-mono"/>
                <button @click="navigator.clipboard.writeText('{{ $generatedLink }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="btn-secondary px-3 py-2 text-sm">
                    <span x-show="!copied">Copiar</span>
                    <span x-show="copied" class="text-green-400">✓</span>
                </button>
            </div>
            <p class="text-xs text-d-muted mt-3">R$ {{ number_format($linkCost, 2, ',', '.') }} deduzido do seu saldo</p>
            <div class="mt-4 pt-4 border-t border-d-border flex items-center justify-between">
                <p class="text-xs text-d-muted">
                    <span class="text-green-400">✓</span> Produto salvo no <strong class="text-d-text">Criador</strong>
                </p>
                <a href="{{ route('posts') }}" class="text-xs font-semibold transition-colors" style="color:var(--color-primary)">
                    Ver no Criador →
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: links bulk --}}
    @if($showBulkModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-xl" x-data="{ allLinks: {{ json_encode(array_column($bulkLinks, 'link')) }} }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Links gerados ({{ count($bulkLinks) }})</h3>
                <button wire:click="$set('showBulkModal', false)" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>
            <div class="space-y-2 max-h-72 overflow-y-auto mb-4">
                @foreach($bulkLinks as $item)
                <div class="flex items-center gap-2 p-2.5 rounded-lg bg-d-input border border-d-border">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-d-text truncate">{{ $item['name'] }}</p>
                        @if($item['ok'])
                        <p class="text-xs text-d-muted font-mono truncate">{{ $item['link'] }}</p>
                        @else
                        <p class="text-xs text-red-400">Erro: {{ $item['error'] ?? 'falhou' }}</p>
                        @endif
                    </div>
                    @if($item['ok'])
                    <button x-data="{ c: false }"
                            @click="navigator.clipboard.writeText('{{ $item['link'] }}'); c = true; setTimeout(() => c = false, 1500)"
                            class="flex-shrink-0 text-xs px-2 py-1 rounded border border-d-border hover:bg-d-card transition-colors">
                        <span x-show="!c">Copiar</span>
                        <span x-show="c" class="text-green-400">✓</span>
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
            <button x-data
                    @click="const links = allLinks.filter(Boolean).join('\n'); navigator.clipboard.writeText(links)"
                    class="btn-primary w-full justify-center py-2">
                Copiar todos os links
            </button>
        </div>
    </div>
    @endif

    {{-- Modal: detalhes do produto --}}
    @if($showDetailModal && !empty($detailProduct))
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Detalhes do produto</h3>
                <button wire:click="$set('showDetailModal', false)" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>
            <img src="{{ $detailProduct['imageUrl'] ?? '' }}" alt=""
                 class="w-full h-40 object-contain rounded-xl bg-d-input mb-4"
                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'160\' height=\'160\' viewBox=\'0 0 160 160\'%3E%3Crect width=\'160\' height=\'160\' rx=\'12\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M40 110l30-40 20 26 14-18 26 32H40z\' fill=\'%23333\'/%3E%3Ccircle cx=\'115\' cy=\'55\' r=\'14\' fill=\'%23333\'/%3E%3C/svg%3E'"/>
            <h4 class="font-semibold text-d-text mb-3">{{ $detailProduct['productName'] }}</h4>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-d-muted">Loja</dt><dd class="font-medium text-d-text">{{ $detailProduct['shopName'] ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Preço</dt><dd class="font-medium text-d-text">R$ {{ number_format($detailProduct['priceMin'] ?? 0, 2, ',', '.') }} – R$ {{ number_format($detailProduct['priceMax'] ?? 0, 2, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Vendidos</dt><dd class="font-medium text-d-text">{{ number_format($detailProduct['sales'] ?? 0) }}</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Avaliação</dt><dd class="font-medium text-d-text">{{ $detailProduct['ratingStar'] ?? '—' }} ★</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Taxa comissão</dt><dd class="font-semibold text-green-400">{{ $detailProduct['commissionRate'] ?? 0 }}%</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Ganho estimado</dt><dd class="font-semibold text-green-400">R$ {{ number_format($detailProduct['commission'] ?? 0, 2, ',', '.') }}</dd></div>
            </dl>
            <button wire:click="generateLink('{{ $detailProduct['offerLink'] ?? $detailProduct['productLink'] ?? '' }}', '{{ addslashes($detailProduct['productName']) }}'); $set('showDetailModal', false)"
                    class="btn-primary w-full justify-center py-2.5 mt-4">
                Gerar link afiliado
            </button>
        </div>
    </div>
    @endif

    {{-- Modal: confirmação de geração de link --}}
    <div x-show="confirmLink" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
         @keydown.escape.window="confirmLink = false">
        <div class="card w-full max-w-sm" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Gerar link afiliado</h3>
                <button @click="confirmLink = false" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>

            {{-- Produto --}}
            <div class="flex items-center gap-3 p-3 rounded-xl mb-4" style="background:#111111">
                <img :src="pendingImage" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0 bg-d-input"
                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'48\' height=\'48\'%3E%3Crect width=\'48\' height=\'48\' rx=\'8\' fill=\'%231A1A1A\'/%3E%3C/svg%3E'"/>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-d-text truncate" x-text="pendingName"></p>
                    <p class="text-xs text-d-muted mt-0.5" x-text="pendingPrice"></p>
                </div>
            </div>

            {{-- Info --}}
            <div class="space-y-2 mb-5">
                <div class="flex justify-between text-sm">
                    <span class="text-d-muted">Ganho estimado</span>
                    <span class="font-semibold text-green-400" x-text="pendingCommission"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-d-muted">Custo da operação</span>
                    <span class="font-semibold text-d-text">R$ {{ number_format($linkCost, 2, ',', '.') }}</span>
                </div>
            </div>

            <p class="text-xs text-d-muted mb-4">
                Será gerado um link de afiliado curto e o produto ficará disponível no <strong class="text-d-text">Criador</strong> para geração de conteúdo.
            </p>

            <div class="flex gap-2">
                <button @click="confirmLink = false"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-d-muted border border-d-border hover:bg-d-input transition-colors">
                    Cancelar
                </button>
                <button @click="confirmLink = false; $wire.generateLink(pendingLink, pendingName)"
                        class="flex-1 btn-primary justify-center py-2.5 text-sm">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

</div>
