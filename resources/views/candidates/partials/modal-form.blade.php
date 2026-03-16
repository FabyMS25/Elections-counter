<div class="modal fade" id="candidateModal" tabindex="-1" aria-labelledby="candidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light p-2">
                <h5 class="modal-title" id="candidateModalLabel">
                    <i class="ri-user-star-line me-1"></i>
                    <span id="modalTitleText">Agregar Nuevo Candidato</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="candidateForm" method="POST" class="tablelist-form" autocomplete="off" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="form-method" name="_method" value="POST">
                <input type="hidden" id="candidate_id" name="id">

                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-12">
                            <h6 class="fw-semibold text-muted text-uppercase small">Información del Candidato</h6>
                        </div>
                        <div class="col-md-12">
                                <label for="name-field" class="form-label">
                                    Nombre Completo <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="name-field" name="name" class="form-control"
                                    placeholder="Ej: Juan Pérez González" value="{{ old('name') }}" required maxlength="255" />
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                                <label for="party-field" class="form-label">
                                    Sigla del Partido <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="party-field" name="party" class="form-control"
                                    placeholder="Ej: MAS, UNE, CC" value="{{ old('party') }}" required maxlength="50" />
                                @error('party') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                                <label for="party_full_name-field" class="form-label">
                                    Nombre Completo del Partido
                                </label>
                                <input type="text" id="party_full_name-field" name="party_full_name" class="form-control"
                                    placeholder="Nombre completo del partido" value="{{ old('party_full_name') }}" maxlength="255" />
                                @error('party_full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-12 mt-2">
                            <h6 class="fw-semibold text-muted text-uppercase small">Información de Lista</h6>
                        </div>
                        <div class="col-md-6">
                                <label for="list_name-field" class="form-label">
                                    Nombre de la Lista
                                </label>
                                <input type="text" id="list_name-field" name="list_name" class="form-control"
                                    placeholder="Ej: Lista 1, Frente Amplio" value="{{ old('list_name') }}" maxlength="255" />
                                @error('list_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                                <label for="list_order-field" class="form-label">
                                    Orden en la Lista
                                </label>
                                <input type="number" id="list_order-field" name="list_order" class="form-control"
                                    placeholder="Ej: 1, 2, 3" value="{{ old('list_order') }}" min="1" step="1" />
                                @error('list_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-12 mt-2">
                            <h6 class="fw-semibold text-muted text-uppercase small">Elección y Color</h6>
                        </div>
                        <div class="col-md-6">
                                <label for="election_type_category_id-field" class="form-label">
                                    Elección / Categoría <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" name="election_type_category_id" id="election_type_category_id-field" required>
                                    <option value="">Seleccione una combinación</option>
                                    @foreach($etcs as $etc)
                                        <option value="{{ $etc->id }}"
                                            data-election-type="{{ $etc->electionType->name }}"
                                            data-category="{{ $etc->electionCategory->name }}"
                                            data-code="{{ $etc->electionCategory->code }}"
                                            data-ballot-order="{{ $etc->ballot_order }}"
                                            data-votes-per-person="{{ $etc->votes_per_person }}"
                                            {{ old('election_type_category_id', $candidate->election_type_category_id ?? '') == $etc->id ? 'selected' : '' }}>
                                            {{ $etc->electionType->name }} - {{ $etc->electionCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('election_type_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                                <label for="color-field" class="form-label">
                                    Color Representativo
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="color" class="form-control form-control-color w-25"
                                        id="color-field" name="color" value="{{ old('color', '#1b8af8') }}"
                                        style="height: 38px; padding: 2px;" title="Seleccione un color" />
                                    <input type="text" class="form-control" id="color-hex"
                                        value="{{ old('color', '#1b8af8') }}" placeholder="#RRGGBB"
                                        pattern="^#[0-9A-Fa-f]{6}$" maxlength="7" />
                                </div>
                                <small class="text-muted">Formato hexadecimal: #RRGGBB</small>
                                @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12" id="category-info-row" style="display: none;">
                            <div class="alert alert-info py-2 small mb-0">
                                <div class="row">
                                    <div class="col-md-4">
                                        <span class="text-muted d-block">Código:</span>
                                        <span class="fw-semibold" id="info-category-code">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted d-block">Franja:</span>
                                        <span class="fw-semibold" id="info-ballot-order">-</span>
                                    </div>
                                    <div class="col-md-4">
                                        <span class="text-muted d-block">Votos por persona:</span>
                                        <span class="fw-semibold" id="info-votes-per-person">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-2">
                            <h6 class="fw-semibold text-muted text-uppercase small">Ubicación Geográfica</h6>
                            <small class="text-muted d-block">Opcional - para candidatos departamentales, provinciales o municipales</small>
                        </div>
                        <div class="col-md-4">
                                <label for="department_id-field" class="form-label">Departamento</label>
                                <select class="form-select" name="department_id" id="department_id-field">
                                    <option value="">Seleccione departamento</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                        </div>
                        <div class="col-md-4">
                                <label for="province_id-field" class="form-label">Provincia</label>
                                <select class="form-select" name="province_id" id="province_id-field" disabled>
                                    <option value="">Primero seleccione departamento</option>
                                </select>
                        </div>
                        <div class="col-md-4">
                                <label for="municipality_id-field" class="form-label">Municipio</label>
                                <select class="form-select" name="municipality_id" id="municipality_id-field" disabled>
                                    <option value="">Primero seleccione provincia</option>
                                </select>
                        </div>
                        <div class="col-12" id="active-status-row" style="display: none;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="active-field" name="active" value="1" checked>
                                    <label class="form-check-label" for="active-field">
                                        Candidato Activo
                                    </label>
                                    <small class="text-muted d-block">Si está inactivo, no aparecerá en las listas</small>
                                </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <h6 class="fw-semibold text-muted text-uppercase small">Imágenes</h6>
                        </div>
                        <div class="col-md-6">
                                <label for="photo-field" class="form-label">Foto del Candidato</label>
                                <input type="file" id="photo-field" name="photo" class="form-control"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" />
                                <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                                <div class="mt-2 text-center p-2 bg-light rounded border" style="border-style:dashed!important">
                                    <img id="photo-preview" class="img-fluid rounded" src="" alt="Vista previa"
                                         style="display: none; max-height: 80px; object-fit: cover;">
                                </div>
                        </div>
                        <div class="col-md-6">
                                <label for="party_logo-field" class="form-label">Logo del Partido</label>
                                <input type="file" id="party_logo-field" name="party_logo" class="form-control"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" />
                                <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                                <div class="mt-2 text-center p-2 bg-light rounded border" style="border-style:dashed!important">
                                    <img id="party-logo-preview" class="img-fluid rounded" src="" alt="Vista previa"
                                         style="display: none; max-height: 80px; object-fit: contain;">
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-1">
                    <button type="button" class="btn btn-soft-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="save-btn">
                        <i class="ri-save-line me-1"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
