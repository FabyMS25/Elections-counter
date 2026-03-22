<?php $__env->startSection('title'); ?> Nuevo Recinto Electoral <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('institutions.index')); ?>">Recintos</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Nuevo Recinto Electoral <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-add-line me-1"></i>Nuevo Recinto Electoral</h5>
        <a href="<?php echo e(route('institutions.index')); ?>" class="btn btn-soft-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Volver
        </a>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('institutions.store')); ?>" method="POST" id="institutionForm">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('institutions.partials.form-fields', [
                'institution' => null,
                'departments' => $departments,
                'statusOptions' => $statusOptions,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="<?php echo e(route('institutions.index')); ?>" class="btn btn-soft-secondary">
                    <i class="ri-close-line me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Crear Recinto
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>
<?php echo $__env->make('institutions.scripts.institution-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\institutions\create.blade.php ENDPATH**/ ?>