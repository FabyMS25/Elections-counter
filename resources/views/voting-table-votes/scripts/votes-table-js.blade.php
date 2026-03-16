{{-- resources/views/voting-table-votes/scripts/votes-table-js.blade.php --}}
<script>
// ─── Utilities ───────────────────────────────────────────────────────────────

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function showToast(icon, title, text = '') {
    return Swal.fire({
        icon, title, text,
        toast: true, position: 'top-end',
        showConfirmButton: false,
        timer: 3500, timerProgressBar: true,
    });
}

function showError(message) {
    return Swal.fire({ icon: 'error', title: 'Error', text: message });
}

function setButtonLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
        btn.dataset.originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="ri-loader-4-line ri-spin"></i>';
        btn.disabled  = true;
    } else {
        btn.innerHTML = btn.dataset.originalHtml ?? btn.innerHTML;
        btn.disabled  = false;
    }
}

function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ─── Vote input helpers ───────────────────────────────────────────────────────

function collectVotes(tableId) {
    const votes = {};
    document.querySelectorAll(`#table-${tableId} .vote-input`).forEach(input => {
        votes[input.dataset.candidate] = parseInt(input.value) || 0;
    });
    return votes;
}

function collectSpecialVotes(tableId, type) {
    const result = {};
    document.querySelectorAll(`#table-${tableId} .${type}-votes-input`).forEach(input => {
        result[input.dataset.category] = parseInt(input.value) || 0;
    });
    return result;
}

// ─── Live totals (FIX: use querySelectorAll + data-attrs, not getElementById) ─

/**
 * Reads all vote/blank/null inputs for a table and updates:
 *   • header category badges  (.cat-total-display[data-category][data-table])
 *   • TOTALES row in tbody    (same selector — both update simultaneously)
 *   • footer valid/blank/null spans
 *   • urn-count display
 *   • participation bar
 *   • ballot balance indicator
 */
window.updateTableTotals = function(tableId) {
    const catValid = {};
    const catBlank = {};
    const catNull  = {};

    document.querySelectorAll(`#table-${tableId} .vote-input`).forEach(input => {
        const cat    = input.dataset.category;
        catValid[cat] = (catValid[cat] ?? 0) + (parseInt(input.value) || 0);
    });
    document.querySelectorAll(`#table-${tableId} .blank-votes-input`).forEach(input => {
        const cat    = input.dataset.category;
        catBlank[cat] = (catBlank[cat] ?? 0) + (parseInt(input.value) || 0);
    });
    document.querySelectorAll(`#table-${tableId} .null-votes-input`).forEach(input => {
        const cat   = input.dataset.category;
        catNull[cat] = (catNull[cat] ?? 0) + (parseInt(input.value) || 0);
    });

    const allCats     = new Set([...Object.keys(catValid), ...Object.keys(catBlank), ...Object.keys(catNull)]);
    let   firstCatTotal = 0;
    let   allSame     = true;

    allCats.forEach(code => {
        const total = (catValid[code] ?? 0) + (catBlank[code] ?? 0) + (catNull[code] ?? 0);

        // FIX: update ALL elements sharing this category+table — header badge AND TOTALES row
        document.querySelectorAll(
            `.cat-total-display[data-category="${code}"][data-table="${tableId}"]`
        ).forEach(el => { el.textContent = total; });

        if (firstCatTotal === 0) firstCatTotal = total;
        if (total !== firstCatTotal && total > 0) allSame = false;
    });

    // Urn count (header summary)
    const urnEl = document.getElementById(`urn-count-${tableId}`);
    if (urnEl) urnEl.textContent = firstCatTotal.toLocaleString();

    // Also update the urn-total display in the card header
    document.querySelectorAll(
        `.cat-total-display[data-display="urn-total"][data-table="${tableId}"]`
    ).forEach(el => { el.textContent = firstCatTotal; });

    // Footer totals (sum across all categories — useful when debugging)
    let fv = 0, fb = 0, fn = 0;
    Object.values(catValid).forEach(v => { fv += v; });
    Object.values(catBlank).forEach(v => { fb += v; });
    Object.values(catNull).forEach(v  => { fn += v; });
    const fvEl = document.getElementById(`footer-valid-${tableId}`);
    const fbEl = document.getElementById(`footer-blank-${tableId}`);
    const fnEl = document.getElementById(`footer-null-${tableId}`);
    if (fvEl) fvEl.textContent = fv;
    if (fbEl) fbEl.textContent = fb;
    if (fnEl) fnEl.textContent = fn;

    // Participation percentage
    const tableCard  = document.getElementById(`table-${tableId}`);
    const expected   = parseInt(tableCard?.dataset?.expectedVoters || '0') || 0;
    const participEl = document.getElementById(`participation-${tableId}`);
    if (participEl && expected > 0) {
        const pct = Math.round((firstCatTotal / expected) * 1000) / 10;
        participEl.textContent = pct + '%';
        participEl.className   = participEl.className.replace(/text-(success|warning|secondary|danger)/g, '');
        participEl.classList.add(pct >= 75 ? 'text-success' : pct >= 50 ? 'text-warning' : 'text-secondary');
        const bar = participEl.closest('.d-flex')?.querySelector('.progress-bar');
        if (bar) {
            bar.style.width = Math.min(100, pct) + '%';
            bar.className   = bar.className.replace(/bg-(success|warning|secondary|danger)/g, '');
            bar.classList.add(pct >= 75 ? 'bg-success' : pct >= 50 ? 'bg-warning' : 'bg-secondary');
        }
    }

    // Ballot balance (No usadas is operator-entered)
    updateBallotBalance(tableId, firstCatTotal);
};

