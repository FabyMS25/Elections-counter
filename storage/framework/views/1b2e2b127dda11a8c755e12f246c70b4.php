<?php $__env->startSection('title'); ?> <?php echo e($institution->name); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/leaflet/leaflet.css')); ?>" rel="stylesheet" />
<style>
.avatar-ring{width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,.15)}
.stat-box{background:#f8f9fa;border-radius:.5rem;padding:.6rem 1rem;text-align:center;min-width:80px}
.stat-box .n{font-size:1.35rem;font-weight:700;line-height:1}
.stat-box .l{font-size:.7rem;color:#74788d;margin-top:.15rem}
.info-row{display:flex;gap:.5rem;padding:.45rem 0;border-bottom:1px solid #f3f6f9}
.info-row:last-child{border-bottom:none}
.info-k{min-width:42%;font-weight:500;color:#6c757d;font-size:.82rem}
.info-v{font-size:.82rem;color:#212529}
#map{height:300px;border-radius:.5rem;border:1px solid #e9e9ef}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> <a href="<?php echo e(route('institutions.index')); ?>">Recintos</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <?php echo e($institution->name); ?> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Detalles del Recinto <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="avatar-ring bg-light d-flex align-items-center justify-content-center">
                    <i class="ri-building-line" style="font-size:2.5rem;color:#0ab39c"></i>
                </div>
            </div>
            <div class="col">
                <h4 class="mb-0"><?php echo e($institution->name); ?></h4>
                <p class="text-muted small mb-1">
                    <span class="badge bg-info-subtle text-info font-monospace me-2"><?php echo e($institution->code); ?></span>
                    <?php if($institution->short_name): ?> <span class="me-2"><?php echo e($institution->short_name); ?></span> <?php endif; ?>
                    <?php if($institution->address): ?> &bull; <?php echo e($institution->address); ?> <?php endif; ?>
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <?php
                        $stColors = ['activo'=>'success','inactivo'=>'danger','en_mantenimiento'=>'warning'];
                        $stLabels = ['activo'=>'Activo','inactivo'=>'Inactivo','en_mantenimiento'=>'Mantenimiento'];
                    ?>
                    <span class="badge bg-<?php echo e($stColors[$institution->status] ?? 'secondary'); ?>">
                        <?php echo e($stLabels[$institution->status] ?? $institution->status); ?>

                    </span>
                    <?php if($institution->is_operative): ?>
                        <span class="badge bg-success-subtle text-success">
                            <i class="ri-flashlight-line me-1"></i>Habilitado para elecciones
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="ri-flashlight-off-line me-1"></i>No habilitado
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-auto d-none d-lg-flex gap-2">
                <div class="stat-box"><div class="n"><?php echo e($institution->votingTables->count()); ?></div><div class="l">Mesas</div></div>
                <div class="stat-box"><div class="n"><?php echo e(number_format($institution->registered_citizens ?? 0)); ?></div><div class="l">Ciudadanos</div></div>
            </div>
            <div class="col-auto d-flex gap-2 flex-wrap">
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_recintos')): ?>
                <a href="<?php echo e(route('institutions.edit', $institution)); ?>" class="btn btn-soft-warning btn" title="Editar">
                    <i class="ri-pencil-line me-1"></i>
                </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_recintos')): ?>
                <button class="btn btn-soft-danger btn" title="Eliminar"
                        onclick="confirmDelete(<?php echo e($institution->id); ?>,'<?php echo e(addslashes($institution->name)); ?>')">
                    <i class="ri-delete-bin-line me-1"></i>
                </button>
                <?php endif; ?>
                <a href="<?php echo e(route('institutions.index')); ?>" class="btn btn-soft-secondary btn" title="Volver">
                    <i class="ri-arrow-left-line me-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-information-line me-1"></i>Información Básica</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Código</div>
                    <div class="info-v"><span class="badge bg-info-subtle text-info font-monospace"><?php echo e($institution->code); ?></span></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Nombre completo</div>
                    <div class="info-v"><?php echo e($institution->name); ?></div>
                </div>
                <?php if($institution->short_name): ?>
                <div class="info-row">
                    <div class="info-k">Nombre corto</div>
                    <div class="info-v"><?php echo e($institution->short_name); ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-k">Dirección</div>
                    <div class="info-v"><?php echo e($institution->address ?? '—'); ?></div>
                </div>
                <?php if($institution->reference): ?>
                <div class="info-row">
                    <div class="info-k">Referencia</div>
                    <div class="info-v"><?php echo e($institution->reference); ?></div>
                </div>
                <?php endif; ?>
                <div class="info-row">
                    <div class="info-k">Estado</div>
                    <div class="info-v">
                        <span class="badge bg-<?php echo e($stColors[$institution->status] ?? 'secondary'); ?>">
                            <?php echo e($stLabels[$institution->status] ?? $institution->status); ?>

                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-k">Habilitado</div>
                    <div class="info-v">
                        <?php if($institution->is_operative): ?>
                            <span class="badge bg-success">Sí</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">No</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-phone-line me-1"></i>Contacto</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Teléfono</div>
                    <div class="info-v"><?php echo e($institution->phone ?? '—'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Email</div>
                    <div class="info-v"><?php echo e($institution->email ?? '—'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Responsable</div>
                    <div class="info-v"><?php echo e($institution->responsible_name ?? '—'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-map-pin-line me-1"></i>Ubicación Geográfica</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Departamento</div>
                    <div class="info-v"><?php echo e($institution->locality->municipality->province->department->name ?? '—'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Provincia</div>
                    <div class="info-v"><?php echo e($institution->locality->municipality->province->name ?? '—'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Municipio</div>
                    <div class="info-v"><?php echo e($institution->locality->municipality->name ?? '—'); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Localidad</div>
                    <div class="info-v"><?php echo e($institution->locality->name ?? '—'); ?></div>
                </div>
                <?php if($institution->district): ?>
                <div class="info-row">
                    <div class="info-k">Distrito</div>
                    <div class="info-v"><?php echo e($institution->district->name); ?></div>
                </div>
                <?php endif; ?>
                <?php if($institution->zone): ?>
                <div class="info-row">
                    <div class="info-k">Zona</div>
                    <div class="info-v"><?php echo e($institution->zone->name); ?></div>
                </div>
                <?php endif; ?>
                <?php if($institution->latitude && $institution->longitude): ?>
                <div class="info-row">
                    <div class="info-k">Coordenadas</div>
                    <div class="info-v">
                        <span class="font-monospace"><?php echo e($institution->latitude); ?>, <?php echo e($institution->longitude); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if($institution->latitude && $institution->longitude): ?>
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-map-2-line me-1"></i>Mapa</h5>
            </div>
            <div class="card-body p-2">
                <div id="map"></div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-table-line me-1"></i>Mesas de Votación (<?php echo e($institution->votingTables->count()); ?>)</h5>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_mesas')): ?>
        <a href="<?php echo e(route('voting-tables.create', ['institution_id' => $institution->id])); ?>" class="btn btn-sm btn-success">
            <i class="ri-add-line me-1"></i>Agregar Mesa
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if($institution->votingTables->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Mesa</th>
                            <th>Código</th>
                            <th>Ciudadanos</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $institution->votingTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><span class="fw-semibold"><?php echo e($table->number); ?></span></td>
                            <td><span class="badge bg-info-subtle text-info font-monospace"><?php echo e($table->code); ?></span></td>
                            <td><?php echo e(number_format($table->registered_citizens ?? 0)); ?></td>
                            <td>
                                <?php
                                    $tColors = ['activo'=>'success','cerrado'=>'secondary','pendiente'=>'warning'];
                                    $tLabels = ['activo'=>'Activo','cerrado'=>'Cerrado','pendiente'=>'Pendiente'];
                                ?>
                                <span class="badge bg-<?php echo e($tColors[$table->status] ?? 'secondary'); ?>-subtle text-<?php echo e($tColors[$table->status] ?? 'secondary'); ?>">
                                    <?php echo e($tLabels[$table->status] ?? $table->status); ?>

                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?php echo e(route('voting-tables.show', $table)); ?>" class="btn btn-sm btn-soft-info" title="Ver">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="<?php echo e(route('voting-tables.edit', $table)); ?>" class="btn btn-sm btn-soft-warning" title="Editar">
                                    <i class="ri-pencil-line"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="ri-table-line d-block mb-2" style="font-size:2.5rem"></i>
                <p class="mb-1">No hay mesas de votación registradas en este recinto</p>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create_mesas')): ?>
                <a href="<?php echo e(route('voting-tables.create', ['institution_id' => $institution->id])); ?>" class="btn btn-sm btn-primary mt-1">
                    <i class="ri-add-line me-1"></i>Agregar primera mesa
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if($institution->observations): ?>
<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri-file-text-line me-1"></i>Observaciones</h5>
    </div>
    <div class="card-body">
        <p class="mb-0"><?php echo e($institution->observations); ?></p>
    </div>
</div>
<?php endif; ?>

<form id="deleteForm" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<?php if($institution->latitude && $institution->longitude): ?>
<script src="<?php echo e(URL::asset('build/libs/leaflet/leaflet.js')); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([<?php echo e($institution->latitude); ?>, <?php echo e($institution->longitude); ?>], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    L.marker([<?php echo e($institution->latitude); ?>, <?php echo e($institution->longitude); ?>])
        .addTo(map)
        .bindPopup('<b><?php echo e($institution->name); ?></b><br><?php echo e($institution->address); ?>');
});
</script>
<?php endif; ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<script>
const CSRF = '<?php echo e(csrf_token()); ?>';
function confirmDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar recinto?',
        html: `¿Desea eliminar el recinto <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#8590a5',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        const form = document.getElementById('deleteForm');
        form.action = `/institutions/${id}`;
        form.submit();
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\institutions\show.blade.php ENDPATH**/ ?>