<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * No modo demonstração, bloqueia o que não faz sentido numa vitrine pública:
 * cadastro, pagamentos, webhooks, troca de credenciais Shopee e painel admin.
 */
class DemoMode
{
    private const BLOCKED = [
        'register',
        'payment.create',
        'payment.webhook',
        'telegram.webhook',
        'connect.store',
        'download.proxy',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        $route = $request->route()?->getName() ?? '';

        if (in_array($route, self::BLOCKED, true) || str_starts_with($route, 'admin.') || $request->is('horizon*', 'log-viewer*')) {
            $message = 'Esta é uma versão de demonstração: cadastro, pagamentos e área administrativa estão desativados.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()->route(auth()->check() ? 'dashboard' : 'login')->with('demo_notice', $message);
        }

        return $next($request);
    }
}
