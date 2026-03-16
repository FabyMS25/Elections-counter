{{-- resources/views/candidates/partials/modal-view.blade.php --}}
<div class="modal fade" id="viewCandidateModal" tabindex="-1"
     aria-labelledby="viewCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-light p-2">
                <h5 class="modal-title" id="viewCandidateModalLabel">
                    <i class="ri-user-star-line me-1"></i>
                    Detalles del Candidato
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body mx-2">
                <div class="border-bottom">
                    <div class="d-flex align-items-start gap-2">
                        <img id="view-photo"
                             src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}"
                             alt="Foto"
                             class="rounded-circle"
                             style="width:100px;height:100px;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.12)">
                        <div class="flex-grow-1">
                            <h5 class="mb-1 fw-semibold" id="view-name">—</h5>
                            <div class="d-flex align-items-center gap-2">
                                <img id="view-party-logo" src="" alt="Logo"
                                     style="width:40px;height:40px;object-fit:contain;display:none">
                                <span class="text-muted" id="view-party-display"></span>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary" id="view-category-badge">—</span>
                                <span id="view-active" class="badge bg-success">Activo</span>
                                @if(false)
                                <span class="badge" style="background-color: var(--bs-warning-bg-subtle); color: var(--bs-warning-text); border:1px solid var(--bs-warning-border-subtle);" id="view-color-badge">
                                    <span id="view-color-dot" style="display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:4px"></span>
                                    <span id="view-color-hex"></span>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="d-none d-md-flex gap-2">
                            <div class="text-center bg-light rounded px-2 py-2">
                                <div class="fw-bold" id="view-stat-order" style="font-size:1.2rem;line-height:1">—</div>
                                <div class="text-muted" style="font-size:.7rem">Orden lista</div>
                            </div>
                            <div class="text-center bg-light rounded px-2 py-2">
                                <div class="fw-bold" id="view-stat-franja" style="font-size:1.2rem;line-height:1">—</div>
                                <div class="text-muted" style="font-size:.7rem">Franja</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pt-3">
                    <h6 class="fw-semibold text-muted text-uppercase small">
                        <i class="ri-vote-line me-1"></i>Elección y Categoría
                    </h6>
                    <div class="info-row">
                        <div class="info-k">Tipo de Elección</div>
                        <div class="info-v" id="view-election-type">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Categoría</div>
                        <div class="info-v" id="view-election-category">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Código</div>
                        <div class="info-v"><code id="view-election-code">—</code></div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Franja (orden en papeleta)</div>
                        <div class="info-v" id="view-ballot-order">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Votos por Persona</div>
                        <div class="info-v" id="view-votes-per-person">—</div>
                    </div>

                    <hr class="my-2">

                    <h6 class="fw-semibold text-muted text-uppercase small">
                        <i class="ri-building-line me-1"></i>Partido Político
                    </h6>
                    <div class="info-row">
                        <div class="info-k">Sigla</div>
                        <div class="info-v fw-semibold" id="view-party">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Nombre completo</div>
                        <div class="info-v" id="view-party-full-name">—</div>
                    </div>
                    <div id="view-color-container" style="display:none;">
                        <div class="info-row">
                            <div class="info-k">Color</div>
                            <div class="info-v d-flex align-items-center gap-2">
                                <span id="view-color-dot" style="display:inline-block;width:20px;height:20px;border-radius:4px;"></span>
                                <span id="view-color-hex" class="font-monospace"></span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <h6 class="fw-semibold text-muted text-uppercase small">
                        <i class="ri-list-ordered me-1"></i>Lista
                    </h6>
                    <div class="info-row">
                        <div class="info-k">Nombre de lista</div>
                        <div class="info-v" id="view-list-name">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Orden en lista</div>
                        <div class="info-v" id="view-list-order">—</div>
                    </div>

                    <hr class="my-2">

                    <h6 class="fw-semibold text-muted text-uppercase small">
                        <i class="ri-map-pin-line me-1"></i>Ubicación Geográfica
                    </h6>
                    <div class="info-row">
                        <div class="info-k">Departamento</div>
                        <div class="info-v" id="view-department">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Provincia</div>
                        <div class="info-v" id="view-province">—</div>
                    </div>
                    <div class="info-row">
                        <div class="info-k">Municipio</div>
                        <div class="info-v" id="view-municipality">—</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-1">
                <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#viewCandidateModal .info-row {
    display: flex;
    gap: .5rem;
    padding: .45rem 0;
    border-bottom: 1px solid #f3f6f9;
}

#viewCandidateModal .info-row:last-child {
    border-bottom: none;
}

#viewCandidateModal .info-k {
    min-width: 42%;
    font-weight: 500;
    color: #6c757d;
    font-size: .82rem;
}

#viewCandidateModal .info-v {
    font-size: .82rem;
    color: #212529;
    flex: 1;
}
</style>
