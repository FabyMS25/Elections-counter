<?php $__env->startSection('title'); ?> Delegaciones — <?php echo e($user->name); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet"/>
<style>
.dt-pill{display:inline-block;padding:.2rem .65rem;border-radius:.3rem;font-size:.72rem;font-weight:600}
.dt-recinto{background:#dcfce7;color:#15803d}
.dt-mesa   {background:#cffafe;color:#0e7490}
.delegate-type-card{transition:all .15s}
.delegate-type-card.selected{border-color:#0ab39c!important;background:#e7f9f5!important}
.delegate-type-card.selected i{color:#0ab39c!important}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Usuarios <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('users.show', $user)); ?>"><?php echo e($user->name); ?></a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Delegaciones <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card mb-3">
    <div class="card-body py-2">
        <div class="d-flex align-items-center gap-3">
            <img src="<?php echo e($user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                 alt="" class="rounded-circle" style="width:40px;height:40px;object-fit:cover">
            <div>
                <div class="fw-semibold small"><?php echo e($user->name); ?> <?php echo e($user->last_name); ?></div>
                <small class="text-muted"><?php echo e($user->email); ?></small>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-soft-warning btn-sm">
                    <i class="ri-shield-user-line me-1"></i>Roles y Permisos
                </a>
                <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-soft-secondary btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Perfil
                </a>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info small py-2 mb-3">
    <i class="ri-information-line me-1"></i>
    Las delegaciones asignan al usuario a un <strong>Recinto</strong> o <strong>Mesa específica</strong>
    con una función y credencial. Son independientes de los roles del sistema —
    los roles se gestionan desde <a href="<?php echo e(route('users.edit', $user)); ?>">Roles y Permisos</a>.
</div>
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div>
            <h5 class="card-title mb-0">Delegaciones asignadas</h5>
            <small class="text-muted">El usuario puede tener múltiples delegaciones en distintos recintos y mesas</small>
        </div>
        <?php if(auth()->user()->hasPermission('assign_delegates')): ?>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
            <i class="ri-add-line me-1"></i>Nueva Delegación
        </button>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Función</th>
                        <th>Recinto</th>
                        <th>Mesa</th>
                        <th>Credencial</th>
                        <th>Estado</th>
                        <th>Vigencia</th>
                        <th>Asignado por</th>
                        <?php if(auth()->user()->hasPermission('assign_delegates')): ?>
                        <th class="text-end">Acción</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <?php $isMesa = in_array($a->delegate_type, $mesaTypes); ?>
                            <span class="dt-pill <?php echo e($isMesa ? 'dt-mesa' : 'dt-recinto'); ?>">
                                <?php echo e($delegateTypes[$a->delegate_type] ?? $a->delegate_type); ?>

                            </span>
                        </td>
                        <td>
                            <?php if($a->institution): ?>
                            <div class="fw-semibold small"><?php echo e($a->institution->name); ?></div>
                            <small class="text-muted"><?php echo e($a->institution->code); ?></small>
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($a->votingTable): ?>
                            <div class="small">
                                <i class="ri-table-line text-info me-1"></i>
                                Mesa <?php echo e($a->votingTable->number); ?><?php echo e($a->votingTable->letter ? ' '.$a->votingTable->letter : ''); ?>

                            </div>
                            <small class="text-muted"><?php echo e($a->votingTable->institution?->name); ?></small>
                            <?php else: ?>
                            <small class="text-muted">Todas las mesas</small>
                            <?php endif; ?>
                        </td>
                        <td><small><?php echo e($a->credential_number ?? '—'); ?></small></td>
                        <td>
                            <?php $s = $a->status; ?>
                            <span class="badge <?php echo e($s==='activo'?'bg-success':($s==='suspendido'?'bg-warning':'bg-secondary')); ?>">
                                <?php echo e(ucfirst($s)); ?>

                            </span>
                        </td>
                        <td>
                            <small class="d-block text-muted">
                                <?php echo e($a->assignment_date ? \Carbon\Carbon::parse($a->assignment_date)->format('d/m/Y') : '—'); ?>

                            </small>
                            <?php if($a->expiration_date): ?>
                            <small class="text-danger">hasta <?php echo e(\Carbon\Carbon::parse($a->expiration_date)->format('d/m/Y')); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><small class="text-muted"><?php echo e($a->assignedBy?->name ?? '—'); ?></small></td>
                        <?php if(auth()->user()->hasPermission('assign_delegates')): ?>
                        <td class="text-end">
                            <form method="POST"
                                  action="<?php echo e(route('users.delegaciones.remove', [$user, $a->id])); ?>"
                                  onsubmit="return confirm('¿Eliminar esta delegación? Esta acción no se puede deshacer.')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-soft-danger">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="ri-map-pin-line d-block mb-2" style="font-size:2rem"></i>
                            <p class="mb-1">Sin delegaciones asignadas</p>
                            <small>Los roles del sistema se gestionan en
                                <a href="<?php echo e(route('users.edit', $user)); ?>">Roles y Permisos</a>
                            </small>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if(auth()->user()->hasPermission('assign_delegates')): ?>
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="ri-add-circle-line me-2"></i>Nueva Delegación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('users.delegaciones.add', $user)); ?>" id="formAdd">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                1. Función del delegado <span class="text-danger">*</span>
                            </label>
                            <div class="row g-2" id="delegateTypeCards">
                                <?php $__currentLoopData = $delegateTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isMesa = in_array($value, $mesaTypes);
                                    $icon = match($value) {
                                        'delegado_general' => 'ri-user-star-line',
                                        'delegado_mesa'    => 'ri-table-line',
                                        'presidente'       => 'ri-award-line',
                                        'secretario'       => 'ri-edit-2-line',
                                        'vocal'            => 'ri-mic-line',
                                        'tecnico'          => 'ri-tools-line',
                                        'observador'       => 'ri-eye-line',
                                        default            => 'ri-user-line',
                                    };
                                ?>
                                <div class="col-6 col-md-3">
                                    <div class="border rounded p-2 text-center delegate-type-card"
                                         style="cursor:pointer" data-value="<?php echo e($value); ?>"
                                         data-needs-mesa="<?php echo e($isMesa ? '1' : '0'); ?>"
                                         onclick="selectDelegateType('<?php echo e($value); ?>', <?php echo e($isMesa ? 'true' : 'false'); ?>)">
                                        <i class="<?php echo e($icon); ?> d-block mb-1" style="font-size:1.3rem;color:#74788d"></i>
                                        <div class="small fw-semibold"><?php echo e($label); ?></div>
                                        <span class="badge mt-1 <?php echo e($isMesa ? 'bg-info' : 'bg-success'); ?>"
                                              style="font-size:.55rem">
                                            <?php echo e($isMesa ? 'Mesa específica' : 'Solo recinto'); ?>

                                        </span>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <input type="hidden" name="delegate_type" id="selectedDelegateType">
                            <div id="dtError" class="text-danger small mt-1" style="display:none">
                                Selecciona una función.
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                2. Recinto <span class="text-danger">*</span>
                            </label>
                            <select class="form-select" name="institution_id" id="addInstitutionId" required>
                                <option value="">Seleccione recinto…</option>
                                <?php $__currentLoopData = $institutions->groupBy(fn($i) => $i->municipality?->name ?? 'Sin municipio'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $muni => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <optgroup label="<?php echo e($muni); ?>">
                                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($inst->id); ?>"><?php echo e($inst->name); ?> (<?php echo e($inst->code); ?>)</option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-12" id="fieldMesa" style="display:none">
                            <label class="form-label fw-semibold">
                                3. Mesa <span class="text-danger">*</span>
                                <small class="text-muted fw-normal">(selecciona primero el recinto)</small>
                            </label>
                            <select class="form-select" name="voting_table_id" id="addVotingTableId" disabled>
                                <option value="">Primero seleccione recinto…</option>
                                <?php $__currentLoopData = $votingTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($vt->id); ?>" data-inst="<?php echo e($vt->institution_id); ?>">
                                    Mesa <?php echo e($vt->number); ?><?php echo e($vt->letter ? ' '.$vt->letter : ''); ?>

                                    — <?php echo e($vt->institution?->name); ?>

                                    (<?php echo e($vt->oep_code ?? $vt->internal_code); ?>)
                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                N° Credencial <small class="text-muted">(opcional)</small>
                            </label>
                            <input type="text" class="form-control" name="credential_number"
                                   placeholder="Ej: CRED-2026-001">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Fecha asignación</label>
                            <input type="date" class="form-control" name="assignment_date"
                                   value="<?php echo e(now()->format('Y-m-d')); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Fecha expiración <small class="text-muted">(opcional)</small>
                            </label>
                            <input type="date" class="form-control" name="expiration_date">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                Observaciones <small class="text-muted">(opcional)</small>
                            </label>
                            <textarea class="form-control" name="observations" rows="2"
                                      placeholder="Notas adicionales…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Guardar Delegación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<script>
let needsMesa = false;

function selectDelegateType(value, requiresMesa) {
    document.getElementById('selectedDelegateType').value = value;
    needsMesa = requiresMesa;
    document.querySelectorAll('.delegate-type-card').forEach(card => {
        const sel = card.dataset.value === value;
        card.classList.toggle('selected', sel);
    });
    document.getElementById('fieldMesa').style.display = requiresMesa ? '' : 'none';
    if (!requiresMesa) {
        const vtSel = document.getElementById('addVotingTableId');
        vtSel.value = '';
        vtSel.disabled = true;
    }
    document.getElementById('dtError').style.display = 'none';
}
document.getElementById('addInstitutionId')?.addEventListener('change', function () {
    const instId = this.value;
    const vtSel  = document.getElementById('addVotingTableId');
    vtSel.value   = '';
    vtSel.disabled = !instId || !needsMesa;

    Array.from(vtSel.options).forEach(o => {
        if (!o.value) return;
        o.style.display = o.dataset.inst === instId ? '' : 'none';
    });
});
document.getElementById('formAdd')?.addEventListener('submit', function (e) {
    const dt = document.getElementById('selectedDelegateType').value;
    if (!dt) {
        e.preventDefault();
        document.getElementById('dtError').style.display = '';
        Swal.fire({ icon: 'warning', title: 'Selecciona una función',
                    text: 'Debes elegir el tipo de delegación antes de guardar.' });
        return;
    }
    if (!document.getElementById('addInstitutionId').value) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Recinto requerido',
                    text: 'Selecciona el recinto para esta delegación.' });
        return;
    }
    if (needsMesa && !document.getElementById('addVotingTableId').value) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Mesa requerida',
                    text: 'Este tipo de función requiere seleccionar una mesa específica.' });
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views/users/delegaciones.blade.php ENDPATH**/ ?>