

<div class="modal fade" id="viewActasModal" tabindex="-1" aria-labelledby="viewActasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewActasModalLabel">
                    <i class="ri-file-copy-2-line me-2"></i>
                    <span id="viewActasTitle">Actas de la Mesa</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3" id="viewActasBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando…</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <small class="text-muted me-auto" id="viewActasCount"></small>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="actaLightboxModal" tabindex="-1" aria-hidden="true" style="z-index:1100;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark border-0">
            <div class="modal-header border-0 pb-1">
                <span class="text-white-50 small" id="lightboxTitle"></span>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-2 text-center">
                <img id="lightboxImage" src="" alt="Acta"
                     style="max-width:100%;max-height:82vh;object-fit:contain;">
            </div>
        </div>
    </div>
</div>

<style>
.acta-card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    transition: box-shadow .15s, transform .15s;
}
.acta-card:hover { box-shadow: 0 4px 18px rgba(0,0,0,.13); transform: translateY(-1px); }

.acta-photo-wrap {
    position: relative;
    height: 220px;
    background: #f0f4f8;
    overflow: hidden;
    cursor: zoom-in;
    display: flex; align-items: center; justify-content: center;
}
.acta-photo-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .2s;
}
.acta-photo-wrap:hover img { transform: scale(1.04); }

