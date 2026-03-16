{{-- resources/views/voting-table-votes/partials/ballot-inputs.blade.php --}}
@php
    $te = $table->elections->first();
    $ballotsInUrn    = $te?->total_voters    ?? 0;
    $ballotsLeftover = $te?->ballots_leftover ?? 0;
    $ballotsSpoiled  = $te?->ballots_spoiled  ?? 0;
    $expectedVoters  = $table->expected_voters ?? 0;

    $expectedLeftover  = max(0, $expectedVoters - $ballotsInUrn - $ballotsSpoiled);
    $hasEnteredLeftover = $te && $te->ballots_leftover !== null && ($ballotsLeftover > 0 || $ballotsInUrn > 0);
    $accountedTotal    = $ballotsInUrn + $ballotsLeftover + $ballotsSpoiled;
    $balanceOk         = $hasEnteredLeftover && ($accountedTotal === $expectedVoters);
    $balanceDiff       = $accountedTotal - $expectedVoters;
    $participation     = ($expectedVoters > 0) ? round(($ballotsInUrn / $expectedVoters) * 100, 1) : 0;
    $canEdit           = ($permissions['can_register'] ?? false) && !$isDisabled;
@endphp

<div class="ballot-data-section mt-2 border rounded bg-white px-2 py-2"
     id="ballot-data-{{ $table->id }}"
     data-table-id="{{ $table->id }}"
     data-expected-voters="{{ $expectedVoters }}">
    <h6 class="fw-semibold mb-2 text-muted text-uppercase small">
        <i class="ri-file-paper-line me-1"></i>Conteo de Papeletas
    </h6>

    <div class="row align-items-start g-2">
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-group-line"></i> Habilitados
                <span class="badge-readonly">padrón</span>
            </div>
            <div class="ballot-value text-info fw-bold mt-1">{{ number_format($expectedVoters) }}</div>
            <div class="ballot-hint">del registro electoral</div>
        </div>
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-inbox-line"></i> En ánfora
                <span class="badge-auto">auto</span>
            </div>
            <div class="ballot-value text-primary fw-bold mt-1" id="urn-count-{{ $table->id }}">
                {{ number_format($ballotsInUrn) }}
            </div>
            <div class="ballot-hint">válidos + blancos + nulos</div>
        </div>
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-file-list-3-line"></i> No utilizadas
                @if($canEdit) <span class="badge-input-lbl">del acta</span> @endif
            </div>
            @if($canEdit)
                <input type="number"
                       id="leftover-{{ $table->id }}"
                       class="ballot-input ballot-leftover-input mt-1"
                       data-table="{{ $table->id }}"
                       value="{{ ($ballotsLeftover > 0 || ($te && $te->ballots_leftover !== null)) ? $ballotsLeftover : '' }}"
                       min="0" max="{{ $expectedVoters }}"
                       placeholder="{{ str_pad($expectedLeftover, 3, '0', STR_PAD_LEFT) }}"
                       title="Copiar del acta física">
            @else
                <div class="ballot-value fw-bold mt-1">{{ number_format($ballotsLeftover) }}</div>
            @endif
            {{-- <div class="ballot-hint">
                @if($expectedVoters > 0 && $ballotsInUrn > 0)
                    <br><span class="text-primary">esperado: <strong>{{ str_pad($expectedLeftover,3,'0',STR_PAD_LEFT) }}</strong></span>
                @endif
            </div> --}}
        </div>
        <div class="col-6 col-md-2">
            <div class="ballot-label">
                <i class="ri-delete-bin-line"></i> Deterioradas
                <span class="badge-optional">opcional</span>
            </div>
            @if($canEdit)
                <input type="number"
                       id="spoiled-{{ $table->id }}"
                       class="ballot-input ballot-spoiled-input mt-1"
                       data-table="{{ $table->id }}"
                       value="{{ $ballotsSpoiled > 0 ? $ballotsSpoiled : '' }}"
                       min="0" placeholder="0"
                       title="Papeletas dañadas (generalmente 0)">
            @else
                <div class="ballot-value fw-bold mt-1">{{ number_format($ballotsSpoiled) }}</div>
            @endif
        </div>
        <div class="col-12 col-md-4">
            <div class="ballot-label    ">
                <i class="ri-percent-line"></i> Participación &amp; Cuadre
            </div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <div class="progress flex-grow-1" style="height:8px;min-width:60px">
                    <div class="progress-bar bg-{{ $participation >= 75 ? 'success' : ($participation >= 50 ? 'warning' : 'secondary') }}"
                         style="width:{{ min(100,$participation) }}%"></div>
                </div>
                <span class="fw-bold small text-{{ $participation >= 75 ? 'success' : ($participation >= 50 ? 'warning' : 'secondary') }}"
                      id="participation-{{ $table->id }}">
                    {{ $participation }}%
                </span>
            </div>
            <div id="ballot-balance-{{ $table->id }}">
                @if(!$hasEnteredLeftover)
                    <div class="badge-balance badge-balance-warn">
                        <i class="ri-pencil-line me-1"></i>
                        Ingrese las <strong>No utilizadas</strong> del acta para verificar el cuadre
                    </div>
                @elseif($balanceOk)
                    <div class="badge-balance badge-balance-ok">
                        <i class="ri-checkbox-circle-line me-1"></i>
                        Cuadre correcto ✓ — {{ number_format($accountedTotal) }} = {{ number_format($expectedVoters) }} habilitados
                    </div>
                @else
                    <div class="badge-balance badge-balance-err"
                         title="Ánfora({{ $ballotsInUrn }}) + NoUsadas({{ $ballotsLeftover }}) + Det.({{ $ballotsSpoiled }}) = {{ $accountedTotal }} ≠ {{ $expectedVoters }}">
                        <i class="ri-alert-line me-1"></i>
                        No cuadra: diferencia de {{ abs($balanceDiff) }} papeleta(s)
                        ({{ $balanceDiff > 0 ? 'sobran' : 'faltan' }})
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="ballot-formula mt-2">
        <i class="ri-equals-line me-1 text-muted"></i>
        <span><strong>Ánfora</strong> <span class="fw-bold text-primary" id="formula-urn-{{ $table->id }}">{{ $ballotsInUrn }}</span></span>
        <span class="text-muted">+</span>
        <span><strong>No usadas</strong> <span class="fw-bold" id="formula-leftover-{{ $table->id }}">{{ $ballotsLeftover }}</span></span>
        <span class="formula-spoiled-wrap" @if($ballotsSpoiled == 0) style="display:none" @endif>
            <span class="text-muted">+</span>
            <span><strong>Deterioradas</strong> <span class="fw-bold" id="formula-spoiled-{{ $table->id }}">{{ $ballotsSpoiled }}</span></span>
        </span>
        <span class="text-muted">=</span>
        <strong id="formula-total-{{ $table->id }}"
                class="{{ $hasEnteredLeftover ? ($balanceOk ? 'text-success' : 'text-danger') : 'text-muted' }}">
            {{ $accountedTotal }}
        </strong>
        <span class="text-muted">/</span>
        <strong class="text-info">{{ $expectedVoters }}</strong>
        <span class="text-muted">habilitados</span>
        @if($hasEnteredLeftover)
            <span class="{{ $balanceOk ? 'text-success' : 'text-danger' }} fw-bold">
                {{ $balanceOk ? '✓' : ('✗ dif: '.($balanceDiff>0?'+':'').$balanceDiff) }}
            </span>
        @endif
    </div>
</div>