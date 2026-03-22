
<?php
    $status      = $table->current_status ?? 'sin_configurar';
    $isFinal     = in_array($status, ['escrutada','transmitida','anulada']);
    $isEditable  = in_array($status, ['configurada','en_espera','votacion']) && !$isFinal && ($permissions['can_register'] ?? false);
    $canSave     = $isEditable;
    $canObserve  = !$isFinal && in_array($status, ['votacion','en_escrutinio','observada']) && ($permissions['can_observe']  ?? false);
    $canCorrect  = $status === 'observada' && ($permissions['can_correct']  ?? false);
    $canValidate = in_array($status, ['votacion','observada'])    && !$isFinal && ($permissions['can_validate'] ?? false);
    $canEscrutar = $status === 'en_escrutinio'                    && ($permissions['can_validate'] ?? false);
    $canReject   = in_array($status, ['votacion','en_escrutinio','observada']) && !$isFinal && ($permissions['can_validate'] ?? false);
    $canReopen   = in_array($status, ['observada','en_escrutinio'])            && ($permissions['can_reopen']   ?? false);
    $canUpload   = !$isFinal && ($permissions['can_upload_acta'] ?? false);
    $canView     = $permissions['can_view'] ?? false;
?>

<div class="d-flex gap-1 flex-wrap justify-content-end align-items-center">
    <?php if($canSave): ?>
    <button type="button"
            class="btn btn-sm btn-soft-success save-table"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            title="Guardar votos">
        <i class="ri-save-line"></i>
    </button>
    <?php endif; ?>
    <?php if($canValidate): ?>
    <button type="button"
            class="btn btn-sm btn-soft-info validate-table"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            data-action="validate"
            title="Validar votos — pasa a En Escrutinio">
        <i class="ri-checkbox-circle-line me-1"></i>Validar
    </button>
    <?php endif; ?>
    <?php if($canEscrutar): ?>
    <button type="button"
            class="btn btn-sm btn-soft-success validate-table"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            data-action="escrutar"
            title="Escrutar — cierra el conteo">
        <i class="ri-check-double-line me-1"></i>Escrutar
    </button>
    <?php endif; ?>
    <?php if($canCorrect): ?>
    <button type="button"
            class="btn btn-sm btn-soft-warning correct-table"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            title="Corregir votos observados">
        <i class="ri-edit-line me-1"></i>Corregir
    </button>
    <?php endif; ?>
    <?php if($canObserve): ?>
    <button type="button"
            class="btn btn-sm btn-soft-warning observe-table-general"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            title="Agregar observación">
        <i class="ri-chat-1-line"></i>
    </button>
    <?php endif; ?>
    <?php if($canReject): ?>
    <button type="button"
            class="btn btn-sm btn-soft-danger validate-table"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            data-action="reject"
            title="Rechazar — mesa vuelve a Observada">
        <i class="ri-close-circle-line"></i>
    </button>
    <?php endif; ?>
    <?php if($canUpload): ?>
    <button type="button"
            class="btn btn-sm btn-soft-primary upload-acta"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            onclick="openActaModal(<?php echo e($table->id); ?>, <?php echo e($electionTypeId ?? 'null'); ?>, '<?php echo e(addslashes($table->number . ' - ' . ($table->internal_code ?? $table->oep_code))); ?>')"
            title="Subir acta electoral">
        <i class="ri-upload-line"></i>
    </button>
    <?php endif; ?>
    <?php if($canView): ?>
    <button type="button"
            class="btn btn-sm btn-soft-secondary view-actas-btn"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            title="Ver actas subidas">
        <i class="ri-file-copy-2-line"></i>
    </button>
    <?php endif; ?>

    <?php if($canReopen): ?>
    <button type="button"
            class="btn btn-sm btn-soft-secondary reopen-table"
            data-table-id="<?php echo e($table->id); ?>"
            data-election-type-id="<?php echo e($electionTypeId); ?>"
            title="Reabrir mesa — vuelve a Votación">
        <i class="ri-lock-unlock-line"></i>
    </button>
    <?php endif; ?>

</div><?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-table-votes\partials\table-actions.blade.php ENDPATH**/ ?>