// Called after server save to sync the displayed totals from the response
function refreshTableTotals(tableId, categoryTotals) {
    Object.entries(categoryTotals).forEach(([code, total]) => {
        document.querySelectorAll(
            `.cat-total-display[data-category="${code}"][data-table="${tableId}"]`
        ).forEach(el => { el.textContent = total; });
    });
    const counts  = Object.values(categoryTotals);
    const grand   = counts.length > 0 ? counts[0] : 0;
    const urnEl   = document.getElementById(`urn-count-${tableId}`);
    if (urnEl) urnEl.textContent = grand.toLocaleString();
    document.querySelectorAll(
        `.cat-total-display[data-display="urn-total"][data-table="${tableId}"]`
    ).forEach(el => { el.textContent = grand; });
}

function setTableStatusClass(tableId, newStatus) {
    const card = document.getElementById(`table-${tableId}`);
    if (!card) return;
    card.className = card.className.replace(/\bstatus-\S+/g, '');
    card.classList.add(`status-${newStatus}`);
}

// ─── Ballot balance display ───────────────────────────────────────────────────

function updateBallotBalance(tableId, urnTotal) {
    const leftoverEl = document.getElementById(`leftover-${tableId}`);
    const spoiledEl  = document.getElementById(`spoiled-${tableId}`);
    const tableCard  = document.getElementById(`table-${tableId}`);
    const expected   = parseInt(tableCard?.dataset?.expectedVoters || '0') || 0;

    const leftoverRaw = leftoverEl?.value?.trim() ?? '';
    const leftover    = leftoverRaw !== '' ? (parseInt(leftoverRaw) || 0) : null;
    const spoiled     = parseInt(spoiledEl?.value || '0') || 0;
    const hasLeftover = leftover !== null;
    const accounted   = urnTotal + (leftover ?? 0) + spoiled;

    // Update formula spans
    const fUrn      = document.getElementById(`formula-urn-${tableId}`);
    const fLeftover = document.getElementById(`formula-leftover-${tableId}`);
    const fSpoiled  = document.getElementById(`formula-spoiled-${tableId}`);
    const fTotal    = document.getElementById(`formula-total-${tableId}`);
    if (fUrn)      fUrn.textContent      = urnTotal.toLocaleString();
    if (fLeftover) fLeftover.textContent = (leftover ?? 0).toLocaleString();
    if (fSpoiled)  fSpoiled.textContent  = spoiled.toLocaleString();

    const spoiledWrap = document.querySelector(`#ballot-data-${tableId} .formula-spoiled-wrap`);
    if (spoiledWrap) spoiledWrap.style.display = spoiled > 0 ? '' : 'none';

    if (fTotal) {
        fTotal.textContent = accounted.toLocaleString();
        fTotal.className   = hasLeftover
            ? (accounted === expected ? 'text-success fw-bold' : 'text-danger fw-bold')
            : 'text-muted';
    }

    const hintExpected = Math.max(0, expected - urnTotal - spoiled);
    const hintEl = leftoverEl?.closest('.ballot-field')?.querySelector('.text-primary strong');
    if (hintEl) hintEl.textContent = String(hintExpected).padStart(3, '0');

    // Participation
    const participEl = document.getElementById(`participation-${tableId}`);
    if (participEl && expected > 0) {
        const pct = Math.round((urnTotal / expected) * 1000) / 10;
        participEl.textContent = pct + '%';
        participEl.className   = participEl.className.replace(/text-(success|warning|secondary|danger)/g, '');
        participEl.classList.add(pct >= 75 ? 'text-success' : pct >= 50 ? 'text-warning' : 'text-secondary');
        const bar = participEl.closest('.d-flex')?.querySelector('.progress-bar');
        if (bar) {
            bar.style.width = Math.min(100, pct) + '%';
            bar.className   = bar.className.replace(/bg-(success|warning|secondary|danger)/g, '');
            bar.classList.add(pct >= 75 ? 'bg-success' : pct >= 50 ? 'bg-warning' : 'bg-secondary');
        }
    }

    const balanceEl = document.getElementById(`ballot-balance-${tableId}`);
    if (!balanceEl) return;

    if (!hasLeftover) {
        balanceEl.innerHTML = `
            <div class="badge-balance badge-balance-warn">
                <i class="ri-pencil-line me-1"></i>
                Ingrese las <strong>Papeletas no utilizadas</strong>
                del acta física para verificar el cuadre
            </div>`;
        return;
    }

    if (accounted === expected) {
        balanceEl.innerHTML = `
            <div class="badge-balance badge-balance-ok">
                <i class="ri-checkbox-circle-line me-1"></i>
                Cuadre correcto ✓ — ${accounted.toLocaleString()} = ${expected.toLocaleString()} habilitados
            </div>`;
    } else {
        const diff    = accounted - expected;
        const spoiledPart = spoiled > 0 ? ` + ${spoiled} deterioradas` : '';
        balanceEl.innerHTML = `
            <div class="badge-balance badge-balance-err">
                <i class="ri-alert-line me-1"></i>
                No cuadra: ${urnTotal} + ${leftover}${spoiledPart} = ${accounted} ≠ ${expected} habilitados
                (${diff > 0 ? '+' : ''}${diff} papeleta${Math.abs(diff) !== 1 ? 's' : ''})
            </div>`;
    }
}

