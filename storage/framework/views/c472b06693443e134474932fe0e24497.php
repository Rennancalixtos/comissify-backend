<div class="space-y-6">

    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

        <?php
            $cards = [
                ['label' => 'Produtos Ativos',       'value' => $metrics['products_active'],    'sub' => $metrics['products_total'].' total',         'color' => 'bg-blue-500'],
                ['label' => 'Vídeos Prontos',         'value' => $metrics['videos_valid'],       'sub' => $metrics['videos_pending'].' aguardando',    'color' => 'bg-green-500'],
                ['label' => 'Fila Pendente',          'value' => $metrics['queue_pending'],      'sub' => $metrics['queue_posted_today'].' postados hoje', 'color' => 'bg-yellow-500'],
                ['label' => 'Contas Ativas',          'value' => $metrics['accounts_active'],    'sub' => $metrics['posts_today'].' posts hoje',       'color' => 'bg-shopee-500'],
            ];
        ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card flex items-start gap-4">
                <div class="w-10 h-10 rounded-lg <?php echo e($card['color']); ?> flex-shrink-0"></div>
                <div>
                    <p class="text-2xl font-bold"><?php echo e($card['value']); ?></p>
                    <p class="text-sm text-gray-500"><?php echo e($card['label']); ?></p>
                    <p class="text-xs text-gray-400 mt-0.5"><?php echo e($card['sub']); ?></p>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    </div>

    
    <div class="card">
        <h2 class="text-base font-semibold mb-4">Atividade Recente — Fila de Postagem</h2>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recentQueue->isEmpty()): ?>
            <p class="text-sm text-gray-400">Nenhuma postagem na fila ainda.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                            <th class="pb-2 pr-4">Produto</th>
                            <th class="pb-2 pr-4">Conta</th>
                            <th class="pb-2 pr-4">Status</th>
                            <th class="pb-2">Agendado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $recentQueue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="py-2 pr-4 font-medium text-gray-800 truncate max-w-[200px]">
                                    <?php echo e($item->video->product->name ?? '—'); ?>

                                </td>
                                <td class="py-2 pr-4 text-gray-500"><?php echo e($item->account->name ?? '—'); ?></td>
                                <td class="py-2 pr-4">
                                    <?php
                                        $badge = match($item->status) {
                                            'posted'     => 'badge-green',
                                            'pending'    => 'badge-yellow',
                                            'processing' => 'badge-blue',
                                            'failed'     => 'badge-red',
                                            default      => 'badge-gray',
                                        };
                                    ?>
                                    <span class="<?php echo e($badge); ?>"><?php echo e($item->status); ?></span>
                                </td>
                                <td class="py-2 text-gray-400 text-xs">
                                    <?php echo e($item->scheduled_at?->format('d/m H:i') ?? '—'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

</div>
<?php /**PATH /home/calixto/shopee-backend/resources/views/livewire/admin/dashboard.blade.php ENDPATH**/ ?>