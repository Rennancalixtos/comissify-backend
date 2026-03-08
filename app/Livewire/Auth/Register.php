<?php

namespace App\Livewire\Auth;

use App\Models\EmailVerification;
use App\Models\User;
use App\Models\UserPaymentMethod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Register extends Component
{
    public int $step = 1;

    // Step 1
    public string $name     = '';
    public string $email    = '';
    public string $password = '';
    public string $password_confirmation = '';

    // Step 2 — OTP
    public string $otp1 = ''; public string $otp2 = '';
    public string $otp3 = ''; public string $otp4 = '';
    public string $otp5 = ''; public string $otp6 = '';
    public ?string $otpError = null;
    public bool $canResend = false;

    // Step 3 — Payment
    public string $cardNumber = '';
    public string $cardName   = '';
    public string $cardExpiry = '';
    public string $cardCvv    = '';

    public ?int $userId = null;

    public function rules(): array
    {
        return match ($this->step) {
            1 => [
                'name'                  => 'required|string|max:100',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|min:8|confirmed',
                'password_confirmation' => 'required',
            ],
            3 => [
                'cardNumber' => 'required|min:16',
                'cardName'   => 'required|string|max:100',
                'cardExpiry' => ['required', 'regex:/^\d{2}\/\d{2}$/'],
                'cardCvv'    => 'required|digits_between:3,4',
            ],
            default => [],
        };
    }

    // ── Step 1 ────────────────────────────────────────────────────────

    public function submitStep1(): void
    {
        $this->validate($this->rules());

        $user = User::create([
            'name'              => $this->name,
            'email'             => $this->email,
            'password'          => Hash::make($this->password),
            'is_admin'          => false,
            'is_active'         => true,
            'email_verified_at' => null,
        ]);
        $user->ensureTokenWallet();

        $this->userId = $user->id;
        $this->sendOtp($user);
        $this->step = 2;
    }

    private function sendOtp(User $user): void
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerification::where('user_id', $user->id)->delete();
        EmailVerification::create([
            'user_id'    => $user->id,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addMinutes(15),
        ]);

        if (env('SHOPEE_MOCK', false)) {
            session()->flash('otp_dev', "DEV — código OTP: {$code}");
        } else {
            try {
                Mail::raw(
                    "Seu código de verificação Comissify: {$code}\n\nExpira em 15 minutos.",
                    fn ($m) => $m->to($user->email)->subject('Verificação de e-mail — Comissify')
                );
            } catch (\Throwable) {
                session()->flash('otp_dev', "Email falhou. DEV — código OTP: {$code}");
            }
        }

        $this->canResend = false;
    }

    public function resendOtp(): void
    {
        if (!$this->canResend || !$this->userId) return;
        $user = User::find($this->userId);
        if ($user) $this->sendOtp($user);
    }

    // ── Step 2 ────────────────────────────────────────────────────────

    public function submitStep2(): void
    {
        $code = $this->otp1.$this->otp2.$this->otp3.$this->otp4.$this->otp5.$this->otp6;
        $verification = EmailVerification::where('user_id', $this->userId)->latest()->first();

        if (!$verification || $verification->isExpired()) {
            $this->otpError = 'Código expirado. Reenvie um novo código.';
            return;
        }
        if (!Hash::check($code, $verification->code_hash)) {
            $this->otpError = 'Código incorreto. Verifique e tente novamente.';
            return;
        }

        $this->otpError = null;
        $user = User::findOrFail($this->userId);
        $user->update(['email_verified_at' => now()]);
        $verification->delete();

        $bonus = (float) env('NEW_USER_TOKEN_BONUS', 0);
        if ($bonus > 0) {
            $user->ensureTokenWallet()->credit($bonus, 'Bônus de boas-vindas');
        }

        $this->step = 3;
    }

    // ── Step 3 ────────────────────────────────────────────────────────

    public function submitStep3(): void
    {
        $this->validate($this->rules());

        $number = preg_replace('/\D/', '', $this->cardNumber);
        $brand  = match (true) {
            str_starts_with($number, '4') => 'Visa',
            str_starts_with($number, '5') => 'Mastercard',
            str_starts_with($number, '6') => 'Elo',
            default                       => 'Outro',
        };

        UserPaymentMethod::create([
            'user_id'          => $this->userId,
            'card_last_four'   => substr($number, -4),
            'card_brand'       => $brand,
            'card_holder_name' => $this->cardName,
            'card_expiry'      => $this->cardExpiry,
            'is_default'       => true,
        ]);

        $this->finishRegistration();
    }

    public function skipStep3(): void
    {
        $this->finishRegistration();
    }

    private function finishRegistration(): void
    {
        Auth::loginUsingId($this->userId);
        $this->redirect(route('connect'));
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.blank', ['title' => 'Criar conta']);
    }
}
