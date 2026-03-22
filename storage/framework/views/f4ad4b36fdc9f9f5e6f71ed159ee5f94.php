
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:50px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th>
                    <a href="<?php echo e(route('voting-tables.index', array_merge(request()->query(),['sort'=>'institution_id','direction'=>request('sort')=='institution_id'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Recinto
                        <?php if(request('sort')=='institution_id'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i>
                        <?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="<?php echo e(route('voting-tables.index', array_merge(request()->query(),['sort'=>'oep_code','direction'=>request('sort')=='oep_code'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Código OEP
                        <?php if(request('sort')=='oep_code'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i>
                        <?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th>Código Interno</th>
                <th>
                    <a href="<?php echo e(route('voting-tables.index', array_merge(request()->query(),['sort'=>'number','direction'=>request('sort')=='number'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        N° Mesa
                        <?php if(request('sort')=='number'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i>
                        <?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="<?php echo e(route('voting-tables.index', array_merge(request()->query(),['sort'=>'expected_voters','direction'=>request('sort')=='expected_voters'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Electores
                        <?php if(request('sort')=='expected_voters'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i>
                        <?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th>Votaron</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $votingTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $te = $vt->elections->first();
                $status = $te?->status ?? 'sin_configurar';
                $total = $te?->total_voters ?? 0;
                $expected = $vt->expected_voters ?? 0;
                $pct = $expected > 0 ? round(($total / $expected) * 100, 1) : 0;
                $stColors = [
                    'configurada' => 'secondary',
                    'en_espera' => 'info',
                    'votacion' => 'primary',
                    'en_escrutinio' => 'warning',
                    'escrutada' => 'success',
                    'observada' => 'danger',
                    'transmitida' => 'success',
                    'anulada' => 'dark',
                    'sin_configurar' => 'light',
                ];
                $stLabels = [
                    'configurada' => 'Configurada',
                    'en_espera' => 'En espera',
                    'votacion' => 'Votación',
                    'en_escrutinio' => 'Escrutinio',
                    'escrutada' => 'Escrutada',
                    'observada' => 'Observada',
                    'transmitida' => 'Transmitida',
                    'anulada' => 'Anulada',
                    'sin_configurar' => 'Sin config.',
                ];
                $typeLabels = ['mixta' => 'Mixta', 'masculina' => 'Masculina', 'femenina' => 'Femenina'];
            ?>
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input child-checkbox" type="checkbox"
                               name="selected_ids[]" value="<?php echo e($vt->id); ?>">
                    </div>
                </td>
                <td>
                    <div class="fw-semibold"><?php echo e($vt->institution->name ?? 'N/A'); ?></div>
                    <small class="text-muted"><?php echo e($vt->institution->code ?? ''); ?></small>
                </td>
                <td>
                    <span class="badge bg-primary-subtle text-primary font-monospace">
                        <?php echo e($vt->oep_code ?? '—'); ?>

                    </span>
                </td>
                <td>
                    <span class="badge bg-info-subtle text-info font-monospace">
                        <?php echo e($vt->internal_code ?? '—'); ?>

                    </span>
                </td>
                <td>
                    <span class="fw-semibold"><?php echo e($vt->number); ?></span><?php echo e($vt->letter ? ' ' . $vt->letter : ''); ?>

                    <br><small class="text-muted"><?php echo e($typeLabels[$vt->type] ?? $vt->type); ?></small>
                </td>
                <td>
                    <span class="fw-semibold"><?php echo e(number_format($expected)); ?></span>
                </td>
                <td>
                    <span class="fw-semibold"><?php echo e(number_format($total)); ?></span>
                    <br><small class="text-<?php echo e($pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'muted')); ?>"><?php echo e($pct); ?>%</small>
                </td>
                <td>
                    <span class="badge bg-<?php echo e($stColors[$status] ?? 'secondary'); ?>-subtle text-<?php echo e($stColors[$status] ?? 'secondary'); ?>">
                        <?php echo e($stLabels[$status] ?? $status); ?>

                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_mesas')): ?>
                        <a href="<?php echo e(route('voting-tables.show', $vt)); ?>"
                           class="btn btn-sm btn-soft-info" title="Ver detalles">
                            <i class="ri-eye-line"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_mesas')): ?>
                        <a href="<?php echo e(route('voting-tables.edit', $vt)); ?>"
                           class="btn btn-sm btn-soft-warning" title="Editar">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <a href="<?php echo e(route('voting-tables.election-config', $vt)); ?>"
                           class="btn btn-sm btn-soft-primary" title="Configuración Electoral">
                            <i class="ri-settings-4-line"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_mesas')): ?>
                        <button class="btn btn-sm btn-soft-danger remove-item-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                            data-id="<?php echo e($vt->id); ?>"
                            data-oep="<?php echo e($vt->oep_code); ?>"
                            data-internal="<?php echo e($vt->internal_code); ?>"
                            data-delete-url="<?php echo e(route('voting-tables.destroy', $vt)); ?>"
                            title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="9" class="text-center py-5">
                    <i class="ri-table-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                    <p class="text-muted mb-1">No se encontraron mesas con los filtros aplicados.</p>
                    <?php if(request()->hasAny(['search','institution_id','status','type'])): ?>
                    <a href="<?php echo e(route('voting-tables.index')); ?>" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-close-line me-1"></i>Limpiar filtros
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views/voting-tables/partials/table.blade.php ENDPATH**/ ?>