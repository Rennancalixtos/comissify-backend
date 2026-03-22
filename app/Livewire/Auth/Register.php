<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Locked;
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

    // #[Locked] impede que o cliente altere este valor via payload Livewire,
    // prevenindo flood de email por manipulação do estado público.
    #[Locked]
    public bool $canResend = false;

    public function rules(): array
    {
        return match ($this->step) {
            1 => [
                'name'                  => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\'\-\.]+$/u'],
                'email'                 => 'required|email:rfc|unique:users,email',
                'phone'                 => 'required|digits_between:10,11|unique:users,phone',
                'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
                'password_confirmation' => 'required',
            ],
            default => [],
        };
    }

    public function messages(): array
    {
        return [
            'name.regex'             => 'O nome deve conter apenas letras, espaços, hífens e apóstrofos.',
            'phone.digits_between'   => 'O telefone deve ter 10 ou 11 dígitos (DDD + número).',
        ];
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
        // Sanitize before validation
        $this->name  = trim(preg_replace('/\s+/', ' ', $this->name));
        $this->email = strtolower(trim($this->email));
        $this->phone = preg_replace('/\D/', '', $this->phone);

        $this->validate($this->rules());

        if ($this->isDisposableEmail($this->email)) {
            $this->addError('email', 'E-mails temporários não são permitidos. Use um e-mail real.');
            return;
        }

        // Salvar dados em cache — nenhum registro no banco ainda
        Cache::put("register_data:{$this->email}", [
            'name'     => $this->name,
            'email'    => $this->email,
            'phone'    => $this->phone,
            'password' => Hash::make($this->password),
        ], now()->addMinutes(30));

        $this->sendOtp($this->email);
        $this->step = 2;
    }

    private function sendOtp(string $email): void
    {
        // Rate limit server-side: máximo 3 reenvios por email a cada 10 minutos.
        $rateLimitKey = "otp_send_rate:{$email}";
        $sends        = (int) Cache::get($rateLimitKey, 0);

        if ($sends >= 3) {
            $this->otpError  = 'Limite de reenvios atingido. Tente novamente em alguns minutos.';
            $this->canResend = false;
            return;
        }

        Cache::put($rateLimitKey, $sends + 1, now()->addMinutes(10));

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("register_otp:{$email}", Hash::make($code), now()->addMinutes(15));

        if (app()->isLocal() && env('SHOPEE_MOCK', false)) {
            session()->flash('otp_dev', "DEV — código OTP: {$code}");
        } else {
            Mail::send(
                'emails.otp',
                ['code' => $code],
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

        $attemptsKey = "register_otp_attempts:{$this->email}";
        $attempts    = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= 5) {
            $this->otpError = 'Muitas tentativas incorretas. Solicite um novo código.';
            Cache::forget("register_otp:{$this->email}");
            Cache::forget($attemptsKey);
            $this->canResend = true;
            return;
        }

        if (!Hash::check($code, $codeHash)) {
            Cache::put($attemptsKey, $attempts + 1, now()->addMinutes(15));
            $remaining = 4 - $attempts;
            $this->otpError = "Código incorreto. {$remaining} tentativa(s) restante(s).";
            return;
        }

        Cache::forget($attemptsKey);

        $data = Cache::get("register_data:{$this->email}");

        if (!$data) {
            $this->otpError = 'Sessão expirada. Reinicie o cadastro.';
            $this->step = 1;
            return;
        }

        // Só agora criamos o usuário no banco.
        // is_admin e is_active não estão no $fillable — definidos via forceFill para
        // garantir valores corretos sem expor a mass assignment.
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => $data['password'],
        ]);
        $user->forceFill([
            'is_admin'          => false,
            'is_active'         => true,
            'email_verified_at' => now(),
        ])->save();
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