// ─── Collect detailed per-category breakdown for error messages ───────────────

/**
 * Returns an object: { ALC: {valid, blank, null_, total}, CON: {...}, ... }
 * Used to show a helpful breakdown when categories don't match totals.
 */
function collectCategoryBreakdown(tableId) {
    const details = {};
    document.querySelectorAll(`#table-${tableId} .vote-input`).forEach(input => {
        const cat = input.dataset.category;
        if (!details[cat]) details[cat] = { valid: 0, blank: 0, null_: 0 };
        details[cat].valid += parseInt(input.value) || 0;
    });
    document.querySelectorAll(`#table-${tableId} .blank-votes-input`).forEach(input => {
        const cat = input.dataset.category;
        if (!details[cat]) details[cat] = { valid: 0, blank: 0, null_: 0 };
        details[cat].blank = parseInt(input.value) || 0;
    });
    document.querySelectorAll(`#table-${tableId} .null-votes-input`).forEach(input => {
        const cat = input.dataset.category;
        if (!details[cat]) details[cat] = { valid: 0, blank: 0, null_: 0 };
        details[cat].null_ = parseInt(input.value) || 0;
    });
    Object.keys(details).forEach(cat => {
        const d = details[cat];
        d.total = d.valid + d.blank + d.null_;
    });
    return details;
}

/**
 * Build a detailed HTML table for the cross-category mismatch error.
 * Makes it immediately obvious which franja is wrong and what to fix.
 *
 * Example output:
 *  ┌──────┬─────────┬────────┬────────┬────────┐
 *  │ Franja│ Válidos │ Blancos│ Nulos  │ Total  │
 *  ├──────┼─────────┼────────┼────────┼────────┤
 *  │ ALC   │  150    │   20   │   10   │  180 ✓ │
 *  │ CON   │  150    │   20   │    0   │  170 ✗ │ ← falta 10 en nulos
 *  └──────┴─────────┴────────┴────────┴────────┘
 */
function buildCategoryMismatchHtml(breakdown, expected) {
    const totals  = Object.entries(breakdown).map(([code, d]) => d.total);
    const maxTotal = Math.max(...totals);

    let rows = '';
    Object.entries(breakdown).forEach(([code, d]) => {
        const ok   = d.total === maxTotal;
        const diff = maxTotal - d.total;
        const hint = !ok && diff !== 0
            ? `<span class="text-danger ms-2 small">← falta ${diff} (¿nulos o blancos de ${code}?)</span>`
            : '';
        rows += `
            <tr class="${ok ? '' : 'table-danger'}">
                <td><strong>${escHtml(code)}</strong></td>
                <td class="text-end">${d.valid}</td>
                <td class="text-end">${d.blank}</td>
                <td class="text-end">${d.null_}</td>
                <td class="text-end fw-bold">
                    ${d.total}
                    ${ok
                        ? '<span class="text-success ms-1">✓</span>'
                        : `<span class="text-danger ms-1">✗</span>`
                    }
                    ${hint}
                </td>
            </tr>`;
    });

    return `
        <p class="text-start text-muted small mb-2">
            En Bolivia, todos los candidatos están en la <strong>misma papeleta física</strong>.
            Por lo tanto, el total de votos en ánfora debe ser igual en todas las franjas.
        </p>
        <table class="table table-sm table-bordered small text-start mb-2">
            <thead class="table-light">
                <tr>
                    <th>Franja</th>
                    <th class="text-end">Válidos</th>
                    <th class="text-end">Blancos</th>
                    <th class="text-end">Nulos</th>
                    <th class="text-end">Total ánfora</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>
        <p class="text-start text-danger small fw-bold mb-0">
            <i class="ri-alert-line me-1"></i>
            Corrija los votos en blanco o nulos de la(s) franja(s) marcadas en rojo
            hasta que todos los totales sean iguales.
        </p>`;
}

