
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deptSelect = document.getElementById('filter-department');
    const provSelect = document.getElementById('filter-province');
    const munSelect = document.getElementById('filter-municipality');
    const locSelect = document.getElementById('filter-locality');
    const institutionSelect = document.getElementById('institution-field');
    const institutions = Array.from(institutionSelect?.options || []).slice(1).map(opt => ({
        id: opt.value,
        text: opt.text,
        department: opt.dataset.department,
        province: opt.dataset.province,
        municipality: opt.dataset.municipality,
        locality: opt.dataset.locality
    }));

    function filterInstitutions() {
        if (!institutionSelect) return;

        const selectedDept = deptSelect?.value;
        const selectedProv = provSelect?.value;
        const selectedMun = munSelect?.value;
        const selectedLoc = locSelect?.value;
        const currentValue = institutionSelect.value;
        institutionSelect.innerHTML = '<option value="">— Seleccione un recinto —</option>';
        let visibleCount = 0;
        institutions.forEach(inst => {
            let show = true;
            if (selectedDept && inst.department !== selectedDept) show = false;
            if (show && selectedProv && inst.province !== selectedProv) show = false;
            if (show && selectedMun && inst.municipality !== selectedMun) show = false;
            if (show && selectedLoc && inst.locality !== selectedLoc) show = false;

            if (show) {
                visibleCount++;
                const opt = document.createElement('option');
                opt.value = inst.id;
                opt.textContent = inst.text;
                if (inst.id === currentValue) opt.selected = true;
                institutionSelect.appendChild(opt);
            }
        });
        if (visibleCount === 0 && (selectedDept || selectedProv || selectedMun || selectedLoc)) {
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = '— No hay recintos en esta ubicación —';
            opt.disabled = true;
            institutionSelect.appendChild(opt);
        }
    }

    async function loadProvinces(departmentId) {
        if (!provSelect) return;
        provSelect.innerHTML = '<option value="">— Cargando provincias... —</option>';
        provSelect.disabled = true;
        munSelect.innerHTML = '<option value="">— Primero seleccione provincia —</option>';
        munSelect.disabled = true;
        locSelect.innerHTML = '<option value="">— Primero seleccione municipio —</option>';
        locSelect.disabled = true;

        if (!departmentId) {
            provSelect.innerHTML = '<option value="">— Primero seleccione departamento —</option>';
            filterInstitutions();
            return;
        }

        try {
            const response = await fetch(`/institutions/provinces/${departmentId}`);
            const provinces = await response.json();

            provSelect.innerHTML = '<option value="">— Todas las provincias —</option>';
            provinces.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name;
                provSelect.appendChild(opt);
            });
            provSelect.disabled = false;
            if (window._selectedProvince) {
                provSelect.value = window._selectedProvince;
                await loadMunicipalities(window._selectedProvince);
                window._selectedProvince = null;
            }
            filterInstitutions();
        } catch (error) {
            console.error('Error loading provinces:', error);
            provSelect.innerHTML = '<option value="">— Error al cargar provincias —</option>';
        }
    }

    async function loadMunicipalities(provinceId) {
        if (!munSelect) return;

        munSelect.innerHTML = '<option value="">— Cargando municipios... —</option>';
        munSelect.disabled = true;
        locSelect.innerHTML = '<option value="">— Primero seleccione municipio —</option>';
        locSelect.disabled = true;

        if (!provinceId) {
            munSelect.innerHTML = '<option value="">— Primero seleccione provincia —</option>';
            filterInstitutions();
            return;
        }

        try {
            const response = await fetch(`/institutions/municipalities/${provinceId}`);
            const municipalities = await response.json();

            munSelect.innerHTML = '<option value="">— Todos los municipios —</option>';
            municipalities.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.name;
                munSelect.appendChild(opt);
            });
            munSelect.disabled = false;
            if (window._selectedMunicipality) {
                munSelect.value = window._selectedMunicipality;
                await loadLocalities(window._selectedMunicipality);
                window._selectedMunicipality = null;
            }

            filterInstitutions();
        } catch (error) {
            console.error('Error loading municipalities:', error);
            munSelect.innerHTML = '<option value="">— Error al cargar municipios —</option>';
        }
    }

    async function loadLocalities(municipalityId) {
        if (!locSelect) return;

        locSelect.innerHTML = '<option value="">— Cargando localidades... —</option>';
        locSelect.disabled = true;

        if (!municipalityId) {
            locSelect.innerHTML = '<option value="">— Primero seleccione municipio —</option>';
            filterInstitutions();
            return;
        }

        try {
            const response = await fetch(`/institutions/localities/${municipalityId}`);
            const localities = await response.json();

            locSelect.innerHTML = '<option value="">— Todas las localidades —</option>';
            localities.forEach(l => {
                const opt = document.createElement('option');
                opt.value = l.id;
                opt.textContent = l.name;
                locSelect.appendChild(opt);
            });
            locSelect.disabled = false;
            if (window._selectedLocality) {
                locSelect.value = window._selectedLocality;
                window._selectedLocality = null;
            }
            filterInstitutions();
        } catch (error) {
            console.error('Error loading localities:', error);
            locSelect.innerHTML = '<option value="">— Error al cargar localidades —</option>';
        }
    }
    if (deptSelect) {
        deptSelect.addEventListener('change', function () {
            loadProvinces(this.value);
        });
    }
    if (provSelect) {
        provSelect.addEventListener('change', function () {
            loadMunicipalities(this.value);
        });
    }
    if (munSelect) {
        munSelect.addEventListener('change', function () {
            loadLocalities(this.value);
        });
    }
    if (locSelect) {
        locSelect.addEventListener('change', filterInstitutions);
    }
    if (institutionSelect && institutionSelect.value) {
        const selected = institutionSelect.options[institutionSelect.selectedIndex];
        if (selected) {
            const deptId = selected.dataset.department;
            const provId = selected.dataset.province;
            const munId = selected.dataset.municipality;
            const locId = selected.dataset.locality;

            if (deptId) {
                window._selectedProvince = provId;
                window._selectedMunicipality = munId;
                window._selectedLocality = locId;
                deptSelect.value = deptId;
                loadProvinces(deptId);
            }
        }
    }
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.child-checkbox');
    const deleteManyBtn = document.getElementById('delete-multiple-btn');
    const exportSelBtn = document.getElementById('export-selected-btn');
    const selectedBadge = document.getElementById('selected-count-badge');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateActionButtons();
        });
    }
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            updateCheckAll();
            updateActionButtons();
        });
    });
    function updateCheckAll() {
        if (!checkboxes.length || !checkAll) return;
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        checkAll.checked = allChecked;
        checkAll.indeterminate = !allChecked && anyChecked;
    }
    function updateActionButtons() {
        const n = document.querySelectorAll('.child-checkbox:checked').length;
        if (deleteManyBtn) {
            deleteManyBtn.style.display = n > 0 ? 'inline-block' : 'none';
            if (n > 0) {
                deleteManyBtn.innerHTML = `<i class="ri-delete-bin-2-line me-1"></i>Eliminar Seleccionados (${n})`;
            }
        }
        if (exportSelBtn) {
            exportSelBtn.disabled = n === 0;
            if (selectedBadge) {
                selectedBadge.style.display = n > 0 ? 'inline-block' : 'none';
                selectedBadge.textContent = n;
            }
        }
    }

    const deleteModal = document.getElementById('deleteRecordModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            if (!btn) return;
            const oep = btn.getAttribute('data-oep') || btn.getAttribute('data-code') || '';
            const internal = btn.getAttribute('data-internal') || '';
            const deleteUrl = btn.getAttribute('data-delete-url') || '';
            const infoEl = document.getElementById('deleteTableInfo');
            if (infoEl) {
                infoEl.textContent = oep
                    ? `Código OEP: ${oep}${internal ? ' — Interno: ' + internal : ''}`
                    : `ID: ${btn.getAttribute('data-id')}`;
            }
            const form = document.getElementById('deleteForm');
            if (form && deleteUrl) {
                form.action = deleteUrl;
            }
        });
    }

    document.querySelectorAll('#filter-form select').forEach(sel => {
        sel.addEventListener('change', () => document.getElementById('filter-form')?.submit());
    });
});

function exportSelected() {
    const ids = Array.from(document.querySelectorAll('.child-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) {
        Swal.fire({
            title: 'Sin selección',
            text: 'Seleccione al menos una mesa.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    document.getElementById('selected-ids-input').value = JSON.stringify(ids);
    document.getElementById('export-selected-form').submit();
}

function deleteMultiple() {
    const ids = Array.from(document.querySelectorAll('.child-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) return;
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar ${ids.length} mesa(s)? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({
            title: 'Eliminando…',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
        fetch('<?php echo e(route("voting-tables.deleteMultiple")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({ ids }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Eliminados',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(() => {
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al eliminar las mesas.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        });
    });
}
</script>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-tables\scripts\voting-table-js.blade.php ENDPATH**/ ?>