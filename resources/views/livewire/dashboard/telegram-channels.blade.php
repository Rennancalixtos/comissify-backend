{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- Canais de Envio — Telegram                                           --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}

<div class="space-y-6">

    {{-- ── Flash / Erro ──────────────────────────────────────────────────── --}}
    @if($flashMsg)
    <div class="rounded-xl px-4 py-3 text-sm flex items-center gap-2"
         style="background:rgba(34,197,94,.08);color:#4ade80;border:1px solid rgba(34,197,94,.2)">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ $flashMsg }}
    </div>
    @endif
    @if($error)
    <div class="alert-error">{{ $error }}</div>
    @endif

    {{-- ── Header ───────────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(42,171,238,.12)">
                {{-- Telegram icon --}}
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" style="color:#2AABEE">
                    <path d="M21.8 3.2L2.4 10.6c-1.3.5-1.3 1.3-.2 1.6l4.9 1.5 1.9 5.8c.3.7.1 1 .8 1 .5 0 .8-.3 1.1-.6l2.6-2.5 5.1 3.8c.9.5 1.6.3 1.8-.9l3.3-15.5c.3-1.4-.5-2-1.9-1.6z" fill="currentColor"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-d-text">Canais de Envio</h2>
                <p class="text-xs text-d-muted">Envie links afiliados para grupos do Telegram com um clique</p>
            </div>
        </div>

        @if($this->groups->isNotEmpty() && !$showAddPanel)
        <button wire:click="showAddPanel"
                class="btn-primary !text-xs !py-2 !px-4 flex-shrink-0 flex items-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Adicionar grupo
        </button>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- GRUPOS CONECTADOS                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($this->groups->isNotEmpty())
    <div class="space-y-3">
        <p class="text-xs font-semibold text-d-muted uppercase tracking-wider">
            {{ $this->groups->count() }} {{ $this->groups->count() === 1 ? 'grupo conectado' : 'grupos conectados' }}
        </p>

        @foreach($this->groups as $group)
        <div class="card !p-0 overflow-hidden" wire:key="group-{{ $group->id }}">

            {{-- Group header --}}
            <div class="flex items-center gap-3 px-4 py-3.5">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(42,171,238,.12)">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" style="color:#2AABEE">
                        <path d="M21.8 3.2L2.4 10.6c-1.3.5-1.3 1.3-.2 1.6l4.9 1.5 1.9 5.8c.3.7.1 1 .8 1 .5 0 .8-.3 1.1-.6l2.6-2.5 5.1 3.8c.9.5 1.6.3 1.8-.9l3.3-15.5c.3-1.4-.5-2-1.9-1.6z" fill="currentColor"/>
                    </svg>
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-d-text truncate">{{ $group->chat_title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-d-muted font-mono">ID {{ $group->chat_id }}</span>
                        <span class="text-d-muted text-xs">·</span>
                        <span class="text-xs text-d-muted">desde {{ $group->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    {{-- Template button --}}
                    <button wire:click="startEditTemplate({{ $group->id }})"
                            class="flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-lg transition-colors hover:bg-d-input"
                            style="color:#9CA3AF">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Template
                    </button>

                    {{-- Disconnect --}}
                    @if($confirmDisconnectId === $group->id)
                    <div class="flex items-center gap-1.5">
                        <span class="text-xs text-d-muted">Confirmar?</span>
                        <button wire:click="disconnectGroup({{ $group->id }})"
                                class="text-xs font-semibold px-2 py-1 rounded-lg transition-colors"
                                style="color:#f87171;background:rgba(248,113,113,.08)">
                            Sim
                        </button>
                        <button wire:click="cancelDisconnect"
                                class="text-xs text-d-muted hover:text-d-text px-1 py-1 rounded-lg transition-colors">
                            Não
                        </button>
                    </div>
                    @else
                    <button wire:click="disconnectGroup({{ $group->id }})"
                            class="text-xs text-d-muted hover:text-red-400 px-2 py-1.5 rounded-lg transition-colors hover:bg-red-400/5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Template editor (inline, shown when editing) --}}
            @if($editingGroupId === $group->id)
            <div class="border-t border-d-border px-4 py-4 space-y-3"
                 style="background:#111111">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-d-text">Template da mensagem</p>
                    <button wire:click="resetTemplate"
                            class="text-xs text-d-muted hover:text-d-text transition-colors">
                        Restaurar padrão
                    </button>
                </div>

                {{-- Variable pills --}}
                <div class="flex flex-wrap gap-1.5">
                    @foreach(['{{nome}}','{{link}}','{{preco}}','{{comissao}}'] as $var)
                    <span class="text-xs font-mono px-2 py-0.5 rounded-md"
                          style="background:rgba(42,171,238,.1);color:#2AABEE;border:1px solid rgba(42,171,238,.2)">{{ $var }}</span>
                    @endforeach
                    <span class="text-xs text-d-muted self-center">— clique para copiar</span>
                </div>

                <textarea wire:model="templateDraft" rows="6"
                          class="form-input w-full font-mono text-xs leading-relaxed resize-none"
                          placeholder="Digite o template da mensagem..."></textarea>

                <div class="flex gap-2 justify-end">
                    <button wire:click="cancelEditTemplate"
                            class="text-xs text-d-muted hover:text-d-text px-3 py-1.5 rounded-lg transition-colors hover:bg-d-input">
                        Cancelar
                    </button>
                    <button wire:click="saveTemplate" class="btn-primary !text-xs !py-1.5 !px-4">
                        Salvar template
                    </button>
                </div>
            </div>
            @elseif($group->message_template)
            <div class="border-t border-d-border px-4 py-2.5"
                 style="background:#111111">
                <p class="text-xs text-d-muted truncate font-mono">{{ $group->message_template }}</p>
            </div>
            @endif

        </div>
        @endforeach
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- EMPTY STATE (sem grupos e sem painel de adição aberto)             --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($this->groups->isEmpty() && !$showAddPanel)
    <div class="card text-center py-14 space-y-5">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto"
             style="background:rgba(42,171,238,.1)">
            <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" style="color:#2AABEE">
                <path d="M21.8 3.2L2.4 10.6c-1.3.5-1.3 1.3-.2 1.6l4.9 1.5 1.9 5.8c.3.7.1 1 .8 1 .5 0 .8-.3 1.1-.6l2.6-2.5 5.1 3.8c.9.5 1.6.3 1.8-.9l3.3-15.5c.3-1.4-.5-2-1.9-1.6z" fill="currentColor"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-d-text mb-1">Nenhum grupo conectado</p>
            <p class="text-xs text-d-muted max-w-xs mx-auto leading-relaxed">
                Conecte um grupo do Telegram para enviar links afiliados diretamente do Criador.
            </p>
        </div>
        <button wire:click="generateCode" class="btn-primary mx-auto flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Conectar primeiro grupo
        </button>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- PAINEL DE CONEXÃO                                                  --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if($showAddPanel)

    {{-- Polling silencioso enquanto aguarda conexão --}}
    @if($connectionCode)
    <div wire:poll.5s="checkConnection"></div>
    @endif

    <div class="card space-y-5">

        {{-- Panel header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                     style="background:rgba(42,171,238,.12)">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" style="color:#2AABEE">
                        <path d="M21.8 3.2L2.4 10.6c-1.3.5-1.3 1.3-.2 1.6l4.9 1.5 1.9 5.8c.3.7.1 1 .8 1 .5 0 .8-.3 1.1-.6l2.6-2.5 5.1 3.8c.9.5 1.6.3 1.8-.9l3.3-15.5c.3-1.4-.5-2-1.9-1.6z" fill="currentColor"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-d-text">Conectar novo grupo</p>
                    <p class="text-xs text-d-muted">Siga os passos abaixo</p>
                </div>
            </div>
            @if($this->groups->isNotEmpty())
            <button wire:click="hideAddPanel"
                    class="text-d-muted hover:text-d-text transition-colors p-1.5 rounded-lg hover:bg-d-input">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            @endif
        </div>

        {{-- Steps --}}
        <div class="space-y-4">

            {{-- Step 1 --}}
            <div class="flex gap-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white mt-0.5"
                     style="background:#2AABEE;min-width:1.5rem">1</div>
                <div>
                    <p class="text-sm font-medium text-d-text">Adicione o bot ao seu grupo</p>
                    <p class="text-xs text-d-muted mt-0.5 leading-relaxed">
                        Pesquise
                        @if(config('services.telegram.bot_username'))
                            <span class="font-mono px-1.5 py-0.5 rounded text-xs"
                                  style="background:#272727;color:#2AABEE">@{{ config('services.telegram.bot_username') }}</span>
                        @else
                            o bot Comissify
                        @endif
                        no Telegram e adicione-o como membro do grupo.
                    </p>
                </div>
            </div>

            {{-- Step 2 --}}
            <div class="flex gap-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white mt-0.5"
                     style="background:#2AABEE;min-width:1.5rem">2</div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-d-text">Gere um código de conexão</p>
                    <p class="text-xs text-d-muted mt-0.5">O código expira em 10 minutos. Gere um novo se necessário.</p>

                    @if($connectionCode)
                    {{-- Code display --}}
                    <div class="mt-3 space-y-2">
                        <div x-data="{ copied: false }"
                             class="flex items-center gap-3 px-4 py-3 rounded-xl"
                             style="background:#111111;border:1px solid rgba(42,171,238,.2)">
                            <span class="font-mono text-lg font-bold tracking-widest flex-1"
                                  style="color:#2AABEE;letter-spacing:.18em">{{ $connectionCode }}</span>
                            <button @click="navigator.clipboard.writeText('/conectar {{ $connectionCode }}'); copied = true; setTimeout(()=>copied=false,2500)"
                                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg transition-all"
                                    style="background:rgba(42,171,238,.12);color:#2AABEE"
                                    x-bind:style="copied ? 'background:rgba(34,197,94,.1);color:#4ade80' : ''">
                                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="copied ? 'Copiado!' : 'Copiar comando'"></span>
                            </button>
                        </div>
                        @if($codeExpiresAt)
                        <p class="text-xs text-d-muted">
                            Expira às {{ date('H:i', $codeExpiresAt) }}
                            · <button wire:click="generateCode" class="hover:text-d-text transition-colors underline underline-offset-2">Gerar novo</button>
                        </p>
                        @endif
                    </div>
                    @else
                    <button wire:click="generateCode" wire:loading.attr="disabled"
                            class="mt-3 btn-primary !text-xs !py-2 !px-4 flex items-center gap-2">
                        <span wire:loading.remove wire:target="generateCode">Gerar código</span>
                        <span wire:loading wire:target="generateCode">Gerando...</span>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Step 3 --}}
            <div class="flex gap-3">
                <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold text-white mt-0.5"
                     style="background:#2AABEE;min-width:1.5rem">3</div>
                <div>
                    <p class="text-sm font-medium text-d-text">Envie o comando no grupo</p>
                    <p class="text-xs text-d-muted mt-0.5 leading-relaxed">
                        No grupo do Telegram, envie a mensagem:
                    </p>
                    @if($connectionCode)
                    <div class="mt-2 px-3 py-2 rounded-lg font-mono text-sm"
                         style="background:#111111;border:1px solid rgba(255,255,255,0.05);color:#e5e7eb">
                        /conectar <span style="color:#2AABEE">{{ $connectionCode }}</span>
                    </div>
                    @else
                    <div class="mt-2 px-3 py-2 rounded-lg font-mono text-sm"
                         style="background:#111111;border:1px solid rgba(255,255,255,0.05);color:#6B7280">
                        /conectar <span class="opacity-40">••••••••</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Waiting indicator --}}
        @if($connectionCode)
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl"
             style="background:rgba(42,171,238,.06);border:1px solid rgba(42,171,238,.12)">
            <span class="w-2 h-2 rounded-full flex-shrink-0 animate-pulse" style="background:#2AABEE"></span>
            <p class="text-xs" style="color:#2AABEE">Aguardando conexão... A página atualiza automaticamente.</p>
        </div>
        @endif

    </div>
    @endif

</div>
