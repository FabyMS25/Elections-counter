{{-- resources/views/institutions/partials/stats-cards.blade.php --}}
@php
    $totalInstitutions = $institutions->total();
    $totalCitizens     = $institutions->sum('registered_citizens');
    $totalComputed     = $institutions->sum('total_computed_records');
    $totalActive       = $institutions->where('status','activo')->count();
    $totalOperative    = $institutions->where('is_operative',true)->count();
    $totalInactive     = $institutions->where('status','inactivo')->count();
    $totalMaintenance  = $institutions->where('status','en_mantenimiento')->count();
    $activePercentage  = $totalInstitutions > 0
        ? round(($totalActive / $totalInstitutions) * 100, 1) : 0;
@endphp

<div class="row g-3 mb-2">
    <div class="col-6 col-md-2">
        <div class="stat-card">
            <div class="icon bg-primary bg-opacity-10 text-primary"><i class="ri-building-line"></i></div>
            <div><div class="num">{{ number_format($totalInstitutions) }}</div><div class="lbl">Total recintos</div></div>
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
            <div><div class="num">{{ $totalActive }}</div><div class="lbl">Activos ({{ $activePercentage }}%)</div></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card">
            <div class="icon bg-warning bg-opacity-10 text-warning"><i class="ri-plug-line"></i></div>
            <div><div class="num">{{ $totalOperative }}</div><div class="lbl">Operativos</div></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card">
            <div class="icon bg-secondary bg-opacity-10 text-secondary"><i class="ri-file-copy-line"></i></div>
            <div><div class="num">{{ number_format($totalComputed) }}</div><div class="lbl">Actas computadas</div></div>
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="stat-card">
            <div class="icon bg-danger bg-opacity-10 text-danger"><i class="ri-close-circle-line"></i></div>
            <div>
                <div class="num">{{ $totalInactive + $totalMaintenance }}</div>
                <div class="lbl">No operativos</div>
            </div>
        </div>
    </div>
</div>
@if($totalInactive > 0 || $totalMaintenance > 0)
<div class="d-flex gap-2 mb-2 justify-content-end">
    @if($totalInactive > 0)
    <span class="badge bg-danger-subtle text-danger">
        <i class="ri-close-circle-line me-1"></i>{{ $totalInactive }} inactivos
    </span>
    @endif
    @if($totalMaintenance > 0)
    <span class="badge bg-warning-subtle text-warning">
        <i class="ri-tools-line me-1"></i>{{ $totalMaintenance }} en mantenimiento
    </span>
    @endif
</div>
@endif
