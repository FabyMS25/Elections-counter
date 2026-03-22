<?php
    $isEdit         = $isEdit ?? false;
    $userRoleIds    = $userRoleIds ?? [];
    $userDirectPermIds = $userDirectPermIds ?? [];
?>
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="text-center p-3 bg-light rounded border" style="border-style:dashed!important">
            <img id="avatarPreview"
                 src="<?php echo e(URL::asset('build/images/users/'.($user?->avatar ?? 'avatar-op-m.png'))); ?>"
                 alt="avatar" class="rounded-circle mb-2"
                 style="width:80px;height:80px;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.12)">
            <p class="small text-muted mb-2" id="avatarHint">—</p>
            <div class="btn-group btn-group-sm w-100" role="group">
                <?php
                    $currentGender = old('gender',
                        ($user?->avatar && str_ends_with(pathinfo($user->avatar, PATHINFO_FILENAME), '-w')) ? 'w' : 'm'
                    );
                ?>
                <input type="radio" class="btn-check" name="gender" id="gM" value="m"
                       <?php echo e($currentGender === 'm' ? 'checked' : ''); ?>>
                <label class="btn btn-outline-secondary" for="gM"><i class="ri-men-line"></i> M</label>
                <input type="radio" class="btn-check" name="gender" id="gW" value="w"
                       <?php echo e($currentGender === 'w' ? 'checked' : ''); ?>>
                <label class="btn btn-outline-secondary" for="gW"><i class="ri-women-line"></i> F</label>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <h6 class="fw-semibold mb-3 text-muted text-uppercase small">Datos Personales</h6>
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label">Nombres <span class="text-danger">*</span></label>
                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       name="name" value="<?php echo e(old('name', $user?->name)); ?>" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Apellidos</label>
                <input type="text" class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       name="last_name" value="<?php echo e(old('last_name', $user?->last_name)); ?>">
                <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Carnet de Identidad</label>
                <input type="text" class="form-control <?php $__errorArgs = ['id_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       name="id_card" value="<?php echo e(old('id_card', $user?->id_card)); ?>">
                <?php $__errorArgs = ['id_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teléfono</label>
                <input type="text" class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       name="phone" value="<?php echo e(old('phone', $user?->phone)); ?>">
                <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-12">
                <label class="form-label">Dirección</label>
                <textarea class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          name="address" rows="2"><?php echo e(old('address', $user?->address)); ?></textarea>
                <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <h6 class="fw-semibold mb-3 text-muted text-uppercase small">Credenciales de Acceso</h6>
        <div class="row g-2">
            <div class="col-12">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       id="emailInput" name="email"
                       value="<?php echo e(old('email', $user?->email)); ?>" required>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <div id="emailFeedback" class="form-text"></div>
            </div>
            <div class="col-md-6">
                <label class="form-label">
                    Contraseña <?php if(!$isEdit): ?><span class="text-danger">*</span><?php else: ?><small class="text-muted">(dejar vacío para no cambiar)</small><?php endif; ?>
                </label>
                <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                       id="passwordInput" name="password" <?php echo e(!$isEdit ? 'required' : ''); ?>>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirmar Contraseña</label>
                <input type="password" class="form-control"
                       id="passwordConfirm" name="password_confirmation">
            </div>
            <?php if($isEdit): ?>
            <div class="col-12">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="isActive"
                           name="is_active" value="1"
                           <?php echo e(old('is_active', $user?->is_active ?? true) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="isActive">Usuario activo</label>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<hr class="my-3">
<h6 class="fw-semibold mb-3 text-muted text-uppercase small">Roles y Permisos</h6>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header py-2 bg-soft-primary">
                <h6 class="card-title mb-0 small">
                    <i class="ri-shield-user-line me-1"></i>Roles
                </h6>
            </div>
            <div class="card-body p-2" style="max-height:420px;overflow-y:auto">
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isChecked = in_array($role->id, old('roles', $userRoleIds));
                ?>
                <div class="border rounded p-2 mb-1 role-card <?php echo e($isChecked ? 'border-primary bg-primary bg-opacity-10' : ''); ?>"
                     style="cursor:pointer"
                     id="roleCard_<?php echo e($role->id); ?>"
                     onclick="document.getElementById('roleCb_<?php echo e($role->id); ?>').click()">
                    <div class="d-flex align-items-start gap-2">
                        <input type="checkbox"
                               class="form-check-input role-cb flex-shrink-0 mt-1"
                               id="roleCb_<?php echo e($role->id); ?>"
                               name="roles[]"
                               value="<?php echo e($role->id); ?>"
                               data-role-id="<?php echo e($role->id); ?>"
                               onclick="event.stopPropagation()"
                               <?php echo e($isChecked ? 'checked' : ''); ?>>
                        <div>
                            <div class="fw-semibold small"><?php echo e($role->display_name ?? $role->name); ?></div>
                            <?php if($role->description): ?>
                            <small class="text-muted"><?php echo e($role->description); ?></small>
                            <?php endif; ?>
                            <small class="text-muted d-block mt-1" style="font-size:.65rem">
                                <?php echo e($role->permissions->count()); ?> permisos
                            </small>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <div class="alert alert-info py-2 mt-2 small mb-0">
            <i class="ri-information-line me-1"></i>
            Al marcar un rol sus permisos se activan. Al desmarcarlo, los permisos <em>exclusivos</em> de ese rol se desactivan. Los permisos manuales nunca se tocan automáticamente.
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header py-2 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 small"><i class="ri-key-line me-1"></i>Permisos directos adicionales</h6>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-xs btn-soft-success" id="btnSelectAll">Todos</button>
                    <button type="button" class="btn btn-xs btn-soft-danger"  id="btnDeselectAll">Ninguno</button>
                </div>
            </div>
            <div class="card-body p-0" style="max-height:440px;overflow-y:auto;overflow-x:hidden">
                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupPermissions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $groupSlug = Str::slug($group);
                    $roleDefaultIds = collect(old('roles', $userRoleIds))
                        ->flatMap(fn($rid) => $rolePermMap[$rid] ?? [])->unique()->values()->toArray();
                ?>
                <div class="perm-group border-bottom">
                    <div class="d-flex align-items-center px-3 py-2 bg-light sticky-top" style="top:0;z-index:1">
                        <input type="checkbox" class="form-check-input group-cb flex-shrink-0 me-2"
                               id="grp_<?php echo e($groupSlug); ?>" data-group="<?php echo e($groupSlug); ?>">
                        <label class="form-check-label fw-semibold small mb-0 flex-grow-1"
                               for="grp_<?php echo e($groupSlug); ?>"><?php echo e($group); ?></label>
                        <span class="badge bg-secondary ms-2" style="font-size:.6rem"><?php echo e(count($groupPermissions)); ?></span>
                    </div>
                    <div class="px-3 py-2 perm-columns">
                        <?php $__currentLoopData = $groupPermissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isChecked = in_array($perm->id, old('permissions', $userDirectPermIds));
                        ?>
                        <div class="perm-item rounded px-1 py-1 mb-1 <?php echo e($isChecked ? 'bg-success bg-opacity-10' : ''); ?>"
                             id="permItem_<?php echo e($perm->id); ?>"
                             style="break-inside:avoid;display:block">
                            <div class="form-check mb-0">
                                <input type="checkbox"
                                       class="form-check-input perm-cb"
                                       id="perm_<?php echo e($perm->id); ?>"
                                       name="permissions[]"
                                       value="<?php echo e($perm->id); ?>"
                                       data-group="<?php echo e($groupSlug); ?>"
                                       data-state="<?php echo e($isChecked ? 'manual' : 'none'); ?>"
                                       <?php echo e($isChecked ? 'checked' : ''); ?>>
                                <label class="form-check-label small" for="perm_<?php echo e($perm->id); ?>"
                                       title="<?php echo e($perm->description); ?>">
                                    <?php echo e($perm->display_name ?? $perm->name); ?>

                                </label>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>
<script id="rolePermData" type="application/json">
<?php echo json_encode($rolePermMap, 15, 512) ?>
</script>
<script id="directPermData" type="application/json">
<?php echo json_encode($userDirectPermIds, 15, 512) ?>
</script>
<script id="roleInitData" type="application/json">
<?php echo json_encode(collect($userRoleIds)->flatMap(fn($rid) => $rolePermMap[$rid] ?? [])->unique()->values()->toArray(), 15, 512) ?>
</script>
<script>
window.__isEdit  = <?php echo e(($isEdit ?? false) ? 'true' : 'false'); ?>;
window.__userId  = <?php echo e(($user?->id) ? $user->id : 'null'); ?>;
</script>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\users\_form.blade.php ENDPATH**/ ?>