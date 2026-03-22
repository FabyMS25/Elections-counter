
<?php
    $isDisabled = in_array($table->current_status, [
                      'en_escrutinio','escrutada','transmitida','anulada'
                  ]) || !($permissions['can_register'] ?? false);

    $categoryColors   = ['primary','success','warning','info','danger','secondary','dark'];
    $categoryColorMap = [];
    $index = 0;
    foreach (array_keys($candidatesByCategory ?? []) as $code) {
        $categoryColorMap[$code] = $categoryColors[$index % count($categoryColors)];
        $index++;
    }
    $hasInconsistencies = false;
    foreach ($table->results_by_category ?? [] as $r) {
        if (!$r['is_consistent']) { $hasInconsistencies = true; break; }
    }
    $statusClasses = [
        'configurada'   => 'secondary', 'en_espera'     => 'info',
        'votacion'      => 'primary',   'en_escrutinio' => 'warning',
        'escrutada'     => 'success',   'observada'     => 'danger',
        'transmitida'   => 'success',   'anulada'       => 'dark',
        'sin_configurar'=> 'secondary',
    ];
    $statusColor = $statusClasses[$table->current_status] ?? 'secondary';
?>
<div class="card table-card status-<?php echo e($table->current_status); ?>"
     id="table-<?php echo e($table->id); ?>"
     data-table-id="<?php echo e($table->id); ?>"
     data-expected-voters="<?php echo e($table->expected_voters); ?>">
    <div class="card-header bg-light py-2">
        <div class="row align-items-center g-2">
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-1 d-flex align-items-center justify-content-center flex-shrink-0
                                bg-<?php echo e($statusColor); ?>-subtle text-<?php echo e($statusColor); ?>"
                         style="width:34px;height:34px">
                        <i class="ri-table-line" style="font-size:1.1rem"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate">
                            Mesa <?php echo e($table->number); ?>

                            <?php if($table->letter): ?><?php echo e($table->letter); ?><?php endif; ?>
                            &nbsp;<span class="text-muted fw-normal"><?php echo e($table->internal_code ?? $table->oep_code); ?></span>
                        </div>
                        <div class="text-muted" style="font-size:.7rem">
                            <i class="ri-building-2-line me-1"></i><?php echo e(Str::limit($table->institution->name ?? 'N/A', 32)); ?>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <span class="badge bg-<?php echo e($statusColor); ?>-subtle text-<?php echo e($statusColor); ?> border border-<?php echo e($statusColor); ?>-subtle d-inline-flex align-items-center gap-1">
                    <?php echo e($statusLabels[$table->current_status] ?? $table->current_status); ?>

                    <?php if($hasInconsistencies): ?>
                        <i class="ri-alert-fill text-warning ms-1" title="Inconsistencias detectadas"></i>
                    <?php endif; ?>
                </span>
                <div class="text-muted mt-1" style="font-size:.7rem">
                    <i class="ri-inbox-line me-1"></i>Ánfora:&nbsp;<span
                        class="fw-semibold text-dark cat-total-display"
                        data-display="urn-total"
                        data-table="<?php echo e($table->id); ?>"><?php echo e($table->total_voters); ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-1 flex-wrap">
                    <?php $__currentLoopData = $candidatesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryCode => $_): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $catTotal     = $table->results_by_category[$categoryCode]['total_votes'] ?? 0;
                        $isConsistent = $table->results_by_category[$categoryCode]['is_consistent'] ?? true;
                        $validVotes   = $table->results_by_category[$categoryCode]['valid_votes']  ?? 0;
                        $blankVotes   = $table->results_by_category[$categoryCode]['blank_votes']  ?? 0;
                        $nullVotes    = $table->results_by_category[$categoryCode]['null_votes']   ?? 0;
                        $cc           = $categoryColorMap[$categoryCode] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?php echo e($cc); ?>-subtle text-<?php echo e($cc); ?> border border-<?php echo e($cc); ?>-subtle"
                          style="font-size:.72rem"
                          title="<?php echo e($categoryCode); ?>: <?php echo e($validVotes); ?>V + <?php echo e($blankVotes); ?>B + <?php echo e($nullVotes); ?>N = <?php echo e($catTotal); ?>">
                        <?php echo e($categoryCode); ?>:
                        <span class="fw-bold cat-total-display"
                              data-display="cat-total"
                              data-category="<?php echo e($categoryCode); ?>"
                              data-table="<?php echo e($table->id); ?>"><?php echo e($catTotal); ?></span>
                        <?php if(!$isConsistent): ?>
                            <i class="ri-alert-fill text-warning ms-1" title="Inconsistente"></i>
                        <?php endif; ?>
                    </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if(($table->observations_count ?? 0) > 0): ?>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle view-observations"
                          data-table-id="<?php echo e($table->id); ?>"
                          style="font-size:.72rem;cursor:pointer"
                          title="Ver <?php echo e($table->observations_count); ?> observación(es) pendiente(s)">
                        <i class="ri-alert-line me-1"></i><?php echo e($table->observations_count); ?> obs.
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-end">
                <?php echo $__env->make('voting-table-votes.partials.table-actions', ['table' => $table], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </div>
        <?php echo $__env->make('voting-table-votes.partials.ballot-inputs', [
            'table'          => $table,
            'isDisabled'     => $isDisabled,
            'permissions'    => $permissions,
            'electionTypeId' => $electionTypeId,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <div class="card-body p-0">
        <?php if(empty($candidatesByCategory)): ?>
            <div class="text-center py-4">
                <i class="ri-user-search-line text-muted d-block mb-1" style="font-size:2rem"></i>
                <p class="text-muted small mb-0">No hay candidatos para esta elección</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:3%">#</th>
                            <th style="width:10%">Partido</th>
                            <?php $__currentLoopData = $candidatesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryCode => $_): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th colspan="3" class="text-center bg-<?php echo e($categoryColorMap[$categoryCode] ?? 'secondary'); ?>-subtle text-<?php echo e($categoryColorMap[$categoryCode] ?? 'secondary'); ?>">
                                <?php echo e($categoryCode); ?>

                            </th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                        <tr>
                            <th></th><th></th>
                            <?php $__currentLoopData = $candidatesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryCode => $_): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $cc = $categoryColorMap[$categoryCode] ?? 'secondary'; ?>
                            <th class="bg-<?php echo e($cc); ?>-subtle small">Candidato</th>
                            <th class="bg-<?php echo e($cc); ?>-subtle small text-center">Votos</th>
                            <th class="bg-<?php echo e($cc); ?>-subtle small text-center">Obs</th>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php echo $__env->make('voting-table-votes.partials.table-rows', [
                            'table'                => $table,
                            'candidatesByCategory' => $candidatesByCategory,
                            'permissions'          => $permissions,
                            'isDisabled'           => $isDisabled,
                            'categoryColorMap'     => $categoryColorMap,
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <?php if(($permissions['can_observe'] ?? false) && !$isDisabled && !empty($candidatesByCategory)): ?>
        <div class="px-3 py-2 bg-light border-top d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div class="small text-muted">
                <span id="selected-count-<?php echo e($table->id); ?>">0</span> votos seleccionados
                <?php $__currentLoopData = $candidatesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryCode => $_): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $cc = $categoryColorMap[$categoryCode] ?? 'secondary'; ?>
                <span class="badge bg-<?php echo e($cc); ?>-subtle text-<?php echo e($cc); ?> ms-1"
                      id="selected-<?php echo e($categoryCode); ?>-<?php echo e($table->id); ?>">0 <?php echo e($categoryCode); ?></span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button class="btn btn-sm btn-soft-warning create-observation-btn"
                    data-table-id="<?php echo e($table->id); ?>">
                <i class="ri-chat-1-line me-1"></i>Crear Observación
            </button>
        </div>
        <?php endif; ?>
        <div class="px-3 py-2 border-top bg-light d-flex flex-wrap gap-3" style="font-size:.78rem">
            <span><span class="text-muted">Válidos:</span>
                  <strong class="ms-1" id="footer-valid-<?php echo e($table->id); ?>"><?php echo e(array_sum(array_column($table->results_by_category ?? [], 'valid_votes'))); ?></strong></span>
            <span><span class="text-muted">Blancos:</span>
                  <strong class="ms-1" id="footer-blank-<?php echo e($table->id); ?>"><?php echo e(array_sum(array_column($table->results_by_category ?? [], 'blank_votes'))); ?></strong></span>
            <span><span class="text-muted">Nulos:</span>
                  <strong class="ms-1" id="footer-null-<?php echo e($table->id); ?>"><?php echo e(array_sum(array_column($table->results_by_category ?? [], 'null_votes'))); ?></strong></span>
            <span><span class="text-muted">No usadas:</span>
                  <strong class="ms-1"><?php echo e($table->elections->first()?->ballots_leftover ?? 0); ?></strong></span>
            <?php if($table->expected_voters > 0 && $table->total_voters > 0): ?>
            <?php $pp = round($table->total_voters / $table->expected_voters * 100, 1); ?>
            <span class="ms-auto">
                <span class="text-muted">Participación:</span>
                <strong class="ms-1 text-<?php echo e($pp >= 75 ? 'success' : ($pp >= 50 ? 'warning' : 'secondary')); ?>"><?php echo e($pp); ?>%</strong>
            </span>
            <?php endif; ?>
        </div>
        <?php if($hasInconsistencies): ?>
        <div class="px-3 py-2 border-top bg-warning bg-opacity-10">
            <div class="fw-semibold small text-warning mb-1">
                <i class="ri-alert-line me-1"></i>Inconsistencias detectadas:
            </div>
            <?php $__currentLoopData = $table->results_by_category ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $catCode => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(!$result['is_consistent']): ?>
                <div class="small text-danger ms-2">
                    <strong><?php echo e($catCode); ?></strong>:
                    <?php echo e($result['valid_votes']); ?>V + <?php echo e($result['blank_votes']); ?>B + <?php echo e($result['null_votes']); ?>N
                    = <?php echo e($result['valid_votes'] + $result['blank_votes'] + $result['null_votes']); ?>

                    (guardado: <?php echo e($result['total_votes']); ?>)
                </div>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-table-votes\partials\table.blade.php ENDPATH**/ ?>