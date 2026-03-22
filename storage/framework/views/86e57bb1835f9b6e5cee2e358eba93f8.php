
<?php if(($permissions['can_observe'] ?? false) && !$isDisabled): ?>
<div class="p-2 bg-light border-top">
    <div class="row align-items-center">
        <div class="col-md-8 small">
            <span class="text-muted">
                <span id="selected-count-<?php echo e($table->id); ?>">0</span> votos seleccionados para observar
            </span>
            <?php $__currentLoopData = $candidatesByCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryCode => $_): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="badge bg-<?php echo e($categoryColorMap[$categoryCode] ?? 'secondary'); ?> ms-2"
                      id="selected-<?php echo e($categoryCode); ?>-<?php echo e($table->id); ?>">
                    0 <?php echo e($categoryCode); ?>

                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="col-md-4 text-end">
            <button type="button"
                    class="btn btn-sm btn-warning create-observation-btn"
                    data-table-id="<?php echo e($table->id); ?>">
                <i class="ri-chat-1-line me-1"></i>Crear Observación
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row g-0 bg-light p-2 border-top small">
    <?php
        $totalValid  = array_sum(array_column($table->results_by_category ?? [], 'valid_votes'));
        $totalBlank  = array_sum(array_column($table->results_by_category ?? [], 'blank_votes'));
        $totalNull   = array_sum(array_column($table->results_by_category ?? [], 'null_votes'));
    ?>

    <div class="col-6 col-md-3">
        <span class="text-muted">Votos Válidos:</span>
        <span class="fw-bold ms-1" id="footer-valid-<?php echo e($table->id); ?>"><?php echo e($totalValid); ?></span>
    </div>
    <div class="col-6 col-md-3">
        <span class="text-muted">Votos en Blanco:</span>
        <span class="fw-bold ms-1" id="footer-blank-<?php echo e($table->id); ?>"><?php echo e($totalBlank); ?></span>
    </div>
    <div class="col-6 col-md-3">
        <span class="text-muted">Votos Nulos:</span>
        <span class="fw-bold ms-1" id="footer-null-<?php echo e($table->id); ?>"><?php echo e($totalNull); ?></span>
    </div>
    <div class="col-6 col-md-3">
        <span class="text-muted">Papeletas Sobrantes:</span>
        <span class="fw-bold ms-1"><?php echo e($table->ballots_leftover ?? 0); ?></span>
    </div>
</div>
<?php
    $hasInconsistencies = collect($table->results_by_category ?? [])
        ->contains(fn($r) => !($r['is_consistent'] ?? true));
?>

<?php if($hasInconsistencies): ?>
<div class="p-2 border-top bg-danger bg-opacity-10 small">
    <strong class="text-danger">
        <i class="ri-alert-line me-1"></i>Inconsistencias detectadas:
    </strong>
    <?php $__currentLoopData = $table->results_by_category ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(!($result['is_consistent'] ?? true)): ?>
            <div class="ms-3 text-danger">
                <?php echo e($code); ?>: <?php echo e($result['valid_votes']); ?> válidos
                + <?php echo e($result['blank_votes']); ?> blancos
                + <?php echo e($result['null_votes']); ?> nulos
                = <?php echo e($result['valid_votes'] + $result['blank_votes'] + $result['null_votes']); ?>

                <?php if($result['total_votes'] !== ($result['valid_votes'] + $result['blank_votes'] + $result['null_votes'])): ?>
                    (guardado: <?php echo e($result['total_votes']); ?>)
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-table-votes\partials\table-footer.blade.php ENDPATH**/ ?>