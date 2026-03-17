
<?php if(isset($tableStats) && ($tableStats['total'] ?? 0) > 0): ?>
<?php
    $total     = $tableStats['total'] ?? 0;
    $gPending  = ($tableStats['configurada'] ?? 0) + ($tableStats['en_espera'] ?? 0);
    $gVoting   = $tableStats['votacion']      ?? 0;
    $gCounting = $tableStats['en_escrutinio'] ?? 0;
    $gDone     = ($tableStats['escrutada'] ?? 0) + ($tableStats['transmitida'] ?? 0);
    $gObserved = $tableStats['observada']     ?? 0;
    $gAnnulled = $tableStats['anulada']       ?? 0;
    $pct       = fn($n) => $total > 0 ? round(($n / $total) * 100, 1) : 0;
?>
<div class="mb-2 mt-1">
    <div class="progress" style="height:18px;border-radius:4px;">
        <?php if($gPending > 0): ?>
        <div class="progress-bar bg-secondary" role="progressbar"
             style="width:<?php echo e($pct($gPending)); ?>%;font-size:.7rem"
             title="Sin iniciar: <?php echo e($gPending); ?>">
            <?php if($pct($gPending) >= 8): ?><?php echo e($pct($gPending)); ?>%<?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($gVoting > 0): ?>
        <div class="progress-bar bg-primary" role="progressbar"
             style="width:<?php echo e($pct($gVoting)); ?>%;font-size:.7rem"
             title="Votación: <?php echo e($gVoting); ?>">
            <?php if($pct($gVoting) >= 8): ?><?php echo e($pct($gVoting)); ?>%<?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($gCounting > 0): ?>
        <div class="progress-bar bg-warning text-dark" role="progressbar"
             style="width:<?php echo e($pct($gCounting)); ?>%;font-size:.7rem"
             title="Escrutinio: <?php echo e($gCounting); ?>">
            <?php if($pct($gCounting) >= 8): ?><?php echo e($pct($gCounting)); ?>%<?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($gObserved > 0): ?>
        <div class="progress-bar bg-danger" role="progressbar"
             style="width:<?php echo e($pct($gObserved)); ?>%;font-size:.7rem"
             title="Observadas: <?php echo e($gObserved); ?>">
            <?php if($pct($gObserved) >= 8): ?><?php echo e($pct($gObserved)); ?>%<?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($gDone > 0): ?>
        <div class="progress-bar bg-success" role="progressbar"
             style="width:<?php echo e($pct($gDone)); ?>%;font-size:.7rem"
             title="Completadas: <?php echo e($gDone); ?>">
            <?php if($pct($gDone) >= 8): ?><?php echo e($pct($gDone)); ?>%<?php endif; ?>
        </div>
        <?php endif; ?>
        <?php if($gAnnulled > 0): ?>
        <div class="progress-bar bg-dark" role="progressbar"
             style="width:<?php echo e($pct($gAnnulled)); ?>%;font-size:.7rem"
             title="Anuladas: <?php echo e($gAnnulled); ?>">
            <?php if($pct($gAnnulled) >= 8): ?><?php echo e($pct($gAnnulled)); ?>%<?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="d-flex flex-wrap gap-3 mt-1" style="font-size:.72rem;color:#74788d">
        <?php if($gPending): ?>  <span><span class="badge bg-secondary">&nbsp;</span> Sin iniciar <?php echo e($gPending); ?></span><?php endif; ?>
        <?php if($gVoting): ?>   <span><span class="badge bg-primary">&nbsp;</span> Votación <?php echo e($gVoting); ?></span><?php endif; ?>
        <?php if($gCounting): ?> <span><span class="badge bg-warning text-dark">&nbsp;</span> Escrutinio <?php echo e($gCounting); ?></span><?php endif; ?>
        <?php if($gObserved): ?> <span><span class="badge bg-danger">&nbsp;</span> Observadas <?php echo e($gObserved); ?></span><?php endif; ?>
        <?php if($gDone): ?>     <span><span class="badge bg-success">&nbsp;</span> Completadas <?php echo e($gDone); ?></span><?php endif; ?>
        <?php if($gAnnulled): ?> <span><span class="badge bg-dark">&nbsp;</span> Anuladas <?php echo e($gAnnulled); ?></span><?php endif; ?>
        <span class="ms-auto fw-semibold text-dark"><?php echo e($total); ?> mesas en total</span>
    </div>
</div>
<?php endif; ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views/voting-table-votes/partials/quick-stats.blade.php ENDPATH**/ ?>