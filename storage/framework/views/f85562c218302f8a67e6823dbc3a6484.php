<?php $__env->startSection('title'); ?> Editar — <?php echo e($user->name); ?> <?php $__env->stopSection(); ?>
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
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('users.show', $user)); ?>"><?php echo e($user->name); ?></a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Editar Usuario <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Editar: <?php echo e($user->name); ?> <?php echo e($user->last_name); ?></h5>
        <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-soft-secondary btn">
            <i class="ri-arrow-left-line me-1"></i>Volver al perfil
        </a>
    </div>
    <div class="card-body">
        <?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <form action="<?php echo e(route('users.update', $user)); ?>" method="POST" id="userForm">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('users._form', [
                'user'             => $user,
                'isEdit'           => true,
                'userRoleIds'      => $userRoleIds,
                'userDirectPermIds'=> $userDirectPermIds,
                'rolePermMap'      => $rolePermMap,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="<?php echo e(route('users.delegaciones.form', $user)); ?>" class="btn btn-soft-primary btn">
                    <i class="ri-map-pin-line me-1"></i>Gestionar Delegaciones
                </a>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-soft-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<?php echo $__env->make('users._form_js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\users\edit.blade.php ENDPATH**/ ?>