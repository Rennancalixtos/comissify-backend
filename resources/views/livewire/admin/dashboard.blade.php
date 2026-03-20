<div class="space-y-6">

    {{-- ── Métricas de vendas ────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label' => 'Vendas Hoje',       'value' => $metrics['sales_today'],                               'sub' => $metrics['sales_month'].' no mês',             'color' => '#6366f1'],
                ['label' => 'Faturamento Hoje',  'value' => 'R$ '.number_format($metrics['revenue_today'], 2, ',', '.'), 'sub' => 'R$ '.number_format($metrics['revenue_month'], 2, ',', '.').' no mês', 'color' => '#10b981'],
                ['label' => 'Faturamento Total', 'value' => 'R$ '.number_format($metrics['revenue_total'], 2, ',', '.'), 'sub' => 'desde o início',                          'color' => '#f59e0b'],
                ['label' => 'Usuários',          'value' => $metrics['users_total'],                               'sub' => $metrics['users_connected'].' com Shopee',      'color' => '#2AABEE'],
            ];
        @endphp

        @foreach ($cards as $card)
            <div class="card flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg flex-shrink-0"
                     style="background:{{ $card['color'] }}22;border:1px solid {{ $card['color'] }}44">
                    <div class="w-full h-full rounded-lg flex items-center justify-center">
                        <div class="w-3 h-3 rounded-full" style="background:{{ $card['color'] }}"></div>
                    </div>
                </div>
                <div>
                    <p class="text-2xl font-bold text-d-text">{{ $card['value'] }}</p>
                    <p class="text-sm text-d-muted">{{ $card['label'] }}</p>
                    <p class="text-xs text-d-muted/60 mt-0.5">{{ $card['sub'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ── Resumo de usuários ─────────────────────────────────────────────── --}}
    <div class="grid md:grid-cols-2 gap-4">

        <div class="card space-y-4">
            <h2 class="text-base font-semibold text-d-text">Usuários</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-d-muted">Total cadastrado</span>
                    <span class="text-sm font-semibold text-d-text">{{ $metrics['users_total'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-d-muted">Com Shopee conectada</span>
                    <span class="text-sm font-semibold text-d-text">{{ $metrics['users_connected'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-d-muted">Sem Shopee conectada</span>
                    <span class="text-sm font-semibold text-d-text">{{ $metrics['users_total'] - $metrics['users_connected'] }}</span>
                </div>
            </div>
        </div>

        <div class="card space-y-4">
            <h2 class="text-base font-semibold text-d-text">Faturamento</h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-d-muted">Hoje ({{ $metrics['sales_today'] }} vendas)</span>
                    <span class="text-sm font-semibold text-d-text">R$ {{ number_format($metrics['revenue_today'], 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-d-muted">Este mês ({{ $metrics['sales_month'] }} vendas)</span>
                    <span class="text-sm font-semibold text-d-text">R$ {{ number_format($metrics['revenue_month'], 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-d-muted">Total acumulado</span>
                    <span class="text-sm font-bold" style="color:var(--color-primary)">R$ {{ number_format($metrics['revenue_total'], 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── APIs externas ───────────────────────────────────────────────────── --}}
    <div class="card space-y-4">
        <h2 class="text-base font-semibold text-d-text">APIs Externas</h2>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background:rgba(234,88,12,0.15);border:1px solid rgba(234,88,12,0.3)">
                    <svg class="w-4 h-4" fill="none" stroke="#ea580c" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-d-text">ScrapeCreators</p>
                    <p class="text-xs text-d-muted">Pinterest scraper & media downloader</p>
                </div>
            </div>

            @if($scrapeCredits['ok'])
                @php
                    $credits   = $scrapeCredits['data']['creditCount'] ?? 0;
                    $cachedAt  = isset($scrapeCredits['cached_at']) ? \Carbon\Carbon::parse($scrapeCredits['cached_at']) : null;
                @endphp
                <div class="text-right">
                    <p class="text-xl font-bold {{ $credits < 500 ? 'text-red-400' : ($credits < 1500 ? 'text-yellow-400' : 'text-green-400') }}">
                        {{ number_format($credits) }}
                    </p>
                    <p class="text-xs text-d-muted">créditos restantes</p>
                    @if($cachedAt)
                        <p class="text-xs text-d-muted/50 mt-0.5">cache · {{ $cachedAt->format('H:i') }}</p>
                    @endif
                </div>
            @else
                <div class="text-right">
                    <p class="text-sm text-red-400">Erro ao consultar</p>
                    <p class="text-xs text-d-muted">{{ $scrapeCredits['error'] }}</p>
                </div>
            @endif
        </div>
    </div>

</div>
