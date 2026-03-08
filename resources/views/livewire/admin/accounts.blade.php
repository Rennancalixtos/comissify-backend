<div class="space-y-4">

    @if (session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">{{ session('success') }}</div>
    @endif

    <div class="flex items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar conta..." class="form-input w-64" />
        <div class="ml-auto">
            <button wire:click="openCreate" class="btn-primary">+ Nova Conta</button>
        </div>
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-4 py-3">Nome</th>
                    <th class="px-4 py-3">Username</th>
                    <th class="px-4 py-3">Posts Hoje</th>
                    <th class="px-4 py-3">Limite Diário</th>
                    <th class="px-4 py-3">Último Post</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($accounts as $account)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $account->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $account->shopee_username }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $account->posts_today >= $account->daily_post_limit ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                {{ $account->posts_today }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $account->daily_post_limit }}</td>
                        <td class="px-4 py-3 text-gray-400 text-xs">
                            {{ $account->last_post_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($account->status) {
                                    'active'   => 'badge-green',
                                    'cooldown' => 'badge-yellow',
                                    'banned'   => 'badge-red',
                                    default    => 'badge-gray',
                                };
                            @endphp
                            <span class="{{ $badge }}">{{ $account->status }}</span>
                        </td>
                        <td class="px-4 py-3 flex items-center gap-2 flex-wrap">
                            <button wire:click="openEdit({{ $account->id }})" class="text-blue-600 hover:underline text-xs">Editar</button>
                            <button wire:click="resetPostCount({{ $account->id }})" class="text-yellow-600 hover:underline text-xs">Zerar</button>
                            <button wire:click="confirmDeleteAccount({{ $account->id }})" class="text-red-600 hover:underline text-xs">Excluir</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhuma conta encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $accounts->links() }}</div>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 space-y-4">
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Editar Conta' : 'Nova Conta' }}</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
                        <input wire:model="name" type="text" class="form-input w-full" />
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Username Shopee *</label>
                        <input wire:model="shopeeUsername" type="text" class="form-input w-full" />
                        @error('shopeeUsername') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Limite Diário</label>
                            <input wire:model="dailyPostLimit" type="number" min="1" max="50" class="form-input w-full" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="accountStatus" class="form-select w-full">
                                <option value="active">Ativo</option>
                                <option value="inactive">Inativo</option>
                                <option value="cooldown">Cooldown</option>
                                <option value="banned">Banido</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showForm', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="save" class="btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmDelete)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4 text-center">
                <p class="text-gray-700">Excluir esta conta?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('confirmDelete', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="deleteAccount" class="btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    @endif

</div>
