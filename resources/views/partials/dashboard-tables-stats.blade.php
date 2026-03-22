{{-- resources/views/partials/dashboard-tables-stats.blade.php --}}

{{-- ── Compact progress bar ───────────────────────────────────────────── --}}
<div class="card mt-3 border-0 shadow-sm">
    <div class="card-body py-3 px-4">
        <div class="row align-items-center g-2">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <span class="text-uppercase fw-semibold text-muted"
                          style="font-size:.68rem;letter-spacing:.05em;white-space:nowrap;">
                        <i class="ri-bar-chart-grouped-line me-1"></i>Escrutinio de Mesas
                    </span>
                    <span class="ms-auto fw-bold text-success" style="font-size:1.1rem;"
                          id="ds-stat-pct">{{ $progressPercentage }}</span>
                    <span class="text-muted small">%</span>
                </div>
                <div class="progress" style="height:8px;border-radius:8px;background:#e9ecef;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                         id="ds-big-bar"
                         role="progressbar"
                         style="width:{{ $progressPercentage }}%;border-radius:8px;transition:width .6s ease;">
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-0 text-center">
                    <div class="col-4 border-end">
                        <div class="fw-bold fs-5 text-success lh-1 reported-tables-count" id="ds-stat-reported">
                            {{ number_format($reportedTables) }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:.7rem;">Computadas</div>
                    </div>
                    <div class="col-4 border-end">
                        <div class="fw-bold fs-5 text-warning lh-1 pending-tables-count" id="ds-stat-pending">
                            {{ number_format($totalTables - $reportedTables) }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:.7rem;">Pendientes</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-5 text-secondary lh-1 total-tables-count" id="ds-stat-total">
                            {{ number_format($totalTables) }}
                        </div>
                        <div class="text-muted mt-1" style="font-size:.7rem;">Habilitadas</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>