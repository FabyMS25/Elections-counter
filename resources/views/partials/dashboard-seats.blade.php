<div class="card border-0 shadow-sm mb-2" id="dashboard-seats">
    <div class="card-header bg-transparent border-bottom">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="flex-shrink-0">
                    <span class="avatar-title bg-info-subtle text-info rounded-circle" style="width:42px;height:42px;">
                        <i class="ri-group-line fs-5"></i>
                    </span>
                </div>
                <div>
                    <h5 class="card-title mb-0">Distribución de Concejales (D'Hondt)</h5>
                    <p class="text-muted small mb-0">Asignación de escaños según método D'Hondt</p>
                </div>
            </div>

            <div class="d-flex gap-2">
                <div class="btn-group btn-group-sm">
                    <a href="?seat_mode=validated{{ request()->has('department') ? '&' . http_build_query(request()->except('seat_mode')) : '' }}"
                       class="btn {{ ($currentSeatMode ?? 'all') == 'validated' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="ri-checkbox-circle-line me-1"></i>📋 Oficial
                    </a>
                    <a href="?seat_mode=all{{ request()->has('department') ? '&' . http_build_query(request()->except('seat_mode')) : '' }}"
                       class="btn {{ ($currentSeatMode ?? 'all') == 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="ri-flashlight-line me-1"></i>⚡ Preliminar
                    </a>
                </div>

                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-download-line me-1"></i> Exportar
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportSeatsToCSV(); return false;">
                                <i class="ri-file-csv-line me-2 text-success"></i> Exportar a CSV
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="exportSeatsToImage(); return false;">
                                <i class="ri-image-line me-2 text-info"></i> Exportar a Imagen (PNG)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="printSeatsTable(); return false;">
                                <i class="ri-printer-line me-2 text-primary"></i> Imprimir
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="copySeatsToClipboard(); return false;">
                                <i class="ri-clipboard-line me-2 text-secondary"></i> Copiar al portapapeles
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @php
            $currentSeats = ($currentSeatMode ?? 'all') == 'validated' ? $concejalSeatsValidated : $concejalSeatsAll;
            $sortedAnalysis = $currentSeats['analysis'];
            $totalValidVotes = array_sum(array_column($sortedAnalysis, 'votes'));
            $partyLogos = [];
            if(isset($concejalStats) && count($concejalStats) > 0) {
                foreach($concejalStats as $stat) {
                    $party = $stat['candidate']->party ?? null;
                    $logo = $stat['candidate']->party_logo ?? null;
                    if($party && $logo) {
                        $partyLogos[$party] = $logo;
                    }
                }
            }
        @endphp

        @if(count($sortedAnalysis) == 0)
            <div class="text-center py-3">
                <i class="ri-bar-chart-line fs-1 text-muted d-block mb-2"></i>
                <p class="text-muted">No hay datos disponibles para mostrar</p>
                <small class="text-muted">Los datos se actualizarán automáticamente cuando se registren votos</small>
            </div>
        @else

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Partido</th>
                            <th class="text-center">Votos</th>
                            <th class="text-center">Escaños</th>
                            <th class="text-center">Falta para +1</th>
                            <th class="text-center">Estado</th>
                            <th style="width:120px;">Progreso</th>
                        </thead>
                    <tbody>
                        @foreach($sortedAnalysis as $partyName => $data)
                            @php
                                $rank = $loop->iteration;
                                $percentage = $totalValidVotes > 0 ? round(($data['votes'] / $totalValidVotes) * 100, 1) : 0;
                                $barColor = $rank == 1 ? '#ffc107' : ($rank == 2 ? '#6c757d' : ($rank == 3 ? '#0ab39c' : '#3b5de7'));
                                $isTop3 = $rank <= 3;
                                $partyLogo = $partyLogos[$partyName] ?? null;
                                if ($data['seats'] > 0) {
                                    $statusClass = 'success';
                                    $statusIcon = 'ri-check-line';
                                    $statusText = 'Tiene escaño';
                                } elseif ($data['votes_needed_for_next_seat'] == 0) {
                                    $statusClass = 'warning';
                                    $statusIcon = 'ri-alert-line';
                                    $statusText = 'Empate técnico';
                                } elseif ($data['competing_for_last_seat'] ?? false) {
                                    $statusClass = 'danger';
                                    $statusIcon = 'ri-fire-line';
                                    $statusText = '🔥 Peleando último escaño';
                                } elseif ($data['is_close']) {
                                    $statusClass = 'warning';
                                    $statusIcon = 'ri-timer-line';
                                    $statusText = 'Cerca';
                                } else {
                                    $statusClass = 'secondary';
                                    $statusIcon = 'ri-subtract-line';
                                    $statusText = 'Lejos';
                                }
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <span class="badge rounded-pill" style="background:{{ $barColor }}; color:white;">
                                        {{ $rank }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($partyLogo)
                                            <img src="{{ asset('storage/'.$partyLogo) }}"
                                                 class="rounded-circle"
                                                 style="width:25px;height:25px;object-fit:cover;">
                                        @else
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width:25px;height:25px;background:{{ $barColor }};">
                                                {{ strtoupper(substr($partyName, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <span class="fw-semibold">{{ $partyName }}</span>
                                            @if($isTop3)
                                                <i class="ri-medal-{{ $rank == 1 ? 'gold' : ($rank == 2 ? 'silver' : 'bronze') }}-line ms-1"></i>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center fw-bold">{{ number_format($data['votes']) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill fs-6 px-3">{{ $data['seats'] }}</span>
                                </td>
                                <td class="text-center">
                                    @if($data['votes_needed_for_next_seat'] > 0)
                                        {{ number_format($data['votes_needed_for_next_seat']) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statusClass }}{{ $statusClass == 'warning' ? ' text-dark' : '' }}">
                                        <i class="{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <div class="progress" style="height:6px;">
                                            <div class="progress-bar" style="width: {{ $percentage }}%; background: {{ $barColor }};"></div>
                                        </div>
                                        <small class="text-muted text-center">{{ $percentage }}%</small>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <div class="card bg-primary-subtle border-0">
                        <div class="card-body py-1">
                            <small class="text-primary fw-semibold">Total Votos Válidos</small>
                            <h4 class="mb-0">{{ number_format($totalValidVotes) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success-subtle border-0">
                        <div class="card-body py-1">
                            <small class="text-success fw-semibold">Total Escaños</small>
                            <h4 class="mb-0">{{ array_sum($currentSeats['seats']) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning-subtle border-0">
                        <div class="card-body py-1">
                            <small class="text-warning fw-semibold">Corte D'Hondt</small>
                            <h4 class="mb-0">{{ number_format($currentSeats['cutoff'], 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>