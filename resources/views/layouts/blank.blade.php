<!DOCTYPE html>
<html lang="pt-BR" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Comissify' }} — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"></noscript>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>{!! \App\Services\ThemeService::cssVars() !!}</style>
</head>
<body class="bg-d-bg text-d-text antialiased min-h-screen flex items-center justify-center p-4">

{{ $slot }}

@livewireScripts
</body>
</html>
