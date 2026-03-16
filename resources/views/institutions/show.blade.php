@extends('layouts.master')
@section('title') {{ $institution->name }} @endsection

@section('css')
<link href="{{ URL::asset('build/libs/leaflet/leaflet.css') }}" rel="stylesheet" />
<style>
.avatar-ring{width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,.15)}
.stat-box{background:#f8f9fa;border-radius:.5rem;padding:.6rem 1rem;text-align:center;min-width:80px}
.stat-box .n{font-size:1.35rem;font-weight:700;line-height:1}
.stat-box .l{font-size:.7rem;color:#74788d;margin-top:.15rem}
.info-row{display:flex;gap:.5rem;padding:.45rem 0;border-bottom:1px solid #f3f6f9}
.info-row:last-child{border-bottom:none}
.info-k{min-width:42%;font-weight:500;color:#6c757d;font-size:.82rem}
.info-v{font-size:.82rem;color:#212529}
#map{height:300px;border-radius:.5rem;border:1px solid #e9e9ef}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('institutions.index') }}">Recintos</a> @endslot
    @slot('li_2') {{ $institution->name }} @endslot
    @slot('title') Detalles del Recinto @endslot
@endcomponent

@include('components.alerts')

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="avatar-ring bg-light d-flex align-items-center justify-content-center">
                    <i class="ri-building-line" style="font-size:2.5rem;color:#0ab39c"></i>
                </div>
            </div>
            <div class="col">
                <h4 class="mb-0">{{ $institution->name }}</h4>
                <p class="text-muted small mb-1">
                    <span class="badge bg-info-subtle text-info font-monospace me-2">{{ $institution->code }}</span>
                    @if($institution->short_name) <span class="me-2">{{ $institution->short_name }}</span> @endif
                    @if($institution->address) &bull; {{ $institution->address }} @endif
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    @php
                        $stColors = ['activo'=>'success','inactivo'=>'danger','en_mantenimiento'=>'warning'];
                        $stLabels = ['activo'=>'Activo','inactivo'=>'Inactivo','en_mantenimiento'=>'Mantenimiento'];
                    @endphp
                    <span class="badge bg-{{ $stColors[$institution->status] ?? 'secondary' }}">
                        {{ $stLabels[$institution->status] ?? $institution->status }}
                    </span>
                    @if($institution->is_operative)
                        <span class="badge bg-success-subtle text-success">
                            <i class="ri-flashlight-line me-1"></i>Habilitado para elecciones
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary">
                            <i class="ri-flashlight-off-line me-1"></i>No habilitado
                        </span>
                    @endif
                </div>
            </div>
            <div class="col-auto d-none d-lg-flex gap-2">
                <div class="stat-box"><div class="n">{{ $institution->votingTables->count() }}</div><div class="l">Mesas</div></div>
                <div class="stat-box"><div class="n">{{ number_format($institution->registered_citizens ?? 0) }}</div><div class="l">Ciudadanos</div></div>
            </div>
            <div class="col-auto d-flex gap-2 flex-wrap">
                @can('edit_recintos')
                <a href="{{ route('institutions.edit', $institution) }}" class="btn btn-soft-warning btn" title="Editar">
                    <i class="ri-pencil-line me-1"></i>
                </a>
                @endcan
                @can('delete_recintos')
                <button class="btn btn-soft-danger btn" title="Eliminar"
                        onclick="confirmDelete({{ $institution->id }},'{{ addslashes($institution->name) }}')">
                    <i class="ri-delete-bin-line me-1"></i>
                </button>
                @endcan
                <a href="{{ route('institutions.index') }}" class="btn btn-soft-secondary btn" title="Volver">
                    <i class="ri-arrow-left-line me-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-information-line me-1"></i>Información Básica</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Código</div>
                    <div class="info-v"><span class="badge bg-info-subtle text-info font-monospace">{{ $institution->code }}</span></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Nombre completo</div>
                    <div class="info-v">{{ $institution->name }}</div>
                </div>
                @if($institution->short_name)
                <div class="info-row">
                    <div class="info-k">Nombre corto</div>
                    <div class="info-v">{{ $institution->short_name }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-k">Dirección</div>
                    <div class="info-v">{{ $institution->address ?? '—' }}</div>
                </div>
                @if($institution->reference)
                <div class="info-row">
                    <div class="info-k">Referencia</div>
                    <div class="info-v">{{ $institution->reference }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-k">Estado</div>
                    <div class="info-v">
                        <span class="badge bg-{{ $stColors[$institution->status] ?? 'secondary' }}">
                            {{ $stLabels[$institution->status] ?? $institution->status }}
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-k">Habilitado</div>
                    <div class="info-v">
                        @if($institution->is_operative)
                            <span class="badge bg-success">Sí</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-phone-line me-1"></i>Contacto</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Teléfono</div>
                    <div class="info-v">{{ $institution->phone ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Email</div>
                    <div class="info-v">{{ $institution->email ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Responsable</div>
                    <div class="info-v">{{ $institution->responsible_name ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-map-pin-line me-1"></i>Ubicación Geográfica</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Departamento</div>
                    <div class="info-v">{{ $institution->locality->municipality->province->department->name ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Provincia</div>
                    <div class="info-v">{{ $institution->locality->municipality->province->name ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Municipio</div>
                    <div class="info-v">{{ $institution->locality->municipality->name ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Localidad</div>
                    <div class="info-v">{{ $institution->locality->name ?? '—' }}</div>
                </div>
                @if($institution->district)
                <div class="info-row">
                    <div class="info-k">Distrito</div>
                    <div class="info-v">{{ $institution->district->name }}</div>
                </div>
                @endif
                @if($institution->zone)
                <div class="info-row">
                    <div class="info-k">Zona</div>
                    <div class="info-v">{{ $institution->zone->name }}</div>
                </div>
                @endif
                @if($institution->latitude && $institution->longitude)
                <div class="info-row">
                    <div class="info-k">Coordenadas</div>
                    <div class="info-v">
                        <span class="font-monospace">{{ $institution->latitude }}, {{ $institution->longitude }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($institution->latitude && $institution->longitude)
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-map-2-line me-1"></i>Mapa</h5>
            </div>
            <div class="card-body p-2">
                <div id="map"></div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-table-line me-1"></i>Mesas de Votación ({{ $institution->votingTables->count() }})</h5>
        @can('create_mesas')
        <a href="{{ route('voting-tables.create', ['institution_id' => $institution->id]) }}" class="btn btn-sm btn-success">
            <i class="ri-add-line me-1"></i>Agregar Mesa
        </a>
        @endcan
    </div>
    <div class="card-body p-0">
        @if($institution->votingTables->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Mesa</th>
                            <th>Código</th>
                            <th>Ciudadanos</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($institution->votingTables as $table)
                        <tr>
                            <td><span class="fw-semibold">{{ $table->number }}</span></td>
                            <td><span class="badge bg-info-subtle text-info font-monospace">{{ $table->code }}</span></td>
                            <td>{{ number_format($table->registered_citizens ?? 0) }}</td>
                            <td>
                                @php
                                    $tColors = ['activo'=>'success','cerrado'=>'secondary','pendiente'=>'warning'];
                                    $tLabels = ['activo'=>'Activo','cerrado'=>'Cerrado','pendiente'=>'Pendiente'];
                                @endphp
                                <span class="badge bg-{{ $tColors[$table->status] ?? 'secondary' }}-subtle text-{{ $tColors[$table->status] ?? 'secondary' }}">
                                    {{ $tLabels[$table->status] ?? $table->status }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('voting-tables.show', $table) }}" class="btn btn-sm btn-soft-info" title="Ver">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="{{ route('voting-tables.edit', $table) }}" class="btn btn-sm btn-soft-warning" title="Editar">
                                    <i class="ri-pencil-line"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="ri-table-line d-block mb-2" style="font-size:2.5rem"></i>
                <p class="mb-1">No hay mesas de votación registradas en este recinto</p>
                @can('create_mesas')
                <a href="{{ route('voting-tables.create', ['institution_id' => $institution->id]) }}" class="btn btn-sm btn-primary mt-1">
                    <i class="ri-add-line me-1"></i>Agregar primera mesa
                </a>
                @endcan
            </div>
        @endif
    </div>
</div>

@if($institution->observations)
<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri-file-text-line me-1"></i>Observaciones</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $institution->observations }}</p>
    </div>
</div>
@endif

<form id="deleteForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
@endsection

@section('script')
@if($institution->latitude && $institution->longitude)
<script src="{{ URL::asset('build/libs/leaflet/leaflet.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var map = L.map('map').setView([{{ $institution->latitude }}, {{ $institution->longitude }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    L.marker([{{ $institution->latitude }}, {{ $institution->longitude }}])
        .addTo(map)
        .bindPopup('<b>{{ $institution->name }}</b><br>{{ $institution->address }}');
});
</script>
@endif
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const CSRF = '{{ csrf_token() }}';
function confirmDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar recinto?',
        html: `¿Desea eliminar el recinto <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#8590a5',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        const form = document.getElementById('deleteForm');
        form.action = `/institutions/${id}`;
        form.submit();
    });
}
</script>
@endsection
