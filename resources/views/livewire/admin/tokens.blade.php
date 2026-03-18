<div class="space-y-5">

    {{-- Preços do sistema --}}
    <div class="card">
        <h2 class="text-base font-semibold text-d-text mb-1">Preços do sistema</h2>
        <div class="alert-warning text-xs mb-4">⚠ Alterações refletem imediatamente em toda a plataforma.</div>

        @if(session('price_saved'))
        <div class="alert-success mb-4">{{ session('price_saved') }}</div>
        @endif

        <p class="text-xs text-d-muted mb-3">Busca e links</p>
        <div class="grid sm:grid-cols-2 gap-4 mb-5">
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Custo por busca (R$)</label>
                <input wire:model="priceSearch" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Custo por link afiliado (R$)</label>
                <input wire:model="priceLink" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
        </div>

        <p class="text-xs text-d-muted mb-3">Criador de conteúdo</p>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Texto AI — após 10 gratuitos (R$)</label>
                <input wire:model="pricePostText" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Imagem com IA (R$)</label>
                <input wire:model="pricePostImage" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Vídeo de Venda (R$)</label>
                <input wire:model="pricePostVideo" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Busca Pinterest (R$)</label>
                <input wire:model="pricePostPin" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
            <div>
                <label class="block text-xs text-d-muted mb-1.5">Envio Telegram por grupo (R$)</label>
                <input wire:model="pricePostTg" type="number" step="0.000001" min="0" class="form-input w-full"/>
            </div>
        </div>

        <button wire:click="savePrices" class="btn-primary !py-2 !px-5">Salvar preços</button>
    </div>

    {{-- Pacotes --}}
    <div class="card !p-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-d-border">
            <h2 class="text-base font-semibold text-d-text">Pacotes de créditos</h2>
            <button wire:click="openNewPackage" class="btn-primary !text-xs !py-1.5 !px-3">+ Novo</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-d-border text-xs text-d-muted uppercase tracking-wider" style="background:#111111">
                    <tr>
                        <th class="px-4 py-3 text-left">Nome</th>
                        <th class="px-4 py-3 text-right">Créditos (R$)</th>
                        <th class="px-4 py-3 text-right">Preço</th>
                        <th class="px-4 py-3 text-center">Ativo</th>
                        <th class="px-4 py-3 text-center">Destaque</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-d-border">
                    @foreach($packages as $pkg)
                    <tr wire:key="pkg-{{ $pkg->id }}" class="hover:bg-d-card2 transition-colors">
                        <td class="px-4 py-3 font-medium text-d-text">{{ $pkg->name }}</td>
                        <td class="px-4 py-3 text-right text-d-muted">{{ number_format($pkg->tokens_amount, 0) }}</td>
                        <td class="px-4 py-3 text-right text-d-text">R$ {{ number_format($pkg->price, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $pkg->is_active ? 'badge-green' : 'badge-gray' }}">{{ $pkg->is_active ? 'Sim' : 'Não' }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($pkg->is_featured)
                            <span class="badge-primary">★ Destaque</span>
                            @else
                            <span class="text-d-border">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button wire:click="openEditPackage({{ $pkg->id }})" class="text-xs text-blue-400 hover:underline">Editar</button>
                                <button wire:click="deletePackage({{ $pkg->id }})" wire:confirm="Excluir este pacote?" class="text-xs text-red-400 hover:underline">Excluir</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Saldo por usuário --}}
    <div class="card !p-0">
        <div class="px-5 py-4 border-b border-d-border">
            <h2 class="text-base font-semibold text-d-text">Saldo de créditos por usuário</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-d-border text-xs text-d-muted uppercase tracking-wider" style="background:#111111">
                    <tr>
                        <th class="px-4 py-3 text-left">Usuário</th>
                        <th class="px-4 py-3 text-right">Saldo</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-d-border">
                    @foreach($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-d-card2 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-d-text">{{ $user->name }}</p>
                            <p class="text-xs text-d-muted">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold" style="color:var(--color-primary)">
                            R$ {{ number_format($user->token?->balance ?? 0, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button wire:click="openAddTokens({{ $user->id }})" class="text-xs font-medium hover:underline" style="color:var(--color-primary)">+ Adicionar</button>
                                <button wire:click="openTransactions({{ $user->id }})" class="text-xs text-d-muted hover:underline">Histórico</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-d-border">{{ $users->links() }}</div>
    </div>

    {{-- Modal: novo/editar pacote --}}
    @if($showPackageModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-d-text">{{ $editingPackageId ? 'Editar pacote' : 'Novo pacote' }}</h3>
                <button wire:click="$set('showPackageModal', false)" class="text-d-muted hover:text-d-text text-xl">✕</button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Nome</label>
                    <input wire:model="pkgName" type="text" class="form-input w-full" placeholder="Ex: Starter"/>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-d-muted mb-1.5">Créditos (R$)</label>
                        <input wire:model="pkgTokens" type="number" step="0.01" min="0" class="form-input w-full"/>
                    </div>
                    <div>
                        <label class="block text-xs text-d-muted mb-1.5">Preço (R$)</label>
                        <input wire:model="pkgPrice" type="number" step="0.01" min="0" class="form-input w-full"/>
                    </div>
                </div>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm text-d-text cursor-pointer">
                        <input wire:model="pkgActive" type="checkbox" class="rounded border-d-border bg-d-input"/> Ativo
                    </label>
                    <label class="flex items-center gap-2 text-sm text-d-text cursor-pointer">
                        <input wire:model="pkgFeatured" type="checkbox" class="rounded border-d-border bg-d-input"/> Destaque
                    </label>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="savePackage" class="btn-primary flex-1 justify-center !py-2">Salvar</button>
                <button wire:click="$set('showPackageModal', false)" class="btn-secondary flex-1 justify-center !py-2">Cancelar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: adicionar tokens --}}
    @if($showAddTokensModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Adicionar créditos</h3>
                <button wire:click="$set('showAddTokensModal', false)" class="text-d-muted hover:text-d-text text-xl">✕</button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Quantidade de créditos (R$)</label>
                    <input wire:model="addTokensAmount" type="number" step="0.01" min="0.01" class="form-input w-full"/>
                </div>
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Descrição</label>
                    <input wire:model="addTokensDesc" type="text" class="form-input w-full"/>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="confirmAddTokens" class="btn-primary flex-1 justify-center !py-2">Confirmar</button>
                <button wire:click="$set('showAddTokensModal', false)" class="btn-secondary flex-1 justify-center !py-2">Cancelar</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: histórico --}}
    @if($showTransactionsModal && $transactionUser)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-lg">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-d-text">Histórico — {{ $transactionUser->name }}</h3>
                    <p class="text-xs text-d-muted">{{ $transactionUser->email }}</p>
                </div>
                <button wire:click="$set('showTransactionsModal', false)" class="text-d-muted hover:text-d-text text-xl">✕</button>
            </div>
            <div class="overflow-y-auto max-h-80 -mx-6">
                <table class="w-full text-sm">
                    <thead class="border-b border-d-border text-xs text-d-muted" style="background:#111111">
                        <tr>
                            <th class="px-4 py-2 text-left">Data</th>
                            <th class="px-4 py-2 text-left">Descrição</th>
                            <th class="px-4 py-2 text-right">Valor</th>
                            <th class="px-4 py-2 text-right">Saldo pós</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-d-border">
                        @forelse($transactions as $tx)
                        <tr class="hover:bg-d-card2">
                            <td class="px-4 py-2 text-d-muted text-xs">{{ $tx->created_at->format('d/m H:i') }}</td>
                            <td class="px-4 py-2 text-d-text">{{ $tx->description }}</td>
                            <td class="px-4 py-2 text-right font-semibold {{ $tx->type === 'credit' ? 'text-green-400' : 'text-red-400' }}">
                                {{ $tx->type === 'credit' ? '+' : '-' }}{{ number_format($tx->amount, 4, ',', '.') }}
                            </td>
                            <td class="px-4 py-2 text-right text-d-muted">{{ number_format($tx->balance_after, 4, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-d-muted text-xs">Nenhuma transação</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

</div>
