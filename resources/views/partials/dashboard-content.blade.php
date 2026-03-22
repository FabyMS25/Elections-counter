{{-- resources/views/partials/dashboard-content.blade.php --}}
@include('partials.dashboard-filters')
<div id="loading-indicator" style="display: none; position: fixed; top: 20px; right: 20px; z-index: 9999; background: #fff; padding: 10px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.2);">
    <div class="d-flex align-items-center">
        <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <span>Actualizando datos...</span>
    </div>
</div>

<div class="row mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-uppercase fw-semibold text-muted mb-1" style="font-size:.68rem;letter-spacing:.05em;">
                            Mesas Escrutadas
                        </p>
                        <h3 class="mb-0 fw-bold">
                            <span id="kpi-reported">{{ $reportedTables }}</span>
                            <small class="text-muted fw-normal fs-6">/ <span id="kpi-total">{{ $totalTables }}</span></small>
                        </h3>
                    </div>
                    <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-2"
                          style="width:48px;height:48px;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="ri-table-line"></i>
                    </span>
                </div>
                <div class="mt-2">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Avance</small>
                        <small class="fw-bold text-primary" id="kpi-pct">{{ $progressPercentage }}%</small>
                    </div>
                    <div class="progress" style="height:6px;border-radius:6px;">
                        <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated"
                             id="kpi-bar" style="width:{{ $progressPercentage }}%"></div>
                    </div>
                </div>
                <div class="mt-2">
                    <small class="text-muted">{{ $selectedElectionType?->name ?? 'N/A' }}</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="text-uppercase fw-semibold text-muted mb-1" style="font-size:.68rem;letter-spacing:.05em;">
                            Papeletas en Ánfora
                        </p>
                        <h3 class="mb-0 fw-bold">
                            <span id="kpi-votes">{{ number_format($totalVotes) }}</span>
                        </h3>
                    </div>
                    <span class="avatar-title bg-success-subtle text-success rounded-3 fs-2"
                          style="width:48px;height:48px;display:inline-flex;align-items:center;justify-content:center;">
                        <i class="ri-inbox-line"></i>
                    </span>
                </div>
                <div class="mt-2 d-flex gap-2">
                    <div>
                        <small class="text-muted d-block">En Blanco</small>
                        <span class="fw-bold text-secondary" id="kpi-blank">{{ number_format($totalBlankVotes) }}</span>
                        <small class="text-muted ms-1">
                            ({{ $totalVotes > 0 ? round(($totalBlankVotes / $totalVotes) * 100, 1) : 0 }}%)
                        </small>
                    </div>
                    <div>
                        <small class="text-muted d-block">Nulos</small>
                        <span class="fw-bold text-danger" id="kpi-null">{{ number_format($totalNullVotes) }}</span>
                        <small class="text-muted ms-1">
                            ({{ $totalVotes > 0 ? round(($totalNullVotes / $totalVotes) * 100, 1) : 0 }}%)
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase fw-semibold text-muted mb-2" style="font-size:.68rem;letter-spacing:.05em;">
                    Candidato Líder · {{ $activeCategoryCode }}
                </p>
                @if(count($candidateStats) > 0)
                    @php $leader = collect($candidateStats)->sortByDesc('votes')->first(); @endphp
                    <div class="d-flex align-items-center gap-2">
                        @if($leader['candidate']->photo)
                            <img src="{{ asset('storage/'.$leader['candidate']->photo) }}"
                                 class="rounded-circle shadow-sm"
                                 style="width:52px;height:52px;object-fit:cover;
                                        border:3px solid {{ $leader['candidate']->color ?? '#0ab39c' }};">
                        @else
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                 style="width:52px;height:52px;flex-shrink:0;font-size:1.3rem;
                                        background:{{ $leader['candidate']->color ?? '#0ab39c' }};">
                                {{ strtoupper(substr($leader['candidate']->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <h6 class="mb-0 fw-bold text-truncate">{{ $leader['candidate']->name }}</h6>
                            <small class="text-muted">{{ $leader['candidate']->party }}</small>
                            <div class="mt-1">
                                <span class="badge bg-success-subtle text-success border border-success-subtle">
                                    {{ number_format($leader['votes']) }} votos · {{ $leader['percentage'] }}%
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress" style="height:5px;border-radius:5px;">
                            <div class="progress-bar bg-success"
                                 style="width:{{ $leader['percentage'] }}%;background:{{ $leader['candidate']->color ?? '#0ab39c' }} !important;"></div>
                        </div>
                    </div>
                @else
                    <div class="text-center text-muted py-3">
                        <i class="ri-bar-chart-line fs-1 d-block mb-1"></i>
                        Sin votos aún
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
                <p class="text-uppercase fw-semibold text-muted mb-1" style="font-size:.68rem;letter-spacing:.05em;">
                    Promedio por Mesa
                </p>
                <h3 class="mb-0 fw-bold">
                    {{ $reportedTables > 0 ? number_format($totalVotes / $reportedTables, 1) : 0 }}
                </h3>
                <small class="text-muted">papeletas / mesa escrutada</small>

                <div class="mt-2 row g-0 text-center border-top pt-3">
                    <div class="col-6 border-end">
                        <div class="fw-bold text-warning" id="kpi-pending">
                            {{ $totalTables - $reportedTables }}
                        </div>
                        <small class="text-muted">Pendientes</small>
                    </div>
                    <div class="col-6">
                        @php
                            $validTotal = $categoryStats[$activeCategoryCode]['totalVotes'] ?? 0;
                            $ballotTotal = $categoryStats[$activeCategoryCode]['totalBallots'] ?? 0;
                            $validPct = $ballotTotal > 0 ? round(($validTotal / $ballotTotal) * 100, 1) : 0;
                        @endphp
                        <div class="fw-bold text-primary">{{ $validPct }}%</div>
                        <small class="text-muted">Votos válidos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="currentSeatMode" value="{{ $currentSeatMode ?? 'all' }}">
<div class="row">
    <div class="card">
        <div class="card-header border-0 align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">
                Resultados por Candidato
                @if(isset($activeCategoryCode))
                    <small class="text-muted fs-6 ms-2">
                        <i class="ri-bar-chart-line"></i> 
                        @php
                            $categoryName = '';
                            foreach($typeCategories as $tc) {
                                if($tc->electionCategory?->code == $activeCategoryCode) {
                                    $categoryName = $tc->electionCategory->name;
                                    break;
                                }
                            }
                        @endphp
                        {{ $categoryName ?: $activeCategoryCode }}
                    </small>
                @endif
            </h4>
            <div>
                <button type="button" class="btn btn-soft-secondary btn-sm" onclick="window.location.reload()">
                    Actualizar
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="candidates_chart"></div>
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom-0 pb-0 bg-transparent">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="card-title mb-0">
                        Resultados por Candidato
                        <small class="text-muted fs-6 ms-2">
                            <i class="ri-bar-chart-line"></i> 
                            @php
                                $categoryName = '';
                                foreach($typeCategories as $tc) {
                                    if($tc->electionCategory?->code == $activeCategoryCode) {
                                        $categoryName = $tc->electionCategory->name;
                                        break;
                                    }
                                }
                            @endphp
                            {{ $categoryName ?: $activeCategoryCode }}
                        </small>
                    </h5>
                    <ul class="nav nav-pills nav-sm gap-1" id="categoryTabs" role="tablist">
                        @foreach($typeCategories as $tc)
                            @php $code = $tc->electionCategory?->code ?? 'UNK'; @endphp
                            <li class="nav-item" role="presentation">
                                <button class="nav-link py-1 px-3 {{ $code === $activeCategoryCode ? 'active' : '' }}"
                                        id="tab-{{ $code }}"
                                        data-bs-toggle="pill"
                                        data-bs-target="#panel-{{ $code }}"
                                        data-category="{{ $code }}"
                                        type="button" role="tab">
                                    {{ $tc->electionCategory?->name ?? $code }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="tab-content">
                    @foreach($typeCategories as $tc)
                        @php
                            $code  = $tc->electionCategory?->code ?? 'UNK';
                            $stats = $categoryStats[$code] ?? null;
                            $sortedStats = $stats
                                ? collect($stats['stats'])->sortByDesc('votes')->values()
                                : collect();
                            $catTotal   = $stats['totalBallots'] ?? 0;
                            $catValid   = $stats['totalVotes']   ?? 0;
                            $catBlank   = $stats['blankVotes']   ?? 0;
                            $catNull    = $stats['nullVotes']    ?? 0;
                        @endphp
                        <div class="tab-pane fade {{ $code === $activeCategoryCode ? 'show active' : '' }}"
                             id="panel-{{ $code }}" role="tabpanel">
                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                    <i class="ri-inbox-line me-1"></i>
                                    Ánfora: <strong>{{ number_format($catTotal) }}</strong>
                                </span>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                    <i class="ri-check-line me-1"></i>
                                    Válidos: <strong>{{ number_format($catValid) }}</strong>
                                    @if($catTotal > 0) ({{ round(($catValid / $catTotal) * 100, 1) }}%) @endif
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary border px-3 py-2">
                                    <i class="ri-subtract-line me-1"></i>
                                    Blancos: <strong>{{ number_format($catBlank) }}</strong>
                                    @if($catTotal > 0) ({{ round(($catBlank / $catTotal) * 100, 1) }}%) @endif
                                </span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                    <i class="ri-close-line me-1"></i>
                                    Nulos: <strong>{{ number_format($catNull) }}</strong>
                                    @if($catTotal > 0) ({{ round(($catNull / $catTotal) * 100, 1) }}%) @endif
                                </span>
                            </div>
                            @forelse($sortedStats as $rank => $s)
                                @php
                                    $cand  = $s['candidate'];
                                    $pct   = $s['percentage'];
                                    $color = $cand->color ?? '#3b5de7';
                                    $isLeader = $rank === 0;
                                @endphp
                                <div class="mb-2 {{ $isLeader ? 'p-2 rounded bg-light border' : '' }}">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge rounded-pill fw-bold"
                                              style="background:{{ $color }};min-width:26px;">
                                            {{ $rank + 1 }}
                                        </span>
                                        @if($cand->photo)
                                            <img src="{{ asset('storage/'.$cand->photo) }}"
                                                 class="rounded-circle"
                                                 style="width:30px;height:30px;object-fit:cover;border:2px solid {{ $color }};">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width:30px;height:30px;flex-shrink:0;font-size:.8rem;background:{{ $color }};">
                                                {{ strtoupper(substr($cand->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        @if($cand->party_logo)
                                            <img src="{{ asset('storage/'.$cand->party_logo) }}"
                                                 style="height:22px;width:auto;object-fit:contain;" alt="{{ $cand->party }}">
                                        @endif
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="fw-bold text-truncate" style="font-size:.88rem;">
                                                {{ $cand->name }}
                                                @if($isLeader)
                                                    <i class="ri-trophy-line text-warning ms-1"></i>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $cand->party }}</small>
                                        </div>
                                        <div class="text-end" style="min-width:100px;">
                                            <div class="fw-bold" style="color:{{ $color }};">
                                                {{ number_format($s['votes']) }}
                                            </div>
                                            <small class="text-muted">{{ $pct }}%</small>
                                        </div>
                                    </div>
                                    <div class="progress ms-5" style="height:{{ $isLeader ? 10 : 6 }}px;border-radius:6px;">
                                        <div class="progress-bar"
                                             role="progressbar"
                                             style="width:{{ $pct }}%;background:{{ $color }};border-radius:6px;transition:width .6s ease;"
                                             aria-valuenow="{{ $pct }}"
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="ri-bar-chart-line fs-1 d-block mb-2"></i>
                                    Sin resultados para esta categoría
                                </div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.dashboard-seats')

@include('partials.institution-avance-table')

<div class="row mb-2">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Resultados por Localidad</h4>
                <div>
                    <button type="button" class="btn btn-soft-secondary btn-sm" onclick="filterLocality('all')">Todos</button>
                    @foreach($localityStats->take(5) as $locality)
                    <button type="button" class="btn btn-soft-secondary btn-sm"
                            onclick="filterLocality('{{ $locality->id }}')">
                        {{ $locality->name }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div class="card-header p-0 border-0 bg-light-subtle">
                <div class="row g-0 text-center">
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <h5 class="mb-1"><span class="counter-value total-votes-counter" data-target="{{ $totalVotes }}">{{ $totalVotes }}</span></h5>
                            <p class="text-muted mb-0">Total de Votos</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <h5 class="mb-1"><span class="counter-value total-tables-counter" data-target="{{ $totalTables }}">{{ $totalTables }}</span></h5>
                            <p class="text-muted mb-0">Mesas Totales</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0">
                            <h5 class="mb-1"><span class="counter-value reported-tables-counter" data-target="{{ $reportedTables }}">{{ $reportedTables }}</span></h5>
                            <p class="text-muted mb-0">Mesas Reportadas</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                            <h5 class="mb-1 text-success"><span class="counter-value progress-counter" data-target="{{ $progressPercentage }}">{{ $progressPercentage }}</span>%</h5>
                            <p class="text-muted mb-0">Avance</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 pb-2">
                <div id="projects-overview-chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Distribución por Partido</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-soft-primary btn-sm" id="exportPartyData">
                        Exportar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="party_distribution_chart" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card card-height-100">
            <div class="card-header align-items-center d-flex">
                <h4 class="card-title mb-0 flex-grow-1">Votos por Localidades</h4>
                <div class="flex-shrink-0">
                    <button type="button" class="btn btn-soft-primary btn-sm" id="exportMapData">
                        Exportar
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 269px; position: relative;">
                    <div id="votes-by-locations"
                        data-colors='["#e9e9ef", "#0ab39c", "#f06548"]'
                        style="height: 100%; width: 100%;"></div>
                </div>
                <div class="px-2 py-2 mt-1 locality-progress-container" style="max-height: 200px; overflow-y: auto;">
                    @foreach($localityStats as $locality)
                    @php
                        $progress = $locality->total_tables > 0
                            ? round(($locality->reported_tables / $locality->total_tables) * 100, 1)
                            : 0;
                    @endphp
                    <div class="locality-progress-item mb-2" data-locality-id="{{ $locality->id }}">
                        <div class="d-flex justify-content-between">
                            <p class="mb-1 small">{{ $locality->name }} ({{ $locality->municipality_name }})</p>
                            <span class="small fw-bold">{{ $progress }}%</span>
                        </div>
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estado de Mesas</h5>
            </div>
            <div class="card-body table-status">
                <div class="d-flex justify-content-around text-center">
                    <div class="total-tables">
                        <h4 class="text-primary total-tables-count">{{ $totalTables }}</h4>
                        <p class="text-muted mb-0">Total de Mesas</p>
                    </div>
                    <div class="reported-tables">
                        <h4 class="text-success reported-tables-count">{{ $reportedTables }}</h4>
                        <p class="text-muted mb-0">Mesas Reportadas</p>
                    </div>
                    <div class="pending-tables">
                        <h4 class="text-warning pending-tables-count">{{ $totalTables - $reportedTables }}</h4>
                        <p class="text-muted mb-0">Mesas Pendientes</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Progreso General</h5>
            </div>
            <div class="card-body progress-general">
                <div class="progress mb-2" style="height: 20px;">
                    <div class="progress-bar bg-success general-progress-bar" role="progressbar"
                         style="width: {{ $progressPercentage }}%"
                         aria-valuenow="{{ $progressPercentage }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        {{ $progressPercentage }}%
                    </div>
                </div>
                <p class="text-muted mb-0 text-center progress-text">
                    {{ $reportedTables }} de {{ $totalTables }} mesas reportadas
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-bottom-0 bg-transparent d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">
                    <i class="ri-list-check-2 me-1 text-primary"></i>Detalle por Localidad
                </h6>
                <button class="btn btn-sm btn-outline-primary" onclick="exportTableToCSV('ds-locality-table','resultados.csv')">
                    <i class="ri-download-line me-1"></i>CSV
                </button>
            </div>
            <div class="card-body p-0">
                @include('partials.dashboard-localities-table')
            </div>
        </div>
    </div>
</div>

<div class="auto-refresh-controls"
     style="position:fixed;bottom:20px;right:20px;z-index:1000;
            background:white;padding:10px;border-radius:8px;
            box-shadow:0 2px 10px rgba(0,0,0,.12);">
    <div class="btn-group btn-group-sm">
        <button class="btn btn-outline-primary"
                onclick="window.refreshDashboard && window.refreshDashboard()"
                title="Actualizar ahora">
            <i class="ri-refresh-line"></i>
        </button>
        <button class="btn btn-outline-success"
                onclick="window.startAutoRefresh && window.startAutoRefresh()"
                title="Iniciar auto-actualización">
            <i class="ri-play-line"></i>
        </button>
        <button class="btn btn-outline-secondary"
                onclick="window.stopAutoRefresh && window.stopAutoRefresh()"
                title="Pausar auto-actualización">
            <i class="ri-pause-line"></i>
        </button>
    </div>
    <div class="mt-1 text-center">
        <small class="text-muted">Auto: 2 min</small>
    </div>
    <div id="refresh-status" class="mt-1 text-center">
        <small class="text-success">● Activo</small>
    </div>
</div>

<div id="institutionTablesModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title">
                    <i class="ri-building-line me-1"></i>
                    <span id="modalInstitutionName">Mesas por Recinto</span>
                </h6>
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-light py-0 px-2" onclick="exportModalToImage()" title="Exportar Imagen">
                        <i class="ri-image-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-light py-0 px-2" onclick="printModalContent()" title="Imprimir">
                        <i class="ri-printer-line"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white" onclick="closeInstitutionModal()"></button>
                </div>
            </div>
            <div class="modal-body p-0" id="modalTablesContent">
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted small">Cargando mesas...</p>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary" onclick="closeInstitutionModal()">Cerrar</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="exportModalToImage()">
                    <i class="ri-image-line"></i> Guardar Imagen
                </button>
            </div>
        </div>
    </div>
</div>

<div id="seatExportModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title">
                    <i class="ri-group-line me-1"></i>Exportar Concejales
                </h6>
                <button type="button" class="btn-close btn-close-white" onclick="closeSeatExportModal()"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-success btn-sm" onclick="exportSeatsToCSV(); closeSeatExportModal();">
                        <i class="ri-file-csv-line me-1"></i> Exportar CSV
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="exportSeatsToImage(); closeSeatExportModal();">
                        <i class="ri-image-line me-1"></i> Exportar Imagen (PNG)
                    </button>
                    <button class="btn btn-outline-primary btn-sm" onclick="printSeatsTable(); closeSeatExportModal();">
                        <i class="ri-printer-line me-1"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.dashboard-scripts')