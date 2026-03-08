<div class="space-y-4">

    {{-- Flash --}}
    @if (session('success'))
        <div class="p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar produto..."
               class="form-input w-64" />
        <select wire:model.live="status" class="form-select w-36">
            <option value="">Todos status</option>
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
            <option value="pending">Pendente</option>
        </select>
        <div class="ml-auto">
            <button wire:click="openCreate" class="btn-primary">+ Novo Produto</button>
        </div>
    </div>

    {{-- Table --}}
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-left text-xs text-gray-500 uppercase tracking-wide">
                    <th class="px-4 py-3 cursor-pointer" wire:click="sort('name')">Nome</th>
                    <th class="px-4 py-3">Categoria</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sort('price')">Preço</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sort('commission_rate')">Comissão</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sort('opportunity_score')">Score</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800 max-w-[220px] truncate">
                            {{ $product->name }}
                            <div class="text-xs text-gray-400 font-normal">{{ $product->shopee_product_id }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $product->category ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $product->formatted_price }}</td>
                        <td class="px-4 py-3">{{ $product->formatted_commission }}</td>
                        <td class="px-4 py-3 font-semibold">{{ number_format($product->opportunity_score, 1) }}</td>
                        <td class="px-4 py-3">
                            <span class="{{ $product->status === 'active' ? 'badge-green' : ($product->status === 'pending' ? 'badge-yellow' : 'badge-gray') }}">
                                {{ $product->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            <button wire:click="openEdit({{ $product->id }})" class="text-blue-600 hover:underline text-xs">Editar</button>
                            <button wire:click="confirmDeleteProduct({{ $product->id }})" class="text-red-600 hover:underline text-xs">Excluir</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhum produto encontrado.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $products->links() }}</div>
    </div>

    {{-- Form modal --}}
    @if ($showForm)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Editar Produto' : 'Novo Produto' }}</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
                        <input wire:model="name" type="text" class="form-input w-full" />
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">ID Shopee *</label>
                        <input wire:model="shopeeProductId" type="text" class="form-input w-full" />
                        @error('shopeeProductId') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                        <input wire:model="category" type="text" class="form-input w-full" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Preço (R$) *</label>
                        <input wire:model="price" type="number" step="0.01" class="form-input w-full" />
                        @error('price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Comissão (%) *</label>
                        <input wire:model="commissionRate" type="number" step="0.01" max="100" class="form-input w-full" />
                        @error('commissionRate') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="productStatus" class="form-select w-full">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                            <option value="pending">Pendente</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button wire:click="$set('showForm', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="save" class="btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Confirm delete modal --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4 text-center">
                <p class="text-gray-700">Tem certeza que deseja excluir este produto?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('confirmDelete', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="deleteProduct" class="btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    @endif

</div>
