<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureShopeeConnected
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()?->isShopeeConnected()) {
            return redirect()->route('connect');
        }

        return $next($request);
    }
}
