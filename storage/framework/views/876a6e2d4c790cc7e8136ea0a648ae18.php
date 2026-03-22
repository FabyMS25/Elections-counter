
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusEl    = document.getElementById('status-field');
    const operativeEl = document.getElementById('is_operative-field');
    const opLabel     = document.getElementById('operative-label');
    const opWarning   = document.getElementById('operative-warning');
    function syncOperative() {
        if (!statusEl || !operativeEl) return;
        const isActive = statusEl.value === 'activo';
        if (!isActive) {
            operativeEl.checked  = false;
            operativeEl.disabled = true;
            if (opWarning) opWarning.style.display = 'block';
        } else {
            operativeEl.disabled = false;
            if (opWarning) opWarning.style.display = 'none';
        }
        updateOperativeLabel();
    }

    function updateOperativeLabel() {
        if (!operativeEl || !opLabel) return;
        if (operativeEl.disabled) {
            opLabel.textContent = 'No disponible (recinto no activo)';
            opLabel.className   = 'form-check-label text-muted fst-italic';
        } else if (operativeEl.checked) {
            opLabel.textContent = 'Sí – habilitado para la jornada electoral';
            opLabel.className   = 'form-check-label fw-semibold text-success';
        } else {
            opLabel.textContent = 'No – excluido de la jornada electoral';
            opLabel.className   = 'form-check-label fw-semibold text-secondary';
        }
    }
    if (statusEl) {
        statusEl.addEventListener('change', syncOperative);
        syncOperative();
    }
    if (operativeEl) {
        operativeEl.addEventListener('change', updateOperativeLabel);
        updateOperativeLabel();
    }
    function fetchCascade(parentEl) {
        const baseUrl   = parentEl.dataset.cascadeUrl;
        const targetSel = parentEl.dataset.cascadeTarget;
        const parentVal = parentEl.value;
        if (!baseUrl || !targetSel) return Promise.resolve();
        const targetEl = document.querySelector(targetSel);
        if (!targetEl) return Promise.resolve();
        clearDescendants(targetEl);
        if (!parentVal) return Promise.resolve();
        const url = baseUrl.replace(/\/$/, '') + '/' + parentVal;
        return fetch(url)
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(data => {
                const restoreVal = targetEl.dataset.restore || '';
                const labelMap = {
                    '#province-field': 'Provincia',
                    '#municipality-field': 'Municipio',
                    '#locality-field': 'Localidad',
                    '#district-field': 'Distrito',
                    '#zone-field': 'Zona',
                };
                const label = labelMap[targetSel] || 'opción';
                targetEl.innerHTML = `<option value="">— Seleccione ${label} —</option>`;
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.name;
                        if (String(item.id) === String(restoreVal)) opt.selected = true;
                        targetEl.appendChild(opt);
                    });
                    targetEl.disabled = false;
                } else {
                    targetEl.disabled = true;
                }
                if (restoreVal && targetEl.value && targetEl.dataset.cascadeUrl) {
                    return fetchCascade(targetEl);
                }
            })
            .catch(err => {
                console.error('Cascade error:', err);
                targetEl.innerHTML = '<option value="">— Error al cargar —</option>';
                targetEl.disabled = true;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de carga',
                        text: 'No se pudieron cargar las opciones.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                    });
                }
            });
    }

    function clearDescendants(el) {
        if (!el) return;
        const labelMap = {
            'province-field': 'Provincia',
            'municipality-field': 'Municipio',
            'locality-field': 'Localidad',
            'district-field': 'Distrito',
            'zone-field': 'Zona',
        };
        const placeholder = labelMap[el.id] || 'opción';
        el.innerHTML = `<option value="">— Seleccione ${placeholder} —</option>`;
        el.disabled = true;
        if (el.dataset.cascadeTarget) {
            clearDescendants(document.querySelector(el.dataset.cascadeTarget));
        }
    }

    document.querySelectorAll('[data-cascade-url][data-cascade-target]').forEach(parentEl => {
        parentEl.addEventListener('change', () => fetchCascade(parentEl));
    });
    const deptEl = document.getElementById('department-field');
    if (deptEl && deptEl.value) {
        fetchCascade(deptEl);
    }

    const checkAll = document.getElementById('checkAll');
    const deleteMultipleBtn = document.getElementById('delete-multiple-btn');
    const exportSelectedBtn = document.getElementById('export-selected-btn');
    const selectedBadge = document.getElementById('selected-count-badge');

    function updateBulkButtons() {
        const checked = document.querySelectorAll('.child-checkbox:checked');
        const count = checked.length;

        if (deleteMultipleBtn) {
            if (count > 0) {
                deleteMultipleBtn.classList.remove('d-none');
                deleteMultipleBtn.innerHTML = `<i class="ri-delete-bin-2-line me-1"></i>Eliminar seleccionados (${count})`;
            } else {
                deleteMultipleBtn.classList.add('d-none');
            }
        }
        if (exportSelectedBtn) {
            exportSelectedBtn.disabled = count === 0;
        }
        if (selectedBadge) {
            selectedBadge.textContent = count;
            selectedBadge.style.display = count > 0 ? 'inline-block' : 'none';
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            document.querySelectorAll('.child-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkButtons();
        });
    }

    document.querySelectorAll('.child-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            const all = document.querySelectorAll('.child-checkbox');
            const checked = document.querySelectorAll('.child-checkbox:checked');
            if (checkAll) {
                checkAll.checked = checked.length === all.length && all.length > 0;
                checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
            }
            updateBulkButtons();
        });
    });
    const deleteModal = document.getElementById('deleteRecordModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            const nameEl = document.getElementById('deleteInstitutionName');
            const form = document.getElementById('deleteForm');
            if (nameEl) nameEl.textContent = btn.dataset.name;
            if (form) form.action = btn.dataset.deleteUrl;
        });
    }
});

window.exportSelected = function () {
    const ids = Array.from(document.querySelectorAll('.child-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Sin selección',
                text: 'Seleccione al menos un recinto para exportar.',
                confirmButtonColor: '#1b8af8'
            });
        }
        return;
    }

    const form = document.getElementById('export-selected-form');
    form.querySelectorAll('input[name="selected_ids[]"]').forEach(i => i.remove());

    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    form.submit();
};

window.deleteMultiple = function () {
    const ids = Array.from(document.querySelectorAll('.child-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Sin selección',
                text: 'Seleccione al menos un recinto para eliminar.',
                confirmButtonColor: '#1b8af8'
            });
        }
        return;
    }

    Swal.fire({
        title: '¿Está seguro?',
        text: `Se eliminarán ${ids.length} recinto(s). Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#8590a5',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (!result.isConfirmed) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("institutions.deleteMultiple")); ?>';
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrf}">
            <input type="hidden" name="_method" value="DELETE">
        `;
        ids.forEach(id => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'ids[]';
            inp.value = id;
            form.appendChild(inp);
        });
        document.body.appendChild(form);
        form.submit();
    });
};
</script>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\institutions\scripts\institution-js.blade.php ENDPATH**/ ?>