// ─── Save votes ───────────────────────────────────────────────────────────────

async function saveTable(tableId, closeAfter = false) {
    // Read urn total from DOM
    const urnEl  = document.getElementById(`urn-count-${tableId}`);
    const urnVal = parseInt((urnEl?.textContent ?? '0').replace(/[^\d]/g, '')) || 0;

    // Read ballot accounting inputs
    const leftoverEl = document.getElementById(`leftover-${tableId}`);
    const spoiledEl  = document.getElementById(`spoiled-${tableId}`);
    const tableCard  = document.getElementById(`table-${tableId}`);
    const expected   = parseInt(tableCard?.dataset?.expectedVoters || '0') || 0;

    const leftoverRaw = leftoverEl?.value?.trim() ?? '';
    const leftover    = leftoverRaw !== '' ? Math.max(0, parseInt(leftoverRaw) || 0) : null;
    const spoiled     = Math.max(0, parseInt(spoiledEl?.value || '0') || 0);

    // ── Client-side ballot validation ───────────────────────────────────────
    // Rule 1: Ánfora ≤ Habilitados
    if (expected > 0 && urnVal > expected) {
        await Swal.fire({
            icon:  'error',
            title: '⚠️ Votos superan el padrón',
            html:  `<p>Los <strong>votos en ánfora</strong>
                    <strong class="text-danger">(${urnVal})</strong>
                    superan los <strong>electores habilitados</strong>
                    <strong class="text-info">(${expected})</strong>.</p>
                    <p class="text-muted small mb-0">
                      Revise los votos por candidato, votos en blanco y votos nulos.
                    </p>`,
            confirmButtonColor: '#f06548',
            confirmButtonText:  'Corregir',
        });
        return false;
    }

    // Rule 2: If no-usadas entered, formula must balance
    if (leftover !== null && expected > 0) {
        const accounted = urnVal + leftover + spoiled;
        if (accounted !== expected) {
            const diff = accounted - expected;
            const spoiledRow = spoiled > 0
                ? `<tr><td>Deterioradas</td><td class="fw-bold text-end">${spoiled}</td></tr>`
                : '';
            const confirmed = await Swal.fire({
                icon:  'error',
                title: '⚠️ No cuadran las papeletas',
                html: `<table class="table table-sm table-bordered small mt-2 mb-2">
                         <thead><tr class="table-light"><th>Concepto</th><th class="text-end">Cantidad</th></tr></thead>
                         <tbody>
                           <tr><td>Papeletas en ánfora <span class="badge bg-secondary ms-1">auto</span></td>
                               <td class="fw-bold text-end">${urnVal}</td></tr>
                           <tr><td>No utilizadas <span class="badge bg-warning text-dark ms-1">del acta</span></td>
                               <td class="fw-bold text-end">${leftover}</td></tr>
                           ${spoiledRow}
                           <tr class="${accounted === expected ? 'table-success' : 'table-danger'}">
                             <td><strong>Total contabilizado</strong></td>
                             <td class="fw-bold text-end"><strong>${accounted}</strong></td>
                           </tr>
                           <tr class="table-info">
                             <td><strong>Electores habilitados</strong></td>
                             <td class="fw-bold text-end"><strong>${expected}</strong></td>
                           </tr>
                         </tbody>
                       </table>
                       <p class="text-danger fw-bold mb-0">
                         Diferencia: <strong>${diff > 0 ? '+' : ''}${diff}</strong>
                         papeleta${Math.abs(diff) !== 1 ? 's' : ''}
                       </p>`,
                confirmButtonColor: '#f06548',
                confirmButtonText:  'Corregir datos',
                cancelButtonText:   'Guardar de todas formas',
                showCancelButton:   true,
            });
            if (confirmed.isConfirmed) return false;
        }
    }

    // ── Cross-category consistency check (client-side preview) ─────────────
    // We do this here to give the user a helpful error BEFORE hitting the server.
    const breakdown = collectCategoryBreakdown(tableId);
    const catTotals = Object.values(breakdown).map(d => d.total).filter(t => t > 0);
    if (catTotals.length > 1) {
        const maxT    = Math.max(...catTotals);
        const allSame = catTotals.every(t => t === maxT);
        if (!allSame) {
            const confirmed = await Swal.fire({
                icon:  'warning',
                title: '⚠️ Los totales de las franjas no coinciden',
                html:  buildCategoryMismatchHtml(breakdown, expected),
                width: 640,
                confirmButtonColor: '#f06548',
                confirmButtonText:  'Corregir',
                cancelButtonText:   'Guardar de todas formas',
                showCancelButton:   true,
            });
            if (confirmed.isConfirmed) return false;
            // If "Guardar de todas formas", proceed — server will also validate and may block
        }
    }

    // ── Submit to server ────────────────────────────────────────────────────
    const btn = document.querySelector(`[data-table-id="${tableId}"].save-table`);
    setButtonLoading(btn, true);

    try {
        const votes      = collectVotes(tableId);
        const blankVotes = collectSpecialVotes(tableId, 'blank');
        const nullVotes  = collectSpecialVotes(tableId, 'null');

        const body = {
            voting_table_id:  tableId,
            election_type_id: window.electionTypeId,
            votes,
            blank_votes:      Object.keys(blankVotes).length ? blankVotes : undefined,
            null_votes:       Object.keys(nullVotes).length  ? nullVotes  : undefined,
            ballots_leftover: leftover !== null ? leftover : undefined,
            ballots_spoiled:  spoiled,
            close:            closeAfter,
        };

        const response = await fetch('/voting-table-votes/register', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept':       'application/json',
            },
            body: JSON.stringify(body),
        });

        const data = await response.json();

        if (data.success) {
            refreshTableTotals(tableId, data.category_totals ?? {});
            setTableStatusClass(tableId, data.table_status);

            // Sync ballot fields from server-confirmed values
            const urnDisplayEl = document.getElementById(`urn-count-${tableId}`);
            if (urnDisplayEl && data.total_voters !== undefined) {
                urnDisplayEl.textContent = Number(data.total_voters).toLocaleString();
            }
            if (data.ballots_leftover !== undefined && leftoverEl) {
                leftoverEl.value = data.ballots_leftover;
            }
            if (data.ballots_spoiled !== undefined && spoiledEl) {
                spoiledEl.value = data.ballots_spoiled;
            }
            updateBallotBalance(tableId, data.total_voters ?? 0);

            showToast('success', data.message);
            document.dispatchEvent(new CustomEvent('tableSaved', { detail: { tableId: String(tableId) } }));
        } else {
            // Server returned an error — if it's the cross-category mismatch, show the detailed breakdown
            const msg = data.errors
                ? Object.values(data.errors).flat().join('\n')
                : (data.message ?? 'Error al guardar votos');

            if (msg.includes('Inconsistencia entre franjas') || msg.includes('misma papeleta')) {
                const freshBreakdown = collectCategoryBreakdown(tableId);
                await Swal.fire({
                    icon:  'error',
                    title: '⚠️ Los totales de las franjas no coinciden',
                    html:  buildCategoryMismatchHtml(freshBreakdown, expected),
                    width: 640,
                    confirmButtonColor: '#f06548',
                    confirmButtonText:  'Corregir',
                });
            } else {
                showError(msg);
            }
        }
    } catch (err) {
        console.error('saveTable error', err);
        showError('Error de conexión. Verifique su red e intente nuevamente.');
    } finally {
        setButtonLoading(btn, false);
    }
}

