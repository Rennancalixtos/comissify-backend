<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conectar Shopee — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireScripts
    <style>{!! \App\Services\ThemeService::cssVars() !!}</style>
</head>
<body class="bg-d-bg min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-lg"
     x-data="{
        guide: false,
        current: 0,
        steps: [
            { img: '/images/connect-guide/1.jpeg',  caption: 'Abra o app da Shopee e toque no ícone de perfil (Eu) na barra inferior da tela.' },
            { img: '/images/connect-guide/2.jpeg',  caption: 'Role a tela para baixo até encontrar Criadores e Afiliados e toque nessa opção.' },
            { img: '/images/connect-guide/3.jpeg',  caption: 'Você já está na área de afiliados. Toque na aba Conta no canto inferior direito.' },
            { img: '/images/connect-guide/4.jpg',   caption: 'Na tela Conta, toque no ícone de engrenagem no canto superior direito para abrir as configurações.' },
            { img: '/images/connect-guide/5.jpeg',  caption: 'Toque em Configurações da Conta para ver os detalhes da sua conta de afiliado.' },
            { img: '/images/connect-guide/6.jpeg',  caption: 'Localize o campo ID de Afiliado e copie o número exibido — você vai precisar dele mais adiante.' },
            { img: '/images/connect-guide/7.jpg',   caption: 'Volte para a tela Conta e toque em Central de Ajuda para solicitar a ativação da API.' },
            { img: '/images/connect-guide/8.jpeg',  caption: 'Role até o final da página da Central de Ajuda para encontrar a seção de Acesso Rápido.' },
            { img: '/images/connect-guide/9.jpeg',  caption: 'Em Acesso Rápido, toque em E-mail para abrir o formulário de solicitação.' },
            { img: '/images/connect-guide/10.jpeg', caption: 'No formulário, responda Sim para confirmar que já é afiliado. Novos campos serão exibidos automaticamente.' },
            { img: '/images/connect-guide/11.jpeg', caption: 'Preencha seu número de telefone e o ID de Afiliado que você copiou anteriormente.' },
            { img: '/images/connect-guide/12.jpeg', caption: 'No campo de tema, selecione Tenho dúvidas/dificuldades com meu cadastro.' },
            { img: '/images/connect-guide/13.jpeg', caption: 'Selecione o cenário Quero ativar a API e toque em Enviar. O retorno da Shopee costuma chegar em até 5 dias úteis.' }
        ],
        showSecret: false,
        prev() { if (this.current > 0) this.current--; },
        next() { if (this.current < this.steps.length - 1) this.current++; }
     }"
     @keydown.escape.window="guide = false"
     @keydown.arrowright.window="if(guide) next()"
     @keydown.arrowleft.window="if(guide) prev()"
>

    {{-- ── Modal Guia ───────────────────────────────────────────────────────── --}}
    <div x-show="guide" x-transition.opacity class="fixed inset-0 z-40 bg-black/70" @click="guide = false" style="display:none"></div>

    <div x-show="guide" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display:none">
        <div class="relative w-full max-w-sm rounded-2xl overflow-hidden shadow-2xl flex flex-col" style="background:#1C1C1C; max-height:90vh">

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 py-3 flex-shrink-0" style="border-bottom:1px solid #2A2A2A">
                <span class="text-sm font-semibold text-d-text">Como obter seu App ID</span>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-d-muted" x-text="(current + 1) + ' / ' + steps.length"></span>
                    <button @click="guide = false" class="text-d-muted hover:text-d-text transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Imagem --}}
            <div class="relative overflow-hidden flex-shrink-0" style="height:420px; background:#141414">
                <template x-for="(step, index) in steps" :key="index">
                    <img
                        x-show="current === index"
                        x-transition.opacity
                        :src="step.img"
                        :alt="'Passo ' + (index + 1)"
                        class="absolute inset-0 w-full h-full object-contain"
                    />
                </template>
            </div>

            {{-- Legenda --}}
            <div class="px-4 py-3 flex-shrink-0" style="min-height:80px">
                <p class="text-sm text-d-muted leading-relaxed" x-text="steps[current].caption"></p>
            </div>

            {{-- Dots --}}
            <div class="flex justify-center gap-1.5 pb-2 flex-shrink-0 flex-wrap px-4">
                <template x-for="(step, index) in steps" :key="index">
                    <button @click="current = index"
                            class="h-1.5 rounded-full transition-all"
                            :style="current === index ? 'background:var(--color-primary); width:16px' : 'background:#444; width:6px'">
                    </button>
                </template>
            </div>

            {{-- Navegação --}}
            <div class="flex gap-2 px-4 pb-4 flex-shrink-0">
                <button @click="prev" :disabled="current === 0"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-all disabled:opacity-30"
                        style="background:#272727; color:#9CA3AF">
                    Anterior
                </button>
                <button @click="next" :disabled="current === steps.length - 1"
                        class="flex-1 py-2 rounded-lg text-sm font-medium transition-all disabled:opacity-30"
                        :style="current < steps.length - 1 ? 'background:var(--color-primary);color:#fff' : 'background:#272727;color:#9CA3AF'">
                    Próximo
                </button>
            </div>
        </div>
    </div>

    {{-- ── Página ───────────────────────────────────────────────────────────── --}}

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
        <ol class="space-y-2 text-sm text-d-muted list-decimal list-inside mb-4">
            <li>Acesse <a href="https://affiliate.shopee.com.br/open_api" target="_blank" class="font-medium" style="color:var(--color-primary)">affiliate.shopee.com.br</a> — se já estiver logado, você será direcionado direto ao painel</li>
            <li>No painel lateral esquerdo, role até o final. Se a opção não aparecer, feche os menus abertos até revelar <strong class="text-d-text">Abrir API</strong></li>
            <li>Acesse <strong class="text-d-text">Abrir API</strong> — se o botão <strong class="text-d-text">Aplicar</strong> estiver disponível, clique nele para solicitar a liberação</li>
            <li>Após liberado, copie o <strong class="text-d-text">App ID</strong> e a <strong class="text-d-text">Chave de acesso</strong> e cole nos campos abaixo</li>
        </ol>
        <div style="border-top:1px solid #2A2A2A; padding-top:12px">
            <p class="text-xs text-d-muted mb-2">Saiba como solicitar a liberação de API:</p>
            <button @click="guide = true; current = 0" type="button"
                    class="inline-flex items-center gap-1.5 text-sm font-medium transition-colors"
                    style="color:var(--color-primary)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2zM12 9v4"/>
                </svg>
                Ver passo a passo.
            </button>
        </div>
    </div>

    {{-- Formulário --}}
    <div class="card">

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
