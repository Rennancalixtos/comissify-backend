<?php

namespace App\Http\Controllers;

use App\Models\UserCredential;
use App\Services\ShopeeAffiliateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

        // Bônus de boas-vindas — mutex atômico via Cache::add() para evitar double-credit
        // em requisições concorrentes com o mesmo AppID.
        $bonusLockKey = "bonus_granted:{$appIdHash}";
        $bonus        = (float) config('services.business.new_user_token_bonus', 0);

        if ($bonus > 0 && Cache::add($bonusLockKey, true, now()->addSeconds(30))) {
            // Verifica no banco se o AppID realmente é novo (dentro do lock)
            $alreadyBonused = UserCredential::where('app_id_hash', $appIdHash)
                ->where('user_id', '!=', Auth::id())
                ->exists();

            if (!$alreadyBonused) {
                $user->ensureTokenWallet()->credit($bonus, 'Bônus de boas-vindas');
            }
        }

        return redirect()->route('dashboard');
    }
}
