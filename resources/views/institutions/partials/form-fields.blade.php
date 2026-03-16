{{-- resources/views/institutions/partials/form-fields.blade.php --}}
@props(['institution' => null, 'departments' => [], 'statusOptions' => []])

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="ri-error-warning-line me-1"></i>
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1 small">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-building-line me-1"></i>Datos Básicos del Recinto
</h6>
<div class="row g-2">
    <div class="col-md-8">
        <label class="form-label">Nombre del Recinto <span class="text-danger">*</span></label>
        <input type="text" id="name-field" name="name"
               class="form-control @error('name') is-invalid @enderror"
               placeholder="Ej: Unidad Educativa Simón Bolívar"
               value="{{ old('name', $institution->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted">Nombre completo del recinto electoral</small>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Código</label>
        <input type="text" id="code-field" name="code"
               class="form-control @error('code') is-invalid @enderror"
               placeholder="Se genera automáticamente"
               value="{{ old('code', $institution->code ?? '') }}">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted"><i class="ri-information-line"></i> Dejar vacío para generar automáticamente</small>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Nombre Corto</label>
        <input type="text" id="short_name-field" name="short_name"
               class="form-control @error('short_name') is-invalid @enderror"
               placeholder="Ej: UE Simón Bolívar"
               value="{{ old('short_name', $institution->short_name ?? '') }}">
        @error('short_name')<div class="invalid-feedback">{{ $message }}</div>
        @else<small class="text-muted">Opcional — nombre abreviado para reportes y listas</small>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Estado del Edificio <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror"
                name="status" id="status-field" required>
            <option value="activo"           {{ old('status', $institution->status ?? 'activo') === 'activo'           ? 'selected' : '' }}>🟢 Activo</option>
            <option value="en_mantenimiento" {{ old('status', $institution->status ?? '') === 'en_mantenimiento'       ? 'selected' : '' }}>🟡 En Mantenimiento</option>
            <option value="inactivo"         {{ old('status', $institution->status ?? '') === 'inactivo'               ? 'selected' : '' }}>🔴 Inactivo</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Estado físico/administrativo del local</small>
    </div>
    <div class="col-md-3">
        <label class="form-label d-block">Habilitado para Elecciones</label>
        <div class="form-check form-switch mt-1">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="is_operative-field" name="is_operative" value="1"
                   {{ old('is_operative', $institution->is_operative ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_operative-field" id="operative-label"></label>
        </div>
        <small class="text-muted" style="font-size:.72rem">
            Activa si participará en la jornada electoral vigente
        </small>
        <div id="operative-warning" class="alert alert-warning py-1 px-2 mt-1 mb-0 small" style="display:none;">
            <i class="ri-error-warning-line me-1"></i>Un recinto no activo no puede estar habilitado para elecciones.
        </div>
    </div>
</div>
<hr class="my-2">
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-map-pin-line me-1"></i>Ubicación Geográfica
</h6>
<div class="row g-2 mb-2">
    <div class="col-md-3">
        <label class="form-label">Departamento <span class="text-danger">*</span></label>
        <select class="form-select @error('department_id') is-invalid @enderror"
                name="department_id" id="department-field"
                data-cascade-url="{{ url('institutions/provinces') }}"
                data-cascade-target="#province-field" required>
            <option value="">— Seleccione —</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}"
                    {{ old('department_id', $institution?->locality?->municipality?->province?->department_id) == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Provincia <span class="text-danger">*</span></label>
        <select class="form-select @error('province_id') is-invalid @enderror"
                name="province_id" id="province-field"
                data-cascade-url="{{ url('institutions/municipalities') }}"
                data-cascade-target="#municipality-field"
                data-restore="{{ old('province_id', $institution?->locality?->municipality?->province_id) }}"
                required disabled>
            <option value="">— Seleccione departamento primero —</option>
        </select>
        @error('province_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Municipio <span class="text-danger">*</span></label>
        <select class="form-select @error('municipality_id') is-invalid @enderror"
                name="municipality_id" id="municipality-field"
                data-cascade-url="{{ url('institutions/localities') }}"
                data-cascade-target="#locality-field"
                data-restore="{{ old('municipality_id', $institution?->locality?->municipality_id) }}"
                required disabled>
            <option value="">— Seleccione provincia primero —</option>
        </select>
        @error('municipality_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Localidad <span class="text-danger">*</span></label>
        <select class="form-select @error('locality_id') is-invalid @enderror"
                name="locality_id" id="locality-field"
                data-cascade-url="{{ url('institutions/districts') }}"
                data-cascade-target="#district-field"
                data-restore="{{ old('locality_id', $institution?->locality_id) }}"
                required disabled>
            <option value="">— Seleccione municipio primero —</option>
        </select>
        @error('locality_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Distrito <small class="text-muted">(opcional)</small></label>
        <select class="form-select @error('district_id') is-invalid @enderror"
                name="district_id" id="district-field"
                data-cascade-url="{{ url('institutions/zones') }}"
                data-cascade-target="#zone-field"
                data-restore="{{ old('district_id', $institution?->district_id) }}"
                disabled>
            <option value="">— Seleccione localidad primero —</option>
        </select>
        @error('district_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">Zona <small class="text-muted">(opcional)</small></label>
        <select class="form-select @error('zone_id') is-invalid @enderror"
                name="zone_id" id="zone-field"
                data-restore="{{ old('zone_id', $institution?->zone_id) }}"
                disabled>
            <option value="">— Seleccione distrito primero —</option>
        </select>
        @error('zone_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<hr class="my-2">
<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-phone-line me-1"></i>Dirección y Contacto <small class="text-muted fw-normal">(opcional)</small>
</h6>
<div class="row g-2 mb-3">
    <div class="col-md-8">
        <label class="form-label">Dirección</label>
        <input type="text" id="address-field" name="address"
               class="form-control @error('address') is-invalid @enderror"
               placeholder="Dirección exacta del recinto"
               value="{{ old('address', $institution->address ?? '') }}">
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Referencia</label>
        <input type="text" id="reference-field" name="reference"
               class="form-control @error('reference') is-invalid @enderror"
               placeholder="Ej: Frente a la plaza"
               value="{{ old('reference', $institution->reference ?? '') }}">
        @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Latitud</label>
        <input type="text" id="latitude-field" name="latitude"
               class="form-control @error('latitude') is-invalid @enderror"
               placeholder="-17.123456"
               value="{{ old('latitude', $institution->latitude ?? '') }}">
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Longitud</label>
        <input type="text" id="longitude-field" name="longitude"
               class="form-control @error('longitude') is-invalid @enderror"
               placeholder="-65.123456"
               value="{{ old('longitude', $institution->longitude ?? '') }}">
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Teléfono</label>
        <input type="text" id="phone-field" name="phone"
               class="form-control @error('phone') is-invalid @enderror"
               placeholder="4-1234567"
               value="{{ old('phone', $institution->phone ?? '') }}">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Email</label>
        <input type="email" id="email-field" name="email"
               class="form-control @error('email') is-invalid @enderror"
               placeholder="recinto@ejemplo.com"
               value="{{ old('email', $institution->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Responsable</label>
        <input type="text" id="responsible-field" name="responsible_name"
               class="form-control @error('responsible_name') is-invalid @enderror"
               placeholder="Nombre del encargado"
               value="{{ old('responsible_name', $institution->responsible_name ?? '') }}">
        @error('responsible_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<hr class="my-2">

<h6 class="fw-semibold text-muted text-uppercase small mb-2">
    <i class="ri-bar-chart-2-line me-1"></i>Datos Electorales
    <small class="text-muted fw-normal">(se actualizan automáticamente con las mesas)</small>
</h6>
<div class="row g-2 mb-3">
    <div class="col-md-3">
        <label class="form-label">Ciudadanos Habilitados</label>
        <input type="number" id="registered_citizens-field" name="registered_citizens"
               class="form-control @error('registered_citizens') is-invalid @enderror"
               value="{{ old('registered_citizens', $institution->registered_citizens ?? 0) }}" min="0">
        @error('registered_citizens')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Total del padrón</small>
    </div>
    <div class="col-md-3">
        <label class="form-label">Actas Computadas</label>
        <input type="number" id="total_computed_records-field" name="total_computed_records"
               class="form-control @error('total_computed_records') is-invalid @enderror"
               value="{{ old('total_computed_records', $institution->total_computed_records ?? 0) }}" min="0">
        @error('total_computed_records')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Actas Anuladas</label>
        <input type="number" id="total_annulled_records-field" name="total_annulled_records"
               class="form-control @error('total_annulled_records') is-invalid @enderror"
               value="{{ old('total_annulled_records', $institution->total_annulled_records ?? 0) }}" min="0">
        @error('total_annulled_records')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label class="form-label">Actas Habilitadas</label>
        <input type="number" id="total_enabled_records-field" name="total_enabled_records"
               class="form-control @error('total_enabled_records') is-invalid @enderror"
               value="{{ old('total_enabled_records', $institution->total_enabled_records ?? 0) }}" min="0">
        @error('total_enabled_records')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="col-12 mb-2">
    <label class="form-label">Observaciones</label>
    <textarea id="observations-field" name="observations"
              class="form-control @error('observations') is-invalid @enderror"
              placeholder="Observaciones adicionales sobre el recinto"
              rows="2">{{ old('observations', $institution->observations ?? '') }}</textarea>
    @error('observations')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
