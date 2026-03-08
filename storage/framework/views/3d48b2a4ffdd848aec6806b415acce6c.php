<div class="space-y-6">

    
    <div class="card flex items-center justify-between">
        <div>
            <p class="text-xs text-d-muted mb-1">Seu saldo atual</p>
            <p class="text-2xl font-bold" style="color:var(--color-primary)">
                R$ <?php echo e(number_format($balance, 2, ',', '.')); ?> em créditos
            </p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($balance > 0): ?>
        <span class="badge-green text-xs">● Ativo</span>
        <?php else: ?>
        <span class="badge-red text-xs">● Sem saldo</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($packages->isEmpty()): ?>
    <div class="card text-center py-10 text-sm text-d-muted">
        Nenhum pacote disponível no momento.
    </div>
    <?php else: ?>
    <div>
        <h2 class="text-base font-semibold text-d-text mb-4">Escolha um pacote</h2>
        <div class="grid md:grid-cols-3 gap-5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $isReportPkg = $pkg->price >= 24; ?>
            <div class="card relative <?php echo e($pkg->is_featured ? 'border-2' : ''); ?>"
                 style="<?php echo e($pkg->is_featured ? 'border-color:var(--color-primary)' : ''); ?>">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pkg->is_featured): ?>
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-xs font-bold text-white"
                     style="background:var(--color-primary)">Mais popular</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <h3 class="text-base font-bold text-d-text mb-1"><?php echo e($pkg->name); ?></h3>
                <div class="flex items-baseline gap-1 mb-3">
                    <span class="text-3xl font-bold" style="color:var(--color-primary)">
                        R$ <?php echo e(number_format($pkg->price, 2, ',', '.')); ?>

                    </span>
                </div>

                <div class="border-t border-d-border pt-3 mb-4 space-y-1.5">
                    <p class="text-sm text-d-muted">
                        <strong class="text-d-text">R$ <?php echo e(number_format($pkg->tokens_amount, 2, ',', '.')); ?></strong> em créditos
                    </p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isReportPkg): ?>
                    <p class="text-xs flex items-center gap-1.5" style="color:var(--color-primary)">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                        </svg>
                        Inclui acesso ao Relatório
                    </p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($whatsapp): ?>
                <a href="https://wa.me/<?php echo e(preg_replace('/\D/', '', $whatsapp)); ?>?text=<?php echo e(urlencode('Olá! Quero adquirir o pacote ' . $pkg->name . ' (R$ ' . number_format($pkg->price, 2, ',', '.') . ').')); ?>"
                   target="_blank" rel="noopener"
                   class="btn-primary w-full justify-center !py-2.5 text-sm flex items-center gap-2"
                   style="<?php echo e($pkg->is_featured ? '' : 'background:var(--color-primary-dark,var(--color-primary))'); ?>">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    Solicitar via WhatsApp
                </a>
                <?php else: ?>
                <div class="rounded-lg px-3 py-2.5 text-xs text-d-muted text-center"
                     style="background:#1A1A1A;border:1px solid #2E2E2E">
                    Entre em contato com o suporte para adquirir este pacote.
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <p class="text-xs text-d-muted text-center">
        Créditos não expiram · Cada busca consome R$ 0,20 · Pacotes ≥ R$ 24,00 desbloqueiam o Relatório de Conversões
    </p>

</div>
<?php /**PATH /home/calixto/shopee-backend/resources/views/livewire/dashboard/credits.blade.php ENDPATH**/ ?>