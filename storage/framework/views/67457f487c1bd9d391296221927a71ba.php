<div class="flex gap-5 h-full" x-data>

    
    <div class="w-72 flex-shrink-0 space-y-3">
        <p class="text-xs font-semibold text-d-muted uppercase tracking-wider px-1">Meus Produtos</p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->links->isEmpty()): ?>
        <div class="card text-center py-10">
            <p class="text-sm text-d-muted mb-3">Nenhum produto afiliado ainda.</p>
            <a href="<?php echo e(route('dashboard')); ?>" class="text-xs font-medium" style="color:var(--color-primary)">
                → Buscar produtos
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-2">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button wire:click="selectLink(<?php echo e($link->id); ?>)"
                    class="w-full text-left rounded-xl p-3 flex items-center gap-3 transition-all duration-150 border"
                    style="<?php echo e($selectedLinkId === $link->id
                        ? 'background:color-mix(in srgb,var(--color-primary) 10%,transparent);border-color:color-mix(in srgb,var(--color-primary) 40%,transparent);'
                        : 'background:#1C1C1C;border-color:rgba(255,255,255,0.06);'); ?>">
                
                <div class="w-10 h-10 rounded-lg flex-shrink-0 overflow-hidden bg-d-border">
                    <img src="<?php echo e($link->image_url ?? ''); ?>" alt=""
                         class="w-full h-full object-cover"
                         onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\' viewBox=\'0 0 40 40\'%3E%3Crect width=\'40\' height=\'40\' rx=\'8\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M12 28l6-8 4 5 3-4 5 7H12z\' fill=\'%23333\'/%3E%3Ccircle cx=\'27\' cy=\'15\' r=\'3\' fill=\'%23333\'/%3E%3C/svg%3E'"/>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-d-text truncate"><?php echo e($link->product_name); ?></p>
                    <p class="text-xs text-d-muted truncate"><?php echo e($link->shop_name); ?></p>
                </div>
            </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="flex-1 min-w-0 space-y-4">

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $selectedLinkId): ?>
        
        <div class="card flex flex-col items-center justify-center py-20 text-center gap-3">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                 style="background:color-mix(in srgb,var(--color-primary) 12%,transparent)">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" style="color:var(--color-primary)">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
                </svg>
            </div>
            <p class="text-sm text-d-muted">Selecione um produto para criar conteúdo</p>
        </div>
        <?php else: ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($flashMsg): ?>
        <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.2)">
            <?php echo e($flashMsg); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($error): ?>
        <div class="rounded-lg px-4 py-3 text-sm" style="background:rgba(239,68,68,.1);color:#f87171;border:1px solid rgba(239,68,68,.2)">
            <?php echo e($error); ?>

        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php $link = $this->selectedLink; ?>
        <div class="card flex items-center gap-4">
            <img src="<?php echo e($link->image_url ?? ''); ?>" alt=""
                 class="w-14 h-14 rounded-xl object-cover flex-shrink-0"
                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'56\' height=\'56\' viewBox=\'0 0 56 56\'%3E%3Crect width=\'56\' height=\'56\' rx=\'10\' fill=\'%231A1A1A\'/%3E%3Cpath d=\'M16 40l10-14 8 10 6-8 10 12H16z\' fill=\'%23333\'/%3E%3Ccircle cx=\'40\' cy=\'20\' r=\'6\' fill=\'%23333\'/%3E%3C/svg%3E'"/>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-d-text truncate"><?php echo e($link->product_name); ?></p>
                <p class="text-xs text-d-muted"><?php echo e($link->shop_name); ?></p>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($link->commission_rate): ?>
                <p class="text-xs mt-0.5" style="color:var(--color-primary)"><?php echo e($link->commission_rate); ?>% comissão</p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <a href="<?php echo e($link->affiliate_link); ?>" target="_blank" rel="noopener"
               class="text-xs text-d-muted hover:text-d-text transition-colors flex-shrink-0">
                Ver link →
            </a>
        </div>

        
        <div class="grid grid-cols-2 gap-3">

            <?php
                $options = [
                    ['key' => 'text',      'label' => 'Post de Texto',       'cost' => $this->prices['text'],      'icon' => 'M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12',                                                                                                                             'desc' => 'Título e descrição com imagem'],
                    ['key' => 'image',     'label' => 'Imagem com IA',        'cost' => $this->prices['image'],     'icon' => 'M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z', 'desc' => 'Imagem gerada por IA'],
                    ['key' => 'video',     'label' => 'Vídeo de Venda',       'cost' => $this->prices['video'],     'icon' => 'M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z', 'desc' => 'Vídeo de 20-30s a partir da imagem'],
                    ['key' => 'pinterest', 'label' => 'Buscar no Pinterest',   'cost' => $this->prices['pinterest'], 'icon' => 'M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 10.607z',                                                                                            'desc' => 'Encontrar pins similares'],
                ];
            ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button wire:click="selectTab('<?php echo e($opt['key']); ?>')"
                    class="card text-left transition-all duration-150 hover:border-opacity-60"
                    style="<?php echo e($activeTab === $opt['key']
                        ? 'border:1px solid color-mix(in srgb,var(--color-primary) 60%,transparent);background:color-mix(in srgb,var(--color-primary) 8%,transparent);'
                        : 'border:1px solid rgba(255,255,255,0.06);'); ?>">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background:color-mix(in srgb,var(--color-primary) 15%,transparent)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" style="color:var(--color-primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo e($opt['icon']); ?>"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-d-text"><?php echo e($opt['label']); ?></p>
                        <p class="text-xs text-d-muted mt-0.5"><?php echo e($opt['desc']); ?></p>
                        <p class="text-xs font-bold mt-1.5" style="color:var(--color-primary)">
                            R$ <?php echo e(number_format($opt['cost'], 2, ',', '.')); ?>

                        </p>
                    </div>
                </div>
            </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeTab === 'text'): ?>
        <div class="card space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-d-text">Post de Texto</h3>
                <span class="text-xs text-d-muted">R$ <?php echo e(number_format($this->prices['text'], 2, ',', '.')); ?></span>
            </div>
            <p class="text-xs text-d-muted">Gera um título e descrição otimizados para divulgação do produto, com a imagem padrão.</p>
            <button wire:click="createPost('text')" wire:loading.attr="disabled"
                    class="btn-primary w-full justify-center">
                <span wire:loading.remove wire:target="createPost">Gerar post de texto</span>
                <span wire:loading wire:target="createPost">Gerando...</span>
            </button>
        </div>

        <?php elseif($activeTab === 'image'): ?>
        <div class="card space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-d-text">Imagem com IA</h3>
                <span class="text-xs text-d-muted">R$ <?php echo e(number_format($this->prices['image'], 2, ',', '.')); ?></span>
            </div>
            <p class="text-xs text-d-muted">Cria uma imagem promocional do produto gerada por inteligência artificial.</p>
            <button wire:click="createPost('image')" wire:loading.attr="disabled"
                    class="btn-primary w-full justify-center">
                <span wire:loading.remove wire:target="createPost">Gerar imagem</span>
                <span wire:loading wire:target="createPost">Gerando...</span>
            </button>
        </div>

        <?php elseif($activeTab === 'video'): ?>
        <div class="card space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-d-text">Vídeo de Venda</h3>
                <span class="text-xs text-d-muted">R$ <?php echo e(number_format($this->prices['video'], 2, ',', '.')); ?></span>
            </div>
            <p class="text-xs text-d-muted">Gera um vídeo de 20 a 30 segundos a partir da imagem do produto para usar em reels e stories.</p>
            <button wire:click="createPost('video')" wire:loading.attr="disabled"
                    class="btn-primary w-full justify-center">
                <span wire:loading.remove wire:target="createPost">Gerar vídeo</span>
                <span wire:loading wire:target="createPost">Gerando...</span>
            </button>
        </div>

        <?php elseif($activeTab === 'pinterest'): ?>
        <div class="card space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-d-text">Buscar no Pinterest</h3>
                <span class="text-xs text-d-muted">R$ <?php echo e(number_format($this->prices['pinterest'], 2, ',', '.')); ?></span>
            </div>
            <div class="flex gap-2">
                <input wire:model="pinterestQuery" type="text"
                       class="form-input flex-1 text-sm"
                       placeholder="Palavra-chave para buscar..."/>
                <button wire:click="searchPinterest" wire:loading.attr="disabled"
                        class="btn-primary !px-4 text-sm flex-shrink-0">
                    <span wire:loading.remove wire:target="searchPinterest">Buscar</span>
                    <span wire:loading wire:target="searchPinterest">Buscando...</span>
                </button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pinterestError): ?>
            <p class="text-xs text-red-400"><?php echo e($pinterestError); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($pinterestPins)): ?>
        <div class="card !p-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-d-border">
                <p class="text-sm font-semibold text-d-text">Resultados — <?php echo e(count($pinterestPins)); ?> pins</p>
            </div>
            <div class="grid grid-cols-3 gap-px" style="background:rgba(255,255,255,0.04)">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $pinterestPins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative group" style="background:#1C1C1C">
                    <div class="aspect-square overflow-hidden">
                        <img src="<?php echo e($pin['image_url']); ?>" alt="<?php echo e($pin['title']); ?>"
                             class="w-full h-full object-cover group-hover:opacity-80 transition-opacity">
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pin['media_type'] === 'video'): ?>
                    <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded text-xs font-bold text-white"
                         style="background:rgba(0,0,0,0.7)">▶ Vídeo</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="absolute inset-0 flex items-end opacity-0 group-hover:opacity-100 transition-opacity p-2"
                         style="background:linear-gradient(to top, rgba(0,0,0,0.8), transparent)">
                        <div class="w-full flex gap-1.5">
                            <a href="<?php echo e($pin['link'] ?? '#'); ?>" target="_blank" rel="noopener"
                               class="flex-1 text-center text-xs font-medium text-white py-1 rounded"
                               style="background:rgba(255,255,255,0.15)">
                                Abrir
                            </a>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pin['video_url'] ?? null): ?>
                            <a href="<?php echo e($pin['video_url']); ?>" download
                               class="flex-1 text-center text-xs font-medium text-white py-1 rounded"
                               style="background:var(--color-primary)">
                                Baixar
                            </a>
                            <?php else: ?>
                            <a href="<?php echo e($pin['image_url']); ?>" download
                               class="flex-1 text-center text-xs font-medium text-white py-1 rounded"
                               style="background:var(--color-primary)">
                                Baixar
                            </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <p class="px-2 py-1.5 text-xs text-d-muted truncate"><?php echo e($pin['title']); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($this->postsForLink)): ?>
        <div class="card !p-0 overflow-hidden">
            <div class="px-4 py-3 border-b border-d-border">
                <p class="text-sm font-semibold text-d-text">Conteúdo gerado</p>
            </div>
            <div class="divide-y divide-d-border">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->postsForLink; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $typeLabel = match($post['type']) {
                        'text'      => 'Texto',
                        'image'     => 'Imagem IA',
                        'video'     => 'Vídeo',
                        'pinterest' => 'Pinterest',
                        default     => $post['type'],
                    };
                    $result = $post['result'] ?? [];
                ?>
                <div class="px-4 py-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold" style="color:var(--color-primary)"><?php echo e($typeLabel); ?></span>
                        <span class="text-xs text-d-muted"><?php echo e(\Carbon\Carbon::parse($post['created_at'])->format('d/m H:i')); ?></span>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($post['type'] === 'text' && $post['status'] === 'completed'): ?>
                    <p class="text-sm font-medium text-d-text"><?php echo e($result['title'] ?? ''); ?></p>
                    <p class="text-xs text-d-muted mt-1"><?php echo e($result['description'] ?? ''); ?></p>

                    <?php elseif($post['type'] === 'image' && $post['status'] === 'completed'): ?>
                    <img src="<?php echo e($result['image_url'] ?? ''); ?>" alt="Imagem gerada" class="rounded-lg max-h-40 object-cover">
                    <div class="mt-2">
                        <a href="<?php echo e($result['image_url'] ?? '#'); ?>" download class="text-xs font-medium" style="color:var(--color-primary)">↓ Baixar imagem</a>
                    </div>

                    <?php elseif($post['type'] === 'video' && $post['status'] === 'completed'): ?>
                    <video src="<?php echo e($result['video_url'] ?? ''); ?>" controls class="rounded-lg w-full max-h-40"></video>
                    <div class="mt-2">
                        <a href="<?php echo e($result['video_url'] ?? '#'); ?>" download class="text-xs font-medium" style="color:var(--color-primary)">↓ Baixar vídeo</a>
                    </div>

                    <?php elseif($post['type'] === 'pinterest' && $post['status'] === 'completed'): ?>
                    <p class="text-xs text-d-muted"><?php echo e(count($result['pins'] ?? [])); ?> pins encontrados para "<?php echo e($result['query'] ?? ''); ?>"</p>

                    <?php elseif($post['status'] === 'pending' || $post['status'] === 'processing'): ?>
                    <p class="text-xs text-yellow-400">● Processando...</p>

                    <?php elseif($post['status'] === 'failed'): ?>
                    <p class="text-xs text-red-400">● Falhou: <?php echo e($post['error_message'] ?? 'Erro desconhecido'); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> 
    </div>
</div>
<?php /**PATH /home/calixto/shopee-backend/resources/views/livewire/dashboard/posts.blade.php ENDPATH**/ ?>