<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 justify-center mb-1">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-lg">C</span>
            </div>
        </a>
        <p class="text-sm text-d-muted mt-2">Comissify</p>
    </div>

    {{-- Stepper --}}
    <div class="flex items-center justify-center gap-2 mb-8">
        @foreach([1 => 'Dados', 2 => 'Verificação'] as $n => $label)
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                 style="{{ $step >= $n
                    ? 'background:var(--color-primary);color:#fff'
                    : 'background:#2E2E2E;color:#9CA3AF' }}">
                {{ $step > $n ? '✓' : $n }}
            </div>
            <span class="text-xs hidden sm:block {{ $step === $n ? 'text-d-text font-medium' : 'text-d-muted' }}">{{ $label }}</span>
        </div>
        @if($n < 2)
        <div class="w-8 h-px flex-shrink-0" style="background:{{ $step > $n ? 'var(--color-primary)' : '#2E2E2E' }}"></div>
        @endif
        @endforeach
    </div>

    {{-- DEV OTP helper --}}
    @if(session('otp_dev'))
    <div class="alert-info mb-4 font-mono text-xs">{{ session('otp_dev') }}</div>
    @endif

    {{-- ── Step 1: Dados pessoais ──────────────────────────────────── --}}
    @if($step === 1)
    <div class="card">
        <h2 class="text-base font-semibold text-d-text mb-5">Dados de acesso</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Nome completo</label>
                <input wire:model="name" type="text" maxlength="100"
                       class="form-input w-full" placeholder="Seu nome" autofocus
                       x-on:input="$event.target.value = $event.target.value.replace(/[^a-zA-ZÀ-ÿ\s'\-\.]/g, '')"/>
                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">E-mail</label>
                <input wire:model="email" type="email" maxlength="254"
                       class="form-input w-full" placeholder="seu@email.com"
                       x-on:blur="$event.target.value = $event.target.value.trim().toLowerCase(); $wire.set('email', $event.target.value)"/>
                @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Telefone (WhatsApp)</label>
                <input wire:model.blur="phone" type="tel" inputmode="numeric" maxlength="15"
                       class="form-input w-full" placeholder="(11) 99999-9999"
                       x-on:input="
                           let v = $event.target.value.replace(/\D/g,'').substring(0,11);
                           if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
                           else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d*)/, '($1) $2-$3');
                           else if (v.length > 2) v = v.replace(/^(\d{2})(\d*)/, '($1) $2');
                           else if (v.length) v = '(' + v;
                           $event.target.value = v;
                       "/>
                @error('phone') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Senha</label>
                <input wire:model="password" type="password" class="form-input w-full" placeholder="Mínimo 8 caracteres"/>
                @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Confirmar senha</label>
                <input wire:model="password_confirmation" type="password" class="form-input w-full" placeholder="••••••••"/>
            </div>
            <button wire:click="submitStep1" wire:loading.attr="disabled" class="btn-primary w-full justify-center py-2.5">
                <span wire:loading.remove wire:target="submitStep1">Continuar</span>
                <span wire:loading wire:target="submitStep1">Aguarde...</span>
            </button>
        </div>
        <p class="mt-4 text-center text-sm text-d-muted">
            Já tem conta? <a href="{{ route('login') }}" class="font-medium" style="color:var(--color-primary)">Entrar</a>
        </p>
    </div>
    @endif

    {{-- ── Step 2: Verificação de e-mail ───────────────────────────── --}}
    @if($step === 2)
    <div class="card" x-data="{
        countdown: 60,
        timer: null,
        start() {
            this.timer = setInterval(() => {
                if (this.countdown > 0) { this.countdown--; }
                else { clearInterval(this.timer); $wire.set('canResend', true); }
            }, 1000);
        }
    }" x-init="start()">
        <h2 class="text-base font-semibold text-d-text mb-2">Verificar e-mail</h2>
        <p class="text-sm text-d-muted mb-6">Enviamos um código de 6 dígitos para <strong class="text-d-text">{{ $email }}</strong></p>

        @if($otpError)
        <div class="alert-error mb-4">{{ $otpError }}</div>
        @endif

        {{-- OTP inputs --}}
        <div class="flex gap-2 justify-center mb-6"
             x-data="{
                 move(e, next, prev) {
                     if (e.key === 'Backspace' && e.target.value === '' && prev) { document.getElementById(prev).focus(); return; }
                     if (e.target.value.length >= 1 && next) { document.getElementById(next).focus(); }
                 }
             }">
            @foreach([1,2,3,4,5,6] as $n)
            @php
                $id = "otp{$n}";
                $next = $n < 6 ? "otp" . ($n+1) : null;
                $prev = $n > 1 ? "otp" . ($n-1) : null;
            @endphp
            <input id="{{ $id }}"
                   wire:model="otp{{ $n }}"
                   type="text" maxlength="1" inputmode="numeric"
                   @keyup="move($event, '{{ $next }}', '{{ $prev }}')"
                   class="form-input w-11 h-11 text-center text-lg font-bold !px-0"
                   {{ $n === 1 ? 'autofocus' : '' }}/>
            @endforeach
        </div>

        <button wire:click="submitStep2" class="btn-primary w-full justify-center py-2.5 mb-4">
            Verificar
        </button>

        <div class="text-center text-sm text-d-muted">
            <span x-show="countdown > 0">Reenviar em <span class="font-mono text-d-text" x-text="countdown"></span>s</span>
            <button x-show="countdown === 0" wire:click="resendOtp" class="font-medium" style="color:var(--color-primary)">
                Reenviar código
            </button>
        </div>
    </div>
    @endif

</div>
