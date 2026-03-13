<!DOCTYPE html>
<html lang="pt-BR" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Automatize. Afilie. Lucre.">
    <title>{{ $title ?? 'Dashboard' }} — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>{!! \App\Services\ThemeService::cssVars() !!}</style>
</head>
<body class="bg-d-bg text-d-text antialiased">

<div class="flex min-h-screen">

    {{-- Sidebar: fixed no mobile, sticky no desktop --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-60 flex flex-col
                  transform transition-transform duration-200
                  lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:flex-shrink-0"
           style="background:#1C1C1C;box-shadow:1px 0 0 rgba(255,255,255,0.04)"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-5 py-5" style="box-shadow:0 1px 0 rgba(255,255,255,0.05)">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                 style="background:var(--color-primary)">
                <span class="text-white font-bold text-sm">C</span>
            </div>
            <div>
                <p class="text-sm font-bold text-d-text leading-tight">Comissify</p>
                <p class="text-xs text-d-muted">Área do cliente</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
               style="{{ request()->routeIs('dashboard') ? 'background:color-mix(in srgb,var(--color-primary) 15%,transparent);color:var(--color-primary);' : 'color:#9CA3AF;' }}"
               :class="{{ request()->routeIs('dashboard') ? 'false' : 'true' }} && 'hover:bg-white/5 hover:text-d-text'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                Buscar Produtos
            </a>

            <a href="{{ route('posts') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
               style="{{ request()->routeIs('posts') ? 'background:color-mix(in srgb,var(--color-primary) 15%,transparent);color:var(--color-primary);' : 'color:#9CA3AF;' }}"
               :class="{{ request()->routeIs('posts') ? 'false' : 'true' }} && 'hover:bg-white/5 hover:text-d-text'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/>
                </svg>
                Criador
            </a>

            <a href="{{ route('report') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
               style="{{ request()->routeIs('report') ? 'background:color-mix(in srgb,var(--color-primary) 15%,transparent);color:var(--color-primary);' : 'color:#9CA3AF;' }}"
               :class="{{ request()->routeIs('report') ? 'false' : 'true' }} && 'hover:bg-white/5 hover:text-d-text'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
                Relatório
            </a>

            <a href="{{ route('credits') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
               style="{{ request()->routeIs('credits') ? 'background:color-mix(in srgb,var(--color-primary) 15%,transparent);color:var(--color-primary);' : 'color:#9CA3AF;' }}"
               :class="{{ request()->routeIs('credits') ? 'false' : 'true' }} && 'hover:bg-white/5 hover:text-d-text'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                Comprar Créditos
            </a>
        </nav>

        {{-- User footer --}}
        <div class="px-4 py-4" style="box-shadow:0 -1px 0 rgba(255,255,255,0.05)">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0"
                     style="background:color-mix(in srgb,var(--color-primary) 20%,transparent)">
                    <span class="text-xs font-bold" style="color:var(--color-primary)">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-d-text truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs font-medium truncate" style="color:var(--color-primary)">
                        R$ {{ number_format(Auth::user()->tokenBalance(), 2, ',', '.') }} em créditos
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-d-muted hover:text-d-text transition-colors">Sair</button>
            </form>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/60 lg:hidden" style="display:none"></div>

    {{-- Main --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="px-6 py-3.5 flex items-center gap-4 flex-shrink-0"
                style="background:#1C1C1C;box-shadow:0 1px 0 rgba(255,255,255,0.05)">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-d-muted hover:text-d-text flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-sm font-semibold text-d-text">{{ $title ?? 'Dashboard' }}</h1>
        </header>

        <main class="flex-1 p-6 overflow-auto">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
