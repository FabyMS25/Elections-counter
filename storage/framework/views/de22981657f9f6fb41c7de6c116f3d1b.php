
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['institution' => null, 'departments' => [], 'statusOptions' => []]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['institution' => null, 'departments' => [], 'statusOptions' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ri-error-warning-line me-1"></i>
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1 small">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-building-line me-1"></i>Datos Básicos del Recinto
</h6>
<div class="row g-2">
    <div class="col-md-8">
        <label class="form-label">Nombre del Recinto <span class="text-danger">*</span></label>
        <input type="text" id="name-field" name="name"
               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Ej: Unidad Educativa Simón Bolívar"
               value="<?php echo e(old('name', $institution->name ?? '')); ?>" required>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted">Nombre completo del recinto electoral</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Código</label>
        <input type="text" id="code-field" name="code"
               class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Se genera automáticamente"
               value="<?php echo e(old('code', $institution->code ?? '')); ?>">
        <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted"><i class="ri-information-line"></i> Dejar vacío para generar automáticamente</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Nombre Corto</label>
        <input type="text" id="short_name-field" name="short_name"
               class="form-control <?php $__errorArgs = ['short_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Ej: UE Simón Bolívar"
               value="<?php echo e(old('short_name', $institution->short_name ?? '')); ?>">
        <?php $__errorArgs = ['short_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted">Opcional — nombre abreviado para reportes y listas</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Estado del Edificio <span class="text-danger">*</span></label>
        <select class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="status" id="status-field" required>
            <option value="activo"           <?php echo e(old('status', $institution->status ?? 'activo') === 'activo'           ? 'selected' : ''); ?>>🟢 Activo</option>
            <option value="en_mantenimiento" <?php echo e(old('status', $institution->status ?? '') === 'en_mantenimiento'       ? 'selected' : ''); ?>>🟡 En Mantenimiento</option>
            <option value="inactivo"         <?php echo e(old('status', $institution->status ?? '') === 'inactivo'               ? 'selected' : ''); ?>>🔴 Inactivo</option>
        </select>
        <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Estado físico/administrativo del local</small>
    </div>
    <div class="col-md-3">
        <label class="form-label d-block">Habilitado para Elecciones</label>
        <div class="form-check form-switch mt-1">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_operative-field" name="is_operative" value="1"
                   <?php echo e(old('is_operative', $institution->is_operative ?? true) ? 'checked' : ''); ?>>
            <label class="form-check-label" for="is_operative-field" id="operative-label"></label>
        </div>
        <small class="text-muted" style="font-size:.72rem">
            Activa si participará en la jornada electoral vigente
        </small>
        <div id="operative-warning" class="alert alert-warning py-1 px-2 mt-1 mb-0 small" style="display:none;">
            <i class="ri-error-warning-line me-1"></i>Un recinto no activo no puede estar habilitado para elecciones.
        </div>
    </div>
</div>
<hr class="my-2">
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-map-pin-line me-1"></i>Ubicación Geográfica
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-3">
        <label class="form-label">Departamento <span class="text-danger">*</span></label>
        <select class="form-select <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="department_id" id="department-field"
                data-cascade-url="<?php echo e(url('institutions/provinces')); ?>"
                data-cascade-target="#province-field" required>
            <option value="">— Seleccione —</option>
            <?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($dept->id); ?>"
                    <?php echo e(old('department_id', $institution?->locality?->municipality?->province?->department_id) == $dept->id ? 'selected' : ''); ?>>
                    <?php echo e($dept->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['department_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Provincia <span class="text-danger">*</span></label>
        <select class="form-select <?php $__errorArgs = ['province_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="province_id" id="province-field"
                data-cascade-url="<?php echo e(url('institutions/municipalities')); ?>"
                data-cascade-target="#municipality-field"
                data-restore="<?php echo e(old('province_id', $institution?->locality?->municipality?->province_id)); ?>"
                required disabled>
            <option value="">— Seleccione departamento primero —</option>
        </select>
        <?php $__errorArgs = ['province_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Municipio <span class="text-danger">*</span></label>
        <select class="form-select <?php $__errorArgs = ['municipality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="municipality_id" id="municipality-field"
                data-cascade-url="<?php echo e(url('institutions/localities')); ?>"
                data-cascade-target="#locality-field"
                data-restore="<?php echo e(old('municipality_id', $institution?->locality?->municipality_id)); ?>"
                required disabled>
            <option value="">— Seleccione provincia primero —</option>
        </select>
        <?php $__errorArgs = ['municipality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Localidad <span class="text-danger">*</span></label>
        <select class="form-select <?php $__errorArgs = ['locality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="locality_id" id="locality-field"
                data-cascade-url="<?php echo e(url('institutions/districts')); ?>"
                data-cascade-target="#district-field"
                data-restore="<?php echo e(old('locality_id', $institution?->locality_id)); ?>"
                required disabled>
            <option value="">— Seleccione municipio primero —</option>
        </select>
        <?php $__errorArgs = ['locality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Distrito <small class="text-muted">(opcional)</small></label>
        <select class="form-select <?php $__errorArgs = ['district_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="district_id" id="district-field"
                data-cascade-url="<?php echo e(url('institutions/zones')); ?>"
                data-cascade-target="#zone-field"
                data-restore="<?php echo e(old('district_id', $institution?->district_id)); ?>"
                disabled>
            <option value="">— Seleccione localidad primero —</option>
        </select>
        <?php $__errorArgs = ['district_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Zona <small class="text-muted">(opcional)</small></label>
        <select class="form-select <?php $__errorArgs = ['zone_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="zone_id" id="zone-field"
                data-restore="<?php echo e(old('zone_id', $institution?->zone_id)); ?>"
                disabled>
            <option value="">— Seleccione distrito primero —</option>
        </select>
        <?php $__errorArgs = ['zone_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<hr class="my-2">
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-phone-line me-1"></i>Dirección y Contacto <small class="text-muted fw-normal">(opcional)</small>
</h6>
<div class="row g-2 mb-3">
    <div class="col-md-8">
        <label class="form-label">Dirección</label>
        <input type="text" id="address-field" name="address"
               class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Dirección exacta del recinto"
               value="<?php echo e(old('address', $institution->address ?? '')); ?>">
        <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Referencia</label>
        <input type="text" id="reference-field" name="reference"
               class="form-control <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Ej: Frente a la plaza"
               value="<?php echo e(old('reference', $institution->reference ?? '')); ?>">
        <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Latitud</label>
        <input type="text" id="latitude-field" name="latitude"
               class="form-control <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="-17.123456"
               value="<?php echo e(old('latitude', $institution->latitude ?? '')); ?>">
        <?php $__errorArgs = ['latitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Longitud</label>
        <input type="text" id="longitude-field" name="longitude"
               class="form-control <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="-65.123456"
               value="<?php echo e(old('longitude', $institution->longitude ?? '')); ?>">
        <?php $__errorArgs = ['longitude'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" id="phone-field" name="phone"
               class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="4-1234567"
               value="<?php echo e(old('phone', $institution->phone ?? '')); ?>">
        <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" id="email-field" name="email"
               class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="recinto@ejemplo.com"
               value="<?php echo e(old('email', $institution->email ?? '')); ?>">
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Responsable</label>
        <input type="text" id="responsible-field" name="responsible_name"
               class="form-control <?php $__errorArgs = ['responsible_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Nombre del encargado"
               value="<?php echo e(old('responsible_name', $institution->responsible_name ?? '')); ?>">
        <?php $__errorArgs = ['responsible_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-bar-chart-2-line me-1"></i>Datos Electorales
    <small class="text-muted fw-normal">(se actualizan automáticamente con las mesas)</small>
</h6>
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <label class="form-label">Ciudadanos Habilitados</label>
        <input type="number" id="registered_citizens-field" name="registered_citizens"
               class="form-control <?php $__errorArgs = ['registered_citizens'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               value="<?php echo e(old('registered_citizens', $institution->registered_citizens ?? 0)); ?>" min="0">
        <?php $__errorArgs = ['registered_citizens'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <small class="text-muted">Total del padrón</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Actas Computadas</label>
        <input type="number" id="total_computed_records-field" name="total_computed_records"
               class="form-control <?php $__errorArgs = ['total_computed_records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               value="<?php echo e(old('total_computed_records', $institution->total_computed_records ?? 0)); ?>" min="0">
        <?php $__errorArgs = ['total_computed_records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Actas Anuladas</label>
        <input type="number" id="total_annulled_records-field" name="total_annulled_records"
               class="form-control <?php $__errorArgs = ['total_annulled_records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               value="<?php echo e(old('total_annulled_records', $institution->total_annulled_records ?? 0)); ?>" min="0">
        <?php $__errorArgs = ['total_annulled_records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Actas Habilitadas</label>
        <input type="number" id="total_enabled_records-field" name="total_enabled_records"
               class="form-control <?php $__errorArgs = ['total_enabled_records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               value="<?php echo e(old('total_enabled_records', $institution->total_enabled_records ?? 0)); ?>" min="0">
        <?php $__errorArgs = ['total_enabled_records'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<div class="col-12 mb-2">
    <label class="form-label">Observaciones</label>
    <textarea id="observations-field" name="observations"
              class="form-control <?php $__errorArgs = ['observations'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
              placeholder="Observaciones adicionales sobre el recinto"
              rows="2"><?php echo e(old('observations', $institution->observations ?? '')); ?></textarea>
    <?php $__errorArgs = ['observations'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views/institutions/partials/form-fields.blade.php ENDPATH**/ ?>