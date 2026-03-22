
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:40px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th>
                    <a href="<?php echo e(route('institutions.index', array_merge(request()->query(),['sort'=>'code','direction'=>request('sort')=='code'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Código <?php if(request('sort')=='code'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i><?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th>
                    <a href="<?php echo e(route('institutions.index', array_merge(request()->query(),['sort'=>'name','direction'=>request('sort')=='name'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Recinto <?php if(request('sort')=='name'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i><?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th>Ubicación</th>
                <th>
                    <a href="<?php echo e(route('institutions.index', array_merge(request()->query(),['sort'=>'registered_citizens','direction'=>request('sort')=='registered_citizens'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Ciudadanos <?php if(request('sort')=='registered_citizens'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i><?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th class="text-center">Mesas</th>
                <th>
                    <a href="<?php echo e(route('institutions.index', array_merge(request()->query(),['sort'=>'status','direction'=>request('sort')=='status'&&request('direction')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                        Estado <?php if(request('sort')=='status'): ?><i class="ri-arrow-<?php echo e(request('direction')=='asc'?'up':'down'); ?>-s-line"></i><?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                    </a>
                </th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input child-checkbox" type="checkbox"
                               name="selected_ids[]" value="<?php echo e($institution->id); ?>">
                    </div>
                </td>
                <td>
                    <span class="badge bg-info-subtle text-info font-monospace"><?php echo e($institution->code); ?></span>
                </td>
                <td>
                    <div class="fw-semibold"><?php echo e($institution->name); ?></div>
                    <?php if($institution->short_name): ?>
                        <small class="text-muted"><?php echo e($institution->short_name); ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="fw-semibold"><?php echo e($institution->locality->municipality->name ?? 'N/A'); ?></span>
                    <br><small class="text-muted"><?php echo e($institution->locality->name ?? ''); ?></small>
                </td>
                <td>
                    <span class="fw-semibold"><?php echo e(number_format($institution->registered_citizens ?? 0)); ?></span>
                </td>
                <td class="text-center">
                    <span class="badge bg-primary-subtle text-primary"><?php echo e($institution->voting_tables_count ?? 0); ?></span>
                </td>
                <td>
                    <?php
                        $stColors = ['activo'=>'success','inactivo'=>'danger','en_mantenimiento'=>'warning'];
                        $stLabels = ['activo'=>'Activo','inactivo'=>'Inactivo','en_mantenimiento'=>'Mantenimiento'];
                    ?>
                    <span class="badge bg-<?php echo e($stColors[$institution->status] ?? 'secondary'); ?>-subtle text-<?php echo e($stColors[$institution->status] ?? 'secondary'); ?>">
                        <?php echo e($stLabels[$institution->status] ?? $institution->status); ?>

                    </span>
                    <?php if(!$institution->is_operative): ?>
                        <br><small class="text-warning"><i class="ri-alert-line"></i> No operativo</small>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_recintos')): ?>
                        <a href="<?php echo e(route('institutions.show', $institution->id)); ?>"
                           class="btn btn-sm btn-soft-info" title="Ver detalles">
                            <i class="ri-eye-line"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_recintos')): ?>
                        <a href="<?php echo e(route('institutions.edit', $institution->id)); ?>"
                           class="btn btn-sm btn-soft-warning" title="Editar">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_recintos')): ?>
                        <button class="btn btn-sm btn-soft-danger remove-item-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                            data-id="<?php echo e($institution->id); ?>"
                            data-name="<?php echo e($institution->name); ?>"
                            data-delete-url="<?php echo e(route('institutions.destroy', $institution->id)); ?>"
                            title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="ri-building-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                    <p class="text-muted mb-1">No se encontraron recintos con los filtros aplicados.</p>
                    <?php if(request()->hasAny(['search','department_id','status','operative'])): ?>
                    <a href="<?php echo e(route('institutions.index')); ?>" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-close-line me-1"></i>Limpiar filtros
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\institutions\partials\table.blade.php ENDPATH**/ ?>