<div class="space-y-6">

    {{-- ── Sucesso ────────────────────────────────────────────────────────── --}}
    @if ($successMessage)
        <div class="alert-green flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ $successMessage }}
        </div>
    @endif

    {{-- ── Formulário ─────────────────────────────────────────────────────── --}}
    <div class="card space-y-5">
        <h2 class="text-base font-semibold text-d-text">Compor E-mail</h2>

        {{-- Assunto --}}
        <div>
            <label class="block text-sm text-d-muted mb-1">Assunto</label>
            <input wire:model.live="subject" type="text" placeholder="Assunto do e-mail..."
                   class="form-input w-full" maxlength="255">
            @error('subject') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Destinatários --}}
        <div>
            <label class="block text-sm text-d-muted mb-1">Destinatários</label>
            <select wire:model.live="audience" class="form-select w-full">
                <option value="all">Todos os usuários</option>
                <option value="connected">Usuários com Shopee conectada</option>
                <option value="zero_balance">Usuários com saldo zerado</option>
            </select>
            <p class="text-xs text-d-muted mt-1">
                <span class="font-semibold text-d-text">{{ $recipientCount }}</span> destinatários estimados
            </p>
            @error('audience') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Corpo --}}
        <div>
            <label class="block text-sm text-d-muted mb-1">Corpo do E-mail (HTML permitido)</label>
            <textarea wire:model.live="body" rows="10" placeholder="Escreva o corpo do e-mail aqui..."
                      class="form-input w-full font-mono text-sm resize-y"></textarea>
            @error('body') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ações --}}
        <div class="flex items-center gap-3 flex-wrap">
            <button wire:click="togglePreview" type="button"
                    class="btn-secondary text-sm px-4 py-2">
                {{ $preview ? 'Fechar Preview' : 'Preview' }}
            </button>

            <button wire:click="send" wire:loading.attr="disabled" wire:loading.class="opacity-60"
                    class="btn-primary text-sm px-5 py-2">
                <span wire:loading.remove wire:target="send">Enviar para {{ $recipientCount }} destinatários</span>
                <span wire:loading wire:target="send">Enfileirando...</span>
            </button>
        </div>
    </div>

    {{-- ── Preview ─────────────────────────────────────────────────────────── --}}
    @if ($preview && $body)
        <div class="card space-y-3">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-d-text">Preview</h3>
                @if($subject)
                    <span class="text-xs text-d-muted">— {{ $subject }}</span>
                @endif
            </div>
            <div class="rounded-lg p-4 text-sm text-d-text leading-relaxed"
                 style="background:#141414;border:1px solid rgba(255,255,255,0.06)">
                {!! $body !!}
            </div>
        </div>
    @endif

    {{-- ── Histórico ───────────────────────────────────────────────────────── --}}
    <div class="card">
        <h2 class="text-base font-semibold text-d-text mb-4">Histórico de Envios</h2>

        @if ($history->isEmpty())
            <p class="text-sm text-d-muted">Nenhum e-mail enviado ainda.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-d-muted" style="border-bottom:1px solid rgba(255,255,255,0.06)">
                            <th class="pb-2 pr-4">Data</th>
                            <th class="pb-2 pr-4">Assunto</th>
                            <th class="pb-2 pr-4">Destinatários</th>
                            <th class="pb-2">Audiência</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $item)
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
                                <td class="py-2.5 pr-4 text-d-muted text-xs whitespace-nowrap">
                                    {{ $item->sent_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-2.5 pr-4 text-d-text font-medium">{{ $item->subject }}</td>
                                <td class="py-2.5 pr-4 text-d-text">{{ $item->recipients_count }}</td>
                                <td class="py-2.5">
                                    <span class="badge-gray text-xs">{{ $item->audienceLabel() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
