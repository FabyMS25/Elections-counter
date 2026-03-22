<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" 
      data-topbar="light" >
<head>
    <meta charset="utf-8" />
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link rel="shortcut icon" href="<?php echo e(URL::asset('build/images/logo_elections.png')); ?>">
    <?php echo $__env->make('layouts.head-css', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<?php echo $__env->yieldContent('body'); ?>
    <div id="layout-wrapper">
        <?php echo $__env->yieldContent('content'); ?>        
    </div>
    <?php echo $__env->make('layouts.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
    <?php echo $__env->yieldContent('script'); ?>
</body>
</html>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\layouts\master-without-nav.blade.php ENDPATH**/ ?>