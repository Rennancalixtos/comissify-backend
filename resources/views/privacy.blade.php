<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade | Comissify</title>
    <meta name="description" content="Política de Privacidade do Comissify — como coletamos, usamos e protegemos seus dados.">
    <link rel="canonical" href="{{ route('privacy') }}">
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
    <h1 class="font-display text-3xl font-extrabold text-d-text mb-2">Política de Privacidade</h1>
    <p class="text-d-muted text-sm mb-10">Última atualização: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>

    <div class="flex flex-col gap-8 text-sm leading-relaxed text-d-muted">

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">1. Quem somos</h2>
            <p>O Comissify (<strong class="text-d-text">comissify.shop</strong>) é uma plataforma para afiliados Shopee que permite buscar produtos, gerar links afiliados e acompanhar conversões. Responsável pelo tratamento dos dados: Comissify.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">2. Dados que coletamos</h2>
            <ul class="list-disc list-inside flex flex-col gap-2">
                <li><strong class="text-d-text">Nome, e-mail e telefone</strong> — informados no cadastro.</li>
                <li><strong class="text-d-text">App ID e Chave de acesso Shopee</strong> — fornecidos voluntariamente para conectar a conta de afiliado. Armazenados de forma criptografada.</li>
                <li><strong class="text-d-text">Dados de uso</strong> — buscas realizadas, links gerados e transações de créditos.</li>
                <li><strong class="text-d-text">Dados de pagamento</strong> — processados pelo Mercado Pago. Não armazenamos número de cartão.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">3. Como usamos seus dados</h2>
            <ul class="list-disc list-inside flex flex-col gap-2">
                <li>Verificar sua identidade via OTP no cadastro.</li>
                <li>Operar a plataforma e gerar links afiliados.</li>
                <li>Processar pagamentos e gerenciar créditos.</li>
                <li>Enviar comunicações transacionais (ex: confirmação de e-mail).</li>
            </ul>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">4. Compartilhamento de dados</h2>
            <p>Não vendemos seus dados. Compartilhamos apenas com prestadores de serviço essenciais:</p>
            <ul class="list-disc list-inside flex flex-col gap-2 mt-2">
                <li><strong class="text-d-text">Mercado Pago</strong> — processamento de pagamentos.</li>
                <li><strong class="text-d-text">MailerSend</strong> — envio de e-mails transacionais.</li>
                <li><strong class="text-d-text">Shopee Affiliate API</strong> — geração de links afiliados com suas credenciais.</li>
            </ul>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">5. Seus direitos (LGPD)</h2>
            <p>Você pode a qualquer momento solicitar acesso, correção ou exclusão dos seus dados. Entre em contato pelo e-mail disponível no rodapé.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">6. Segurança</h2>
            <p>Seus dados são armazenados em servidores seguros. Credenciais Shopee são criptografadas em repouso. Utilizamos HTTPS em todas as comunicações.</p>
        </section>

        <section>
            <h2 class="font-display font-bold text-d-text text-lg mb-3">7. Contato</h2>
            <p>Dúvidas sobre esta política? Entre em contato: <strong class="text-d-text">contato@comissify.shop</strong></p>
        </section>

    </div>
</main>

{{-- Footer --}}
<footer class="py-8 border-t border-d-border mt-16" style="background:#0a0a0a">
    <div class="max-w-4xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <span class="text-xs text-d-muted">© {{ date('Y') }} Comissify</span>
        <div class="flex items-center gap-5 text-xs text-d-muted">
            <a href="{{ route('home') }}" class="hover:text-d-text transition-colors">Início</a>
            <a href="{{ route('terms') }}" class="hover:text-d-text transition-colors">Termos de uso</a>
            <a href="{{ route('privacy') }}" class="hover:text-d-text transition-colors" style="color:var(--color-primary)">Privacidade</a>
        </div>
    </div>
</footer>

</body>
</html>
