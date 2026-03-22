<div class="p-3">
    <?php
        $summary = collect($tables);
        $totalCount = $summary->count();
        $pendingCount = $summary->where('state','pending')->count();
        $partialCount = $summary->where('state','partial')->count();
        $completeCount = $summary->where('state','complete')->count();
    ?>
    <div class="row g-2 mb-2">
        <div class="col-3">
            <div class="card border-0 bg-light text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Total</small>
                    <h3 class="mb-0 fw-bold"><?php echo e($totalCount); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card border-0 bg-danger bg-opacity-10 text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Pendientes</small>
                    <h3 class="mb-0 fw-bold text-danger"><?php echo e($pendingCount); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card border-0 bg-warning bg-opacity-10 text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Parciales</small>
                    <h3 class="mb-0 fw-bold text-warning"><?php echo e($partialCount); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card border-0 bg-success bg-opacity-10 text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Completas</small>
                    <h3 class="mb-0 fw-bold text-success"><?php echo e($completeCount); ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%;">Mesa</th>
                    <th style="width: 25%;" class="text-center">Estado</th>
                    <th style="width: 25%;" class="text-center">Votos</th>
                    <th style="width: 25%;" class="text-center">Validación</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $tables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="fw-semibold">Mesa <?php echo e($t['number']); ?></td>
                    <td class="text-center">
                        <?php if($t['state'] == 'pending'): ?>
                            <span class="badge bg-danger px-2 py-1" style="font-size: 10px;">Pendiente</span>
                        <?php elseif($t['state'] == 'partial'): ?>
                            <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 10px;">Parcial</span>
                        <?php else: ?>
                            <span class="badge bg-success px-2 py-1" style="font-size: 10px;">Completa</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center fw-bold"><?php echo e(number_format($t['votes'])); ?></td>
                    <td class="text-center">
                        <?php if($t['validated']): ?>
                            <span class="badge bg-success px-2 py-1" style="font-size: 10px;">✔ Validada</span>
                        <?php else: ?>
                            <span class="badge bg-secondary px-2 py-1" style="font-size: 10px;">Pendiente</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">
                        <small>No hay mesas registradas</small>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-2 text-center">
        <small class="text-muted" style="font-size: 9px;">
            <i class="ri-information-line"></i>
            Actualizado: <?php echo e(now()->format('d/m/Y H:i')); ?>

        </small>
    </div>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\partials\institution-tables-content.blade.php ENDPATH**/ ?>