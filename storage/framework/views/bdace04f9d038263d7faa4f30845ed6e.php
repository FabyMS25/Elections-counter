<?php $__env->startSection('title'); ?> Configuración de Perfil <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<style>
.avatar-upload{width:140px;height:140px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,.15)}
.avatar-edit{position:relative;display:inline-block}
.avatar-edit .avatar-edit-btn{position:absolute;bottom:0;right:0;background:#0ab39c;border:2px solid #fff;border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;transition:all .2s}
.avatar-edit .avatar-edit-btn:hover{background:#099885;transform:scale(1.1)}
.avatar-dropdown-container {
    position: relative;
    width: 100%;
    margin-top: 15px;
}
.avatar-dropdown-btn {
    width: 100%;
    padding: 8px 12px;
    background: #f8f9fa;
    border: 1px solid #e2e5e8;
    border-radius: 6px;
    text-align: left;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s;
}
.avatar-dropdown-btn:hover {
    background: #e9ecef;
    border-color: #0ab39c;
}
.avatar-dropdown-btn .selected-avatar-preview {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
}
.avatar-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e5e8;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 1000;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    margin-top: 4px;
    padding: 8px;
}
.avatar-dropdown-menu.show {
    display: block;
}
.avatar-option {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 12px;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s;
    margin-bottom: 4px;
}
.avatar-option:hover {
    background: #f0f9f7;
}
.avatar-option.selected {
    background: #e6f7f2;
    border-left: 3px solid #0ab39c;
}
.avatar-option img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}
.avatar-option .avatar-info {
    flex: 1;
}
.avatar-option .avatar-name {
    font-weight: 500;
    font-size: 0.9rem;
}
.avatar-option .avatar-desc {
    font-size: 0.75rem;
    color: #6c757d;
}
.btn-group-xs > .btn, .btn-xs {
    padding: .25rem .4rem;
    font-size: .75rem;
    border-radius: .2rem;
}
.info-row {
    display: flex;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.info-row:last-child {
    border-bottom: none;
}
.info-k {
    width: 120px;
    font-size: 0.85rem;
    color: #64748b;
}
.info-v {
    flex: 1;
    font-size: 0.9rem;
    font-weight: 500;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('profile.index')); ?>">Mi Perfil</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Configuración de Perfil <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-image-line me-1"></i>Foto de Perfil</h5>
            </div>
            <div class="card-body text-center">
                <div class="avatar-edit mb-2">
                    <img src="<?php echo e($user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-op-m.png')); ?>"
                         alt="avatar" class="avatar-upload" id="avatar-preview">
                    <form id="avatar-form" action="<?php echo e(route('profile.avatar')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="file" id="avatar-input" name="avatar" accept="image/*" style="display:none">
                        <label for="avatar-input" class="avatar-edit-btn" title="Subir foto personalizada">
                            <i class="ri-camera-line"></i>
                        </label>
                    </form>
                </div>
                <div class="btn-group w-100" role="group">
                    <?php
                        $currentGender = old('gender',
                            ($user->avatar && str_contains($user->avatar, '-w.')) ? 'w' : 'm'
                        );
                    ?>
                    <input type="radio" class="btn-check" name="gender" id="genderM" value="m" autocomplete="off"
                           <?php echo e($currentGender === 'm' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-secondary" for="genderM">
                        <i class="ri-men-line"></i> Masculino
                    </label>
                    <input type="radio" class="btn-check" name="gender" id="genderW" value="w" autocomplete="off"
                           <?php echo e($currentGender === 'w' ? 'checked' : ''); ?>>
                    <label class="btn btn-outline-secondary" for="genderW">
                        <i class="ri-women-line"></i> Femenino
                    </label>
                </div>
                <div class="avatar-dropdown-container">
                    <?php
                        $currentAvatar = $user->avatar ?? 'avatar-op-m.png';
                        $avatars = [
                            ['file' => 'avatar-admin-m.png', 'name' => 'Administrador', 'gender' => 'm', 'tier' => 'admin'],
                            ['file' => 'avatar-admin-w.png', 'name' => 'Administradora', 'gender' => 'w', 'tier' => 'admin'],
                            ['file' => 'avatar-manager-m.png', 'name' => 'Coordinador', 'gender' => 'm', 'tier' => 'manager'],
                            ['file' => 'avatar-manager-w.png', 'name' => 'Coordinadora', 'gender' => 'w', 'tier' => 'manager'],
                            ['file' => 'avatar-op-m.png', 'name' => 'Operador', 'gender' => 'm', 'tier' => 'op'],
                            ['file' => 'avatar-op-w.png', 'name' => 'Operadora', 'gender' => 'w', 'tier' => 'op'],
                        ];
                        $currentAvatarData = collect($avatars)->firstWhere('file', $currentAvatar) ?? $avatars[4];
                    ?>
                    <button type="button" class="avatar-dropdown-btn" id="avatarDropdownBtn">
                        <img src="<?php echo e(URL::asset('build/images/users/'.$currentAvatar)); ?>"
                             class="selected-avatar-preview" id="selectedAvatarPreview">
                        <span class="flex-grow-1 text-start" id="selectedAvatarName">
                            <?php echo e($currentAvatarData['name']); ?>

                        </span>
                        <i class="ri-arrow-down-s-line"></i>
                    </button>
                    <div class="avatar-dropdown-menu" id="avatarDropdownMenu">
                        <?php $__currentLoopData = $avatars; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $avatar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="avatar-option <?php echo e($currentAvatar === $avatar['file'] ? 'selected' : ''); ?>"
                                 data-avatar="<?php echo e($avatar['file']); ?>"
                                 data-gender="<?php echo e($avatar['gender']); ?>"
                                 data-name="<?php echo e($avatar['name']); ?>"
                                 onclick="selectAvatar('<?php echo e($avatar['file']); ?>', '<?php echo e($avatar['gender']); ?>', '<?php echo e($avatar['name']); ?>')">
                                <img src="<?php echo e(URL::asset('build/images/users/'.$avatar['file'])); ?>"
                                     alt="<?php echo e($avatar['name']); ?>">
                                <div class="avatar-info">
                                    <div class="avatar-name"><?php echo e($avatar['name']); ?></div>
                                    <div class="avatar-desc">
                                        <?php echo e($avatar['tier'] === 'admin' ? 'Nivel Administrativo' : ($avatar['tier'] === 'manager' ? 'Nivel Coordinación' : 'Nivel Operativo')); ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <form id="avatar-select-form" action="<?php echo e(route('profile.avatar-select')); ?>" method="POST" style="display:none;">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="avatar" id="selected-avatar-input">
                </form>
                <p class="text-muted small mt-2 mb-0">
                    <i class="ri-information-line me-1"></i>
                    Puedes subir tu propia foto o elegir un avatar del catálogo
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-information-line me-1"></i>Información de la Cuenta</h5>
            </div>
            <div class="card-body p-2">
                <div class="info-row">
                    <div class="info-k"><i class="ri-calendar-line me-2"></i>Miembro desde</div>
                    <div class="info-v"><?php echo e($user->created_at?->format('d/m/Y')); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k"><i class="ri-history-line me-2"></i>Último acceso</div>
                    <div class="info-v"><?php echo e($user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k"><i class="ri-shield-user-line me-2"></i>Estado</div>
                    <div class="info-v">
                        <span class="badge bg-<?php echo e($user->is_active ? 'success' : 'danger'); ?>">
                            <?php echo e($user->is_active ? 'Activo' : 'Inactivo'); ?>

                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-k"><i class="ri-fingerprint-line me-2"></i>Carnet</div>
                    <div class="info-v"><?php echo e($user->id_card ?? 'No registrado'); ?></div>
                </div>
                <?php
                    $mainRole = $user->roles->first();
                    $roleTier = 'op';
                    $roleName = 'Operador';
                    if ($mainRole) {
                        $roleName = strtolower($mainRole->name);
                        if (str_contains($roleName, 'admin')) {
                            $roleTier = 'admin';
                            $roleName = 'Administrador';
                        } elseif (str_contains($roleName, 'supervisor')) {
                            $roleTier = 'manager';
                            $roleName = 'Coordinador';
                        } else {
                            $roleName = 'Operador';
                        }
                    }
                ?>
                <div class="info-row">
                    <div class="info-k"><i class="ri-user-star-line me-2"></i>Rol principal</div>
                    <div class="info-v">
                        <span class="badge bg-info">
                            <?php echo e($roleName); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personal-info" role="tab">
                            <i class="ri-user-line me-1"></i>Información Personal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#change-password" role="tab">
                            <i class="ri-lock-password-line me-1"></i>Cambiar Contraseña
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <div class="tab-pane active" id="personal-info" role="tabpanel">
                        <form action="<?php echo e(route('profile.update')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
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
                                           name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>">
                                    <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
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
                                           name="phone" value="<?php echo e(old('phone', $user->phone)); ?>">
                                    <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
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
                                           name="id_card" value="<?php echo e(old('id_card', $user->id_card)); ?>">
                                    <?php $__errorArgs = ['id_card'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
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
                                              name="address" rows="2"><?php echo e(old('address', $user->address)); ?></textarea>
                                    <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="reset" class="btn btn-soft-secondary">
                                            <i class="ri-close-line me-1"></i>Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i>Guardar Cambios
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="change-password" role="tabpanel">
                        <form action="<?php echo e(route('profile.password')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <div class="row g-2">
                                <div class="col-12">
                                    <label class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                    <input type="password"
                                        class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        name="current_password"
                                        value="<?php echo e(old('current_password')); ?>"
                                        required>
                                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                    <?php if(session('error')): ?>
                                        <div class="text-danger small mt-1"><?php echo e(session('error')); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                    <input type="password"
                                        class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        name="password"
                                        value="<?php echo e(old('password')); ?>"
                                        required minlength="8">
                                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                    <input type="password"
                                        class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        name="password_confirmation"
                                        value="<?php echo e(old('password_confirmation')); ?>"
                                        required>
                                    <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <div class="col-12">
                                    <div class="alert alert-info py-2">
                                        <i class="ri-information-line me-1"></i>
                                        <small>La contraseña debe tener al menos 8 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="reset" class="btn btn-soft-secondary">
                                            <i class="ri-close-line me-1"></i>Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i>Actualizar Contraseña
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionality
    const dropdownBtn = document.getElementById('avatarDropdownBtn');
    const dropdownMenu = document.getElementById('avatarDropdownMenu');

    if (dropdownBtn && dropdownMenu) {
        dropdownBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }

    // Función para seleccionar avatar predefinido
    window.selectAvatar = function(avatarFile, gender, avatarName) {
        // Actualizar radio button de género
        const genderRadio = document.querySelector(`input[name="gender"][value="${gender}"]`);
        if (genderRadio) genderRadio.checked = true;

        // Actualizar vista previa grande
        document.getElementById('avatar-preview').src = '<?php echo e(URL::asset("build/images/users")); ?>/' + avatarFile;

        // Actualizar botón del dropdown
        document.getElementById('selectedAvatarPreview').src = '<?php echo e(URL::asset("build/images/users")); ?>/' + avatarFile;
        document.getElementById('selectedAvatarName').textContent = avatarName;

        // Actualizar selección visual en dropdown
        document.querySelectorAll('.avatar-option').forEach(opt => {
            opt.classList.remove('selected');
        });
        event.currentTarget.classList.add('selected');

        // Cerrar dropdown
        dropdownMenu.classList.remove('show');

        // Enviar formulario
        document.getElementById('selected-avatar-input').value = avatarFile;
        document.getElementById('avatar-select-form').submit();
    };

    // Avatar upload personalizado
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const avatarForm = document.getElementById('avatar-form');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El archivo debe ser una imagen (JPG, PNG o GIF)'
                    });
                    this.value = '';
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'La imagen no debe superar los 2MB'
                    });
                    this.value = '';
                    return;
                }

                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    // También actualizar preview del dropdown
                    document.getElementById('selectedAvatarPreview').src = e.target.result;
                }
                reader.readAsDataURL(file);

                // Auto-submit form
                Swal.fire({
                    title: 'Actualizando foto...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        avatarForm.submit();
                    }
                });
            }
        });
    }

    // Validación de contraseñas en tiempo real
    const password = document.querySelector('input[name="password"]');
    const confirmPassword = document.querySelector('input[name="password_confirmation"]');

    if (password && confirmPassword) {
        confirmPassword.addEventListener('keyup', function() {
            if (password.value !== this.value) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Filtrar avatares por género (opcional)
    const genderRadios = document.querySelectorAll('input[name="gender"]');
    genderRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const gender = this.value;
            document.querySelectorAll('.avatar-option').forEach(opt => {
                const avatarGender = opt.dataset.gender;
                if (avatarGender === gender) {
                    opt.style.display = 'flex';
                } else {
                    opt.style.display = 'none';
                }
            });
        });
    });

    // Trigger initial filter
    const checkedGender = document.querySelector('input[name="gender"]:checked');
    if (checkedGender) {
        checkedGender.dispatchEvent(new Event('change'));
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\profile\settings.blade.php ENDPATH**/ ?>