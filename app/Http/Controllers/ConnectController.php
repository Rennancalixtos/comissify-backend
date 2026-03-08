<?php

namespace App\Http\Controllers;

use App\Models\UserCredential;
use App\Services\ShopeeAffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectController extends Controller
{
    public function show()
    {
        // Já conectado → redirecionar para dashboard
        if (Auth::user()->isShopeeConnected()) {
            return redirect()->route('dashboard');
        }

        return view('connect');
    }

    public function store(Request $request, ShopeeAffiliateService $shopee)
    {
        $request->validate([
            'app_id'     => 'required|string',
            'app_secret' => 'required|string',
        ]);

        // Em modo mock, aceita qualquer credencial sem chamar a API
        if (! env('SHOPEE_MOCK', false)) {
            $tempCred = new UserCredential([
                'user_id'    => Auth::id(),
                'app_id'     => $request->app_id,
                'app_secret' => $request->app_secret,
            ]);

            $result = $shopee->getProducts($tempCred, ['limit' => 1]);

            if (isset($result['error'])) {
                return back()
                    ->withInput($request->only('app_id'))
                    ->with('error', $result['error']);
            }
        }

        // Salvar credenciais criptografadas
        UserCredential::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'app_id'       => $request->app_id,
                'app_secret'   => $request->app_secret,
                'is_connected' => true,
                'connected_at' => now(),
            ]
        );

        // Garantir carteira de tokens
        Auth::user()->ensureTokenWallet();

        return redirect()->route('dashboard');
    }
}
