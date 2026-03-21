<!DOCTYPE html>
<html lang="pt-BR" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>{!! \App\Services\ThemeService::cssVars() !!}</style>
</head>

<body class="bg-d-bg text-d-text antialiased">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-60 flex flex-col
                  transform transition-transform duration-200 ease-in-out
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
                <span class="text-xs font-medium px-1.5 py-0.5 rounded"
                      style="background:color-mix(in srgb,var(--color-primary) 20%,transparent);color:var(--color-primary)">Admin</span>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            @php
                $nav = [
                    ['href' => route('admin.dashboard'),         'label' => 'Dashboard',        'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['href' => route('admin.queue.index'),       'label' => 'Fila de Postagem',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['href' => route('admin.tokens.index'),      'label' => 'Tokens',            'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['href' => route('admin.users.index'),       'label' => 'Usuários',          'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['href' => route('admin.emails.index'),      'label' => 'E-mails',           'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['href' => route('admin.screenshots.index'), 'label' => 'Screenshots',       'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['href' => route('admin.settings.index'),    'label' => 'Configurações',     'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ];
                $currentUrl = request()->url();
            @endphp

            @foreach ($nav as $item)
                @php $active = $currentUrl === $item['href']; @endphp
                <a href="{{ $item['href'] }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-150"
                   style="{{ $active ? 'background:color-mix(in srgb,var(--color-primary) 15%,transparent);color:var(--color-primary)' : 'color:#9CA3AF' }}"
                   @if(!$active) onmouseover="this.style.background='rgba(255,255,255,0.05)';this.style.color='#F1F1F1'" onmouseout="this.style.background='';this.style.color='#9CA3AF'" @endif>
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        {{-- Footer --}}
        <div class="px-4 py-4 space-y-2" style="box-shadow:0 -1px 0 rgba(255,255,255,0.05)">
            <a href="{{ route('horizon.index') }}" target="_blank"
               class="flex items-center gap-2 text-xs text-d-muted hover:text-d-text transition-colors">
                <span class="w-2 h-2 rounded-full bg-green-400"></span>
                Laravel Horizon
            </a>
            <a href="/log-viewer" target="_blank"
               class="flex items-center gap-2 text-xs text-d-muted hover:text-d-text transition-colors">
                <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                Log Viewer
            </a>
        </div>
    </aside>

    {{-- Overlay mobile --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black/60 lg:hidden" style="display:none"></div>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top bar --}}
        <header class="px-6 py-3.5 flex items-center gap-4 flex-shrink-0"
                style="background:#1C1C1C;box-shadow:0 1px 0 rgba(255,255,255,0.05)">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-d-muted hover:text-d-text flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="text-base font-semibold text-d-text">{{ $title ?? 'Dashboard' }}</h1>
            <div class="ml-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-d-muted hover:text-d-text transition-colors">Sair</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-auto">
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
