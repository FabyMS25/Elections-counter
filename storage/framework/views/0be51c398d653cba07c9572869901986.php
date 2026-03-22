<?php $__env->startSection('title'); ?> Crear Usuario <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet"/>
<style>
.btn-xs{padding:.15rem .45rem;font-size:.75rem}
.perm-item{transition:background .15s;border-radius:.25rem}
.role-card{transition:all .15s}
.perm-columns{columns:2;column-gap:0}
.perm-group:last-child{border-bottom:none!important}
@media(min-width:1200px){.perm-columns{columns:3}}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Usuarios <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('users.index')); ?>">Lista</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Crear Nuevo Usuario <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Nuevo Usuario</h5></div>
    <div class="card-body">
        <?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <form action="<?php echo e(route('users.store')); ?>" method="POST" id="userForm">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('users._form', [
                'user'             => null,
                'isEdit'           => false,
                'userRoleIds'      => [],
                'userDirectPermIds'=> [],
                'rolePermMap'      => $rolePermMap,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-soft-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<?php echo $__env->make('users._form_js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\users\create.blade.php ENDPATH**/ ?>