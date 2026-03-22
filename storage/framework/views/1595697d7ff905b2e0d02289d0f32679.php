
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:48px;">#</th>
                <th>Candidato</th>
                <th>Partido</th>
                <th>Votos</th>
                <th>%</th>
            </tr>
        </thead>
        <tbody>
            <?php $sorted = collect($stats)->sortByDesc('votes')->values(); ?>
            <?php $__empty_1 = true; $__currentLoopData = $sorted; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $colors = ['success', 'info', 'warning'];
                    $color  = $colors[$i] ?? 'secondary';
                    $width  = max(2, $stat['percentage']);
                ?>
                <tr>
                    <td>
                        <span class="badge bg-<?php echo e($color); ?> rounded-pill">#<?php echo e($i + 1); ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if($stat['candidate']->photo): ?>
                                <img src="<?php echo e(asset('storage/'.$stat['candidate']->photo)); ?>"
                                     class="rounded-circle"
                                     style="width:28px;height:28px;object-fit:cover;" alt="">
                            <?php endif; ?>
                            <div>
                                <h6 class="mb-0 small"><?php echo e($stat['candidate']->name); ?></h6>
                                <small class="text-muted">
                                    <?php echo e($stat['candidate']->party_full_name ?? $stat['candidate']->party); ?>

                                </small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border"><?php echo e($stat['candidate']->party); ?></span>
                    </td>
                    <td><strong><?php echo e(number_format($stat['votes'])); ?></strong></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="min-width:36px;"><?php echo e($stat['percentage']); ?>%</span>
                            <div class="progress flex-grow-1" style="height:6px;min-width:60px;">
                                <div class="progress-bar bg-<?php echo e($color); ?>"
                                     role="progressbar" style="width:<?php echo e($width); ?>%"></div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Sin votos registrados aún</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\partials\dashboard-candidates-table.blade.php ENDPATH**/ ?>