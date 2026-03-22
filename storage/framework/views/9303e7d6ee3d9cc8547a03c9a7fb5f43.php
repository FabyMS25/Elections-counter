
<div class="quick-actions mt-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center g-2">
                <div class="col-md-5 col-lg-6">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="text-muted small">
                            <i class="ri-table-line me-1"></i>
                            <strong id="qa-visible-count"><?php echo e($votingTables->count()); ?></strong>
                            mesa<?php echo e($votingTables->count() !== 1 ? 's' : ''); ?> visibles
                        </span>
                        <span class="text-muted small" id="qa-pending-indicator" style="display:none">
                            <i class="ri-pencil-line me-1 text-warning"></i>
                            <strong id="qa-pending-count" class="text-warning">0</strong> con cambios
                        </span>
                        <?php if(($totals['expected'] ?? 0) > 0): ?>
                        <span class="text-muted small">
                            <i class="ri-user-line me-1"></i>
                            <?php echo e(number_format($totals['total'])); ?> / <?php echo e(number_format($totals['expected'])); ?>

                            <span class="badge ms-1 bg-<?php echo e(($totals['participation']??0) >= 75 ? 'success' : (($totals['participation']??0) >= 50 ? 'warning text-dark' : 'secondary')); ?>"
                                  style="font-size:.65rem">
                                <?php echo e($totals['participation'] ?? 0); ?>%
                            </span>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-7 col-lg-6 d-flex justify-content-end align-items-center gap-2 flex-wrap">

                    <?php if($permissions['can_register'] ?? false): ?>
                    <button class="btn btn-success btn-sm" id="saveAllBtn"
                            title="Guardar todas las mesas visibles (Ctrl+S)">
                        <i class="ri-save-line me-1"></i>
                        <span class="d-none d-md-inline">Guardar todo</span>
                    </button>
                    <?php endif; ?>

                    <?php if($permissions['can_validate'] ?? false): ?>
                    <button class="btn btn-info text-white btn-sm" id="validateAllBtn"
                            title="Validar todas las mesas en votación">
                        <i class="ri-checkbox-circle-line me-1"></i>
                        <span class="d-none d-md-inline">Validar todo</span>
                    </button>
                    <button class="btn btn-success btn-sm" id="escrutarAllBtn"
                            title="Escrutar todas las mesas en escrutinio">
                        <i class="ri-check-double-line me-1"></i>
                        <span class="d-none d-md-inline">Escrutar todo</span>
                    </button>
                    <?php endif; ?>

                    <button class="btn btn-outline-secondary btn-sm" id="qaRefreshBtn" title="Recargar (F5)">
                        <i class="ri-refresh-line"></i>
                    </button>

                    <button class="btn btn-outline-secondary btn-sm" type="button"
                            data-bs-toggle="popover" data-bs-trigger="focus"
                            data-bs-placement="top" data-bs-html="true"
                            data-bs-title="Atajos de teclado"
                            data-bs-content="
                                <table class='table table-sm table-borderless mb-0 small'>
                                  <tr><td><kbd>Ctrl+S</kbd></td><td>Guardar todo</td></tr>
                                  <tr><td><kbd>Ctrl+V</kbd></td><td>Validar todo</td></tr>
                                  <tr><td><kbd>Ctrl+Enter</kbd></td><td>Guardar mesa activa</td></tr>
                                  <tr><td><kbd>F5</kbd></td><td>Actualizar</td></tr>
                                  <tr><td><kbd>Esc</kbd></td><td>Deseleccionar</td></tr>
                                </table>">
                        <i class="ri-keyboard-line"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    window.pendingTables = window.pendingTables ?? new Set();

    function updatePendingDisplay() {
        const n         = window.pendingTables.size;
        const indicator = document.getElementById('qa-pending-indicator');
        const badge     = document.getElementById('qa-pending-count');
        if (!indicator || !badge) return;
        badge.textContent       = n;
        indicator.style.display = n > 0 ? '' : 'none';
    }

    document.querySelectorAll('.vote-input, .blank-votes-input, .null-votes-input').forEach(input => {
        input.addEventListener('input', function () {
            if (this.dataset.table) {
                window.pendingTables.add(this.dataset.table);
                updatePendingDisplay();
            }
        });
    });
    document.addEventListener('tableSaved', function (e) {
        if (e.detail?.tableId) {
            window.pendingTables.delete(String(e.detail.tableId));
            updatePendingDisplay();
        }
    });

    // ── Save all ──────────────────────────────────────────────────────────────
    document.getElementById('saveAllBtn')?.addEventListener('click', async function () {
        const buttons = document.querySelectorAll('.save-table');
        if (buttons.length === 0) {
            Swal.fire({ icon:'info', title:'Sin mesas editables', text:'No hay mesas en votación en esta página.' });
            return;
        }
        if (!(await Swal.fire({
            title: `¿Guardar ${buttons.length} mesa${buttons.length !== 1 ? 's' : ''}?`,
            text:  'Se guardarán todas las mesas visibles.',
            icon: 'question', showCancelButton: true,
            confirmButtonText: 'Guardar todo', confirmButtonColor: '#0ab39c',
        })).isConfirmed) return;
        for (const btn of buttons) {
            const tableId = parseInt(btn.dataset.tableId);
            if (tableId) await saveTable(tableId);
        }
    });

    // ── Validate all ──────────────────────────────────────────────────────────
    document.getElementById('validateAllBtn')?.addEventListener('click', function () {
        const buttons = document.querySelectorAll('.validate-table[data-action="validate"]');
        if (buttons.length === 0) {
            Swal.fire({ icon:'info', title:'Sin mesas para validar', text:'No hay mesas en estado Votación.' });
            return;
        }
        Swal.fire({
            title: `¿Validar ${buttons.length} mesa${buttons.length !== 1 ? 's' : ''}?`,
            text:  'Las mesas pasarán a En Escrutinio si no tienen observaciones pendientes.',
            icon: 'question', showCancelButton: true,
            confirmButtonText: 'Sí, validar todo', confirmButtonColor: '#17a2b8',
        }).then(r => { if (r.isConfirmed) buttons.forEach((b, i) => setTimeout(() => b.click(), i * 300)); });
    });

    // ── Escrutar all ──────────────────────────────────────────────────────────
    document.getElementById('escrutarAllBtn')?.addEventListener('click', function () {
        const buttons = document.querySelectorAll('.validate-table[data-action="escrutar"]');
        if (buttons.length === 0) {
            Swal.fire({ icon:'info', title:'Sin mesas para escrutar', text:'No hay mesas en estado En Escrutinio.' });
            return;
        }
        Swal.fire({
            title: `¿Escrutar ${buttons.length} mesa${buttons.length !== 1 ? 's' : ''}?`,
            text:  'Las mesas quedarán como Escrutadas. Esta acción es definitiva.',
            icon: 'warning', showCancelButton: true,
            confirmButtonText: 'Sí, escrutar todo', confirmButtonColor: '#0ab39c',
        }).then(r => { if (r.isConfirmed) buttons.forEach((b, i) => setTimeout(() => b.click(), i * 300)); });
    });

    document.getElementById('qaRefreshBtn')?.addEventListener('click', () => location.reload());

    // ── Keyboard shortcuts ────────────────────────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.ctrlKey && e.key === 's')     { e.preventDefault(); document.getElementById('saveAllBtn')?.click(); }
        if (e.ctrlKey && e.key === 'v')     { e.preventDefault(); document.getElementById('validateAllBtn')?.click(); }
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            const el = document.activeElement;
            if (el?.dataset?.table) {
                document.querySelector(`.save-table[data-table-id="${el.dataset.table}"]`)?.click();
            }
        }
        if (e.key === 'F5')     { e.preventDefault(); location.reload(); }
        if (e.key === 'Escape') { document.activeElement?.blur(); }
    });
})();
</script>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-table-votes\partials\quick-actions.blade.php ENDPATH**/ ?>