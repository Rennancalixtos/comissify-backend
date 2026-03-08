<!DOCTYPE html>
<html lang="pt-BR" x-data>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($title ?? 'Comissify'); ?> — Comissify</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

    <style><?php echo \App\Services\ThemeService::cssVars(); ?></style>
</head>
<body class="bg-d-bg text-d-text antialiased min-h-screen flex items-center justify-center p-4">

<?php echo e($slot); ?>


<?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

</body>
</html>
<?php /**PATH /home/calixto/shopee-backend/resources/views/layouts/blank.blade.php ENDPATH**/ ?>