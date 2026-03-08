<div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
    <div class="card max-w-sm w-full">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-5 bg-d-input">
            <svg class="w-7 h-7 text-d-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>

        <h2 class="text-lg font-bold text-d-text mb-2">Conecte sua conta Shopee Afiliados</h2>
        <p class="text-sm text-d-muted mb-6 leading-relaxed">
            Para acessar o Comissify, você precisa estar aprovado no <strong class="text-d-text">Programa de Afiliados da Shopee</strong> e conectar suas credenciais.
        </p>

        <a href="{{ route('connect') }}" class="btn-primary w-full justify-center py-2.5 mb-4">
            → Conectar agora
        </a>

        <p class="text-xs text-d-muted">
            Não tem App ID ainda?
            <a href="https://affiliate.shopee.com.br" target="_blank" class="font-medium" style="color:var(--color-primary)">
                Veja como solicitar ↗
            </a>
        </p>
    </div>
</div>
