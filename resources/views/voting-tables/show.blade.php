{{-- resources/views/voting-tables/show.blade.php --}}
@extends('layouts.master')
@section('title') Mesa {{ $votingTable->oep_code ?? $votingTable->internal_code }} @endsection

@section('css')
<link href="{{ URL::asset('build/libs/apexcharts/apexcharts.min.css') }}" rel="stylesheet" />
<style>
.avatar-ring{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,.15)}
.stat-box{background:#f8f9fa;border-radius:.5rem;padding:.6rem 1rem;text-align:center;min-width:80px}
.stat-box .n{font-size:1.35rem;font-weight:700;line-height:1}
.stat-box .l{font-size:.7rem;color:#74788d;margin-top:.15rem}
.info-row{display:flex;gap:.5rem;padding:.45rem 0;border-bottom:1px solid #f3f6f9}
.info-row:last-child{border-bottom:none}
.info-k{min-width:42%;font-weight:500;color:#6c757d;font-size:.82rem}
.info-v{font-size:.82rem;color:#212529}
.delegate-card{border:1px solid #e9e9ef;border-radius:.5rem;padding:.75rem;text-align:center;transition:box-shadow .2s;background:#fff}
.delegate-card:hover{box-shadow:0 3px 8px rgba(0,0,0,.08)}
.delegate-avatar{width:48px;height:48px;border-radius:50%;margin:0 auto .5rem;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:600;color:#fff}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('voting-tables.index') }}">Mesas</a> @endslot
    @slot('li_2') {{ $votingTable->institution->name ?? '' }} @endslot
    @slot('title') Mesa {{ $votingTable->number }}{{ $votingTable->letter ?? '' }} @endslot
@endcomponent

@include('components.alerts')

@php
    $latestElection = $votingTable->elections->sortByDesc('updated_at')->first();
    $status = $latestElection?->status ?? 'configurada';
    $totalVoters = $votingTable->elections->sum('total_voters');
    $ballotsReceived = $votingTable->elections->sum('ballots_received');
    $ballotsUsed = $votingTable->elections->sum('ballots_used');
    $ballotsLeftover = $votingTable->elections->sum('ballots_leftover');
    $ballotsSpoiled = $votingTable->elections->sum('ballots_spoiled');

    $statusColors = [
        'configurada' => 'secondary',
        'en_espera' => 'info',
        'votacion' => 'primary',
        'cerrada' => 'warning',
        'en_escrutinio' => 'dark',
        'escrutada' => 'success',
        'observada' => 'danger',
        'transmitida' => 'success',
        'anulada' => 'dark'
    ];
    $statusLabels = [
        'configurada' => 'Configurada',
        'en_espera' => 'En Espera',
        'votacion' => 'Votación',
        'cerrada' => 'Cerrada',
        'en_escrutinio' => 'En Escrutinio',
        'escrutada' => 'Escrutada',
        'observada' => 'Observada',
        'transmitida' => 'Transmitida',
        'anulada' => 'Anulada'
    ];
@endphp

<div class="card mb-2">
    <div class="card-body py-2">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <div class="avatar-ring bg-light d-flex align-items-center justify-content-center">
                    <i class="ri-table-line" style="font-size:2rem;color:#0ab39c"></i>
                </div>
            </div>
            <div class="col">
                <h4 class="mb-0">Mesa {{ $votingTable->number }}{{ $votingTable->letter ?? '' }}</h4>
                <p class="text-muted mb-1">
                    <span class="badge bg-primary-subtle text-primary font-monospace me-2">{{ $votingTable->oep_code ?? '—' }}</span>
                    <span class="badge bg-info-subtle text-info font-monospace me-2">{{ $votingTable->internal_code ?? '—' }}</span>
                    {{ $votingTable->institution->name ?? '' }}
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge bg-{{ $statusColors[$status] ?? 'secondary' }}">
                        {{ $statusLabels[$status] ?? $status }}
                    </span>
                    @php
                        $typeLabels = ['mixta' => 'Mixta', 'masculina' => 'Masculina', 'femenina' => 'Femenina'];
                    @endphp
                    <span class="badge bg-info-subtle text-info">
                        {{ $typeLabels[$votingTable->type] ?? $votingTable->type }}
                    </span>
                </div>
            </div>
            <div class="col-auto d-none d-lg-flex gap-2">
                <div class="stat-box"><div class="n">{{ number_format($votingTable->expected_voters ?? 0) }}</div><div class="l">Electores</div></div>
                <div class="stat-box"><div class="n">{{ number_format($totalVoters) }}</div><div class="l">Votaron</div></div>
            </div>
            <div class="col-auto d-flex gap-2 flex-wrap">
                @can('edit_mesas')
                <a href="{{ route('voting-tables.edit', $votingTable) }}" class="btn btn-soft-warning btn" title="Editar">
                    <i class="ri-pencil-line me-1"></i>
                </a>
                <a href="{{ route('voting-tables.election-config', $votingTable) }}" class="btn btn-soft-info btn" title="Configuración Electoral">
                    <i class="ri-settings-4-line me-1"></i>
                </a>
                @endcan
                <a href="{{ route('voting-tables.index') }}" class="btn btn-soft-secondary btn" title="Volver">
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
                <h5 class="card-title mb-0"><i class="ri-information-line me-1"></i>Información de la Mesa</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Recinto</div>
                    <div class="info-v">
                        <strong>{{ $votingTable->institution->name ?? '—' }}</strong>
                        @if($votingTable->institution->code)
                        <br><span class="text-muted">{{ $votingTable->institution->code }}</span>
                        @endif
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-k">Código OEP</div>
                    <div class="info-v"><span class="badge bg-primary-subtle text-primary font-monospace">{{ $votingTable->oep_code ?? '—' }}</span></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Código Interno</div>
                    <div class="info-v"><span class="badge bg-info-subtle text-info font-monospace">{{ $votingTable->internal_code ?? '—' }}</span></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Número</div>
                    <div class="info-v">{{ $votingTable->number }}{{ $votingTable->letter ? ' ' . $votingTable->letter : '' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Tipo</div>
                    <div class="info-v">{{ $typeLabels[$votingTable->type] ?? $votingTable->type }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Electores habilitados</div>
                    <div class="info-v"><strong>{{ number_format($votingTable->expected_voters ?? 0) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-group-line me-1"></i>Rango de Votantes</h5>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-k">Desde</div>
                    <div class="info-v">{{ $votingTable->voter_range_start_name ?? '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Hasta</div>
                    <div class="info-v">{{ $votingTable->voter_range_end_name ?? '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="ri-bar-chart-line me-1"></i>Resumen Electoral</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-2">
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <div class="text-muted small">Recibidas</div>
                            <div class="h4 mb-0 text-primary">{{ number_format($ballotsReceived) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <div class="text-muted small">Usadas</div>
                            <div class="h4 mb-0 text-success">{{ number_format($ballotsUsed) }}</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-light rounded">
                            <div class="text-muted small">Sobrantes</div>
                            <div class="h4 mb-0 text-warning">{{ number_format($ballotsLeftover) }}</div>
                        </div>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-k">Papeletas deterioradas</div>
                    <div class="info-v text-danger">{{ number_format($ballotsSpoiled) }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Total votantes</div>
                    <div class="info-v"><strong>{{ number_format($totalVoters) }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-k">Participación</div>
                    <div class="info-v">
                        @php $pct = $votingTable->expected_voters > 0 ? round(($totalVoters / $votingTable->expected_voters) * 100, 1) : 0; @endphp
                        <span class="badge bg-{{ $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'secondary') }}">
                            {{ $pct }}%
                        </span>
                    </div>
                </div>
                @if($latestElection)
                <div class="info-row">
                    <div class="info-k">Hora apertura</div>
                    <div class="info-v">{{ $latestElection->opening_time ? \Carbon\Carbon::parse($latestElection->opening_time)->format('H:i') : '—' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-k">Hora cierre</div>
                    <div class="info-v">{{ $latestElection->closing_time ? \Carbon\Carbon::parse($latestElection->closing_time)->format('H:i') : '—' }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-user-star-line me-1"></i>Delegados de Mesa</h5>
        @can('edit_mesas')
        <a href="{{ route('voting-tables.assign-delegates', $votingTable) }}" class="btn btn-sm btn-primary">
            <i class="ri-user-add-line me-1"></i>Asignar Delegados
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="row">
            @php
                $delegates = [
                    'president' => ['label' => 'Presidente', 'color' => 'primary'],
                    'secretary' => ['label' => 'Secretario', 'color' => 'success'],
                    'vocal1' => ['label' => 'Vocal 1', 'color' => 'info'],
                    'vocal2' => ['label' => 'Vocal 2', 'color' => 'warning'],
                    'vocal3' => ['label' => 'Vocal 3', 'color' => 'secondary'],
                ];
            @endphp

            @foreach($delegates as $relation => $info)
                @php $delegate = $votingTable->$relation; @endphp
                <div class="col-md-4 col-lg-2 mb-2">
                    <div class="delegate-card">
                        @if($delegate)
                            <div class="delegate-avatar bg-{{ $info['color'] }}">
                                {{ strtoupper(substr($delegate->name, 0, 1)) }}{{ strtoupper(substr($delegate->last_name ?? '', 0, 1)) }}
                            </div>
                            <h6 class="mb-1 small">{{ $delegate->name }} {{ $delegate->last_name }}</h6>
                            <small class="text-muted d-block">{{ $info['label'] }}</small>
                        @else
                            <div class="delegate-avatar bg-light text-muted">
                                <i class="ri-user-line"></i>
                            </div>
                            <h6 class="mb-1 text-muted small">No asignado</h6>
                            <small class="text-muted d-block">{{ $info['label'] }}</small>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($votingTable->vocal4_name)
            <div class="col-md-4 col-lg-2 mb-2">
                <div class="delegate-card">
                    <div class="delegate-avatar bg-dark">
                        <i class="ri-user-line"></i>
                    </div>
                    <h6 class="mb-1 small">{{ $votingTable->vocal4_name }}</h6>
                    <small class="text-muted d-block">Vocal 4</small>
                    <small class="text-muted">Externo</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($votingTable->observations)
<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri-chat-1-line me-1"></i>Observaciones</h5>
    </div>
    <div class="card-body">
        <p class="mb-0">{{ $votingTable->observations }}</p>
    </div>
</div>
@endif

<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title mb-0"><i class="ri-history-line me-1"></i>Información de Auditoría</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <small class="text-muted d-block">Creado por</small>
                <strong>{{ $votingTable->createdBy->name ?? 'Sistema' }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Fecha creación</small>
                <strong>{{ $votingTable->created_at?->format('d/m/Y H:i') ?? '—' }}</strong>
            </div>
            @if($votingTable->updatedBy)
            <div class="col-md-3">
                <small class="text-muted d-block">Actualizado por</small>
                <strong>{{ $votingTable->updatedBy->name }}</strong>
            </div>
            <div class="col-md-3">
                <small class="text-muted d-block">Fecha actualización</small>
                <strong>{{ $votingTable->updated_at?->format('d/m/Y H:i') ?? '—' }}</strong>
            </div>
            @endif
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/apexcharts/apexcharts.min.js') }}"></script>
@if($votingTable->votes->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    var options = {
        series: [{
            data: [@foreach($votingTable->votes as $vote) {{ $vote->quantity }}, @endforeach]
        }],
        chart: { type: 'bar', height: 350 },
        plotOptions: { bar: { borderRadius: 4, horizontal: true } },
        xaxis: {
            categories: [@foreach($votingTable->votes as $vote) '{{ $vote->candidate->name ?? "N/A" }}', @endforeach]
        },
        colors: ['#0ab39c'],
        title: { text: 'Resultados por Candidato', align: 'center', style: { fontSize: '16px', fontWeight: 'bold' } }
    };
    var chart = new ApexCharts(document.querySelector("#results-chart"), options);
    chart.render();
});
</script>
@endif
<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: '¿Eliminar mesa?',
        html: `¿Desea eliminar la mesa <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f06548',
        cancelButtonColor: '#8590a5',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        const form = document.getElementById('deleteForm');
        form.action = `/voting-tables/${id}`;
        form.submit();
    });
}
</script>
@endsection
