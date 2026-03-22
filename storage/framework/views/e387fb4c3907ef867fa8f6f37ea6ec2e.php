<?php $__env->startSection('title'); ?> Editar Mesa <?php echo e($votingTable->number); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css')); ?>" rel="stylesheet"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('voting-tables.index')); ?>">Mesas</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('voting-tables.show', $votingTable)); ?>">Mesa <?php echo e($votingTable->number); ?><?php echo e($votingTable->letter ?? ''); ?></a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Editar Mesa <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            <i class="ri-pencil-line me-1"></i>Editando: <span class="text-primary">Mesa <?php echo e($votingTable->number); ?><?php echo e($votingTable->letter ?? ''); ?></span></h5>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('voting-tables.show', $votingTable)); ?>" class="btn btn-soft-info btn-sm">
                <i class="ri-eye-line me-1"></i>Ver
            </a>
            <a href="<?php echo e(route('voting-tables.index')); ?>" class="btn btn-soft-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('voting-tables.update', $votingTable)); ?>" method="POST" id="votingTableForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('voting-tables.partials.form-fields', [
                'votingTable' => $votingTable,
                'institutions' => $institutions,
                'users' => $users ?? [],
                'departments' => $departments ?? [],
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="<?php echo e(route('voting-tables.show', $votingTable)); ?>" class="btn btn-soft-secondary">
                    <i class="ri-close-line me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Actualizar Mesa
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js')); ?>"></script>
<?php echo $__env->make('voting-tables.scripts.voting-table-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-tables\edit.blade.php ENDPATH**/ ?>