// ─── Review / Correct / Validate / Reopen (unchanged logic, kept concise) ────

async function reviewTable(tableId) {
    let votes = [];
    try {
        const r = await fetch(
            `/voting-table-votes/${tableId}/votes?election_type_id=${window.electionTypeId}`,
            { headers: { 'Accept': 'application/json' } }
        );
        votes = await r.json();
    } catch { showError('No se pudieron cargar los votos de la mesa'); return; }

    const byCategory = {};
    votes.forEach(v => {
        const cat = v.category_code || 'General';
        if (!byCategory[cat]) byCategory[cat] = [];
        byCategory[cat].push(v);
    });

    let rows = '';
    Object.entries(byCategory).forEach(([cat, catVotes]) => {
        rows += `<div class="mb-2">
            <div class="text-muted small fw-bold border-bottom pb-1 mb-1">${escHtml(cat)}</div>`;
        catVotes.forEach(v => {
            const isObs = v.vote_status === 'observed';
            rows += `
            <div class="form-check mb-1 ${isObs ? 'text-warning' : ''}">
                <input class="form-check-input review-observe-cb"
                       type="checkbox" value="${v.id}" id="rev_${v.id}"
                       ${isObs ? 'checked disabled' : ''}>
                <label class="form-check-label d-flex justify-content-between" for="rev_${v.id}">
                    <span>
                        <strong>${escHtml(v.candidate_name)}</strong>
                        <small class="text-muted ms-1">${escHtml(v.candidate_party)}</small>
                        ${isObs ? '<span class="badge bg-warning text-dark ms-1">Ya observado</span>' : ''}
                    </span>
                    <span class="badge bg-secondary">${v.quantity} votos</span>
                </label>
            </div>`;
        });
        rows += `</div>`;
    });

    const { value: formValues } = await Swal.fire({
        title: `Revisión — Mesa ${tableId}`, width: 620,
        html: `
            <p class="text-start text-muted small mb-2">
                Marque los candidatos cuyos votos desea observar.
                Si todo está correcto, deje sin marcar y confirme.
            </p>
            <div class="text-start border rounded p-2 mb-3" style="max-height:300px;overflow-y:auto;">
                ${rows || '<p class="text-muted text-center py-3">No hay votos registrados</p>'}
            </div>
            <div class="text-start">
                <label class="form-label fw-bold">Notas (opcional):</label>
                <textarea id="review-notes" class="form-control" rows="2"
                          placeholder="Describa lo observado…"></textarea>
            </div>`,
        showCancelButton: true, confirmButtonText: 'Confirmar revisión', cancelButtonText: 'Cancelar',
        preConfirm: () => ({
            observed_vote_ids: Array.from(document.querySelectorAll('.review-observe-cb:checked:not(:disabled)')).map(cb => parseInt(cb.value)),
            observation_notes: document.getElementById('review-notes').value.trim(),
        }),
    });
    if (!formValues) return;

    const btn = document.querySelector(`[data-table-id="${tableId}"].review-table`);
    setButtonLoading(btn, true);
    try {
        const resp = await fetch(`/voting-table-votes/${tableId}/review`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            body: JSON.stringify({ election_type_id: window.electionTypeId, observed_vote_ids: formValues.observed_vote_ids, observation_notes: formValues.observation_notes || null }),
        });
        const data = await resp.json();
        if (data.success) { setTableStatusClass(tableId, data.table_status); showToast(data.has_observations ? 'warning' : 'success', data.message); setTimeout(() => location.reload(), 1800); }
        else showError(data.message);
    } finally { setButtonLoading(btn, false); }
}

