
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:50px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th style="width:48px;">Foto</th>
                <?php
                    $sortCols = [
                        'name'              => 'Nombre',
                        'party'             => 'Partido',
                        'election_type'     => 'Tipo Elección',
                        'election_category' => 'Categoría',
                    ];
                ?>
                <?php $__currentLoopData = $sortCols; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $col => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th>
                    <a href="<?php echo e(route('candidates.index', array_merge(request()->query(), [
                            'sort'      => $col,
                            'direction' => (request('sort') === $col && request('direction') === 'asc') ? 'desc' : 'asc',
                        ]))); ?>" class="sort-link">
                        <?php echo e($label); ?>

                        <?php if(request('sort') === $col): ?>
                            <i class="ri-arrow-<?php echo e(request('direction') === 'asc' ? 'up' : 'down'); ?>-s-line"></i>
                        <?php else: ?>
                            <i class="ri-arrow-up-down-line text-muted opacity-50"></i>
                        <?php endif; ?>
                    </a>
                </th>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <th>Color</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $candidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input child-checkbox" type="checkbox"
                               name="selected_ids[]" value="<?php echo e($candidate->id); ?>">
                    </div>
                </td>
                <td>
                    <?php if($candidate->photo): ?>
                        <img src="<?php echo e($candidate->photo_url); ?>" alt="<?php echo e($candidate->name); ?>"
                             class="avatar-xs rounded-circle" style="object-fit:cover;">
                    <?php else: ?>
                        <div class="avatar-xs bg-light rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-user-line text-muted" style="font-size:.9rem"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="fw-semibold"><?php echo e($candidate->name); ?></div>
                    <?php if($candidate->list_name): ?>
                        <small class="text-muted"><?php echo e($candidate->list_name); ?>

                            <?php if($candidate->list_order): ?> · #<?php echo e($candidate->list_order); ?> <?php endif; ?>
                        </small>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <?php if($candidate->party_logo): ?>
                            <img src="<?php echo e($candidate->party_logo_url); ?>" alt="<?php echo e($candidate->party); ?>"
                                 style="width:24px;height:24px;object-fit:contain;flex-shrink:0;">
                        <?php endif; ?>
                        <div>
                            <div class="fw-semibold"><?php echo e($candidate->party); ?></div>
                            <?php if($candidate->party_full_name): ?>
                                <small class="text-muted"><?php echo e(Str::limit($candidate->party_full_name, 30)); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
                <td>
                    <small class="text-muted"><?php echo e($candidate->electionTypeCategory?->electionType?->name ?? '—'); ?></small>
                </td>
                <td>
                    <?php if($candidate->electionTypeCategory?->electionCategory): ?>
                        <?php $cat = $candidate->electionTypeCategory->electionCategory; ?>
                        <span class="badge bg-primary-subtle text-primary" style="font-size:.68rem;">
                            <?php echo e($cat->code); ?>

                        </span>
                        <small class="text-muted d-block" style="font-size:.68rem;">
                            <?php echo e($cat->name); ?>

                        </small>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($candidate->color): ?>
                        <span class="color-dot" style="background-color:<?php echo e($candidate->color); ?>;"
                              title="<?php echo e($candidate->color); ?>"></span>
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>

                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view_candidatos')): ?>
                        <button type="button" class="btn btn-sm btn-soft-info view-item-btn"
                            data-bs-toggle="modal" data-bs-target="#viewCandidateModal"
                            data-id="<?php echo e($candidate->id); ?>"
                            data-name="<?php echo e($candidate->name); ?>"
                            data-party="<?php echo e($candidate->party); ?>"
                            data-party_full_name="<?php echo e($candidate->party_full_name); ?>"
                            data-color="<?php echo e($candidate->color); ?>"
                            data-election_type="<?php echo e($candidate->electionTypeCategory?->electionType?->name ?? 'N/A'); ?>"
                            data-election_category="<?php echo e($candidate->electionTypeCategory?->electionCategory?->name ?? 'N/A'); ?>"
                            data-election_category_code="<?php echo e($candidate->electionTypeCategory?->electionCategory?->code ?? ''); ?>"
                            data-ballot_order="<?php echo e($candidate->electionTypeCategory?->ballot_order ?? ''); ?>"
                            data-votes_per_person="<?php echo e($candidate->electionTypeCategory?->votes_per_person ?? 1); ?>"
                            data-list_order="<?php echo e($candidate->list_order); ?>"
                            data-list_name="<?php echo e($candidate->list_name); ?>"
                            data-department_name="<?php echo e($candidate->department?->name ?? ''); ?>"
                            data-province_name="<?php echo e($candidate->province?->name ?? ''); ?>"
                            data-municipality_name="<?php echo e($candidate->municipality?->name ?? ''); ?>"
                            data-photo-url="<?php echo e($candidate->photo_url); ?>"
                            data-party-logo-url="<?php echo e($candidate->party_logo_url); ?>"
                            data-active="<?php echo e($candidate->active ? '1' : '0'); ?>"
                            title="Ver detalles">
                            <i class="ri-eye-line"></i>
                        </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_candidatos')): ?>
                        <button type="button" class="btn btn-sm btn-soft-warning edit-item-btn"
                            data-bs-toggle="modal" data-bs-target="#candidateModal"
                            data-id="<?php echo e($candidate->id); ?>"
                            data-update-url="<?php echo e(route('candidates.update', $candidate->id)); ?>"
                            data-name="<?php echo e($candidate->name); ?>"
                            data-party="<?php echo e($candidate->party); ?>"
                            data-party_full_name="<?php echo e($candidate->party_full_name); ?>"
                            data-color="<?php echo e($candidate->color); ?>"
                            data-election_type_category_id="<?php echo e($candidate->election_type_category_id); ?>"
                            data-list_order="<?php echo e($candidate->list_order); ?>"
                            data-list_name="<?php echo e($candidate->list_name); ?>"
                            data-department_id="<?php echo e($candidate->department_id); ?>"
                            data-province_id="<?php echo e($candidate->province_id); ?>"
                            data-municipality_id="<?php echo e($candidate->municipality_id); ?>"
                            data-photo-url="<?php echo e($candidate->photo_url); ?>"
                            data-party-logo-url="<?php echo e($candidate->party_logo_url); ?>"
                            data-active="<?php echo e($candidate->active ? '1' : '0'); ?>"
                            title="Editar">
                            <i class="ri-pencil-line"></i>
                        </button>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_candidatos')): ?>
                        <button class="btn btn-sm btn-soft-danger remove-item-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                            data-id="<?php echo e($candidate->id); ?>"
                            data-name="<?php echo e($candidate->name); ?>"
                            data-delete-url="<?php echo e(route('candidates.destroy', $candidate->id)); ?>"
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
                    <i class="ri-user-search-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                    <p class="text-muted mb-1">No hay candidatos que coincidan con los filtros aplicados.</p>
                    <?php if(request()->hasAny(['search','election_type_id','election_type_category_id','department_id','province_id'])): ?>
                    <a href="<?php echo e(route('candidates.index')); ?>" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-close-line me-1"></i>Limpiar filtros
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\candidates\partials\table.blade.php ENDPATH**/ ?>