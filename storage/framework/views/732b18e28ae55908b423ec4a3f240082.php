<script>
function makeChoices(selector, placeholder) {
    const el = document.querySelector(selector);
    if (!el) return null;
    return new Choices(el, {
        searchEnabled: true,
        shouldSort: false,
        placeholder: true,
        placeholderValue: placeholder,
        itemSelectText: '',
        allowHTML: false,
    });
}

function destroyChoices(instance) {
    try { if (instance) instance.destroy(); } catch (_) {}
    return null;
}

function initializeForms() {
    window.choicesETC  = makeChoices('#election_type_category_id-field', 'Seleccione categoría');
    setupGeographicSelectsModal();
    setupColorPicker();
    setupImagePreviews();
    setupCreateButton();
    setupEditButton();
    setupViewButton();
    setupDeleteButton();
    setupCheckAll();
    setupCategoryInfo();
}

function setupCategoryInfo() {
    const categorySelect = document.getElementById('election_type_category_id-field');
    const categoryInfoRow = document.getElementById('category-info-row');
    const infoCode = document.getElementById('info-category-code');
    const infoBallot = document.getElementById('info-ballot-order');
    const infoVotes = document.getElementById('info-votes-per-person');

    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];

            if (this.value) {
                const code = selectedOption.dataset.code || '-';
                const ballotOrder = selectedOption.dataset.ballotOrder || '-';
                const votesPerPerson = selectedOption.dataset.votesPerPerson || '1';

                infoCode.textContent = code;
                infoBallot.textContent = ballotOrder;
                infoVotes.textContent = votesPerPerson;
                categoryInfoRow.style.display = 'block';
            } else {
                categoryInfoRow.style.display = 'none';
            }
        });
    }
}

function setupColorPicker() {
    const picker = document.getElementById('color-field');
    const hex    = document.getElementById('color-hex');
    if (!picker || !hex) return;

    picker.addEventListener('input', () => hex.value = picker.value);
    hex.addEventListener('input', function () {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) picker.value = this.value;
    });
}

function setupGeographicSelectsModal() {
    const deptSel = document.getElementById('department_id-field');
    const provSel = document.getElementById('province_id-field');
    const munSel  = document.getElementById('municipality_id-field');
    if (!deptSel || !provSel || !munSel) return;
    window.choicesDept = destroyChoices(window.choicesDept);
    window.choicesProv = destroyChoices(window.choicesProv);
    window.choicesMun  = destroyChoices(window.choicesMun);
    window.choicesDept = makeChoices('#department_id-field', 'Seleccione departamento');
    window.choicesProv = makeChoices('#province_id-field',  'Primero seleccione departamento');
    window.choicesMun  = makeChoices('#municipality_id-field', 'Primero seleccione provincia');
    deptSel.addEventListener('change', function () {
        loadProvinces(this.value);
    });
    provSel.addEventListener('change', function () {
        loadMunicipalities(this.value);
    });
}

function loadProvinces(departmentId) {
    const provSel = document.getElementById('province_id-field');
    const munSel  = document.getElementById('municipality_id-field');

    window.choicesProv = destroyChoices(window.choicesProv);
    window.choicesMun  = destroyChoices(window.choicesMun);

    munSel.innerHTML = '<option value="">Primero seleccione provincia</option>';
    munSel.disabled  = true;
    window.choicesMun = makeChoices('#municipality_id-field', 'Primero seleccione provincia');

    if (!departmentId) {
        provSel.innerHTML = '<option value="">Primero seleccione departamento</option>';
        provSel.disabled  = true;
        window.choicesProv = makeChoices('#province_id-field', 'Primero seleccione departamento');
        return;
    }

    fetch(`/candidates/provinces/${departmentId}`)
        .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
        .then(provinces => {
            provSel.innerHTML = '<option value="">Seleccione una provincia</option>';
            provinces.forEach(p => {
                provSel.insertAdjacentHTML('beforeend', `<option value="${p.id}">${p.name}</option>`);
            });
            provSel.disabled = false;
            window.choicesProv = makeChoices('#province_id-field', 'Seleccione una provincia');

            if (window._pendingProvinceId) {
                setTimeout(() => {
                    window.choicesProv?.setChoiceByValue(String(window._pendingProvinceId));
                    provSel.dispatchEvent(new Event('change', { bubbles: true }));
                    window._pendingProvinceId = null;
                }, 150);
            }
        })
        .catch(() => {
            provSel.innerHTML = '<option value="">Error al cargar provincias</option>';
            provSel.disabled  = false;
            window.choicesProv = makeChoices('#province_id-field', 'Error al cargar');
        });
}