async function correctTable(tableId) {
    let votes = [];
    try {
        const r = await fetch(`/voting-table-votes/${tableId}/votes?election_type_id=${window.electionTypeId}`, { headers: { 'Accept': 'application/json' } });
        votes = await r.json();
    } catch { showError('No se pudieron cargar los votos de la mesa'); return; }

    const byCategory = {};
    votes.forEach(v => { const cat = v.category_code || 'General'; if (!byCategory[cat]) byCategory[cat] = []; byCategory[cat].push(v); });
    const blankByCategory = {};
    document.querySelectorAll(`#table-${tableId} .blank-votes-input`).forEach(inp => { blankByCategory[inp.dataset.category] = parseInt(inp.value) || 0; });
    const nullByCategory = {};
    document.querySelectorAll(`#table-${tableId} .null-votes-input`).forEach(inp => { nullByCategory[inp.dataset.category] = parseInt(inp.value) || 0; });

    let rows = '';
    Object.entries(byCategory).forEach(([cat, catVotes]) => {
        rows += `<div class="mb-3"><div class="fw-bold small text-muted border-bottom pb-1 mb-2">${escHtml(cat)}</div>`;
        catVotes.forEach(v => {
            const isObs = v.vote_status === 'observed';
            rows += `<div class="row align-items-center mb-1 ${isObs ? 'bg-warning bg-opacity-10 rounded px-1' : ''}">
                <div class="col-7 small">${isObs ? '<i class="ri-alert-line text-warning me-1"></i>' : ''}
                    <strong>${escHtml(v.candidate_name)}</strong><div class="text-muted">${escHtml(v.candidate_party)}</div></div>
                <div class="col-5"><input type="number" class="form-control form-control-sm correction-input"
                    data-vote-id="${v.id}" data-category="${escHtml(cat)}" value="${v.quantity}" min="0"></div>
            </div>`;
        });
        rows += `<div class="row align-items-center mb-1 mt-2">
                    <div class="col-7 small text-muted"><i class="ri-subtract-line me-1"></i>En Blanco</div>
                    <div class="col-5"><input type="number" class="form-control form-control-sm blank-correction-input"
                        data-category="${escHtml(cat)}" value="${blankByCategory[cat] ?? 0}" min="0"></div>
                 </div>
                 <div class="row align-items-center mb-2">
                    <div class="col-7 small text-muted"><i class="ri-close-line me-1"></i>Nulos</div>
                    <div class="col-5"><input type="number" class="form-control form-control-sm null-correction-input"
                        data-category="${escHtml(cat)}" value="${nullByCategory[cat] ?? 0}" min="0"></div>
                 </div></div>`;
    });

    const { value: formValues } = await Swal.fire({
        title: `Corrección — Mesa ${tableId}`, width: 640,
        html: `<div class="text-start border rounded p-2 mb-3" style="max-height:340px;overflow-y:auto;">${rows || '<p class="text-muted text-center py-3">No hay votos</p>'}</div>
               <div class="text-start"><label class="fw-bold form-label">Motivo <span class="text-danger">*</span></label>
               <textarea id="correction-notes" class="form-control" rows="2" placeholder="Describa el motivo…"></textarea></div>`,
        showCancelButton: true, confirmButtonText: 'Aplicar correcciones', cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const notes = document.getElementById('correction-notes').value.trim();
            if (!notes) { Swal.showValidationMessage('El motivo es obligatorio'); return false; }
            const corrections = {}, blank_votes = {}, null_votes = {};
            document.querySelectorAll('.correction-input').forEach(inp => { corrections[inp.dataset.voteId] = parseInt(inp.value) || 0; });
            document.querySelectorAll('.blank-correction-input').forEach(inp => { blank_votes[inp.dataset.category] = parseInt(inp.value) || 0; });
            document.querySelectorAll('.null-correction-input').forEach(inp => { null_votes[inp.dataset.category] = parseInt(inp.value) || 0; });
            return { corrections, notes, blank_votes, null_votes };
        },
    });
    if (!formValues) return;

    const btn = document.querySelector(`[data-table-id="${tableId}"].correct-table`);
    setButtonLoading(btn, true);
    try {
        const resp = await fetch(`/voting-table-votes/${tableId}/correct`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            body: JSON.stringify({ election_type_id: window.electionTypeId, ...formValues }),
        });
        const data = await resp.json();
        if (data.success) { showToast('success', data.message); setTimeout(() => location.reload(), 1800); }
        else showError(data.message);
    } finally { setButtonLoading(btn, false); }
}

