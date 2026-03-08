<div class="space-y-4">

    
    <div class="card !p-4">
        <div class="flex flex-col sm:flex-row gap-3">

            
            <div class="flex-1">
                <select wire:model="categoryId" class="form-select w-full">
                    <option value="">Todas as categorias</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>

            
            <div class="flex-1">
                <input wire:model="keyword" type="text" placeholder="Pesquisar produto..."
                       class="form-input w-full" @keydown.enter="$wire.search()"/>
            </div>

            <button wire:click="search" wire:loading.attr="disabled"
                    class="btn-primary px-6 py-2.5 whitespace-nowrap">
                <span wire:loading.remove wire:target="search">Buscar</span>
                <span wire:loading wire:target="search">Buscando...</span>
            </button>
        </div>

        <p class="mt-3 text-xs text-d-muted">
            Cada busca consome <strong class="text-d-text">R$ <?php echo e(number_format($searchCost, 2, ',', '.')); ?></strong> em créditos
            &nbsp;·&nbsp; Seu saldo: <strong style="color:var(--color-primary)">R$ <?php echo e(number_format($balance, 2, ',', '.')); ?></strong>
        </p>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error): ?>
    <div class="alert-error"><?php echo e($error); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($selected)): ?>
    <div class="flex items-center justify-between px-4 py-3 rounded-xl border"
         style="background:color-mix(in srgb,var(--color-primary) 10%,transparent);border-color:color-mix(in srgb,var(--color-primary) 30%,transparent)">
        <span class="text-sm font-medium" style="color:var(--color-primary)"><?php echo e(count($selected)); ?> produto(s) selecionado(s)</span>
        <button wire:click="generateBulkLinks"
                wire:confirm="Gerar links para <?php echo e(count($selected)); ?> produto(s)? Consumirá R$ <?php echo e(number_format($linkCost * count($selected), 2, ',', '.')); ?> em créditos."
                class="btn-primary text-sm py-1.5 px-4">
            Gerar Links
        </button>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($products)): ?>
    <div class="card !p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-d-border text-xs text-d-muted uppercase tracking-wider" style="background:#111111">
                    <tr>
                        <th class="px-4 py-3 text-left w-8">
                            <input type="checkbox" wire:click="toggleSelectAll"
                                   :checked="<?php echo e(json_encode($selectAll)); ?>"
                                   class="rounded border-d-border bg-d-input"/>
                        </th>
                        <th class="px-4 py-3 text-left">#</th>
                        <th class="px-4 py-3 text-left">Produto</th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:text-d-text" wire:click="sortBy(2)">Vendidos</th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:text-d-text" wire:click="sortBy(5)">Ganho Afiliado</th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:text-d-text" wire:click="sortBy(4)">Preço Mín</th>
                        <th class="px-4 py-3 text-right cursor-pointer hover:text-d-text" wire:click="sortBy(3)">Preço Máx</th>
                        <th class="px-4 py-3 text-right">Taxa Com.</th>
                        <th class="px-4 py-3 text-right">Desconto</th>
                        <th class="px-4 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-d-border">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="transition-colors hover:bg-d-card2" wire:key="product-<?php echo e($product['itemId']); ?>">
                        <td class="px-4 py-3">
                            <input type="checkbox"
                                   wire:click="toggleSelect('<?php echo e($product['itemId']); ?>')"
                                   <?php if(in_array($product['itemId'], $selected)): echo 'checked'; endif; ?>
                                   class="rounded border-d-border bg-d-input"/>
                        </td>
                        <td class="px-4 py-3 text-d-muted"><?php echo e($i + 1); ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3 max-w-xs">
                                <img src="<?php echo e($product['imageUrl'] ?? ''); ?>" alt=""
                                     class="w-10 h-10 rounded-lg object-cover flex-shrink-0 bg-d-input"
                                     onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\' viewBox=\'0 0 40 40\'%3E%3Crect width=\'40\' height=\'40\' rx=\'8\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M12 28l6-8 4 5 3-4 5 7H12z\' fill=\'%23333\'/%3E%3Ccircle cx=\'27\' cy=\'15\' r=\'3\' fill=\'%23333\'/%3E%3C/svg%3E'"/>
                                <div class="min-w-0">
                                    <p class="font-medium text-d-text truncate"><?php echo e($product['productName']); ?></p>
                                    <p class="text-xs text-d-muted truncate"><?php echo e($product['shopName'] ?? ''); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-d-muted"><?php echo e(number_format($product['sales'] ?? 0)); ?></td>
                        <td class="px-4 py-3 text-right font-semibold text-green-400">
                            R$ <?php echo e(number_format($product['commission'] ?? 0, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right text-d-text">
                            R$ <?php echo e(number_format(($product['priceMin'] ?? 0) / 100000, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right text-d-text">
                            R$ <?php echo e(number_format(($product['priceMax'] ?? 0) / 100000, 2, ',', '.')); ?>

                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="badge-green"><?php echo e($product['commissionRate'] ?? 0); ?>%</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($product['priceDiscountRate'])): ?>
                            <span class="badge-primary">-<?php echo e($product['priceDiscountRate']); ?>%</span>
                            <?php else: ?>
                            <span class="text-d-border">—</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <button wire:click="showDetail('<?php echo e($product['itemId']); ?>')"
                                        class="p-1.5 text-d-muted hover:text-d-text rounded-lg hover:bg-d-input transition-colors" title="Ver detalhes">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                                <button wire:click="generateLink('<?php echo e($product['offerLink'] ?? $product['productLink']); ?>', '<?php echo e(addslashes($product['productName'])); ?>')"
                                        class="p-1.5 rounded-lg hover:bg-d-input transition-colors" title="Gerar link"
                                        style="color:var(--color-primary)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pageInfo)): ?>
        <div class="px-4 py-3 border-t border-d-border flex items-center justify-between text-sm text-d-muted">
            <span>Página <?php echo e($pageInfo['page'] ?? 1); ?></span>
            <div class="flex gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($pageInfo['page'] ?? 1) > 1): ?>
                <button wire:click="loadPage(<?php echo e(($pageInfo['page'] - 1)); ?>)" class="px-3 py-1 border border-d-border rounded-lg hover:bg-d-input transition-colors">← Anterior</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($pageInfo['hasNextPage'])): ?>
                <button wire:click="loadPage(<?php echo e(($pageInfo['page'] ?? 1) + 1); ?>)" class="px-3 py-1 border border-d-border rounded-lg hover:bg-d-input transition-colors">Próxima →</button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php else: ?>
    
    <div class="card text-center py-16">
        <svg class="w-12 h-12 text-d-muted mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <p class="text-d-muted text-sm">Selecione uma categoria ou pesquise um produto e clique em <strong class="text-d-text">Buscar</strong></p>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showLinkModal): ?>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md" x-data="{ copied: false }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Link gerado</h3>
                <button wire:click="$set('showLinkModal', false)" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>
            <p class="text-sm text-d-muted mb-3 truncate"><?php echo e($linkProductName); ?></p>
            <div class="flex gap-2">
                <input type="text" value="<?php echo e($generatedLink); ?>" readonly
                       class="form-input flex-1 text-xs font-mono"/>
                <button @click="navigator.clipboard.writeText('<?php echo e($generatedLink); ?>'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="btn-secondary px-3 py-2 text-sm">
                    <span x-show="!copied">Copiar</span>
                    <span x-show="copied" class="text-green-400">✓</span>
                </button>
            </div>
            <p class="text-xs text-d-muted mt-3">R$ <?php echo e(number_format($linkCost, 2, ',', '.')); ?> deduzido do seu saldo</p>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showBulkModal): ?>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-xl" x-data="{ allLinks: <?php echo e(json_encode(array_column($bulkLinks, 'link'))); ?> }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Links gerados (<?php echo e(count($bulkLinks)); ?>)</h3>
                <button wire:click="$set('showBulkModal', false)" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>
            <div class="space-y-2 max-h-72 overflow-y-auto mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $bulkLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 p-2.5 rounded-lg bg-d-input border border-d-border">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-d-text truncate"><?php echo e($item['name']); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['ok']): ?>
                        <p class="text-xs text-d-muted font-mono truncate"><?php echo e($item['link']); ?></p>
                        <?php else: ?>
                        <p class="text-xs text-red-400">Erro: <?php echo e($item['error'] ?? 'falhou'); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['ok']): ?>
                    <button x-data="{ c: false }"
                            @click="navigator.clipboard.writeText('<?php echo e($item['link']); ?>'); c = true; setTimeout(() => c = false, 1500)"
                            class="flex-shrink-0 text-xs px-2 py-1 rounded border border-d-border hover:bg-d-card transition-colors">
                        <span x-show="!c">Copiar</span>
                        <span x-show="c" class="text-green-400">✓</span>
                    </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <button x-data
                    @click="const links = allLinks.filter(Boolean).join('\n'); navigator.clipboard.writeText(links)"
                    class="btn-primary w-full justify-center py-2">
                Copiar todos os links
            </button>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDetailModal && !empty($detailProduct)): ?>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
        <div class="card w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-d-text">Detalhes do produto</h3>
                <button wire:click="$set('showDetailModal', false)" class="text-d-muted hover:text-d-text text-xl leading-none">✕</button>
            </div>
            <img src="<?php echo e($detailProduct['imageUrl'] ?? ''); ?>" alt=""
                 class="w-full h-40 object-contain rounded-xl bg-d-input mb-4"
                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'160\' height=\'160\' viewBox=\'0 0 160 160\'%3E%3Crect width=\'160\' height=\'160\' rx=\'12\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M40 110l30-40 20 26 14-18 26 32H40z\' fill=\'%23333\'/%3E%3Ccircle cx=\'115\' cy=\'55\' r=\'14\' fill=\'%23333\'/%3E%3C/svg%3E'"/>
            <h4 class="font-semibold text-d-text mb-3"><?php echo e($detailProduct['productName']); ?></h4>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-d-muted">Loja</dt><dd class="font-medium text-d-text"><?php echo e($detailProduct['shopName'] ?? '—'); ?></dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Preço</dt><dd class="font-medium text-d-text">R$ <?php echo e(number_format(($detailProduct['priceMin'] ?? 0) / 100000, 2, ',', '.')); ?> – R$ <?php echo e(number_format(($detailProduct['priceMax'] ?? 0) / 100000, 2, ',', '.')); ?></dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Vendidos</dt><dd class="font-medium text-d-text"><?php echo e(number_format($detailProduct['sales'] ?? 0)); ?></dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Avaliação</dt><dd class="font-medium text-d-text"><?php echo e($detailProduct['ratingStar'] ?? '—'); ?> ★</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Taxa comissão</dt><dd class="font-semibold text-green-400"><?php echo e($detailProduct['commissionRate'] ?? 0); ?>%</dd></div>
                <div class="flex justify-between"><dt class="text-d-muted">Ganho estimado</dt><dd class="font-semibold text-green-400">R$ <?php echo e(number_format($detailProduct['commission'] ?? 0, 2, ',', '.')); ?></dd></div>
            </dl>
            <button wire:click="generateLink('<?php echo e($detailProduct['offerLink'] ?? $detailProduct['productLink'] ?? ''); ?>', '<?php echo e(addslashes($detailProduct['productName'])); ?>'); $set('showDetailModal', false)"
                    class="btn-primary w-full justify-center py-2.5 mt-4">
                Gerar link afiliado
            </button>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>
<?php /**PATH /home/calixto/comissify-backend/resources/views/livewire/dashboard/product-search.blade.php ENDPATH**/ ?>