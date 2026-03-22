<?php $__env->startSection('title'); ?> Asignar Delegados <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
.user-card{transition:all .2s;cursor:pointer;border:2px solid transparent}
.user-card:hover{transform:translateY(-3px);box-shadow:0 4px 8px rgba(0,0,0,0.1);border-color:#0ab39c}
.user-card.selected{border-color:#0ab39c;background-color:#f0f9f8}
.role-badge{font-size:.8rem;padding:.3rem .6rem}
.current-assignment{background-color:#e8f5e9;border-left:4px solid #0ab39c;padding:1rem;margin-bottom:1.5rem;border-radius:.375rem}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('voting-tables.index')); ?>">Mesas</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('voting-tables.show', $votingTable)); ?>">Mesa <?php echo e($votingTable->number); ?><?php echo e($votingTable->letter ?? ''); ?></a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Asignar Delegados <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-user-star-line me-1"></i>Asignar Delegados - Mesa <?php echo e($votingTable->number); ?><?php echo e($votingTable->letter ?? ''); ?></h5>
        <a href="<?php echo e(route('voting-tables.show', $votingTable)); ?>" class="btn btn-soft-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Volver
        </a>
    </div>
    <div class="card-body">
        <div class="alert alert-info small">
            <i class="ri-information-line me-1"></i>
            <strong>Recinto:</strong> <?php echo e($votingTable->institution->name); ?> |
            <strong>Código OEP:</strong> <?php echo e($votingTable->oep_code ?? 'N/A'); ?> |
            <strong>Código Interno:</strong> <?php echo e($votingTable->internal_code ?? 'N/A'); ?>

        </div>

        <div class="current-assignment">
            <h6 class="mb-3 fw-semibold">
                <i class="ri-user-settings-line me-1"></i>
                Delegados Actualmente Asignados
            </h6>
            <div class="row g-3">
                <?php
                    $currentDelegates = [
                        'president' => ['label' => 'Presidente', 'color' => 'primary'],
                        'secretary' => ['label' => 'Secretario', 'color' => 'success'],
                        'vocal1' => ['label' => 'Vocal 1', 'color' => 'info'],
                        'vocal2' => ['label' => 'Vocal 2', 'color' => 'warning'],
                        'vocal3' => ['label' => 'Vocal 3', 'color' => 'secondary'],
                    ];
                ?>
                <?php $__currentLoopData = $currentDelegates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $relation = $field . '_id';
                        $userId = $votingTable->$relation;
                        $user = $userId ? \App\Models\User::find($userId) : null;
                    ?>
                    <div class="col-md-4">
                        <span class="badge bg-<?php echo e($info['color']); ?> role-badge me-1"><?php echo e($info['label']); ?>:</span>
                        <?php if($user): ?>
                            <strong><?php echo e($user->name); ?> <?php echo e($user->last_name); ?></strong>
                            <small class="text-muted d-block"><?php echo e($user->email); ?></small>
                        <?php else: ?>
                            <span class="text-muted">No asignado</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($votingTable->vocal4_name): ?>
                    <div class="col-md-4">
                        <span class="badge bg-dark role-badge me-1">Vocal 4:</span>
                        <strong><?php echo e($votingTable->vocal4_name); ?></strong>
                        <small class="text-muted d-block">Externo</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <form action="<?php echo e(route('voting-tables.assign-delegates.store', $votingTable)); ?>" method="POST">
            <?php echo csrf_field(); ?>

            <h5 class="mb-3">Seleccionar Nuevos Delegados</h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Presidente de Mesa</label>
                    <select class="form-select <?php $__errorArgs = ['president_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="president_id">
                        <option value="">— Sin asignar —</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"
                                <?php echo e(old('president_id', $votingTable->president_id) == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> (<?php echo e($user->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['president_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Secretario</label>
                    <select class="form-select <?php $__errorArgs = ['secretary_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="secretary_id">
                        <option value="">— Sin asignar —</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"
                                <?php echo e(old('secretary_id', $votingTable->secretary_id) == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> (<?php echo e($user->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['secretary_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Vocal 1</label>
                    <select class="form-select <?php $__errorArgs = ['vocal1_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="vocal1_id">
                        <option value="">— Sin asignar —</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"
                                <?php echo e(old('vocal1_id', $votingTable->vocal1_id) == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> (<?php echo e($user->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['vocal1_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Vocal 2</label>
                    <select class="form-select <?php $__errorArgs = ['vocal2_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="vocal2_id">
                        <option value="">— Sin asignar —</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"
                                <?php echo e(old('vocal2_id', $votingTable->vocal2_id) == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> (<?php echo e($user->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['vocal2_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label fw-semibold">Vocal 3</label>
                    <select class="form-select <?php $__errorArgs = ['vocal3_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="vocal3_id">
                        <option value="">— Sin asignar —</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>"
                                <?php echo e(old('vocal3_id', $votingTable->vocal3_id) == $user->id ? 'selected' : ''); ?>>
                                <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> (<?php echo e($user->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['vocal3_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Vocal 4 (Externo)</label>
                    <input type="text" class="form-control <?php $__errorArgs = ['vocal4_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           name="vocal4_name" value="<?php echo e(old('vocal4_name', $votingTable->vocal4_name)); ?>"
                           placeholder="Nombre completo del vocal externo">
                    <?php $__errorArgs = ['vocal4_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <small class="text-muted">Para personas no registradas en el sistema</small>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="<?php echo e(route('voting-tables.show', $votingTable)); ?>" class="btn btn-soft-secondary">
                        <i class="ri-close-line me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Guardar Asignaciones
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-tables\assign-delegates.blade.php ENDPATH**/ ?>