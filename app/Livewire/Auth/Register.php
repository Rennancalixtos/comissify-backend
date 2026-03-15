<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Register extends Component
{
    public int $step = 1;

    // Step 1
    public string $name     = '';
    public string $email    = '';
    public string $phone    = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Step 2 — OTP
    public string $otp1 = ''; public string $otp2 = '';
    public string $otp3 = ''; public string $otp4 = '';
    public string $otp5 = ''; public string $otp6 = '';
    public ?string $otpError = null;
    public bool $canResend = false;

    public function rules(): array
    {
        return match ($this->step) {
            1 => [
                'name'                  => 'required|string|max:100',
                'email'                 => 'required|email|unique:users,email',
                'phone'                 => 'required|string|min:10|max:20|unique:users,phone',
                'password'              => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ],
            default => [],
        };
    }

    private function isDisposableEmail(string $email): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("https://open.kickbox.com/v1/disposable/{$email}");

            return $response->successful() && $response->json('disposable') === true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function submitStep1(): void
    {
        $this->validate($this->rules());

        if ($this->isDisposableEmail($this->email)) {
            $this->addError('email', 'E-mails temporários não são permitidos. Use um e-mail real.');
            return;
        }

        // Salvar dados em cache — nenhum registro no banco ainda
        Cache::put("register_data:{$this->email}", [
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => preg_replace('/\D/', '', $this->phone),
            'password' => Hash::make($this->password),
        ], now()->addMinutes(30));

        $this->sendOtp($this->email);
        $this->step = 2;
    }

    private function sendOtp(string $email): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("register_otp:{$email}", Hash::make($code), now()->addMinutes(15));

        if (app()->isLocal() && env('SHOPEE_MOCK', false)) {
            session()->flash('otp_dev', "DEV — código OTP: {$code}");
        } else {
            Mail::raw(
                "Seu código de verificação Comissify: {$code}\n\nExpira em 15 minutos.",
                fn ($m) => $m->to($email)->subject('Verificação de e-mail — Comissify')
            );
        }

        $this->canResend = false;
    }

    public function resendOtp(): void
    {
        if (!$this->canResend) return;
        $this->sendOtp($this->email);
    }

    public function submitStep2(): void
    {
        $code = $this->otp1.$this->otp2.$this->otp3.$this->otp4.$this->otp5.$this->otp6;

        $codeHash = Cache::get("register_otp:{$this->email}");

        if (!$codeHash) {
            $this->otpError = 'Código expirado. Reenvie um novo código.';
            return;
        }

        if (!Hash::check($code, $codeHash)) {
            $this->otpError = 'Código incorreto. Verifique e tente novamente.';
            return;
        }

        $data = Cache::get("register_data:{$this->email}");

        if (!$data) {
            $this->otpError = 'Sessão expirada. Reinicie o cadastro.';
            $this->step = 1;
            return;
        }

        // Só agora criamos o usuário no banco
        $user = User::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'],
            'password'          => $data['password'],
            'is_admin'          => false,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);
        $user->ensureTokenWallet();

        Cache::forget("register_otp:{$this->email}");
        Cache::forget("register_data:{$this->email}");

        Auth::login($user);
        $this->redirect(route('connect'));
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.blank', ['title' => 'Criar conta']);
    }
}