async function validateTable(tableId, action) {
    const meta = {
        validate: { icon: 'question', label: 'Validar votos',  text: 'Los votos quedarán validados y la mesa pasará a En Escrutinio.', color: '#17a2b8' },
        escrutar: { icon: 'warning',  label: 'Escrutar mesa',  text: 'Se cerrará el conteo definitivamente.', color: '#0ab39c' },
        reject:   { icon: 'warning',  label: 'Rechazar mesa',  text: 'La mesa volverá a estado Observada.', color: '#f06548' },
    }[action] ?? { icon: 'question', label: action, text: '', color: '#17a2b8' };

    const { value: notes, isConfirmed } = await Swal.fire({
        title: meta.label, text: meta.text, icon: meta.icon,
        input: 'textarea', inputLabel: action === 'reject' ? 'Motivo (obligatorio)' : 'Notas (opcional)',
        inputPlaceholder: 'Agregue notas…', showCancelButton: true,
        confirmButtonText: `Sí, ${meta.label.toLowerCase()}`, confirmButtonColor: meta.color, cancelButtonText: 'Cancelar',
        preConfirm: val => { if (action === 'reject' && !val?.trim()) { Swal.showValidationMessage('El motivo es obligatorio'); return false; } return val; },
    });
    if (!isConfirmed) return;

    const btn = document.querySelector(`[data-table-id="${tableId}"].validate-table[data-action="${action}"]`);
    setButtonLoading(btn, true);
    try {
        const resp = await fetch(`/voting-table-votes/${tableId}/validate`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
            body: JSON.stringify({ election_type_id: window.electionTypeId, action, notes: notes || null }),
        });
        const data = await resp.json();
        if (data.success) { setTableStatusClass(tableId, data.table_status); showToast('success', data.message); setTimeout(() => location.reload(), 1800); }
        else showError(data.message);
    } finally { setButtonLoading(btn, false); }
}

async function reopenTable(tableId) {
    if (!(await Swal.fire({ title: '¿Reabrir mesa?', text: 'La mesa volverá al estado Votación.', icon: 'question', showCancelButton: true, confirmButtonText: 'Sí, reabrir' })).isConfirmed) return;
    const btn = document.querySelector(`[data-table-id="${tableId}"].reopen-table`);
    setButtonLoading(btn, true);
    try {
        const resp = await fetch(`/voting-table-votes/${tableId}/reopen`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }, body: JSON.stringify({ election_type_id: window.electionTypeId }) });
        const data = await resp.json();
        if (data.success) { setTableStatusClass(tableId, 'votacion'); showToast('success', data.message); setTimeout(() => location.reload(), 1800); }
        else showError(data.message);
    } finally { setButtonLoading(btn, false); }
}

