{{-- resources/views/voting-table-votes/partials/table.blade.php --}}
@php
    $isDisabled = in_array($table->current_status, [
                      'en_escrutinio','escrutada','transmitida','anulada'
                  ]) || !($permissions['can_register'] ?? false);

    $categoryColors   = ['primary','success','warning','info','danger','secondary','dark'];
    $categoryColorMap = [];
    $index = 0;
    foreach (array_keys($candidatesByCategory ?? []) as $code) {
        $categoryColorMap[$code] = $categoryColors[$index % count($categoryColors)];
        $index++;
    }
    $hasInconsistencies = false;
    foreach ($table->results_by_category ?? [] as $r) {
        if (!$r['is_consistent']) { $hasInconsistencies = true; break; }
    }
    $statusClasses = [
        'configurada'   => 'secondary', 'en_espera'     => 'info',
        'votacion'      => 'primary',   'en_escrutinio' => 'warning',
        'escrutada'     => 'success',   'observada'     => 'danger',
        'transmitida'   => 'success',   'anulada'       => 'dark',
        'sin_configurar'=> 'secondary',
    ];
    $statusColor = $statusClasses[$table->current_status] ?? 'secondary';
@endphp
<div class="card table-card status-{{ $table->current_status }}"
     id="table-{{ $table->id }}"
     data-table-id="{{ $table->id }}"
     data-expected-voters="{{ $table->expected_voters }}">
    <div class="card-header bg-light py-2">
        <div class="row align-items-center g-2">
            <div class="col-md-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-1 d-flex align-items-center justify-content-center flex-shrink-0
                                bg-{{ $statusColor }}-subtle text-{{ $statusColor }}"
                         style="width:34px;height:34px">
                        <i class="ri-table-line" style="font-size:1.1rem"></i>
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate">
                            Mesa {{ $table->number }}
                            @if($table->letter){{ $table->letter }}@endif
                            &nbsp;<span class="text-muted fw-normal">{{ $table->internal_code ?? $table->oep_code }}</span>
                        </div>
                        <div class="text-muted" style="font-size:.7rem">
                            <i class="ri-building-2-line me-1"></i>{{ Str::limit($table->institution->name ?? 'N/A', 32) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle d-inline-flex align-items-center gap-1">
                    {{ $statusLabels[$table->current_status] ?? $table->current_status }}
                    @if($hasInconsistencies)
                        <i class="ri-alert-fill text-warning ms-1" title="Inconsistencias detectadas"></i>
                    @endif
                </span>
                <div class="text-muted mt-1" style="font-size:.7rem">
                    <i class="ri-inbox-line me-1"></i>Ánfora:&nbsp;<span
                        class="fw-semibold text-dark cat-total-display"
                        data-display="urn-total"
                        data-table="{{ $table->id }}">{{ $table->total_voters }}</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-1 flex-wrap">
                    @foreach($candidatesByCategory as $categoryCode => $_)
                    @php
                        $catTotal     = $table->results_by_category[$categoryCode]['total_votes'] ?? 0;
                        $isConsistent = $table->results_by_category[$categoryCode]['is_consistent'] ?? true;
                        $validVotes   = $table->results_by_category[$categoryCode]['valid_votes']  ?? 0;
                        $blankVotes   = $table->results_by_category[$categoryCode]['blank_votes']  ?? 0;
                        $nullVotes    = $table->results_by_category[$categoryCode]['null_votes']   ?? 0;
                        $cc           = $categoryColorMap[$categoryCode] ?? 'secondary';
                    @endphp
                    <span class="badge bg-{{ $cc }}-subtle text-{{ $cc }} border border-{{ $cc }}-subtle"
                          style="font-size:.72rem"
                          title="{{ $categoryCode }}: {{ $validVotes }}V + {{ $blankVotes }}B + {{ $nullVotes }}N = {{ $catTotal }}">
                        {{ $categoryCode }}:
                        <span class="fw-bold cat-total-display"
                              data-display="cat-total"
                              data-category="{{ $categoryCode }}"
                              data-table="{{ $table->id }}">{{ $catTotal }}</span>
                        @if(!$isConsistent)
                            <i class="ri-alert-fill text-warning ms-1" title="Inconsistente"></i>
                        @endif
                    </span>
                    @endforeach

                    @if(($table->observations_count ?? 0) > 0)
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle view-observations"
                          data-table-id="{{ $table->id }}"
                          style="font-size:.72rem;cursor:pointer"
                          title="Ver {{ $table->observations_count }} observación(es) pendiente(s)">
                        <i class="ri-alert-line me-1"></i>{{ $table->observations_count }} obs.
                    </span>
                    @endif
                </div>
            </div>
            <div class="col-md-3 d-flex justify-content-end">
                @include('voting-table-votes.partials.table-actions', ['table' => $table])
            </div>
        </div>
        @include('voting-table-votes.partials.ballot-inputs', [
            'table'          => $table,
            'isDisabled'     => $isDisabled,
            'permissions'    => $permissions,
            'electionTypeId' => $electionTypeId,
        ])
    </div>
    <div class="card-body p-0">
        @if(empty($candidatesByCategory))
            <div class="text-center py-4">
                <i class="ri-user-search-line text-muted d-block mb-1" style="font-size:2rem"></i>
                <p class="text-muted small mb-0">No hay candidatos para esta elección</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:3%">#</th>
                            <th style="width:10%">Partido</th>
                            @foreach($candidatesByCategory as $categoryCode => $_)
                            <th colspan="3" class="text-center bg-{{ $categoryColorMap[$categoryCode] ?? 'secondary' }}-subtle text-{{ $categoryColorMap[$categoryCode] ?? 'secondary' }}">
                                {{ $categoryCode }}
                            </th>
                            @endforeach
                        </tr>
                        <tr>
                            <th></th><th></th>
                            @foreach($candidatesByCategory as $categoryCode => $_)
                            @php $cc = $categoryColorMap[$categoryCode] ?? 'secondary'; @endphp
                            <th class="bg-{{ $cc }}-subtle small">Candidato</th>
                            <th class="bg-{{ $cc }}-subtle small text-center">Votos</th>
                            <th class="bg-{{ $cc }}-subtle small text-center">Obs</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @include('voting-table-votes.partials.table-rows', [
                            'table'                => $table,
                            'candidatesByCategory' => $candidatesByCategory,
                            'permissions'          => $permissions,
                            'isDisabled'           => $isDisabled,
                            'categoryColorMap'     => $categoryColorMap,
                        ])
                    </tbody>
                </table>
            </div>
        @endif
        @if(($permissions['can_observe'] ?? false) && !$isDisabled && !empty($candidatesByCategory))
        <div class="px-3 py-2 bg-light border-top d-flex align-items-center justify-content-between gap-2 flex-wrap">
            <div class="small text-muted">
                <span id="selected-count-{{ $table->id }}">0</span> votos seleccionados
                @foreach($candidatesByCategory as $categoryCode => $_)
                @php $cc = $categoryColorMap[$categoryCode] ?? 'secondary'; @endphp
                <span class="badge bg-{{ $cc }}-subtle text-{{ $cc }} ms-1"
                      id="selected-{{ $categoryCode }}-{{ $table->id }}">0 {{ $categoryCode }}</span>
                @endforeach
            </div>
            <button class="btn btn-sm btn-soft-warning create-observation-btn"
                    data-table-id="{{ $table->id }}">
                <i class="ri-chat-1-line me-1"></i>Crear Observación
            </button>
        </div>
        @endif
        <div class="px-3 py-2 border-top bg-light d-flex flex-wrap gap-3" style="font-size:.78rem">
            <span><span class="text-muted">Válidos:</span>
                  <strong class="ms-1" id="footer-valid-{{ $table->id }}">{{ array_sum(array_column($table->results_by_category ?? [], 'valid_votes')) }}</strong></span>
            <span><span class="text-muted">Blancos:</span>
                  <strong class="ms-1" id="footer-blank-{{ $table->id }}">{{ array_sum(array_column($table->results_by_category ?? [], 'blank_votes')) }}</strong></span>
            <span><span class="text-muted">Nulos:</span>
                  <strong class="ms-1" id="footer-null-{{ $table->id }}">{{ array_sum(array_column($table->results_by_category ?? [], 'null_votes')) }}</strong></span>
            <span><span class="text-muted">No usadas:</span>
                  <strong class="ms-1">{{ $table->elections->first()?->ballots_leftover ?? 0 }}</strong></span>
            @if($table->expected_voters > 0 && $table->total_voters > 0)
            @php $pp = round($table->total_voters / $table->expected_voters * 100, 1); @endphp
            <span class="ms-auto">
                <span class="text-muted">Participación:</span>
                <strong class="ms-1 text-{{ $pp >= 75 ? 'success' : ($pp >= 50 ? 'warning' : 'secondary') }}">{{ $pp }}%</strong>
            </span>
            @endif
        </div>
        @if($hasInconsistencies)
        <div class="px-3 py-2 border-top bg-warning bg-opacity-10">
            <div class="fw-semibold small text-warning mb-1">
                <i class="ri-alert-line me-1"></i>Inconsistencias detectadas:
            </div>
            @foreach($table->results_by_category ?? [] as $catCode => $result)
                @if(!$result['is_consistent'])
                <div class="small text-danger ms-2">
                    <strong>{{ $catCode }}</strong>:
                    {{ $result['valid_votes'] }}V + {{ $result['blank_votes'] }}B + {{ $result['null_votes'] }}N
                    = {{ $result['valid_votes'] + $result['blank_votes'] + $result['null_votes'] }}
                    (guardado: {{ $result['total_votes'] }})
                </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>