<!DOCTYPE html>
<html lang="pt-BR" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectar Shopee — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>{!! \App\Services\ThemeService::cssVars() !!}</style>
</head>
<body class="bg-d-bg min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-lg">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex w-12 h-12 rounded-xl items-center justify-center mb-3"
             style="background:var(--color-primary)">
            <span class="text-white font-bold text-lg">C</span>
        </div>
        <h1 class="text-xl font-bold text-d-text">Conectar conta Shopee</h1>
        <p class="text-sm text-d-muted mt-1">Olá, {{ Auth::user()->name }}</p>
    </div>

    {{-- Pré-requisito --}}
    <div class="alert-warning mb-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="font-semibold text-yellow-400 mb-1">Pré-requisito</p>
                <p class="text-yellow-300/80 text-sm">Para usar esta ferramenta, você precisa estar aprovado no <strong>Programa de Afiliados da Shopee</strong> e ter solicitado a liberação de acesso à API diretamente no app da Shopee.</p>
            </div>
        </div>
    </div>

    {{-- Instruções --}}
    <div class="card mb-4">
        <h3 class="text-sm font-semibold text-d-text mb-3">Como obter seu App ID e Chave</h3>
        <ol class="space-y-2 text-sm text-d-muted list-decimal list-inside">
            <li>Acesse <a href="https://affiliate.shopee.com.br" target="_blank" class="font-medium" style="color:var(--color-primary)">affiliate.shopee.com.br</a></li>
            <li>Vá em <strong class="text-d-text">Ferramentas → API</strong> e solicite a liberação caso ainda não tenha</li>
            <li>Crie um novo App e copie o <strong class="text-d-text">App ID</strong> e a <strong class="text-d-text">Chave de acesso</strong></li>
        </ol>
    </div>

    {{-- Formulário --}}
    <div class="card" x-data="{ showSecret: false }">

        @if (session('error'))
            <div class="alert-error mb-4">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('connect.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-medium text-d-muted mb-1.5" for="app_id">App ID</label>
                <input id="app_id" name="app_id" type="text" value="{{ old('app_id') }}" required
                       class="form-input w-full font-mono" placeholder="Ex: 1234567890"/>
            </div>

            <div>
                <label class="block text-xs font-medium text-d-muted mb-1.5" for="app_secret">Chave de acesso</label>
                <div class="relative">
                    <input id="app_secret" name="app_secret"
                           :type="showSecret ? 'text' : 'password'"
                           required class="form-input w-full pr-10 font-mono" placeholder="••••••••••••••••"/>
                    <button type="button" @click="showSecret = !showSecret"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-d-muted hover:text-d-text">
                        <svg x-show="!showSecret" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="showSecret" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-2.5">
                Conectar minha conta
            </button>
        </form>

        <div class="mt-4 text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-xs text-d-muted hover:text-d-text transition-colors">Sair</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
