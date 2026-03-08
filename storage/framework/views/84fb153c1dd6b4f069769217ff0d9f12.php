<div class="space-y-4">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="p-3 bg-green-50 border border-green-200 rounded-md text-sm text-green-700">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
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
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800 max-w-[220px] truncate">
                            <?php echo e($product->name); ?>

                            <div class="text-xs text-gray-400 font-normal"><?php echo e($product->shopee_product_id); ?></div>
                        </td>
                        <td class="px-4 py-3 text-gray-500"><?php echo e($product->category ?? '—'); ?></td>
                        <td class="px-4 py-3"><?php echo e($product->formatted_price); ?></td>
                        <td class="px-4 py-3"><?php echo e($product->formatted_commission); ?></td>
                        <td class="px-4 py-3 font-semibold"><?php echo e(number_format($product->opportunity_score, 1)); ?></td>
                        <td class="px-4 py-3">
                            <span class="<?php echo e($product->status === 'active' ? 'badge-green' : ($product->status === 'pending' ? 'badge-yellow' : 'badge-gray')); ?>">
                                <?php echo e($product->status); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 flex items-center gap-2">
                            <button wire:click="openEdit(<?php echo e($product->id); ?>)" class="text-blue-600 hover:underline text-xs">Editar</button>
                            <button wire:click="confirmDeleteProduct(<?php echo e($product->id); ?>)" class="text-red-600 hover:underline text-xs">Excluir</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">Nenhum produto encontrado.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100"><?php echo e($products->links()); ?></div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showForm): ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 space-y-4">
                <h2 class="text-lg font-semibold"><?php echo e($editingId ? 'Editar Produto' : 'Novo Produto'); ?></h2>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Nome *</label>
                        <input wire:model="name" type="text" class="form-input w-full" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">ID Shopee *</label>
                        <input wire:model="shopeeProductId" type="text" class="form-input w-full" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['shopeeProductId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Categoria</label>
                        <input wire:model="category" type="text" class="form-input w-full" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Preço (R$) *</label>
                        <input wire:model="price" type="number" step="0.01" class="form-input w-full" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Comissão (%) *</label>
                        <input wire:model="commissionRate" type="number" step="0.01" max="100" class="form-input w-full" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['commissionRate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($confirmDelete): ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4 text-center">
                <p class="text-gray-700">Tem certeza que deseja excluir este produto?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('confirmDelete', false)" class="btn-secondary">Cancelar</button>
                    <button wire:click="deleteProduct" class="btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH /home/calixto/shopee-backend/resources/views/livewire/admin/products.blade.php ENDPATH**/ ?>