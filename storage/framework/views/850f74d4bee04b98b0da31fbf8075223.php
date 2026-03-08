<!DOCTYPE html>
<html lang="pt-BR" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Automatize. Afilie. Lucre. — Plataforma de afiliados Shopee.">
    <title>Comissify — Automatize. Afilie. Lucre.</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style><?php echo \App\Services\ThemeService::cssVars(); ?></style>
</head>
<body class="bg-d-bg text-d-text antialiased">


<nav class="border-b border-d-border sticky top-0 z-50 backdrop-blur-sm" style="background:rgba(15,15,15,0.9)">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-sm">C</span>
            </div>
            <span class="font-bold text-d-text">Comissify</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('login')); ?>" class="btn-secondary !py-1.5 !px-4 text-sm">Entrar</a>
            <a href="<?php echo e(route('register')); ?>" class="btn-primary !py-1.5 !px-4 text-sm">Começar</a>
        </div>
    </div>
</nav>


<section class="max-w-6xl mx-auto px-6 py-20">
    <div class="grid lg:grid-cols-2 gap-14 items-center">

        
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-medium mb-6"
                 style="border-color:color-mix(in srgb,var(--color-primary) 40%,transparent);background:color-mix(in srgb,var(--color-primary) 10%,transparent);color:var(--color-primary)">
                ⚡ Automação para afiliados Shopee
            </div>
            <h1 class="text-5xl font-bold text-d-text mb-6 leading-tight">
                Automatize.<br>
                <span style="color:var(--color-primary)">Afilie.</span> Lucre.
            </h1>
            <p class="text-lg text-d-muted mb-8 leading-relaxed">
                Encontre produtos com as maiores comissões, gere links afiliados com um clique e maximize seus ganhos na Shopee.
                <span class="block mt-2 text-sm" style="color:var(--color-primary)">
                    Ao criar sua conta você recebe <strong>R$ 5,00 em créditos para testar.</strong>
                </span>
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mb-8">
                <a href="<?php echo e(route('register')); ?>" class="btn-primary !py-3 !px-8 !text-base">
                    Começar agora
                </a>
                <a href="<?php echo e(route('login')); ?>" class="btn-secondary !py-3 !px-8 !text-base">
                    Entrar na conta
                </a>
            </div>
            <p class="text-sm text-d-muted mb-8">R$ 5,00 em créditos para experimentar — sem compromisso</p>

            
            <div class="flex flex-col gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['Busque produtos com maiores comissões em segundos', 'Gere links afiliados com um clique', 'Acompanhe seu saldo de créditos em tempo real']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bullet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-2 text-sm text-d-muted">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs text-white flex-shrink-0"
                          style="background:var(--color-primary)">✓</span>
                    <?php echo e($bullet); ?>

                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <?php $screenshotCount = $screenshots->count(); ?>
        <div x-data="{
                current: 0,
                total: <?php echo e($screenshotCount); ?>,
                init() {
                    if (this.total > 1) {
                        setInterval(() => { this.current = (this.current + 1) % this.total; }, 4000);
                    }
                }
             }"
             class="relative">

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($screenshots->isNotEmpty()): ?>
                
                <div class="relative rounded-2xl overflow-hidden border border-d-border"
                     style="background:#111111;aspect-ratio:16/10;">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $screenshots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div x-show="current === <?php echo e($i); ?>"
                         x-transition:enter="transition ease-out duration-500"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute inset-0"
                         style="display:none">
                        <img src="<?php echo e(Storage::url($s->image_path)); ?>"
                             alt="<?php echo e($s->title); ?>"
                             class="w-full h-full object-contain">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s->title): ?>
                        <div class="absolute bottom-0 left-0 right-0 px-4 py-3"
                             style="background:linear-gradient(to top,rgba(0,0,0,0.7),transparent)">
                            <p class="text-xs text-white font-medium"><?php echo e($s->title); ?></p>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($screenshotCount > 1): ?>
                    <button type="button"
                            @click="current = (current - 1 + total) % total"
                            class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-white transition-opacity"
                            style="background:rgba(0,0,0,0.5)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button type="button"
                            @click="current = (current + 1) % total"
                            class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-white transition-opacity"
                            style="background:rgba(0,0,0,0.5)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($screenshotCount > 1): ?>
                <div class="flex justify-center gap-1.5 mt-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $screenshots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button"
                            @click="current = <?php echo e($i); ?>"
                            class="w-2 h-2 rounded-full transition-all duration-300"
                            :style="current === <?php echo e($i); ?> ? 'background:var(--color-primary);width:1.5rem;' : 'background:#2E2E2E'">
                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php else: ?>
                
                <div class="rounded-2xl border border-d-border p-6" style="background:#111111;aspect-ratio:16/10;">
                    <div class="h-full flex flex-col gap-3">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <div class="flex-1 h-5 rounded" style="background:#1A1A1A"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 flex-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($r = 0; $r < 4; $r++): ?>
                            <div class="h-8 rounded" style="background:#1A1A1A;opacity:<?php echo e(1 - $r * 0.15); ?>"></div>
                            <div class="h-8 rounded col-span-2" style="background:#1A1A1A;opacity:<?php echo e(1 - $r * 0.15); ?>"></div>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div class="mt-auto text-center">
                            <p class="text-xs text-d-muted">Preview da plataforma em breve</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <div class="absolute -inset-4 -z-10 rounded-3xl opacity-20 blur-3xl"
                 style="background:var(--color-primary)"></div>
        </div>

    </div>
</section>


