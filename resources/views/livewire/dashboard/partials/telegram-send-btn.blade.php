{{--
  Partial: botão "Enviar para Telegram"
  Variáveis esperadas: $postId (int)
  Contexto: dentro de posts.blade.php, requer $this->telegramGroups não-vazio
--}}
<div x-data="{
        open: false,
        groupId: {{ $this->telegramGroups->first()?->id ?? 0 }},
        sent: false,
        send() {
            this.open = false;
            this.sent = true;
            $wire.sendToTelegram({{ $postId }}, this.groupId);
            setTimeout(() => this.sent = false, 3000);
        }
     }"
     class="relative flex-shrink-0">

    <button @click="open = !open"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl border text-xs font-semibold transition-all"
            style="border-color:#2A2A2A;color:#9CA3AF"
            x-bind:style="sent ? 'border-color:rgba(42,171,238,.4);color:#2AABEE;background:rgba(42,171,238,.06)' : open ? 'border-color:rgba(42,171,238,.3);color:#2AABEE;background:rgba(42,171,238,.06)' : ''">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M21.8 3.2L2.4 10.6c-1.3.5-1.3 1.3-.2 1.6l4.9 1.5 1.9 5.8c.3.7.1 1 .8 1 .5 0 .8-.3 1.1-.6l2.6-2.5 5.1 3.8c.9.5 1.6.3 1.8-.9l3.3-15.5c.3-1.4-.5-2-1.9-1.6z"/>
        </svg>
        <span x-text="sent ? 'Enviado!' : 'Telegram'"></span>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute bottom-full right-0 mb-2 z-20 w-52 rounded-xl p-3 space-y-2.5 shadow-xl"
         style="background:#1C1C1C;border:1px solid rgba(42,171,238,.2)"
         style="display:none">

        @if($this->telegramGroups->count() > 1)
        <div>
            <p class="text-xs text-d-muted mb-1.5">Enviar para:</p>
            <select x-model="groupId" class="form-select w-full !text-xs !py-1.5">
                @foreach($this->telegramGroups as $tg)
                <option value="{{ $tg->id }}">{{ $tg->chat_title }}</option>
                @endforeach
            </select>
        </div>
        @else
        <p class="text-xs text-d-muted">
            Enviar para
            <span class="text-d-text font-medium">{{ $this->telegramGroups->first()->chat_title }}</span>
        </p>
        @endif

        <button @click="send()"
                class="w-full flex items-center justify-center gap-1.5 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                style="background:rgba(42,171,238,.15);color:#2AABEE">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.8 3.2L2.4 10.6c-1.3.5-1.3 1.3-.2 1.6l4.9 1.5 1.9 5.8c.3.7.1 1 .8 1 .5 0 .8-.3 1.1-.6l2.6-2.5 5.1 3.8c.9.5 1.6.3 1.8-.9l3.3-15.5c.3-1.4-.5-2-1.9-1.6z"/>
            </svg>
            Confirmar envio
        </button>
    </div>
</div>
