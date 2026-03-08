<div class="space-y-5">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
    <div class="alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card !p-0">
        <div class="flex items-center justify-between px-5 py-4 border-b border-d-border">
            <div>
                <h2 class="text-base font-semibold text-d-text">Screenshots da Landing Page</h2>
                <p class="text-xs text-d-muted mt-0.5">Imagens exibidas no slideshow da página inicial</p>
            </div>
            <button wire:click="openNew" class="btn-primary !text-xs !py-1.5 !px-3">+ Adicionar</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-d-border text-xs text-d-muted uppercase tracking-wider" style="background:#111111">
                    <tr>
                        <th class="px-4 py-3 text-left">Preview</th>
                        <th class="px-4 py-3 text-left">Título</th>
                        <th class="px-4 py-3 text-center">Ordem</th>
                        <th class="px-4 py-3 text-center">Ativo</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-d-border">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $screenshots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr wire:key="ss-<?php echo e($s['id']); ?>" class="hover:bg-d-card2 transition-colors">
                        <td class="px-4 py-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_starts_with($s['image_path'], 'landing/placeholder')): ?>
                            <div class="w-16 h-10 rounded bg-d-input flex items-center justify-center text-d-muted text-xs">preview</div>
                            <?php else: ?>
                            <img src="<?php echo e(Storage::url($s['image_path'])); ?>" class="w-16 h-10 rounded object-cover bg-d-input"/>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-d-text"><?php echo e($s['title'] ?? '—'); ?></td>
                        <td class="px-4 py-3 text-center text-d-muted"><?php echo e($s['order']); ?></td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActive(<?php echo e($s['id']); ?>)"
                                    class="<?php echo e($s['is_active'] ? 'badge-green' : 'badge-gray'); ?>">
                                <?php echo e($s['is_active'] ? 'Ativo' : 'Oculto'); ?>

                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button wire:click="openEdit(<?php echo e($s['id']); ?>)" class="text-xs text-blue-400 hover:underline">Editar</button>
                                <button wire:click="delete(<?php echo e($s['id']); ?>)" wire:confirm="Excluir este screenshot?" class="text-xs text-red-400 hover:underline">Excluir</button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="px-4 py-8 text-center text-d-muted text-sm">Nenhum screenshot cadastrado</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModal): ?>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-d-text"><?php echo e($editingId ? 'Editar screenshot' : 'Novo screenshot'); ?></h3>
                <button wire:click="$set('showModal', false)" class="text-d-muted hover:text-d-text text-xl">✕</button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Título (opcional)</label>
                    <input wire:model="title" type="text" class="form-input w-full" placeholder="Ex: Dashboard de produtos"/>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-d-muted mb-1.5">Ordem</label>
                        <input wire:model="order" type="number" min="0" class="form-input w-full"/>
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 text-sm text-d-text cursor-pointer">
                            <input wire:model="is_active" type="checkbox" class="rounded border-d-border bg-d-input"/> Ativo
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-d-muted mb-1.5">Imagem <?php echo e($editingId ? '(deixe vazio para manter)' : '*'); ?></label>
                    <input wire:model="image" type="file" accept="image/*" class="form-input w-full !py-1.5 text-xs"/>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>
                    <img src="<?php echo e($image->temporaryUrl()); ?>" class="w-full h-32 object-cover rounded-lg mt-2 bg-d-input"/>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button wire:click="save" class="btn-primary flex-1 justify-center !py-2">Salvar</button>
                <button wire:click="$set('showModal', false)" class="btn-secondary flex-1 justify-center !py-2">Cancelar</button>
            </div>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH /home/calixto/shopee-backend/resources/views/livewire/admin/screenshots.blade.php ENDPATH**/ ?>