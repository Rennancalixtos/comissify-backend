@push('scripts')
<script src="https://sdk.mercadopago.com/js/v2"></script>
@endpush

<div class="space-y-6">

    {{-- ── Flash messages ──────────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.2)">
        {{ session('success') }}
    </div>
    @elseif(session('error'))
    <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)">
        {{ session('error') }}
    </div>
    @elseif(session('info'))
    <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(59,130,246,.1);color:#60a5fa;border:1px solid rgba(59,130,246,.2)">
        {{ session('info') }}
    </div>
    @endif

    {{-- ── Saldo atual ──────────────────────────────────────────────────────── --}}
    <div class="card flex items-center justify-between">
        <div>
            <p class="text-xs text-d-muted mb-1">Seu saldo atual</p>
            <p class="text-2xl font-bold" style="color:var(--color-primary)">
                R$ {{ number_format($balance, 2, ',', '.') }} em créditos
            </p>
        </div>
        @if($balance > 0)
        <span class="badge-green text-xs">● Ativo</span>
        @else
        <span class="badge-red text-xs">● Sem saldo</span>
        @endif
    </div>

    {{-- ── Pacotes ───────────────────────────────────────────────────────────── --}}
    @if($packages->isEmpty())
    <div class="card text-center py-10 text-sm text-d-muted">
        Nenhum pacote disponível no momento.
    </div>
    @else
    <div>
        <h2 class="text-base font-semibold text-d-text mb-4">Escolha um pacote</h2>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach($packages as $pkg)
            @php $isReportPkg = $pkg->price >= 24; @endphp
            <div class="card relative {{ $pkg->is_featured ? 'border-2' : '' }}"
                 style="{{ $pkg->is_featured ? 'border-color:var(--color-primary)' : '' }}">

                @if($pkg->is_featured)
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-xs font-bold text-white"
                     style="background:var(--color-primary)">Mais popular</div>
                @endif

                <h3 class="text-base font-bold text-d-text mb-1">{{ $pkg->name }}</h3>
                <div class="flex items-baseline gap-1 mb-3">
                    <span class="text-3xl font-bold" style="color:var(--color-primary)">
                        R$ {{ number_format($pkg->price, 2, ',', '.') }}
                    </span>
                </div>

                <div class="border-t border-d-border pt-3 mb-4 space-y-1.5">
                    <p class="text-sm text-d-muted">
                        <strong class="text-d-text">R$ {{ number_format($pkg->tokens_amount, 2, ',', '.') }}</strong> em créditos
                    </p>
                    @if($isReportPkg)
                    <p class="text-xs flex items-center gap-1.5" style="color:var(--color-primary)">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Inclui acesso ao Relatório
                    </p>
                    @endif
                </div>

                <button
                    x-data="{ loading: false }"
                    @click="
                        loading = true;
                        fetch('{{ route('payment.create') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({ package_id: {{ $pkg->id }} })
                        })
                        .then(r => { console.log('MP status:', r.status); return r.text(); })
                        .then(text => {
                            console.log('MP response:', text);
                            const data = JSON.parse(text);
                            const mp = new MercadoPago('{{ config('services.mercadopago.public_key') }}', { locale: 'pt-BR' });
                            mp.checkout({ preference: { id: data.preference_id }, autoOpen: true });
                        })
                        .catch(e => { loading = false; console.error('MP error:', e); alert('Erro ao iniciar pagamento. Tente novamente.'); })
                    "
                    :disabled="loading"
                    class="btn-primary w-full justify-center !py-2.5 text-sm flex items-center gap-2 disabled:opacity-60"
                    style="{{ $pkg->is_featured ? '' : 'background:var(--color-primary-dark,var(--color-primary))' }}">
                    <svg x-show="!loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                    <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <span x-text="loading ? 'Aguarde...' : 'Comprar agora'"></span>
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Nota ─────────────────────────────────────────────────────────────── --}}
    <p class="text-xs text-d-muted text-center">
        Créditos não expiram · Cada busca consome R$ 0,20 · Pacotes ≥ R$ 24,00 desbloqueiam o Relatório de Conversões
    </p>

</div>
