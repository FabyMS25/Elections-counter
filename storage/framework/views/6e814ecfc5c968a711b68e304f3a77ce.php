
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['votingTable' => null, 'institutions' => [], 'users' => [], 'departments' => [], 'provinces' => [], 'municipalities' => [], 'localities' => []]));

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

foreach (array_filter((['votingTable' => null, 'institutions' => [], 'users' => [], 'departments' => [], 'provinces' => [], 'municipalities' => [], 'localities' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php if($errors->any()): ?>
<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
    <i class="ri-error-warning-line me-1"></i>
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1 small">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-map-pin-line me-1"></i>Ubicación de la Mesa
</h6>
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <label class="form-label small">Departamento</label>
        <select class="form-select form-select-sm" id="filter-department" data-cascade>
            <option value="">-- Todos los departamentos --</option>
            <?php $__currentLoopData = $departments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($dept->id); ?>"><?php echo e($dept->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Provincia</label>
        <select class="form-select form-select-sm" id="filter-province" data-cascade disabled>
            <option value="">-- Primero seleccione departamento --</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Municipio</label>
        <select class="form-select form-select-sm" id="filter-municipality" data-cascade disabled>
            <option value="">-- Primero seleccione provincia --</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Localidad</label>
        <select class="form-select form-select-sm" id="filter-locality" data-cascade disabled>
            <option value="">-- Primero seleccione municipio --</option>
        </select>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-12">
        <label class="form-label">Recinto Electoral <span class="text-danger">*</span></label>
        <select class="form-select <?php $__errorArgs = ['institution_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                name="institution_id" id="institution-field" required>
            <option value="">— Seleccione un recinto —</option>
            <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($inst->id); ?>"
                    data-department="<?php echo e($inst->locality?->municipality?->province?->department_id); ?>"
                    data-province="<?php echo e($inst->locality?->municipality?->province_id); ?>"
                    data-municipality="<?php echo e($inst->locality?->municipality_id); ?>"
                    data-locality="<?php echo e($inst->locality_id); ?>"
                    <?php echo e(old('institution_id', $votingTable->institution_id ?? '') == $inst->id ? 'selected' : ''); ?>>
                    <?php echo e($inst->name); ?> <?php if($inst->code): ?>(<?php echo e($inst->code); ?>)<?php endif; ?>
                    <?php if($inst->locality?->name): ?> - <?php echo e($inst->locality->name); ?><?php endif; ?>
                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['institution_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?>
            <small class="text-muted">Seleccione el recinto donde se encuentra esta mesa</small>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-settings-4-line me-1"></i>Identificación de la Mesa
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-3">
        <label class="form-label">N° Mesa <span class="text-danger">*</span></label>
        <input type="number" name="number" id="number-field"
               class="form-control <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Ej: 1"
               value="<?php echo e(old('number', $votingTable->number ?? '')); ?>"
               min="1" required>
        <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-2">
        <label class="form-label">Letra</label>
        <input type="text" name="letter" id="letter-field"
               class="form-control <?php $__errorArgs = ['letter'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="A, B, C…"
               value="<?php echo e(old('letter', $votingTable->letter ?? '')); ?>"
               maxlength="1">
        <?php $__errorArgs = ['letter'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted">Opcional</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-3">
        <label class="form-label">Tipo de Mesa</label>
        <select class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="type" id="type-field">
            <option value="mixta"     <?php echo e(old('type', $votingTable->type ?? 'mixta') === 'mixta'     ? 'selected' : ''); ?>>Mixta (Hombres y Mujeres)</option>
            <option value="masculina" <?php echo e(old('type', $votingTable->type ?? '') === 'masculina'       ? 'selected' : ''); ?>>Masculina</option>
            <option value="femenina"  <?php echo e(old('type', $votingTable->type ?? '') === 'femenina'        ? 'selected' : ''); ?>>Femenina</option>
        </select>
        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-4">
        <label class="form-label">Electores Habilitados</label>
        <input type="number" name="expected_voters" id="expected-voters-field"
               class="form-control <?php $__errorArgs = ['expected_voters'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="0"
               value="<?php echo e(old('expected_voters', $votingTable->expected_voters ?? '')); ?>"
               min="0">
        <?php $__errorArgs = ['expected_voters'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted">Total del padrón electoral</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-barcode-line me-1"></i>Códigos de Identificación
    <small class="text-muted fw-normal">(se generan automáticamente si se dejan vacíos)</small>
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-6">
        <label class="form-label">Código OEP</label>
        <input type="text" name="oep_code" id="oep-code-field"
               class="form-control <?php $__errorArgs = ['oep_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Se genera automáticamente"
               value="<?php echo e(old('oep_code', $votingTable->oep_code ?? '')); ?>"
               maxlength="20">
        <?php $__errorArgs = ['oep_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted"><i class="ri-information-line"></i> Formato: [Código Recinto]-[N° Mesa][Letra]</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Código Interno</label>
        <input type="text" name="internal_code" id="internal-code-field"
               class="form-control <?php $__errorArgs = ['internal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Se genera automáticamente"
               value="<?php echo e(old('internal_code', $votingTable->internal_code ?? '')); ?>"
               maxlength="20">
        <?php $__errorArgs = ['internal_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php else: ?><small class="text-muted"><i class="ri-information-line"></i> Formato: [Código Recinto]-M[N° Mesa][Letra]</small><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-group-line me-1"></i>Rango de Votantes
    <small class="text-muted fw-normal">(rango alfabético del padrón)</small>
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-6">
        <label class="form-label">Desde (apellido)</label>
        <input type="text" name="voter_range_start_name" id="voter-range-start-field"
               class="form-control <?php $__errorArgs = ['voter_range_start_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Ej: AAAA AAAA"
               value="<?php echo e(old('voter_range_start_name', $votingTable->voter_range_start_name ?? '')); ?>">
        <?php $__errorArgs = ['voter_range_start_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <div class="col-md-6">
        <label class="form-label">Hasta (apellido)</label>
        <input type="text" name="voter_range_end_name" id="voter-range-end-field"
               class="form-control <?php $__errorArgs = ['voter_range_end_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
               placeholder="Ej: ZZZZ ZZZZ"
               value="<?php echo e(old('voter_range_end_name', $votingTable->voter_range_end_name ?? '')); ?>">
        <?php $__errorArgs = ['voter_range_end_name'];
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
    <i class="ri-chat-1-line me-1"></i>Observaciones
    <small class="text-muted fw-normal">(opcional)</small>
</h6>
<div class="row g-2 mb-2">
    <div class="col-12">
        <textarea name="observations" id="observations-field"
                  class="form-control <?php $__errorArgs = ['observations'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                  rows="2"
                  placeholder="Observaciones adicionales sobre esta mesa de votación"><?php echo e(old('observations', $votingTable->observations ?? '')); ?></textarea>
        <?php $__errorArgs = ['observations'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-tables\partials\form-fields.blade.php ENDPATH**/ ?>