function loadMunicipalities(provinceId) {
    const munSel = document.getElementById('municipality_id-field');
    window.choicesMun = destroyChoices(window.choicesMun);
    if (!provinceId) {
        munSel.innerHTML = '<option value="">Primero seleccione provincia</option>';
        munSel.disabled  = true;
        window.choicesMun = makeChoices('#municipality_id-field', 'Primero seleccione provincia');
        return;
    }
    fetch(`/candidates/municipalities/${provinceId}`)
        .then(r => { if (!r.ok) throw new Error('Network error'); return r.json(); })
        .then(municipalities => {
            munSel.innerHTML = '<option value="">Seleccione un municipio</option>';
            municipalities.forEach(m => {
                munSel.insertAdjacentHTML('beforeend', `<option value="${m.id}">${m.name}</option>`);
            });
            munSel.disabled = false;
            window.choicesMun = makeChoices('#municipality_id-field', 'Seleccione un municipio');

            if (window._pendingMunicipalityId) {
                setTimeout(() => {
                    window.choicesMun?.setChoiceByValue(String(window._pendingMunicipalityId));
                    window._pendingMunicipalityId = null;
                }, 150);
            }
        })
        .catch(() => {
            munSel.innerHTML = '<option value="">Error al cargar municipios</option>';
            munSel.disabled  = false;
            window.choicesMun = makeChoices('#municipality_id-field', 'Error al cargar');
        });
}

function setupImagePreviews() {
    bindPreview('photo-field', 'photo-preview');
    bindPreview('party_logo-field', 'party-logo-preview');
}

function bindPreview(inputId, previewId) {
    const input   = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    if (!input || !preview) return;
    input.addEventListener('change', function () {
        if (!this.files?.[0]) return;
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(this.files[0]);
    });
}

function setupCreateButton() {
    document.getElementById('create-btn')?.addEventListener('click', function () {
        const form = document.getElementById('candidateForm');
        if (form) form.reset();
        document.getElementById('modalTitleText').textContent = 'Agregar Nuevo Candidato';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('candidate_id').value = '';
        if (form) form.action = '/candidates';
        document.getElementById('color-field').value = '#1b8af8';
        document.getElementById('color-hex').value = '#1b8af8';
        document.getElementById('photo-preview').style.display = 'none';
        document.getElementById('party-logo-preview').style.display = 'none';
        const activeRow = document.getElementById('active-status-row');
        if (activeRow) activeRow.style.display = 'none';
        document.getElementById('category-info-row').style.display = 'none';
        window.choicesETC?.setChoiceByValue('');
        resetGeographicModalSelects();
    });
}

function resetGeographicModalSelects() {
    const deptSel = document.getElementById('department_id-field');
    const provSel = document.getElementById('province_id-field');
    const munSel  = document.getElementById('municipality_id-field');
    if (deptSel) { window.choicesDept?.setChoiceByValue(''); }
    if (provSel) {
        window.choicesProv = destroyChoices(window.choicesProv);
        provSel.innerHTML = '<option value="">Primero seleccione departamento</option>';
        provSel.disabled  = true;
        window.choicesProv = makeChoices('#province_id-field', 'Primero seleccione departamento');
    }
    if (munSel) {
        window.choicesMun = destroyChoices(window.choicesMun);
        munSel.innerHTML = '<option value="">Primero seleccione provincia</option>';
        munSel.disabled  = true;
        window.choicesMun = makeChoices('#municipality_id-field', 'Primero seleccione provincia');
    }
}

