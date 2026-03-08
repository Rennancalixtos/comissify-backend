<div class="space-y-5">

    {{-- ── ACESSO BLOQUEADO ────────────────────────────────────────────────── --}}
    @if(! $hasAccess)
    <div class="card flex flex-col items-center justify-center py-16 text-center gap-4">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
             style="background:color-mix(in srgb,var(--color-primary) 12%,transparent)">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"
                 style="color:var(--color-primary)">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
            </svg>
        </div>
        <div>
            <p class="text-base font-semibold text-d-text">Relatório disponível para assinantes</p>
            <p class="text-sm text-d-muted mt-1 max-w-sm">
                Adquira o pacote de <strong class="text-d-text">R$ 24,00</strong> ou superior para desbloquear
                o relatório de conversões. O acesso é mantido enquanto houver saldo em créditos.
            </p>
        </div>
        <a href="{{ route('credits') }}" class="btn-primary !px-6 !py-2.5 text-sm">
            Ver pacotes de créditos
        </a>
    </div>
    @else

    {{-- ── FILTROS ──────────────────────────────────────────────────────────── --}}
    <div class="card">
        <div class="flex flex-wrap items-end gap-3">

            <div>
                <label class="block text-xs text-d-muted mb-1.5">Período</label>
                <select wire:model.live="period" class="form-input !py-1.5 text-sm">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30">Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                    <option value="custom">Personalizado</option>
                </select>
            </div>

            @if($period === 'custom')
            <div>
                <label class="block text-xs text-d-muted mb-1.5">De</label>
                <input wire:model="dateStart" type="date" class="form-input !py-1.5 text-sm"/>
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Até</label>
                <input wire:model="dateEnd" type="date" class="form-input !py-1.5 text-sm"/>
            </div>
            <button wire:click="applyCustomDates" class="btn-primary !py-1.5 text-sm">Filtrar</button>
            @endif

            <div>
                <label class="block text-xs text-d-muted mb-1.5">Status</label>
                <select wire:model.live="orderStatus" class="form-input !py-1.5 text-sm">
                    <option value="">Todos</option>
                    <option value="COMPLETED">Concluído</option>
                    <option value="PENDING">Pendente</option>
                    <option value="CANCELLED">Cancelado</option>
                    <option value="UNPAID">Não pago</option>
                </select>
            </div>

            <div wire:loading class="text-xs text-d-muted flex items-center gap-1.5 pb-1">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                Carregando...
            </div>
        </div>
    </div>

    @if($error)
    <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)">
        {{ $error }}
    </div>
    @endif

    {{-- ── CARDS RESUMO ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card text-center">
            <p class="text-xs text-d-muted mb-1">Conversões</p>
            <p class="text-2xl font-bold text-d-text">{{ count($nodes) }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-d-muted mb-1">Comissão Total</p>
            <p class="text-2xl font-bold" style="color:var(--color-primary)">R$ {{ $this->totalCommission }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-d-muted mb-1">Comissão Líquida</p>
            <p class="text-2xl font-bold text-green-400">R$ {{ $this->netCommission }}</p>
        </div>
        <div class="card text-center">
            <p class="text-xs text-d-muted mb-1">Concluídos / Pendentes</p>
            <p class="text-2xl font-bold text-d-text">{{ $this->completedCount }}<span class="text-base text-d-muted"> / {{ $this->pendingCount }}</span></p>
        </div>
    </div>

    {{-- ── LISTA DE CONVERSÕES ──────────────────────────────────────────────── --}}
    @if(count($nodes) === 0 && ! $error)
    <div class="card text-center py-10 text-sm text-d-muted">
        Nenhuma conversão encontrada para o período selecionado.
    </div>
    @else

    <div class="space-y-3">
        @foreach($nodes as $conversion)
        @php
            $firstOrder  = $conversion['orders'][0] ?? null;
            $st          = $firstOrder['orderStatus'] ?? '';
            $statusColor = match($st) {
                'COMPLETED' => ['bg' => 'rgba(34,197,94,.08)', 'dot' => '#22c55e', 'text' => '#22c55e', 'label' => 'Concluído'],
                'PENDING'   => ['bg' => 'rgba(234,179,8,.08)',  'dot' => '#eab308', 'text' => '#eab308', 'label' => 'Pendente'],
                'CANCELLED' => ['bg' => 'rgba(239,68,68,.08)',  'dot' => '#ef4444', 'text' => '#ef4444', 'label' => 'Cancelado'],
                default     => ['bg' => 'rgba(156,163,175,.08)','dot' => '#9ca3af', 'text' => '#9ca3af', 'label' => $st ?: '—'],
            };
        @endphp
        <div class="card !p-0 overflow-hidden">

            {{-- Cabeçalho da conversão --}}
            <div class="flex items-center justify-between px-4 py-3"
                 style="background:#111111;border-bottom:1px solid rgba(255,255,255,0.05)">
                <div class="flex items-center gap-4 min-w-0">
                    <span class="text-xs text-d-muted flex-shrink-0">
                        {{ date('d/m/Y H:i', $conversion['purchaseTime']) }}
                    </span>
                    @if($firstOrder)
                    <span class="text-xs font-mono text-d-muted truncate">{{ $firstOrder['orderId'] }}</span>
                    @endif
                </div>
                <div class="flex items-center gap-4 flex-shrink-0 ml-4">
                    @if($firstOrder)
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full"
                          style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }}">
                        ● {{ $statusColor['label'] }}
                    </span>
                    @endif
                    <div class="text-right">
                        <p class="text-xs text-d-muted leading-none mb-0.5">comissão</p>
                        <p class="text-sm font-bold leading-none" style="color:var(--color-primary)">
                            R$ {{ number_format((float) $conversion['totalCommission'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Itens --}}
            @foreach($conversion['orders'] ?? [] as $order)
                @foreach($order['items'] ?? [] as $item)
                <div class="flex items-center gap-4 px-4 py-3">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-d-text font-medium truncate">{{ $item['itemName'] }}</p>
                        <p class="text-xs text-d-muted mt-0.5">{{ $item['shopName'] }} · {{ $item['qty'] }}×</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-sm text-d-text font-medium">
                            R$ {{ number_format((float) $item['actualAmount'], 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-d-muted">pago</p>
                    </div>
                </div>
                @endforeach
            @endforeach

        </div>
        @endforeach
    </div>

    {{-- Paginação --}}
    <div class="flex items-center justify-between pt-1">
        <button wire:click="prevPage"
                @class(['text-xs font-medium transition-opacity', 'opacity-30 cursor-default pointer-events-none' => $currentPage <= 1])
                style="color:{{ $currentPage > 1 ? 'var(--color-primary)' : '#9CA3AF' }}">
            ← Anterior
        </button>
        <span class="text-xs text-d-muted">Página {{ $currentPage }}</span>
        <button wire:click="nextPage"
                @class(['text-xs font-medium transition-opacity', 'opacity-30 cursor-default pointer-events-none' => empty($pageInfo['hasNextPage'])])
                style="color:{{ !empty($pageInfo['hasNextPage']) ? 'var(--color-primary)' : '#9CA3AF' }}">
            Próxima →
        </button>
    </div>

    @endif {{-- /nodes --}}

    @endif {{-- /hasAccess --}}
</div>
