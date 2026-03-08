<div class="space-y-5">

    <div class="card !p-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-d-border">
            <h2 class="text-base font-semibold text-d-text">Clientes</h2>
            <input wire:model.live="search" type="text" placeholder="Buscar por nome ou e-mail..."
                   class="form-input !w-56 text-sm !py-1.5"/>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-d-border text-xs text-d-muted uppercase tracking-wider" style="background:#111111">
                    <tr>
                        <th class="px-4 py-3 text-left">Usuário</th>
                        <th class="px-4 py-3 text-right">Créditos</th>
                        <th class="px-4 py-3 text-center">Shopee</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Cadastro</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-d-border">
                    @forelse($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-d-card2 transition-colors">
                        <td class="px-4 py-3">
                            <p class="font-medium text-d-text">{{ $user->name }}</p>
                            <p class="text-xs text-d-muted">{{ $user->email }}</p>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold" style="color:var(--color-primary)">
                            R$ {{ number_format($user->token?->balance ?? 0, 2, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($user->credential?->is_connected)
                            <span class="badge-green">● Conectado</span>
                            @else
                            <span class="text-d-border text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="{{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                {{ $user->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-d-muted">
                            {{ $user->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button wire:click="openAddTokens({{ $user->id }})" class="text-xs font-medium hover:underline" style="color:var(--color-primary)">+ Créditos</button>
                                <button wire:click="toggleActive({{ $user->id }})"
                                        class="text-xs hover:underline {{ $user->is_active ? 'text-red-400' : 'text-green-400' }}">
                                    {{ $user->is_active ? 'Desativar' : 'Ativar' }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-d-muted text-sm">Nenhum usuário encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-3 border-t border-d-border">{{ $users->links() }}</div>
    </div>

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

</div>
