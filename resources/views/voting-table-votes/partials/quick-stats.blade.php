{{-- resources/views/voting-table-votes/partials/quick-stats.blade.php --}}
@if(isset($tableStats) && ($tableStats['total'] ?? 0) > 0)
@php
    $total     = $tableStats['total'] ?? 0;
    $gPending  = ($tableStats['configurada'] ?? 0) + ($tableStats['en_espera'] ?? 0);
    $gVoting   = $tableStats['votacion']      ?? 0;
    $gCounting = $tableStats['en_escrutinio'] ?? 0;
    $gDone     = ($tableStats['escrutada'] ?? 0) + ($tableStats['transmitida'] ?? 0);
    $gObserved = $tableStats['observada']     ?? 0;
    $gAnnulled = $tableStats['anulada']       ?? 0;
    $pct       = fn($n) => $total > 0 ? round(($n / $total) * 100, 1) : 0;
@endphp
<div class="mb-2 mt-1">
    <div class="progress" style="height:18px;border-radius:4px;">
        @if($gPending > 0)
        <div class="progress-bar bg-secondary" role="progressbar"
             style="width:{{ $pct($gPending) }}%;font-size:.7rem"
             title="Sin iniciar: {{ $gPending }}">
            @if($pct($gPending) >= 8){{ $pct($gPending) }}%@endif
        </div>
        @endif
        @if($gVoting > 0)
        <div class="progress-bar bg-primary" role="progressbar"
             style="width:{{ $pct($gVoting) }}%;font-size:.7rem"
             title="Votación: {{ $gVoting }}">
            @if($pct($gVoting) >= 8){{ $pct($gVoting) }}%@endif
        </div>
        @endif
        @if($gCounting > 0)
        <div class="progress-bar bg-warning text-dark" role="progressbar"
             style="width:{{ $pct($gCounting) }}%;font-size:.7rem"
             title="Escrutinio: {{ $gCounting }}">
            @if($pct($gCounting) >= 8){{ $pct($gCounting) }}%@endif
        </div>
        @endif
        @if($gObserved > 0)
        <div class="progress-bar bg-danger" role="progressbar"
             style="width:{{ $pct($gObserved) }}%;font-size:.7rem"
             title="Observadas: {{ $gObserved }}">
            @if($pct($gObserved) >= 8){{ $pct($gObserved) }}%@endif
        </div>
        @endif
        @if($gDone > 0)
        <div class="progress-bar bg-success" role="progressbar"
             style="width:{{ $pct($gDone) }}%;font-size:.7rem"
             title="Completadas: {{ $gDone }}">
            @if($pct($gDone) >= 8){{ $pct($gDone) }}%@endif
        </div>
        @endif
        @if($gAnnulled > 0)
        <div class="progress-bar bg-dark" role="progressbar"
             style="width:{{ $pct($gAnnulled) }}%;font-size:.7rem"
             title="Anuladas: {{ $gAnnulled }}">
            @if($pct($gAnnulled) >= 8){{ $pct($gAnnulled) }}%@endif
        </div>
        @endif
    </div>
    <div class="d-flex flex-wrap gap-3 mt-1" style="font-size:.72rem;color:#74788d">
        @if($gPending)  <span><span class="badge bg-secondary">&nbsp;</span> Sin iniciar {{ $gPending }}</span>@endif
        @if($gVoting)   <span><span class="badge bg-primary">&nbsp;</span> Votación {{ $gVoting }}</span>@endif
        @if($gCounting) <span><span class="badge bg-warning text-dark">&nbsp;</span> Escrutinio {{ $gCounting }}</span>@endif
        @if($gObserved) <span><span class="badge bg-danger">&nbsp;</span> Observadas {{ $gObserved }}</span>@endif
        @if($gDone)     <span><span class="badge bg-success">&nbsp;</span> Completadas {{ $gDone }}</span>@endif
        @if($gAnnulled) <span><span class="badge bg-dark">&nbsp;</span> Anuladas {{ $gAnnulled }}</span>@endif
        <span class="ms-auto fw-semibold text-dark">{{ $total }} mesas en total</span>
    </div>
</div>
@endif