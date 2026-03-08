<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>{!! \App\Services\ThemeService::cssVars() !!}</style>
</head>
<body class="bg-d-bg min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <div class="inline-flex w-14 h-14 rounded-2xl items-center justify-center mb-4"
             style="background:var(--color-primary)">
            <span class="text-white font-bold text-2xl">C</span>
        </div>
        <h1 class="text-xl font-bold text-d-text">Comissify</h1>
        <p class="text-sm text-d-muted mt-1">Crie sua conta gratuitamente</p>
    </div>

    <div class="card">
        <h2 class="text-base font-semibold text-d-text mb-5">Dados de acesso</h2>

        <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
            @csrf

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div>
                <label class="block text-xs font-medium text-d-muted mb-1.5" for="name">Nome completo</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                       class="form-input w-full" placeholder="Seu nome" autofocus />
            </div>

            <div>
                <label class="block text-xs font-medium text-d-muted mb-1.5" for="email">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                       class="form-input w-full" placeholder="seu@email.com" />
            </div>

            <div>
                <label class="block text-xs font-medium text-d-muted mb-1.5" for="password">Senha</label>
                <input id="password" name="password" type="password" required
                       class="form-input w-full" placeholder="Mínimo 8 caracteres" />
            </div>

            <div>
                <label class="block text-xs font-medium text-d-muted mb-1.5" for="password_confirmation">Confirmar senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="form-input w-full" placeholder="••••••••" />
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-2.5 mt-1">Criar conta</button>
        </form>

        <p class="mt-5 text-center text-sm text-d-muted">
            Já tem conta?
            <a href="{{ route('login') }}" class="font-medium" style="color:var(--color-primary)">Entrar</a>
        </p>
    </div>
</div>

</body>
</html>
