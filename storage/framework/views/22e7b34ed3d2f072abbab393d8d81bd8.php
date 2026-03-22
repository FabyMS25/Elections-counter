
<?php if(session('import_errors') || session('import_warnings')): ?>
<div class="modal fade" id="importErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header <?php echo e(session('success_count') ? 'bg-warning' : 'bg-danger'); ?>">
                <h5 class="modal-title text-white">
                    <i class="ri-alert-line me-1"></i>
                    <?php echo e(session('success_count') ? 'Importación con advertencias' : 'Errores de Importación'); ?>

                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php if(session('success_count')): ?>
                    <div class="alert alert-success d-flex gap-2">
                        <i class="ri-check-double-line fs-5 flex-shrink-0"></i>
                        <div>
                            <strong><?php echo e(session('success_count')); ?> mesa(s)</strong> importadas correctamente.
                            <br>
                            <small class="text-muted">
                                Se creó automáticamente un registro en <code>voting_table_elections</code>
                                para cada tipo de elección activo.
                            </small>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if(session('import_errors') && count(session('import_errors')) > 0): ?>
                    <div class="alert alert-danger d-flex gap-2">
                        <i class="ri-error-warning-line fs-5 flex-shrink-0"></i>
                        <div>
                            <strong><?php echo e(count(session('import_errors'))); ?> fila(s) con errores</strong>
                            no pudieron procesarse:
                        </div>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Descripción del error</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="text-center text-muted"><?php echo e($index + 1); ?></td>
                                        <td class="text-danger">
                                            <i class="ri-error-warning-line me-1"></i><?php echo e($error); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <?php if(session('import_warnings') && count(session('import_warnings')) > 0): ?>
                    <div class="alert alert-warning d-flex gap-2">
                        <i class="ri-information-line fs-5 flex-shrink-0"></i>
                        <strong><?php echo e(count(session('import_warnings'))); ?> aviso(s):</strong>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th>Aviso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = session('import_warnings'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $warning): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="text-center text-muted"><?php echo e($index + 1); ?></td>
                                        <td class="text-warning-emphasis">
                                            <i class="ri-information-line me-1"></i><?php echo e($warning); ?>

                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="alert alert-light border mt-2 mb-0 py-2">
                    <i class="ri-lightbulb-line me-1 text-warning"></i>
                    <small>
                        Descargue la <strong>plantilla oficial</strong> para ver el formato correcto de cada columna.
                        Los <strong>recintos</strong> deben coincidir con el <em>nombre exacto</em>
                        o el <em>código</em> del sistema.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo e(route('voting-tables.template')); ?>" class="btn btn-outline-info">
                    <i class="ri-download-line me-1"></i>Descargar Plantilla
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('importErrorModal');
    if (el) new bootstrap.Modal(el).show();
});
</script>
<?php endif; ?>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-tables\partials\modal-import-errors.blade.php ENDPATH**/ ?>