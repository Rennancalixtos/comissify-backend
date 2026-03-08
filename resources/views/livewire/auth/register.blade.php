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
        @foreach([1 => 'Dados', 2 => 'Verificação', 3 => 'Pagamento'] as $n => $label)
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all"
                 style="{{ $step >= $n
                    ? 'background:var(--color-primary);color:#fff'
                    : 'background:#2E2E2E;color:#9CA3AF' }}">
                {{ $step > $n ? '✓' : $n }}
            </div>
            <span class="text-xs hidden sm:block {{ $step === $n ? 'text-d-text font-medium' : 'text-d-muted' }}">{{ $label }}</span>
        </div>
        @if($n < 3)
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
                <input wire:model="name" type="text" class="form-input w-full" placeholder="Seu nome" autofocus/>
                @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">E-mail</label>
                <input wire:model="email" type="email" class="form-input w-full" placeholder="seu@email.com"/>
                @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
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

    {{-- ── Step 3: Meio de pagamento ────────────────────────────────── --}}
    @if($step === 3)
    <div class="card" x-data="{
        number: '', name: '', expiry: '', cvv: '',
        flipped: false,
        brand() {
            if (this.number.startsWith('4')) return 'VISA';
            if (this.number.startsWith('5')) return 'MASTERCARD';
            if (this.number.startsWith('6')) return 'ELO';
            return '••••';
        },
        fmt(v) { return v.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim(); },
        fmtExp(v) {
            v = v.replace(/\D/g,'');
            if (v.length > 2) v = v.slice(0,2)+'/'+v.slice(2,4);
            return v;
        }
    }">
        <h2 class="text-base font-semibold text-d-text mb-2">Meio de pagamento</h2>
        <p class="text-sm text-d-muted mb-5">Estrutural — nenhum pagamento será processado agora.</p>

        {{-- Card preview --}}
        <div class="relative h-40 mb-6 select-none cursor-pointer perspective-1000" @click="flipped = !flipped">
            <div class="absolute inset-0 transition-transform duration-500 transform-style-3d" :class="flipped && 'rotate-y-180'">
                {{-- Front --}}
                <div class="absolute inset-0 rounded-2xl p-5 flex flex-col justify-between backface-hidden"
                     style="background:linear-gradient(135deg,var(--color-primary),var(--color-primary-dark))">
                    <div class="flex justify-between items-start">
                        <span class="text-white text-xs opacity-70">Comissify Pay</span>
                        <span class="text-white text-sm font-bold opacity-90" x-text="brand()"></span>
                    </div>
                    <div>
                        <p class="text-white font-mono text-lg tracking-widest" x-text="fmt(number) || '•••• •••• •••• ••••'"></p>
                        <div class="flex gap-6 mt-2">
                            <div>
                                <p class="text-white opacity-50 text-xs uppercase">Titular</p>
                                <p class="text-white text-sm font-medium" x-text="name || 'SEU NOME'"></p>
                            </div>
                            <div>
                                <p class="text-white opacity-50 text-xs uppercase">Validade</p>
                                <p class="text-white text-sm font-mono" x-text="expiry || 'MM/AA'"></p>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Back --}}
                <div class="absolute inset-0 rounded-2xl flex flex-col justify-center backface-hidden rotate-y-180"
                     style="background:linear-gradient(135deg,var(--color-primary-dark),#1a1a2e)">
                    <div class="h-10 mx-0" style="background:#000"></div>
                    <div class="px-5 mt-4 flex justify-end">
                        <div class="bg-white rounded px-3 py-1">
                            <p class="font-mono text-black text-sm" x-text="cvv || '•••'"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Número do cartão</label>
                <input wire:model="cardNumber" type="text" maxlength="19" inputmode="numeric"
                       x-model="number" @input="$wire.set('cardNumber', $event.target.value)"
                       class="form-input w-full font-mono" placeholder="0000 0000 0000 0000"/>
                @error('cardNumber') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Nome no cartão</label>
                <input wire:model="cardName" type="text"
                       x-model="name" @input="$wire.set('cardName', $event.target.value)"
                       class="form-input w-full" placeholder="NOME COMO NO CARTÃO"/>
                @error('cardName') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Validade</label>
                    <input wire:model="cardExpiry" type="text" maxlength="5" inputmode="numeric"
                           x-model="expiry" @input="expiry = fmtExp($event.target.value); $wire.set('cardExpiry', expiry)"
                           class="form-input w-full font-mono" placeholder="MM/AA"/>
                    @error('cardExpiry') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">CVV</label>
                    <input wire:model="cardCvv" type="text" maxlength="4" inputmode="numeric"
                           x-model="cvv" @focus="flipped=true" @blur="flipped=false"
                           @input="$wire.set('cardCvv', $event.target.value)"
                           class="form-input w-full font-mono" placeholder="•••"/>
                    @error('cardCvv') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <button wire:click="submitStep3" class="btn-primary w-full justify-center py-2.5 mt-5">
            Finalizar cadastro
        </button>
        <div class="text-center mt-3">
            <button wire:click="skipStep3" class="text-xs text-d-muted hover:text-d-text transition-colors">
                Pular por agora →
            </button>
        </div>
    </div>
    @endif

</div>
