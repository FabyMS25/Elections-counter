{{-- resources/views/institutions/index.blade.php --}}
@extends('layouts.master')
@section('title') Gestión de Recintos Electorales @endsection

@section('css')
{{-- <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/> --}}
{{-- <link href="{{ URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"/> --}}
<style>
.stat-card{background:#fff;border:1px solid #e9e9ef;border-radius:.5rem;padding:.75rem 1.1rem;display:flex;align-items:center;gap:.9rem}
.stat-card .icon{width:42px;height:42px;border-radius:.4rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
.stat-card .num{font-size:1.4rem;font-weight:700;line-height:1}
.stat-card .lbl{font-size:.72rem;color:#74788d}
.sort-link{color:inherit;text-decoration:none;white-space:nowrap}
.sort-link:hover{color:#0ab39c}
.sort-link i{font-size:.7rem;vertical-align:middle}
.stats-toggle{cursor:pointer;user-select:none}
.stats-toggle i{transition:transform .3s}
.stats-toggle.collapsed i{transform:rotate(-90deg)}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Registros Electorales @endslot
    @slot('title') Gestión de Recintos Electorales @endslot
@endcomponent

@include('components.alerts')

<div class="d-flex justify-content-end mb-2">
    <button class="btn btn-sm btn-light stats-toggle" id="statsToggle" onclick="toggleStats()">
        <i class="ri-arrow-down-s-line me-1"></i><span id="statsToggleLabel">Mostrar estadísticas</span>
    </button>
</div>

<div id="statsContainer" class="d-none">
    <div class="row g-3 mb-2">
        @php
            $totalInst = $institutions->total();
            $totalCitizens = $institutions->sum('registered_citizens');
            $totalActive = $institutions->where('status','activo')->count();
            $totalOperative = $institutions->where('is_operative',true)->count();
            $totalInactive = $institutions->where('status','inactivo')->count();
            $totalMaint = $institutions->where('status','en_mantenimiento')->count();
        @endphp
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="ri-building-line"></i></div>
                <div><div class="num">{{ number_format($totalInst) }}</div><div class="lbl">Total recintos</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="ri-group-line"></i></div>
                <div><div class="num">{{ number_format($totalCitizens) }}</div><div class="lbl">Ciudadanos hab.</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="ri-checkbox-circle-line"></i></div>
                <div><div class="num">{{ $totalActive }}</div><div class="lbl">Activos</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="ri-flashlight-line"></i></div>
                <div><div class="num">{{ $totalOperative }}</div><div class="lbl">Operativos</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-danger bg-opacity-10 text-danger"><i class="ri-close-circle-line"></i></div>
                <div><div class="num">{{ $totalInactive }}</div><div class="lbl">Inactivos</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-secondary bg-opacity-10 text-secondary"><i class="ri-tools-line"></i></div>
                <div><div class="num">{{ $totalMaint }}</div><div class="lbl">Mantenimiento</div></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body py-2 px-2">
        <form method="GET" action="{{ route('institutions.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Buscar</label>
                    <div class="input-group input-group">
                        <span class="input-group-text bg-white"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="Nombre, código, dirección…" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Departamento</label>
                    <select name="department_id" class="form-select form-select">
                        <option value="">Todos</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="status" class="form-select form-select">
                        <option value="">Todos</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Operativo</label>
                    <select name="operative" class="form-select form-select">
                        <option value="">Todos</option>
                        <option value="true" {{ request('operative') == 'true' ? 'selected' : '' }}>Sí</option>
                        <option value="false" {{ request('operative') == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    @if(request()->hasAny(['search','department_id','status','operative']))
                    <a href="{{ route('institutions.index') }}" class="btn btn-outline-secondary btn" title="Limpiar">
                        <i class="ri-close-line"></i>
                    </a>
                    @endif
                </div>
            </div>
            <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">

            @if(request()->hasAny(['search','department_id','status','operative']))
            <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted" style="font-size:.78rem">Filtros activos:</span>
                @if(request('search'))
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-search-line"></i> "{{ Str::limit(request('search'),20) }}"
                    <a href="{{ route('institutions.index', request()->except(['search','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('department_id') && ($selDept = $departments->find(request('department_id'))))
                <span class="badge bg-info d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-map-pin-line"></i> {{ $selDept->name }}
                    <a href="{{ route('institutions.index', request()->except(['department_id','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('status') && isset($statusOptions[request('status')]))
                <span class="badge bg-success d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    {{ $statusOptions[request('status')] }}
                    <a href="{{ route('institutions.index', request()->except(['status','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('operative') !== null && request('operative') !== '')
                <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    Operativo: {{ request('operative')=='true'?'Sí':'No' }}
                    <a href="{{ route('institutions.index', request()->except(['operative','page'])) }}" class="text-dark ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
        <h5 class="card-title mb-0">
            Recintos Electorales <span class="badge bg-secondary ms-1">{{ $institutions->total() }}</span>
        </h5>
        <div class="d-flex gap-2">
            @can('create_recintos')
            <a href="{{ route('institutions.create') }}" class="btn btn-success btn">
                <i class="ri-add-line me-1"></i>Nuevo Recinto
            </a>
            @endcan
            <div class="btn-group">
                <button type="button" class="btn btn-info btn dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="ri-download-line"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('institutions.export-all') . '?' . http_build_query(request()->except('selected_ids','page')) }}">
                        <i class="ri-file-excel-line me-2 text-success"></i>Exportar todo ({{ $institutions->total() }})
                    </a></li>
                    <li><button class="dropdown-item" id="export-selected-btn" onclick="exportSelected()" disabled>
                        <i class="ri-file-excel-line me-2 text-success"></i>Exportar seleccionados
                        <span id="selected-count-badge" class="badge bg-primary ms-1" style="display:none">0</span>
                    </button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('institutions.template') }}">
                        <i class="ri-file-download-line me-2 text-secondary"></i>Plantilla CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="ri-file-upload-line me-2 text-secondary"></i>Importar
                    </a></li>
                </ul>
            </div>
            <button class="btn btn-soft-danger btn-sm d-none" id="delete-multiple-btn" onclick="deleteMultiple()">
                <i class="ri-delete-bin-2-line me-1"></i>Eliminar sel.
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        @include('institutions.partials.table')
    </div>

    @if($institutions->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">Mostrando {{ $institutions->firstItem() }}–{{ $institutions->lastItem() }} de {{ $institutions->total() }} recintos</small>
            <select class="form-select form-select-sm" style="width:auto" onchange="window.location.href=this.value">
                @foreach([20,50,100,200] as $pp)
                <option value="{{ route('institutions.index', ['per_page'=>$pp] + request()->except('per_page','page')) }}"
                    {{ request('per_page',20)==$pp ? 'selected' : '' }}>{{ $pp }} / página</option>
                @endforeach
            </select>
        </div>
        {{ $institutions->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@include('institutions.partials.modal-delete')
@include('institutions.partials.modal-import')
@if(session('import_errors'))
    @include('institutions.partials.modal-import-errors')
@endif

<form id="export-selected-form" action="{{ route('institutions.export-selected') }}" method="POST" style="display:none">@csrf</form>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
@include('institutions.scripts.institution-js')
<script>
function toggleStats() {
    const container = document.getElementById('statsContainer');
    const btn       = document.getElementById('statsToggle');
    const label     = document.getElementById('statsToggleLabel');
    const isHidden  = container.classList.contains('d-none');
    container.classList.toggle('d-none', !isHidden);
    btn.classList.toggle('collapsed', !isHidden);
    btn.querySelector('i').className = isHidden ? 'ri-arrow-down-s-line me-1' : 'ri-arrow-right-s-line me-1';
    label.textContent = isHidden ? 'Ocultar estadísticas' : 'Mostrar estadísticas';
    localStorage.setItem('institutionStatsVisible', String(isHidden));
}

document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('institutionStatsVisible') === 'true') {
        const container = document.getElementById('statsContainer');
        const btn       = document.getElementById('statsToggle');
        const label     = document.getElementById('statsToggleLabel');
        if (container) container.classList.remove('d-none');
        if (btn) { btn.classList.remove('collapsed'); btn.querySelector('i').className = 'ri-arrow-down-s-line me-1'; }
        if (label) label.textContent = 'Ocultar estadísticas';
    }
    setTimeout(() => document.querySelectorAll('.alert-dismissible').forEach(a => bootstrap.Alert.getOrCreateInstance(a)?.close()), 5000);
});
</script>
@endsection
