<?php $__env->startSection('title'); ?> Editar <?php echo e($institution->name); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css')); ?>" rel="stylesheet"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('institutions.index')); ?>">Recintos</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('institutions.show', $institution)); ?>"><?php echo e($institution->name); ?></a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Editar Recinto <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-pencil-line me-1"></i>Editando: <span class="text-primary"><?php echo e($institution->name); ?></span></h5>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('institutions.show', $institution)); ?>" class="btn btn-soft-info btn-sm">
                <i class="ri-eye-line me-1"></i>Ver
            </a>
            <a href="<?php echo e(route('institutions.index')); ?>" class="btn btn-soft-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('institutions.update', $institution)); ?>" method="POST" id="institutionForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('institutions.partials.form-fields', [
                'institution' => $institution,
                'departments' => $departments,
                'statusOptions' => $statusOptions,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="<?php echo e(route('institutions.show', $institution)); ?>" class="btn btn-soft-secondary">
                    <i class="ri-close-line me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Actualizar Recinto
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<?php echo $__env->make('institutions.scripts.institution-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\institutions\edit.blade.php ENDPATH**/ ?>