.acta-photo-overlay {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,.6));
    color: #fff;
    padding: 24px 10px 8px;
    font-size: .75rem;
    pointer-events: none;
}
.acta-photo-placeholder {
    width: 100%; height: 220px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    color: #adb5bd; background: #f8f9fa; font-size: .82rem;
}
.acta-badge {
    display: inline-block; padding: 2px 9px; border-radius: 20px;
    font-size: .7rem; font-weight: 600; border: 1px solid;
}
.s-uploaded  { background: #cff4fc; color: #055160; border-color: #b6effb; }
.s-verified  { background: #d1e7dd; color: #0f5132; border-color: #a3cfbb; }
.s-observed  { background: #fff3cd; color: #664d03; border-color: #ffe69c; }
.s-corrected { background: #cfe2ff; color: #084298; border-color: #b6d4fe; }
.s-approved  { background: #d1e7dd; color: #0f5132; border-color: #a3cfbb; }
.s-rejected  { background: #f8d7da; color: #842029; border-color: #f5c2c7; }
</style>

<script>
(function () {
    'use strict';
    const PLACEHOLDER_HTML = `
        <div class="acta-photo-placeholder">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none"
                 stroke="#ced4da" stroke-width="1.2" viewBox="0 0 24 24">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <path d="M3 9h18M9 21V9"/>
                <line x1="12" y1="13" x2="12" y2="17"/>
                <line x1="10" y1="15" x2="14" y2="15"/>
            </svg>
            <p class="mt-2 mb-0 small text-muted">Sin imagen disponible</p>
        </div>`;
    function csrf() { return document.querySelector('meta[name="csrf-token"]')?.content ?? ''; }

    function esc(s) {
        return String(s ?? '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function statusInfo(status) {
        const map = {
            uploaded:  { label: 'Subida',     cls: 's-uploaded'  },
            verified:  { label: 'Verificada', cls: 's-verified'  },
            observed:  { label: 'Observada',  cls: 's-observed'  },
            corrected: { label: 'Corregida',  cls: 's-corrected' },
            approved:  { label: 'Aprobada',   cls: 's-approved'  },
            rejected:  { label: 'Rechazada',  cls: 's-rejected'  },
        };
        return map[status] ?? { label: status ?? '—', cls: 'bg-secondary text-white' };
    }

    window.handleActaPhotoError = function (img) {
        img.onerror = null; 
        img.src     = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='; 

        const wrap = img.closest('.acta-photo-wrap');
        if (wrap) {
            wrap.style.cursor = 'default';
            wrap.onclick      = null;
            wrap.innerHTML    = PLACEHOLDER_HTML;
        }
    };

    window.openActaLightbox = function (wrap, actaCode) {
        const img = wrap.querySelector('img');
        if (!img || !img.src || img.src.startsWith('data:')) return;

        const lbImg   = document.getElementById('lightboxImage');
        const lbTitle = document.getElementById('lightboxTitle');

        if (lbImg) {
            lbImg.onerror = null;
            lbImg.src     = img.src;
            lbImg.onerror = function () { this.onerror = null; this.style.display = 'none'; };
        }
        if (lbTitle) lbTitle.textContent = String(actaCode ?? 'Acta');

        new bootstrap.Modal(document.getElementById('actaLightboxModal')).show();
    };

    function renderActaCard(acta, tableId, electionTypeId) {
        const si       = statusInfo(acta.status);
        const photoUrl = acta.photo_url ?? null;  
        let photoHtml;
        if (photoUrl) {
            const safeUrl  = esc(photoUrl);
            const safeCode = esc(acta.code ?? '');
            photoHtml = `
                <div class="acta-photo-wrap"
                     onclick="openActaLightbox(this,'${safeCode}')"
                     title="Click para ampliar">
                    <img src="${safeUrl}"
                         alt="${safeCode}"
                         loading="lazy"
                         onerror="handleActaPhotoError(this)">
                    <div class="acta-photo-overlay">
                        <i class="ri-zoom-in-line me-1"></i>Click para ampliar
                    </div>
                </div>`;
        } else {
            photoHtml = PLACEHOLDER_HTML;
        }
        let inconsHtml = '';
        const incs = Array.isArray(acta.inconsistencies) ? acta.inconsistencies : [];
        if (incs.length > 0) {
            inconsHtml = `
                <div class="mt-2 p-2 bg-warning bg-opacity-10 border border-warning rounded small">
                    <strong class="text-warning">
                        <i class="ri-alert-line me-1"></i>Inconsistencias:
                    </strong>
                    <ul class="mb-0 mt-1 ps-3">
                        ${incs.map(i => `<li>${esc(String(i))}</li>`).join('')}
                    </ul>
                </div>`;
        }
        const id = Number(acta.id);
        let actionHtml = '';
        if (acta.status === 'uploaded' || acta.status === 'observed') {
            actionHtml = `
                <button class="btn btn-primary btn-sm w-100 mt-2 verify-acta-btn"
                        data-acta-id="${id}"
                        data-table-id="${Number(tableId)}"
                        data-election-type-id="${Number(electionTypeId) || ''}">
                    <i class="ri-shield-check-line me-1"></i>Verificar
                </button>`;
        } else if (acta.status === 'verified') {
            actionHtml = `
                <div class="d-flex gap-1 mt-2">
                    <button class="btn btn-success btn-sm flex-grow-1 approve-acta-btn" data-acta-id="${id}">
                        <i class="ri-check-double-line me-1"></i>Aprobar
                    </button>
                    <button class="btn btn-danger btn-sm flex-grow-1 reject-acta-btn" data-acta-id="${id}">
                        <i class="ri-close-circle-line me-1"></i>Rechazar
                    </button>
                </div>`;
        } else if (acta.status === 'approved') {
            actionHtml = `
                <div class="mt-2 p-1 bg-success bg-opacity-10 border border-success rounded text-center small text-success">
                    <i class="ri-checkbox-circle-line me-1"></i>Acta aprobada y finalizada
                </div>`;
        } else if (acta.status === 'rejected') {
            actionHtml = `
                <div class="mt-2 p-1 bg-danger bg-opacity-10 border border-danger rounded text-center small text-danger">
                    <i class="ri-close-circle-line me-1"></i>Acta rechazada
                </div>`;
        }

        const pdfHtml = acta.pdf_url
            ? `<a href="${esc(acta.pdf_url)}" target="_blank" rel="noopener"
                  class="btn btn-outline-secondary btn-sm mt-1 w-100">
                 <i class="ri-file-pdf-line me-1"></i>Ver PDF
               </a>`
            : '';

        return `
            <div class="col-md-6 mb-2">
                <div class="acta-card">
                    ${photoHtml}
                    <div class="p-2">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <strong class="small">${esc(acta.code ?? 'Acta')}</strong>
                            <span class="acta-badge ${si.cls}">${esc(si.label)}</span>
                        </div>
                        <div class="text-muted mb-1" style="font-size:.7rem;">
                            <i class="ri-hashtag me-1"></i>N° ${esc(acta.acta_number ?? '—')}
                        </div>
                        <div class="text-muted mb-1" style="font-size:.7rem;">
                            <i class="ri-calendar-line me-1"></i>${esc(acta.created_at ?? '—')}
                        </div>
                        <div class="text-muted" style="font-size:.7rem;">
                            <i class="ri-user-line me-1"></i>
                            Subida por: <strong>${esc(acta.uploaded_by ?? 'N/A')}</strong>
                        </div>
                        ${acta.signed_by ? `<div class="text-muted mt-1" style="font-size:.7rem;"><i class="ri-pen-nib-line me-1"></i>Aprobada por: <strong>${esc(acta.signed_by)}</strong></div>` : ''}
                        ${acta.file_size_formatted ? `<div class="text-muted" style="font-size:.65rem;">${esc(acta.file_size_formatted)}</div>` : ''}
                        ${inconsHtml}
                        ${actionHtml}
                        ${pdfHtml}
                    </div>
                </div>
            </div>`;
    }

    /* ── Load actas for a table ─────────────────────────────────────────────── */
    window.loadActasForTable = async function (tableId, tableLabel, electionTypeId) {
        const bodyEl  = document.getElementById('viewActasBody');
        const countEl = document.getElementById('viewActasCount');
        const titleEl = document.getElementById('viewActasTitle');

        if (titleEl) titleEl.textContent = 'Actas de la Mesa — ' + String(tableLabel ?? tableId);
        if (countEl) countEl.textContent = '';
        if (bodyEl) {
            bodyEl.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;">
                        <span class="visually-hidden">Cargando…</span>
                    </div>
                    <p class="text-muted mt-3 mb-0">Cargando actas…</p>
                </div>`;
        }

        const modal = document.getElementById('viewActasModal');
        if (modal) new bootstrap.Modal(modal).show();

        try {
            // Route: GET /actas/table/{tableId}
            const url = new URL('/actas/table/' + encodeURIComponent(tableId), window.location.origin);
            if (electionTypeId) url.searchParams.set('election_type_id', String(electionTypeId));

            const resp = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf() }
            });
            if (!resp.ok) throw new Error('HTTP ' + resp.status + ' — ' + resp.statusText);

            const payload = await resp.json();
            if (payload.error) throw new Error(payload.error);

            // API returns a plain array of acta objects
            const actas = Array.isArray(payload) ? payload : (payload.data ?? []);

            if (actas.length === 0) {
                bodyEl.innerHTML = `
                    <div class="text-center py-5">
                        <i class="ri-file-copy-2-line text-muted" style="font-size:3rem;"></i>
                        <h6 class="mt-3 text-muted">No hay actas subidas para esta mesa</h6>
                        <p class="text-muted small mb-0">
                            Use el botón <i class="ri-upload-line"></i> de la mesa para subir el acta.
                        </p>
                    </div>`;
                if (countEl) countEl.textContent = 'Sin actas';
                return;
            }

            let html = '<div class="row">';
            actas.forEach(a => { html += renderActaCard(a, tableId, electionTypeId); });
            html += '</div>';
            bodyEl.innerHTML = html;

            const n = actas.length;
            if (countEl) countEl.textContent = n + ' acta' + (n !== 1 ? 's' : '');

            // Wire buttons
            bodyEl.querySelectorAll('.verify-acta-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    verifyActa(this.dataset.actaId, this, tableId, tableLabel, electionTypeId);
                });
            });
            bodyEl.querySelectorAll('.approve-acta-btn').forEach(btn => {
                btn.addEventListener('click', function () { approveActa(this.dataset.actaId, this); });
            });
            bodyEl.querySelectorAll('.reject-acta-btn').forEach(btn => {
                btn.addEventListener('click', function () { rejectActa(this.dataset.actaId, this, tableId, tableLabel, electionTypeId); });
            });

        } catch (err) {
            console.error('loadActasForTable:', err);
            if (bodyEl) {
                bodyEl.innerHTML = `
                    <div class="text-center py-5">
                        <i class="ri-wifi-off-line text-danger" style="font-size:2.5rem;"></i>
                        <h6 class="mt-3 text-danger">Error al cargar las actas</h6>
                        <p class="text-muted small mb-2">${esc(err.message)}</p>
                        <button class="btn btn-outline-primary btn-sm" id="retryActasBtn">
                            <i class="ri-refresh-line me-1"></i>Reintentar
                        </button>
                    </div>`;
                document.getElementById('retryActasBtn')?.addEventListener('click', () => {
                    loadActasForTable(tableId, tableLabel, electionTypeId);
                });
            }
        }
    };

    /* ── Acta actions ───────────────────────────────────────────────────────── */

    async function verifyActa(actaId, btn, tableId, tableLabel, electionTypeId) {
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ri-loader-4-line ri-spin me-1"></i>Verificando…'; }
        try {
            const resp = await fetch('/actas/' + encodeURIComponent(actaId) + '/verify', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
                body:    '{}',
            });
            const data = await resp.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message ?? 'Acta verificada', toast: true, position: 'top-end', showConfirmButton: false, timer: 2500 });
                // Reload the list so the new status badge appears
                await loadActasForTable(tableId, tableLabel, electionTypeId);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message ?? 'Error al verificar' });
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-shield-check-line me-1"></i>Verificar'; }
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error de conexión', text: err.message });
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ri-shield-check-line me-1"></i>Verificar'; }
        }
    }

    async function approveActa(actaId, btn) {
        if (!(await Swal.fire({
            title: '¿Aprobar acta?',
            text:  'Esta acción es definitiva. El acta quedará aprobada y no podrá subirse otra.',
            icon: 'question', showCancelButton: true,
            confirmButtonText: 'Sí, aprobar', confirmButtonColor: '#0ab39c',
        })).isConfirmed) return;

        if (btn) btn.disabled = true;
        try {
            const resp = await fetch('/actas/' + encodeURIComponent(actaId) + '/approve', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            });
            const data = await resp.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message ?? 'Acta aprobada', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                setTimeout(() => location.reload(), 1800);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                if (btn) btn.disabled = false;
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            if (btn) btn.disabled = false;
        }
    }

    async function rejectActa(actaId, btn, tableId, tableLabel, electionTypeId) {
        const { value: notes, isConfirmed } = await Swal.fire({
            title: 'Rechazar acta',
            text:  'Podrá subir una nueva acta después del rechazo.',
            icon:  'warning',
            input: 'textarea', inputLabel: 'Motivo del rechazo (obligatorio)',
            inputPlaceholder: 'Describa el motivo…',
            showCancelButton: true, confirmButtonColor: '#f06548', confirmButtonText: 'Rechazar',
            preConfirm: val => {
                if (!val?.trim()) { Swal.showValidationMessage('El motivo es obligatorio'); return false; }
                return val;
            },
        });
        if (!isConfirmed) return;

        if (btn) btn.disabled = true;
        try {
            const resp = await fetch('/actas/' + encodeURIComponent(actaId) + '/observe', {
                method:  'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
                body:    JSON.stringify({ notes }),
            });
            const data = await resp.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: data.message ?? 'Acta rechazada', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                await loadActasForTable(tableId, tableLabel, electionTypeId);
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                if (btn) btn.disabled = false;
            }
        } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            if (btn) btn.disabled = false;
        }
    }
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.view-actas-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const tableId = this.dataset.tableId;
                const etId    = this.dataset.electionTypeId ?? window.electionTypeId;
                const card    = document.getElementById('table-' + tableId);
                const label   = card?.querySelector('h5')?.textContent?.trim() ?? ('Mesa ' + tableId);
                loadActasForTable(tableId, label, etId);
            });
        });
    });

})();
</script><?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-table-votes\partials\modals\view-actas-modal.blade.php ENDPATH**/ ?>