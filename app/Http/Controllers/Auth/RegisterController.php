<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function show()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => false,
            'is_active' => true,
        ]);

        // Criar carteira de tokens
        $wallet = $user->ensureTokenWallet();

        // Bônus de boas-vindas
        $bonus = (float) env('NEW_USER_TOKEN_BONUS', 0);
        if ($bonus > 0) {
            $wallet->credit($bonus, 'Bônus de boas-vindas');
        }

        Auth::login($user);

        return redirect()->route('connect');
    }
}
