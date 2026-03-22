<div class="p-3">
    @php
        $summary = collect($tables);
        $totalCount = $summary->count();
        $pendingCount = $summary->where('state','pending')->count();
        $partialCount = $summary->where('state','partial')->count();
        $completeCount = $summary->where('state','complete')->count();
    @endphp
    <div class="row g-2 mb-2">
        <div class="col-3">
            <div class="card border-0 bg-light text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Total</small>
                    <h3 class="mb-0 fw-bold">{{ $totalCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card border-0 bg-danger bg-opacity-10 text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Pendientes</small>
                    <h3 class="mb-0 fw-bold text-danger">{{ $pendingCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card border-0 bg-warning bg-opacity-10 text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Parciales</small>
                    <h3 class="mb-0 fw-bold text-warning">{{ $partialCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="card border-0 bg-success bg-opacity-10 text-center py-2">
                <div class="card-body p-1">
                    <small class="text-muted text-uppercase" style="font-size: 10px;">Completas</small>
                    <h3 class="mb-0 fw-bold text-success">{{ $completeCount }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-bordered mb-0" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%;">Mesa</th>
                    <th style="width: 25%;" class="text-center">Estado</th>
                    <th style="width: 25%;" class="text-center">Votos</th>
                    <th style="width: 25%;" class="text-center">Validación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tables as $t)
                <tr>
                    <td class="fw-semibold">Mesa {{ $t['number'] }}</td>
                    <td class="text-center">
                        @if($t['state'] == 'pending')
                            <span class="badge bg-danger px-2 py-1" style="font-size: 10px;">Pendiente</span>
                        @elseif($t['state'] == 'partial')
                            <span class="badge bg-warning text-dark px-2 py-1" style="font-size: 10px;">Parcial</span>
                        @else
                            <span class="badge bg-success px-2 py-1" style="font-size: 10px;">Completa</span>
                        @endif
                    </td>
                    <td class="text-center fw-bold">{{ number_format($t['votes']) }}</td>
                    <td class="text-center">
                        @if($t['validated'])
                            <span class="badge bg-success px-2 py-1" style="font-size: 10px;">✔ Validada</span>
                        @else
                            <span class="badge bg-secondary px-2 py-1" style="font-size: 10px;">Pendiente</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-3 text-muted">
                        <small>No hay mesas registradas</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-2 text-center">
        <small class="text-muted" style="font-size: 9px;">
            <i class="ri-information-line"></i>
            Actualizado: {{ now()->format('d/m/Y H:i') }}
        </small>
    </div>
</div>