function setupEditButton() {
    document.querySelectorAll('.edit-item-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            document.getElementById('modalTitleText').textContent = 'Editar Candidato';
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('candidate_id').value = d.id;
            document.getElementById('candidateForm').action = d.updateUrl;
            document.getElementById('name-field').value = d.name ?? '';
            document.getElementById('party-field').value = d.party ?? '';
            document.getElementById('party_full_name-field').value = d.party_full_name ?? '';
            document.getElementById('list_order-field').value = d.list_order ?? '';
            document.getElementById('list_name-field').value = d.list_name ?? '';

            const colour = d.color || '#1b8af8';
            document.getElementById('color-field').value = colour;
            document.getElementById('color-hex').value = colour;
            window.choicesETC?.setChoiceByValue(d.election_type_category_id ?? '');
            const activeRow = document.getElementById('active-status-row');
            if (activeRow) {
                activeRow.style.display = 'block';
                const activeField = document.getElementById('active-field');
                if (activeField) activeField.checked = (d.active === '1');
            }
            setPreviewUrl('photo-preview', d.photoUrl);
            setPreviewUrl('party-logo-preview', d.partyLogoUrl);
            setTimeout(() => {
                document.getElementById('election_type_category_id-field')?.dispatchEvent(new Event('change'));
            }, 100);
            if (d.department_id) {
                window._pendingProvinceId = d.province_id || null;
                window._pendingMunicipalityId = d.municipality_id || null;
                window.choicesDept?.setChoiceByValue(d.department_id);
                setTimeout(() => {
                    document.getElementById('department_id-field')
                        ?.dispatchEvent(new Event('change', { bubbles: true }));
                }, 100);
            } else {
                resetGeographicModalSelects();
            }
        });
    });
}

function setPreviewUrl(previewId, url) {
    const el = document.getElementById(previewId);
    if (!el) return;
    if (url && url !== 'null' && url !== 'undefined') {
        el.src = url;
        el.style.display = 'block';
    } else {
        el.style.display = 'none';
    }
}

function setupViewButton() {
    document.querySelectorAll('.view-item-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const d = this.dataset;
            setText('view-name', d.name);
            setText('view-party', d.party);
            setText('view-party-full-name', d.party_full_name || '—');
            const partyDisplay = document.getElementById('view-party-display');
            if (partyDisplay) {
                partyDisplay.textContent = d.party || '—';
            }
            setText('view-election-type', d.election_type || '—');
            setText('view-election-category', d.election_category || '—');
            setText('view-election-code', d.election_category_code || '—');
            setText('view-ballot-order', d.ballot_order || '—');
            setText('view-votes-per-person', d.votes_per_person || '1');
            setText('view-list-name', d.list_name || '—');
            setText('view-list-order', d.list_order || '—');
            setText('view-stat-order', d.list_order || '—');
            setText('view-stat-franja', d.ballot_order || '—');
            setText('view-department', d.department_name || '—');
            setText('view-province', d.province_name || '—');
            setText('view-municipality', d.municipality_name || '—');
            const categoryBadge = document.getElementById('view-category-badge');
            if (categoryBadge) {
                const categoryCode = d.election_category_code || d.election_category || '—';
                categoryBadge.textContent = categoryCode;
            }
            const activeEl = document.getElementById('view-active');
            if (activeEl) {
                if (d.active === '1') {
                    activeEl.className = 'badge bg-success';
                    activeEl.textContent = 'Activo';
                } else {
                    activeEl.className = 'badge bg-danger';
                    activeEl.textContent = 'Inactivo';
                }
            }
            const photo = document.getElementById('view-photo');
            if (photo) {
                if (d.photoUrl && d.photoUrl !== 'null' && d.photoUrl !== 'undefined') {
                    photo.src = d.photoUrl;
                } else {
                    photo.src = '/build/images/users/user-dummy-img.jpg';
                }
                photo.style.display = 'block';
            }
            const logo = document.getElementById('view-party-logo');
            if (logo) {
                if (d.partyLogoUrl && d.partyLogoUrl !== 'null' && d.partyLogoUrl !== 'undefined') {
                    logo.src = d.partyLogoUrl;
                    logo.style.display = 'inline-block';
                } else {
                    logo.style.display = 'none';
                }
            }
            const colorContainer = document.getElementById('view-color-container');
            const colorDot = document.getElementById('view-color-dot');
            const colorHex = document.getElementById('view-color-hex');
            if (colorContainer && colorDot && colorHex) {
                if (d.color && d.color !== 'null' && d.color !== 'undefined') {
                    colorDot.style.backgroundColor = d.color;
                    colorHex.textContent = d.color;
                    colorContainer.style.display = 'block';
                } else {
                    colorContainer.style.display = 'none';
                }
            }
        });
    });
}

