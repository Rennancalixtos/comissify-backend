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

        $appIdHash = hash('sha256', trim($request->app_id));

        // Bloquear AppID já vinculado a outra conta
        $alreadyUsed = UserCredential::where('app_id_hash', $appIdHash)
            ->where('user_id', '!=', Auth::id())
            ->exists();

        if ($alreadyUsed) {
            return back()
                ->withInput($request->only('app_id'))
                ->with('error', 'Este AppID já está vinculado a outra conta.');
        }

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

        // Verificar se é a primeira vez que este AppID é conectado (para o bônus)
        $isNewAppId = !UserCredential::where('app_id_hash', $appIdHash)->exists();

        UserCredential::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'app_id'       => $request->app_id,
                'app_id_hash'  => $appIdHash,
                'app_secret'   => $request->app_secret,
                'is_connected' => true,
                'connected_at' => now(),
            ]
        );

        $user = Auth::user();
        $user->ensureTokenWallet();

        // Bônus de boas-vindas — apenas na primeira conexão deste AppID
        if ($isNewAppId) {
            $bonus = (float) env('NEW_USER_TOKEN_BONUS', 0);
            if ($bonus > 0) {
                $user->ensureTokenWallet()->credit($bonus, 'Bônus de boas-vindas');
            }
        }

        return redirect()->route('dashboard');
    }
}
