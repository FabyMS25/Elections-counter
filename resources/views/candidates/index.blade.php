@extends('layouts.master')
@section('title') Gestión de Candidatos @endsection

@section('css')
<style>
.stat-card{background:#fff;border:1px solid #e9e9ef;border-radius:.5rem;padding:.75rem 1.1rem;display:flex;align-items:center;gap:.9rem}
.stat-card .icon{width:42px;height:42px;border-radius:.4rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
.stat-card .num{font-size:1.4rem;font-weight:700;line-height:1}
.stat-card .lbl{font-size:.72rem;color:#74788d}
.color-dot{width:30px;height:30px;border-radius:20%;display:inline-block;vertical-align:middle}
.sort-link{color:inherit;text-decoration:none;white-space:nowrap}
.sort-link:hover{color:#0ab39c}
.sort-link i{font-size:.7rem;vertical-align:middle}
.stats-toggle{cursor:pointer;user-select:none}
.stats-toggle i{transition:transform .3s}
.stats-toggle.collapsed i{transform:rotate(-90deg)}
.avatar-xs{width:36px;height:36px;border-radius:50%;object-fit:cover}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Registros Electorales @endslot
    @slot('title') Gestión de Candidatos @endslot
@endcomponent

@include('components.alerts')

<div class="d-flex justify-content-end mb-2">
    <button class="btn btn-sm btn-light stats-toggle" id="statsToggle" onclick="toggleStats()">
        <i class="ri-arrow-down-s-line me-1"></i><span id="statsToggleLabel">Mostrar estadísticas</span>
    </button>
</div>

<div id="statsContainer" class="d-none">
    <div class="row g-3 mb-2">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="ri-user-star-line"></i></div>
                <div><div class="num">{{ $candidates->total() }}</div><div class="lbl">Total candidatos</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="ri-stack-line"></i></div>
                <div><div class="num">{{ $stats['byCategory']->count() }}</div><div class="lbl">Categorías activas</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="ri-map-pin-line"></i></div>
                <div><div class="num">{{ $stats['byDepartment']->count() }}</div><div class="lbl">Departamentos</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="ri-government-line"></i></div>
                <div><div class="num">{{ $stats['byElectionType']->count() }}</div><div class="lbl">Tipos de elección</div></div>
            </div>
        </div>
    </div>
    <div class="mb-2">
        @include('candidates.partials.stats-cards')
    </div>
</div>

<div class="card mb-2">
    <div class="card-body py-2 px-2">
        <form method="GET" action="{{ route('candidates.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Buscar</label>
                    <div class="input-group input-group">
                        <span class="input-group-text bg-white"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="Nombre, partido, lista…" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipo Elección</label>
                    <select name="election_type_id" class="form-select form-select">
                        <option value="">Todos</option>
                        @foreach($electionTypes as $et)
                            <option value="{{ $et->id }}" {{ request('election_type_id') == $et->id ? 'selected' : '' }}>
                                {{ $et->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Categoría</label>
                    <select name="election_type_category_id" class="form-select form-select">
                        <option value="">Todas</option>
                        @foreach($etcs as $etc)
                            <option value="{{ $etc->id }}" {{ request('election_type_category_id') == $etc->id ? 'selected' : '' }}>
                                {{ $etc->electionCategory->code }} — {{ $etc->electionCategory->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Departamento</label>
                    <select name="department_id" class="form-select form-select" id="filter-department">
                        <option value="">Todos</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    @if(request()->hasAny(['search','election_type_id','election_type_category_id','department_id']))
                    <a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary btn" title="Limpiar">
                        <i class="ri-close-line"></i>
                    </a>
                    @endif
                </div>
            </div>
            <input type="hidden" name="sort" value="{{ request('sort', 'name') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">

            @if(request()->hasAny(['search','election_type_id','election_type_category_id','department_id']))
            <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted" style="font-size:.78rem">Filtros activos:</span>
                @if(request('search'))
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-search-line"></i> "{{ Str::limit(request('search'),20) }}"
                    <a href="{{ route('candidates.index', request()->except(['search','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('election_type_id') && ($selEt = $electionTypes->find(request('election_type_id'))))
                <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-government-line"></i> {{ $selEt->name }}
                    <a href="{{ route('candidates.index', request()->except(['election_type_id','page'])) }}" class="text-dark ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('election_type_category_id') && ($selCat = $etcs->find(request('election_type_category_id'))))
                <span class="badge bg-info d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-stack-line"></i> {{ $selCat->electionCategory->code }}
                    <a href="{{ route('candidates.index', request()->except(['election_type_category_id','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('department_id') && ($selDept = $departments->find(request('department_id'))))
                <span class="badge bg-success d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-map-pin-line"></i> {{ $selDept->name }}
                    <a href="{{ route('candidates.index', request()->except(['department_id','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
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
            Candidatos <span class="badge bg-secondary ms-1">{{ $candidates->total() }}</span>
        </h5>
        <div class="d-flex gap-2">
            @can('create_candidatos')
            <button type="button" class="btn btn-success btn" data-bs-toggle="modal" data-bs-target="#candidateModal">
                <i class="ri-add-line me-1"></i>Nuevo Candidato
            </button>
            @endcan
            <div class="btn-group">
                <button type="button" class="btn btn-info btn dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="ri-download-line"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('candidates.export-all') . '?' . http_build_query(request()->except('selected_ids','page')) }}">
                        <i class="ri-file-excel-line me-2 text-success"></i>Exportar todo ({{ $candidates->total() }})
                    </a></li>
                    <li><button class="dropdown-item" id="export-selected-btn" onclick="exportSelected()" disabled>
                        <i class="ri-file-excel-line me-2 text-success"></i>Exportar seleccionados
                        <span id="selected-count-badge" class="badge bg-primary ms-1" style="display:none">0</span>
                    </button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('candidates.template') }}">
                        <i class="ri-file-download-line me-2 text-secondary"></i>Plantilla CSV
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="ri-file-upload-line me-2 text-secondary"></i>Importar CSV
                    </a></li>
                </ul>
            </div>
            <button class="btn btn-soft-danger btn-sm d-none" id="delete-multiple-btn" onclick="deleteMultiple()">
                <i class="ri-delete-bin-2-line me-1"></i>Eliminar sel.
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        @include('candidates.partials.table')
    </div>

    @if($candidates->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">Mostrando {{ $candidates->firstItem() }}–{{ $candidates->lastItem() }} de {{ $candidates->total() }} candidatos</small>
            <select class="form-select form-select-sm" style="width:auto"
                    onchange="window.location.href=this.value">
                @foreach([20,50,100,200] as $pp)
                <option value="{{ route('candidates.index', ['per_page'=>$pp] + request()->except('per_page','page')) }}"
                    {{ request('per_page',20)==$pp ? 'selected' : '' }}>{{ $pp }} / página</option>
                @endforeach
            </select>
        </div>
        {{ $candidates->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Modals --}}
@include('candidates.partials.modal-form')
@include('candidates.partials.modal-view')
@include('candidates.partials.modal-delete')
@include('candidates.partials.modal-import')
@if(session('import_errors'))
    @include('candidates.partials.modal-import-errors')
@endif

<form id="export-selected-form" action="{{ route('candidates.export-selected') }}" method="POST" style="display:none">@csrf</form>
@endsection

@section('script')
@include('candidates.scripts.candidates-js')
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
    localStorage.setItem('candidateStatsVisible', String(isHidden));
}
document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('candidateStatsVisible') === 'true') {
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
