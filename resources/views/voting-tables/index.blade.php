{{-- resources/views/voting-tables/index.blade.php --}}
@extends('layouts.master')
@section('title') Gestión de Mesas de Votación @endsection

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
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
    @slot('title') Gestión de Mesas de Votación @endslot
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
            $totalTables = $votingTables->total();
            $totalExpected = $votingTables->getCollection()->sum('expected_voters');
            $totalVoted = $votingTables->getCollection()->sum(function($vt) {
                return $vt->elections->first()?->total_voters ?? 0;
            });
            $votingTablesColl = $votingTables->getCollection();
            $configuradas = $votingTablesColl->filter(function($vt) {
                return $vt->elections->first()?->status === 'configurada';
            })->count();
            $votacion = $votingTablesColl->filter(function($vt) {
                return $vt->elections->first()?->status === 'votacion';
            })->count();
            $escrutadas = $votingTablesColl->filter(function($vt) {
                return in_array($vt->elections->first()?->status, ['escrutada', 'transmitida']);
            })->count();
            $observadas = $votingTablesColl->filter(function($vt) {
                return $vt->elections->first()?->status === 'observada';
            })->count();
        @endphp
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="ri-table-line"></i></div>
                <div><div class="num">{{ number_format($totalTables) }}</div><div class="lbl">Total mesas</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="ri-group-line"></i></div>
                <div><div class="num">{{ number_format($totalExpected) }}</div><div class="lbl">Electores hab.</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="ri-check-line"></i></div>
                <div><div class="num">{{ number_format($totalVoted) }}</div><div class="lbl">Votaron</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-secondary bg-opacity-10 text-secondary"><i class="ri-settings-4-line"></i></div>
                <div><div class="num">{{ $configuradas }}</div><div class="lbl">Configuradas</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="ri-vote-line"></i></div>
                <div><div class="num">{{ $votacion }}</div><div class="lbl">En votación</div></div>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="stat-card">
                <div class="icon bg-danger bg-opacity-10 text-danger"><i class="ri-error-warning-line"></i></div>
                <div><div class="num">{{ $observadas }}</div><div class="lbl">Observadas</div></div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body py-2 px-2">
        <form method="GET" action="{{ route('voting-tables.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Buscar</label>
                    <div class="input-group input-group">
                        <span class="input-group-text bg-white"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="Código, N° mesa, recinto…" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Recinto</label>
                    <select name="institution_id" class="form-select form-select">
                        <option value="">Todos</option>
                        @foreach($institutions as $inst)
                            <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                                {{ $inst->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="status" class="form-select form-select">
                        <option value="">Todos</option>
                        @foreach($statusOptions ?? [] as $val => $lbl)
                            <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipo</label>
                    <select name="type" class="form-select form-select">
                        <option value="">Todos</option>
                        <option value="mixta" {{ request('type') == 'mixta' ? 'selected' : '' }}>Mixta</option>
                        <option value="masculina" {{ request('type') == 'masculina' ? 'selected' : '' }}>Masculina</option>
                        <option value="femenina" {{ request('type') == 'femenina' ? 'selected' : '' }}>Femenina</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    @if(request()->hasAny(['search','institution_id','status','type']))
                    <a href="{{ route('voting-tables.index') }}" class="btn btn-outline-secondary btn" title="Limpiar">
                        <i class="ri-close-line"></i>
                    </a>
                    @endif
                </div>
            </div>
            <input type="hidden" name="sort" value="{{ request('sort', 'number') }}">
            <input type="hidden" name="direction" value="{{ request('direction', 'asc') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page', 20) }}">

            @if(request()->hasAny(['search','institution_id','status','type']))
            <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted" style="font-size:.78rem">Filtros activos:</span>
                @if(request('search'))
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-search-line"></i> "{{ Str::limit(request('search'),20) }}"
                    <a href="{{ route('voting-tables.index', request()->except(['search','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('institution_id') && ($selInst = $institutions->find(request('institution_id'))))
                <span class="badge bg-info d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-building-line"></i> {{ $selInst->name }}
                    <a href="{{ route('voting-tables.index', request()->except(['institution_id','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('status'))
                <span class="badge bg-secondary d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    {{ $statusOptions[request('status')] ?? request('status') }}
                    <a href="{{ route('voting-tables.index', request()->except(['status','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
                @if(request('type'))
                <span class="badge bg-success d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    {{ ucfirst(request('type')) }}
                    <a href="{{ route('voting-tables.index', request()->except(['type','page'])) }}" class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                @endif
            </div>
            @endif
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-2 px-2">
        <h5 class="card-title mb-0">
            Mesas de Votación <span class="badge bg-secondary ms-1">{{ $votingTables->total() }}</span>
        </h5>
        <div class="d-flex gap-2">
            @can('create_mesas')
            <a href="{{ route('voting-tables.create') }}" class="btn btn-success btn">
                <i class="ri-add-line me-1"></i>Nueva Mesa
            </a>
            @endcan
            <div class="btn-group">
                <button type="button" class="btn btn-info btn dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="ri-download-line"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('voting-tables.export-all') . '?' . http_build_query(request()->except('selected_ids','page')) }}">
                        <i class="ri-file-excel-line me-2 text-success"></i>Exportar todo ({{ $votingTables->total() }})
                    </a></li>
                    <li><button class="dropdown-item" id="export-selected-btn" onclick="exportSelected()" disabled>
                        <i class="ri-file-excel-line me-2 text-success"></i>Exportar seleccionados
                        <span id="selected-count-badge" class="badge bg-primary ms-1" style="display:none">0</span>
                    </button></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('voting-tables.template') }}">
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
        @include('voting-tables.partials.table')
    </div>

    @if($votingTables->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <div class="d-flex align-items-center gap-2">
            <small class="text-muted">Mostrando {{ $votingTables->firstItem() }}–{{ $votingTables->lastItem() }} de {{ $votingTables->total() }} mesas</small>
            <select class="form-select form-select-sm" style="width:auto" onchange="window.location.href=this.value">
                @foreach([20,50,100,200] as $pp)
                <option value="{{ route('voting-tables.index', ['per_page'=>$pp] + request()->except('per_page','page')) }}"
                    {{ request('per_page',20)==$pp ? 'selected' : '' }}>{{ $pp }} / página</option>
                @endforeach
            </select>
        </div>
        {{ $votingTables->onEachSide(1)->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

@include('voting-tables.partials.modal-delete')
@include('voting-tables.partials.modal-import')
@if(session('import_errors'))
    @include('voting-tables.partials.modal-import-errors')
@endif

<form id="export-selected-form" action="{{ route('voting-tables.export-selected') }}" method="POST" style="display:none">@csrf</form>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
@include('voting-tables.scripts.voting-table-js')
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
    localStorage.setItem('vtStatsVisible', String(isHidden));
}

document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('vtStatsVisible') === 'true') {
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