function setText(id, value) {
    const el = document.getElementById(id);
    if (el) {
        el.textContent = (value && value !== 'null' && value !== 'undefined') ? value : '—';
    }
}

function setupDeleteButton() {
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('deleteForm');
            const msg = document.getElementById('deleteMessage');
            if (form) form.action = this.dataset.deleteUrl;
            if (msg) msg.textContent = `¿Eliminar al candidato "${this.dataset.name}"?`;
        });
    });
}

function setupCheckAll() {
    const checkAll = document.getElementById('checkAll');
    const children = () => document.querySelectorAll('.child-checkbox');
    const delBtn = document.getElementById('delete-multiple-btn');
    const expBtn = document.getElementById('export-selected-btn');
    const badge = document.getElementById('selected-count-badge');
    if (checkAll) {
        checkAll.addEventListener('change', function () {
            children().forEach(cb => cb.checked = this.checked);
            updateBulkButtons();
        });
    }
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('child-checkbox')) return;
        const all = children();
        const checked = Array.from(all).filter(cb => cb.checked);
        if (checkAll) {
            checkAll.checked = checked.length === all.length && all.length > 0;
            checkAll.indeterminate = checked.length > 0 && checked.length < all.length;
        }
        updateBulkButtons();
    });

    function updateBulkButtons() {
        const n = Array.from(children()).filter(cb => cb.checked).length;
        if (delBtn) {
            delBtn.classList.toggle('d-none', n === 0);
            delBtn.innerHTML = `<i class="ri-delete-bin-2-line me-1"></i>Eliminar Seleccionados (${n})`;
        }
        if (expBtn) expBtn.disabled = n === 0;
        if (badge) { badge.textContent = n; badge.style.display = n > 0 ? 'inline-block' : 'none'; }
    }
}

window.exportSelected = function () {
    const ids = Array.from(document.querySelectorAll('.child-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) {
        return Swal.fire({
            icon: 'warning',
            title: 'Sin selección',
            text: 'Seleccione al menos un candidato para exportar.',
            confirmButtonColor: '#1b8af8'
        });
    }

    const form = document.getElementById('export-selected-form');
    form.querySelectorAll('input[name="selected_ids[]"]').forEach(i => i.remove());
    ids.forEach(id => {
        const input = Object.assign(document.createElement('input'), {
            type: 'hidden', name: 'selected_ids[]', value: id,
        });
        form.appendChild(input);
    });
    form.submit();
};

window.deleteMultiple = function () {
    const ids = Array.from(document.querySelectorAll('.child-checkbox:checked')).map(cb => cb.value);
    if (!ids.length) {
        return Swal.fire({
            icon: 'warning',
            title: 'Sin selección',
            text: 'Seleccione al menos un candidato para eliminar.',
            confirmButtonColor: '#1b8af8'
        });
    }
    Swal.fire({
        title: '¿Está seguro?',
        text: `Se eliminarán ${ids.length} candidato(s). Esta acción no se puede deshacer.`,
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
        form.action = '/candidates/multiple-delete';
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

document.addEventListener('DOMContentLoaded', function() {
    initializeForms();
    const categorySelect = document.getElementById('election_type_category_id-field');
    if (categorySelect && categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    }
});
</script>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views/candidates/scripts/candidates-js.blade.php ENDPATH**/ ?>