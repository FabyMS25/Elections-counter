
<div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body text-center py-3 px-4">
                <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                    colors="primary:#f7b84b,secondary:#f06548"
                    style="width:72px;height:72px">
                </lord-icon>
                <h5 class="mt-3 fw-semibold">¿Está seguro?</h5>
                <p class="text-muted mb-1">¿Desea eliminar el recinto:</p>
                <p class="fw-bold mt-1" id="deleteInstitutionName"></p>
                <small class="text-muted">
                    <i class="ri-information-line me-1"></i>
                    Esta acción no se puede deshacer.
                </small>
            </div>
            <div class="modal-footer justify-content-center gap-2 pt-0 border-0 pb-4">
                <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancelar
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line me-1"></i>Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views/institutions/partials/modal-delete.blade.php ENDPATH**/ ?>