<section class="py-20" style="background:#1A1A1A">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-d-text text-center mb-12">Como funciona</h2>
        <div class="grid md:grid-cols-3 gap-8">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['num' => '1', 'title' => 'Cadastre-se', 'desc' => 'Crie sua conta em segundos e conecte suas credenciais de afiliado da Shopee.'],
                ['num' => '2', 'title' => 'Busque produtos', 'desc' => 'Filtre por categoria ou palavra-chave e encontre os produtos mais rentáveis.'],
                ['num' => '3', 'title' => 'Gere seus links', 'desc' => 'Com um clique, gere links afiliados encurtados prontos para compartilhar.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="text-center">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl font-bold text-white mx-auto mb-4"
                     style="background:var(--color-primary)"><?php echo e($step['num']); ?></div>
                <h3 class="text-lg font-semibold text-d-text mb-2"><?php echo e($step['title']); ?></h3>
                <p class="text-d-muted text-sm leading-relaxed"><?php echo e($step['desc']); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($packages->isNotEmpty()): ?>
<section id="pacotes" class="py-20">
    <div class="max-w-6xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-d-text text-center mb-3">Pacotes de créditos</h2>
        <p class="text-d-muted text-center mb-12">Escolha o pacote ideal para o seu volume de trabalho</p>
        <div class="grid md:grid-cols-3 gap-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pkg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card relative <?php echo e($pkg->is_featured ? 'border-2' : ''); ?>"
                 style="<?php echo e($pkg->is_featured ? 'border-color:var(--color-primary)' : ''); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pkg->is_featured): ?>
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-xs font-bold text-white"
                     style="background:var(--color-primary)">Mais popular</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h3 class="text-lg font-bold text-d-text mb-1"><?php echo e($pkg->name); ?></h3>
                <div class="flex items-baseline gap-1 mb-4">
                    <span class="text-3xl font-bold" style="color:var(--color-primary)">R$ <?php echo e(number_format($pkg->price, 2, ',', '.')); ?></span>
                </div>
                <div class="border-t border-d-border pt-4 mb-5">
                    <p class="text-d-muted text-sm">
                        <strong class="text-d-text">R$ <?php echo e(number_format($pkg->tokens_amount, 2, ',', '.')); ?></strong> em créditos
                    </p>
                </div>
                <a href="<?php echo e(route('register')); ?>"
                   class="btn-primary w-full justify-center !py-2.5"
                   style="<?php echo e($pkg->is_featured ? '' : 'background:var(--color-primary-dark)'); ?>">
                    Escolher plano
                </a>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<section class="py-20" style="background:#1A1A1A">
    <div class="max-w-3xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-d-text text-center mb-12">Perguntas frequentes</h2>
        <div class="space-y-3" x-data="{ open: null }">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                ['q' => 'O que são créditos?', 'a' => 'Créditos são o saldo da sua conta, usado para realizar buscas de produtos e gerar links afiliados. Cada busca consome R$ 0,20 em créditos, garantindo uso justo da plataforma.'],
                ['q' => 'Preciso ser aprovado no programa de afiliados da Shopee?', 'a' => 'Sim. Para usar o Comissify, você precisa já participar do Programa de Afiliados Shopee e ter solicitado a liberação de acesso à API diretamente no app da Shopee. Sem essa aprovação, não é possível conectar.'],
                ['q' => 'Como conecto minha conta Shopee?', 'a' => 'Após criar sua conta no Comissify, você será direcionado para uma tela de conexão onde inserirá seu App ID e sua Chave de acesso (fornecidos pela Shopee ao liberar a API). A validação é feita automaticamente.'],
                ['q' => 'Como funciona a cobrança?', 'a' => 'Você compra pacotes de créditos e usa conforme necessário. Não há assinatura mensal — você paga apenas o que usar.'],
                ['q' => 'Posso cancelar a qualquer momento?', 'a' => 'Sim. Não há fidelidade. Seus créditos não expiram e você pode parar de usar quando quiser.'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card !p-0 overflow-hidden">
                <button type="button" @click="open === <?php echo e($i); ?> ? open = null : open = <?php echo e($i); ?>"
                        class="w-full text-left px-5 py-4 flex items-center justify-between gap-4">
                    <span class="font-medium text-d-text text-sm"><?php echo e($faq['q']); ?></span>
                    <svg class="w-4 h-4 text-d-muted flex-shrink-0 transition-transform" :class="open === <?php echo e($i); ?> && 'rotate-180'"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === <?php echo e($i); ?>"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     style="display:none">
                    <div class="px-5 pb-4 text-sm text-d-muted border-t border-d-border pt-4"><?php echo e($faq['a']); ?></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>


<section class="py-24 text-center">
    <div class="max-w-2xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-d-text mb-4">Pronto para começar a lucrar?</h2>
        <p class="text-d-muted mb-8">Junte-se a afiliados que já usam o Comissify para automatizar seus ganhos.</p>
        <a href="<?php echo e(route('register')); ?>" class="btn-primary !py-3 !px-10 !text-base">
            Criar minha conta
        </a>
    </div>
</section>


<footer class="py-8" style="background:#111111">
    <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md flex items-center justify-center" style="background:var(--color-primary)">
                <span class="text-white font-bold text-xs">C</span>
            </div>
            <span class="text-sm font-semibold text-d-text">Comissify</span>
            <span class="text-xs text-d-muted ml-1">— Automatize. Afilie. Lucre.</span>
        </div>
        <div class="flex items-center gap-5 text-xs text-d-muted">
            <a href="#" class="hover:text-d-text transition-colors">Termos de uso</a>
            <a href="#" class="hover:text-d-text transition-colors">Privacidade</a>
            <a href="#" class="hover:text-d-text transition-colors">Contato</a>
        </div>
    </div>
</footer>

<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /home/calixto/shopee-backend/resources/views/welcome.blade.php ENDPATH**/ ?>