window.saveAllTables = async function() {
    const saveBtns = document.querySelectorAll('.save-table');
    if (saveBtns.length === 0) { showToast('info', 'No hay mesas editables en esta página'); return; }
    if (!(await Swal.fire({ title: `¿Guardar ${saveBtns.length} mesa(s)?`, text: 'Se guardarán todos los votos ingresados.', icon: 'question', showCancelButton: true, confirmButtonText: 'Guardar todo' })).isConfirmed) return;
    let ok = 0, fail = 0;
    for (const btn of saveBtns) {
        try { const res = await saveTable(parseInt(btn.dataset.tableId)); if (res !== false) ok++; else fail++; }
        catch { fail++; }
    }
    showToast('success', `${ok} mesa(s) guardada(s)${fail > 0 ? `, ${fail} con errores` : ''}`);
};

// ─── Ballot input listeners ───────────────────────────────────────────────────

function bindBallotInputs() {
    document.querySelectorAll('.ballot-leftover-input, .ballot-spoiled-input').forEach(input => {
        input.addEventListener('focus', function () { this.select(); });
        input.addEventListener('input', function () {
            const tableId = this.dataset.table;
            if (!tableId) return;
            const urnEl  = document.getElementById(`urn-count-${tableId}`);
            const urnVal = parseInt((urnEl?.textContent ?? '0').replace(/[^\d]/g, '')) || 0;
            updateBallotBalance(tableId, urnVal);
            if (window.pendingTables) window.pendingTables.add(String(tableId));
        });
    });
}

function initBallotBalances() {
    document.querySelectorAll('.ballot-data-section').forEach(section => {
        const tableId = section.dataset.tableId;
        if (!tableId) return;
        const urnEl  = document.getElementById(`urn-count-${tableId}`);
        const urnVal = parseInt((urnEl?.textContent ?? '0').replace(/[^\d]/g, '')) || 0;
        updateBallotBalance(tableId, urnVal);
    });
}

// ─── Main listener wiring ─────────────────────────────────────────────────────

window.initVoteListeners = function() {
    document.querySelectorAll('.vote-input').forEach(input => {
        input.addEventListener('focus', function () { this.select(); });
        input.addEventListener('input', () => window.updateTableTotals(input.dataset.table));
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter' || (e.key === 'Tab' && !e.shiftKey)) {
                const all = Array.from(document.querySelectorAll(`#table-${input.dataset.table} .vote-input`));
                const idx = all.indexOf(input);
                if (idx < all.length - 1) { e.preventDefault(); all[idx + 1].focus(); }
            }
        });
    });

    document.querySelectorAll('.blank-votes-input, .null-votes-input').forEach(input => {
        input.addEventListener('focus', function () { this.select(); });
        input.addEventListener('input', () => window.updateTableTotals(input.dataset.table));
    });

    document.querySelectorAll('.save-table').forEach(btn =>
        btn.addEventListener('click', async (e) => { e.preventDefault(); await saveTable(parseInt(btn.dataset.tableId)); })
    );
    document.querySelectorAll('.review-table').forEach(btn =>
        btn.addEventListener('click', () => reviewTable(parseInt(btn.dataset.tableId)))
    );
    document.querySelectorAll('.correct-table').forEach(btn =>
        btn.addEventListener('click', () => correctTable(parseInt(btn.dataset.tableId)))
    );
    document.querySelectorAll('.validate-table').forEach(btn =>
        btn.addEventListener('click', () => validateTable(parseInt(btn.dataset.tableId), btn.dataset.action ?? 'validate'))
    );
    document.querySelectorAll('.reopen-table').forEach(btn =>
        btn.addEventListener('click', () => reopenTable(parseInt(btn.dataset.tableId)))
    );

    document.querySelectorAll('.observe-checkbox').forEach(cb => {
        cb.addEventListener('change', () => {
            if (cb.checked && !cb.dataset.voteId) { cb.checked = false; showError('Guarde los votos antes de marcarlos como observados'); return; }
            const tableId = cb.dataset.table;
            const allChecked = Array.from(document.querySelectorAll(`#table-${tableId} .observe-checkbox:checked`)).filter(c => c.dataset.voteId);
            const countEl = document.getElementById(`selected-count-${tableId}`);
            if (countEl) countEl.textContent = allChecked.length;
            const cats = {};
            allChecked.forEach(c => { cats[c.dataset.category] = (cats[c.dataset.category] ?? 0) + 1; });
            document.querySelectorAll(`#table-${tableId} [id^="selected-"]`).forEach(el => { if (el.id !== `selected-count-${tableId}`) el.textContent = ''; });
            Object.entries(cats).forEach(([code, n]) => { const el = document.getElementById(`selected-${code}-${tableId}`); if (el) el.textContent = `${n} ${code}`; });
        });
    });

    bindBallotInputs();
    initBallotBalances();
};
</script>
