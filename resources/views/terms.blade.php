<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Uso | Comissify</title>
    <meta name="description" content="Termos de Uso do Comissify — regras de utilização da plataforma.">
    <link rel="canonical" href="{{ route('terms') }}">
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
    <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-sm font-display">C</span>
            </div>
            <span class="font-bold text-d-text font-display tracking-tight">Comissify</span>
        </a>
        <a href="{{ route('home') }}" class="text-sm text-d-muted hover:text-white transition-colors">← Voltar</a>
    </div>
</nav>

{{-- Content --}}
<main class="max-w-4xl mx-auto px-6 py-16">
    <h1 class="font-display text-3xl font-extrabold text-d-text mb-2">Termos de Uso</h1>
    <p class="text-d-muted text-sm mb-10">Última atualização: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>

    <div class="flex flex-col gap-8 text-sm leading-relaxed text-d-muted">

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">1. Aceitação</h2>
            <p>Ao criar uma conta no Comissify, você concorda com estes Termos de Uso. Se não concordar, não utilize a plataforma.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">2. O serviço</h2>
            <p>O Comissify é uma ferramenta para afiliados Shopee que facilita a busca de produtos, geração de links afiliados e acompanhamento de comissões. O uso requer participação prévia no Programa de Afiliados da Shopee e credenciais de API válidas.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">3. Créditos</h2>
            <ul class="list-disc list-inside flex flex-col gap-2">
                <li>Novos usuários recebem créditos de bônus para testar a plataforma.</li>
                <li>Créditos adicionais podem ser adquiridos via Mercado Pago.</li>
                <li>Créditos não são reembolsáveis após utilização.</li>
                <li>Cada busca e geração de link consome créditos conforme tabela vigente.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">4. Uso aceitável</h2>
            <p>É proibido:</p>
            <ul class="list-disc list-inside flex flex-col gap-2 mt-2">
                <li>Usar a plataforma para fins ilegais ou que violem os Termos da Shopee.</li>
                <li>Tentar burlar o sistema de créditos ou registro.</li>
                <li>Compartilhar credenciais de acesso com terceiros.</li>
                <li>Criar múltiplas contas para obter bônus repetidamente.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">5. Responsabilidade</h2>
            <p>O Comissify é uma ferramenta auxiliar. Não garantimos resultados de vendas ou comissões. As comissões são definidas pela Shopee e podem variar a qualquer momento.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">6. Encerramento de conta</h2>
            <p>Reservamo-nos o direito de suspender contas que violem estes termos. Você pode solicitar a exclusão da sua conta a qualquer momento pelo e-mail de contato.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">7. Alterações</h2>
            <p>Podemos atualizar estes termos a qualquer momento. Mudanças relevantes serão comunicadas por e-mail.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">8. Contato</h2>
            <p>Dúvidas: <strong class="text-d-text">contato@comissify.shop</strong></p>
        </section>

    </div>
</main>

{{-- Footer --}}
<footer class="py-8 border-t border-d-border mt-16" style="background:#0a0a0a">
    <div class="max-w-4xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span class="text-xs text-d-muted">© {{ date('Y') }} Comissify</span>
        <div class="flex items-center gap-5 text-xs text-d-muted">
            <a href="{{ route('home') }}" class="hover:text-d-text transition-colors">Início</a>
            <a href="{{ route('terms') }}" class="hover:text-d-text transition-colors" style="color:var(--color-primary)">Termos de uso</a>
            <a href="{{ route('privacy') }}" class="hover:text-d-text transition-colors">Privacidade</a>
        </div>
    </div>
</footer>

</body>
</html>
