
<?php
    $te = $table->elections->first();
    $ballotsInUrn    = $te?->total_voters    ?? 0;
    $ballotsLeftover = $te?->ballots_leftover ?? 0;
    $ballotsSpoiled  = $te?->ballots_spoiled  ?? 0;
    $expectedVoters  = $table->expected_voters ?? 0;

    $expectedLeftover  = max(0, $expectedVoters - $ballotsInUrn - $ballotsSpoiled);
    $hasEnteredLeftover = $te && $te->ballots_leftover !== null && ($ballotsLeftover > 0 || $ballotsInUrn > 0);
    $accountedTotal    = $ballotsInUrn + $ballotsLeftover + $ballotsSpoiled;
    $balanceOk         = $hasEnteredLeftover && ($accountedTotal === $expectedVoters);
    $balanceDiff       = $accountedTotal - $expectedVoters;
    $participation     = ($expectedVoters > 0) ? round(($ballotsInUrn / $expectedVoters) * 100, 1) : 0;
    $canEdit           = ($permissions['can_register'] ?? false) && !$isDisabled;
?>

<div class="ballot-data-section mt-2 border rounded bg-white px-2 py-2"
     id="ballot-data-<?php echo e($table->id); ?>"
     data-table-id="<?php echo e($table->id); ?>"
     data-expected-voters="<?php echo e($expectedVoters); ?>">
    <h6 class="fw-semibold mb-2 text-muted text-uppercase small">
        <i class="ri-file-paper-line me-1"></i>Conteo de Papeletas
    </h6>

    <div class="row align-items-start g-2">
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-group-line"></i> Habilitados
                <span class="badge-readonly">padrón</span>
            </div>
            <div class="ballot-value text-info fw-bold mt-1"><?php echo e(number_format($expectedVoters)); ?></div>
            <div class="ballot-hint">del registro electoral</div>
        </div>
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-inbox-line"></i> En ánfora
                <span class="badge-auto">auto</span>
            </div>
            <div class="ballot-value text-primary fw-bold mt-1" id="urn-count-<?php echo e($table->id); ?>">
                <?php echo e(number_format($ballotsInUrn)); ?>

            </div>
            <div class="ballot-hint">válidos + blancos + nulos</div>
        </div>
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-file-list-3-line"></i> No utilizadas
                <?php if($canEdit): ?> <span class="badge-input-lbl">del acta</span> <?php endif; ?>
            </div>
            <?php if($canEdit): ?>
                <input type="number"
                       id="leftover-<?php echo e($table->id); ?>"
                       class="ballot-input ballot-leftover-input mt-1"
                       data-table="<?php echo e($table->id); ?>"
                       value="<?php echo e(($ballotsLeftover > 0 || ($te && $te->ballots_leftover !== null)) ? $ballotsLeftover : ''); ?>"
                       min="0" max="<?php echo e($expectedVoters); ?>"
                       placeholder="<?php echo e(str_pad($expectedLeftover, 3, '0', STR_PAD_LEFT)); ?>"
                       title="Copiar del acta física">
            <?php else: ?>
                <div class="ballot-value fw-bold mt-1"><?php echo e(number_format($ballotsLeftover)); ?></div>
            <?php endif; ?>
            
        </div>
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-delete-bin-line"></i> Deterioradas
                <span class="badge-optional">opcional</span>
            </div>
            <?php if($canEdit): ?>
                <input type="number"
                       id="spoiled-<?php echo e($table->id); ?>"
                       class="ballot-input ballot-spoiled-input mt-1"
                       data-table="<?php echo e($table->id); ?>"
                       value="<?php echo e($ballotsSpoiled > 0 ? $ballotsSpoiled : ''); ?>"
                       min="0" placeholder="0"
                       title="Papeletas dañadas (generalmente 0)">
            <?php else: ?>
                <div class="ballot-value fw-bold mt-1"><?php echo e(number_format($ballotsSpoiled)); ?></div>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-4">
            <div class="ballot-label    ">
                <i class="ri-percent-line"></i> Participación &amp; Cuadre
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="progress flex-grow-1" style="height:8px;min-width:60px">
                    <div class="progress-bar bg-<?php echo e($participation >= 75 ? 'success' : ($participation >= 50 ? 'warning' : 'secondary')); ?>"
                         style="width:<?php echo e(min(100,$participation)); ?>%"></div>
                </div>
                <span class="fw-bold small text-<?php echo e($participation >= 75 ? 'success' : ($participation >= 50 ? 'warning' : 'secondary')); ?>"
                      id="participation-<?php echo e($table->id); ?>">
                    <?php echo e($participation); ?>%
                </span>
            </div>
            <div id="ballot-balance-<?php echo e($table->id); ?>">
                <?php if(!$hasEnteredLeftover): ?>
                    <div class="badge-balance badge-balance-warn">
                        <i class="ri-pencil-line me-1"></i>
                        Ingrese las <strong>No utilizadas</strong> del acta para verificar el cuadre
                    </div>
                <?php elseif($balanceOk): ?>
                    <div class="badge-balance badge-balance-ok">
                        <i class="ri-checkbox-circle-line me-1"></i>
                        Cuadre correcto ✓ — <?php echo e(number_format($accountedTotal)); ?> = <?php echo e(number_format($expectedVoters)); ?> habilitados
                    </div>
                <?php else: ?>
                    <div class="badge-balance badge-balance-err"
                         title="Ánfora(<?php echo e($ballotsInUrn); ?>) + NoUsadas(<?php echo e($ballotsLeftover); ?>) + Det.(<?php echo e($ballotsSpoiled); ?>) = <?php echo e($accountedTotal); ?> ≠ <?php echo e($expectedVoters); ?>">
                        <i class="ri-alert-line me-1"></i>
                        No cuadra: diferencia de <?php echo e(abs($balanceDiff)); ?> papeleta(s)
                        (<?php echo e($balanceDiff > 0 ? 'sobran' : 'faltan'); ?>)
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="ballot-formula mt-2">
        <i class="ri-equals-line me-1 text-muted"></i>
        <span><strong>Ánfora</strong> <span class="fw-bold text-primary" id="formula-urn-<?php echo e($table->id); ?>"><?php echo e($ballotsInUrn); ?></span></span>
        <span class="text-muted">+</span>
        <span><strong>No usadas</strong> <span class="fw-bold" id="formula-leftover-<?php echo e($table->id); ?>"><?php echo e($ballotsLeftover); ?></span></span>
        <span class="formula-spoiled-wrap" <?php if($ballotsSpoiled == 0): ?> style="display:none" <?php endif; ?>>
            <span class="text-muted">+</span>
            <span><strong>Deterioradas</strong> <span class="fw-bold" id="formula-spoiled-<?php echo e($table->id); ?>"><?php echo e($ballotsSpoiled); ?></span></span>
        </span>
        <span class="text-muted">=</span>
        <strong id="formula-total-<?php echo e($table->id); ?>"
                class="<?php echo e($hasEnteredLeftover ? ($balanceOk ? 'text-success' : 'text-danger') : 'text-muted'); ?>">
            <?php echo e($accountedTotal); ?>

        </strong>
        <span class="text-muted">/</span>
        <strong class="text-info"><?php echo e($expectedVoters); ?></strong>
        <span class="text-muted">habilitados</span>
        <?php if($hasEnteredLeftover): ?>
            <span class="<?php echo e($balanceOk ? 'text-success' : 'text-danger'); ?> fw-bold">
                <?php echo e($balanceOk ? '✓' : ('✗ dif: '.($balanceDiff>0?'+':'').$balanceDiff)); ?>

            </span>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\_Mine\sistema_electoral\resources\views/voting-table-votes/partials/ballot-inputs.blade.php ENDPATH**/ ?>