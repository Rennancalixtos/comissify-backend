<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\ProcessProductCommand::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // Confiar apenas na VPS de borda (74.208.125.134) e nas redes internas Docker/Traefik.
        // NUNCA usar '*' pois permitiria que qualquer host falsificasse X-Forwarded-For,
        // inutilizando rate limiting e Fail2ban.
        $middleware->trustProxies(at: [
            '74.208.125.134',   // VPS IONOS (edge proxy)
            '10.0.0.0/8',       // redes internas Docker / FRP
            '172.16.0.0/12',    // bridge networks Docker
            '192.168.0.0/16',   // rede local física
        ]);
        $middleware->web(append: [\App\Http\Middleware\DemoMode::class]);

        $middleware->alias([
            'shopee.connected' => \App\Http\Middleware\EnsureShopeeConnected::class,
            'admin'            => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            '/webhook/mercadopago',
            '/webhook/telegram',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();