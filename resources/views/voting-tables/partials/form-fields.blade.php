{{-- resources/views/voting-tables/partials/form-fields.blade.php --}}
@props(['votingTable' => null, 'institutions' => [], 'users' => [], 'departments' => [], 'provinces' => [], 'municipalities' => [], 'localities' => []])
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
    <i class="ri-error-warning-line me-1"></i>
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1 small">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-map-pin-line me-1"></i>Ubicación de la Mesa
</h6>
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <label class="form-label small">Departamento</label>
        <select class="form-select form-select-sm" id="filter-department" data-cascade>
            <option value="">-- Todos los departamentos --</option>
            @foreach($departments ?? [] as $dept)
                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Provincia</label>
        <select class="form-select form-select-sm" id="filter-province" data-cascade disabled>
            <option value="">-- Primero seleccione departamento --</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Municipio</label>
        <select class="form-select form-select-sm" id="filter-municipality" data-cascade disabled>
            <option value="">-- Primero seleccione provincia --</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small">Localidad</label>
        <select class="form-select form-select-sm" id="filter-locality" data-cascade disabled>
            <option value="">-- Primero seleccione municipio --</option>
        </select>
    </div>
</div>

<div class="row g-2 mb-2">
    <div class="col-12">
        <label class="form-label">Recinto Electoral <span class="text-danger">*</span></label>
        <select class="form-select @error('institution_id') is-invalid @enderror"
                name="institution_id" id="institution-field" required>
            <option value="">— Seleccione un recinto —</option>
            @foreach($institutions as $inst)
                <option value="{{ $inst->id }}"
                    data-department="{{ $inst->locality?->municipality?->province?->department_id }}"
                    data-province="{{ $inst->locality?->municipality?->province_id }}"
                    data-municipality="{{ $inst->locality?->municipality_id }}"
                    data-locality="{{ $inst->locality_id }}"
                    {{ old('institution_id', $votingTable->institution_id ?? '') == $inst->id ? 'selected' : '' }}>
                    {{ $inst->name }} @if($inst->code)({{ $inst->code }})@endif
                    @if($inst->locality?->name) - {{ $inst->locality->name }}@endif
                </option>
            @endforeach
        </select>
        @error('institution_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @else
            <small class="text-muted">Seleccione el recinto donde se encuentra esta mesa</small>
        @enderror
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-settings-4-line me-1"></i>Identificación de la Mesa
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-3">
        <label class="form-label">N° Mesa <span class="text-danger">*</span></label>
        <input type="number" name="number" id="number-field"
               class="form-control @error('number') is-invalid @enderror"
               placeholder="Ej: 1"
               value="{{ old('number', $votingTable->number ?? '') }}"
               min="1" required>
        @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-2">
        <label class="form-label">Letra</label>
        <input type="text" name="letter" id="letter-field"
               class="form-control @error('letter') is-invalid @enderror"
               placeholder="A, B, C…"
               value="{{ old('letter', $votingTable->letter ?? '') }}"
               maxlength="1">
        @error('letter')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted">Opcional</small>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Tipo de Mesa</label>
        <select class="form-select @error('type') is-invalid @enderror" name="type" id="type-field">
            <option value="mixta"     {{ old('type', $votingTable->type ?? 'mixta') === 'mixta'     ? 'selected' : '' }}>Mixta (Hombres y Mujeres)</option>
            <option value="masculina" {{ old('type', $votingTable->type ?? '') === 'masculina'       ? 'selected' : '' }}>Masculina</option>
            <option value="femenina"  {{ old('type', $votingTable->type ?? '') === 'femenina'        ? 'selected' : '' }}>Femenina</option>
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Electores Habilitados</label>
        <input type="number" name="expected_voters" id="expected-voters-field"
               class="form-control @error('expected_voters') is-invalid @enderror"
               placeholder="0"
               value="{{ old('expected_voters', $votingTable->expected_voters ?? '') }}"
               min="0">
        @error('expected_voters')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted">Total del padrón electoral</small>@enderror
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-barcode-line me-1"></i>Códigos de Identificación
    <small class="text-muted fw-normal">(se generan automáticamente si se dejan vacíos)</small>
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-6">
        <label class="form-label">Código OEP</label>
        <input type="text" name="oep_code" id="oep-code-field"
               class="form-control @error('oep_code') is-invalid @enderror"
               placeholder="Se genera automáticamente"
               value="{{ old('oep_code', $votingTable->oep_code ?? '') }}"
               maxlength="20">
        @error('oep_code')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted"><i class="ri-information-line"></i> Formato: [Código Recinto]-[N° Mesa][Letra]</small>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Código Interno</label>
        <input type="text" name="internal_code" id="internal-code-field"
               class="form-control @error('internal_code') is-invalid @enderror"
               placeholder="Se genera automáticamente"
               value="{{ old('internal_code', $votingTable->internal_code ?? '') }}"
               maxlength="20">
        @error('internal_code')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted"><i class="ri-information-line"></i> Formato: [Código Recinto]-M[N° Mesa][Letra]</small>@enderror
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-group-line me-1"></i>Rango de Votantes
    <small class="text-muted fw-normal">(rango alfabético del padrón)</small>
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-6">
        <label class="form-label">Desde (apellido)</label>
        <input type="text" name="voter_range_start_name" id="voter-range-start-field"
               class="form-control @error('voter_range_start_name') is-invalid @enderror"
               placeholder="Ej: AAAA AAAA"
               value="{{ old('voter_range_start_name', $votingTable->voter_range_start_name ?? '') }}">
        @error('voter_range_start_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Hasta (apellido)</label>
        <input type="text" name="voter_range_end_name" id="voter-range-end-field"
               class="form-control @error('voter_range_end_name') is-invalid @enderror"
               placeholder="Ej: ZZZZ ZZZZ"
               value="{{ old('voter_range_end_name', $votingTable->voter_range_end_name ?? '') }}">
        @error('voter_range_end_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-chat-1-line me-1"></i>Observaciones
    <small class="text-muted fw-normal">(opcional)</small>
</h6>
<div class="row g-2 mb-2">
    <div class="col-12">
        <textarea name="observations" id="observations-field"
                  class="form-control @error('observations') is-invalid @enderror"
                  rows="2"
                  placeholder="Observaciones adicionales sobre esta mesa de votación">{{ old('observations', $votingTable->observations ?? '') }}</textarea>
        @error('observations')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
