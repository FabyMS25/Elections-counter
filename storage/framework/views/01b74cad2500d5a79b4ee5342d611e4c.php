    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Votos Totales</p>
                        </div>
                        <div class="flex-shrink-0">                            
                                        <h5 class="text-success fs-14 mb-0">
                                            <i class="ri-arrow-right-up-line fs-13 align-middle"></i> En vivo
                                        </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="flex-grow-1">
                                <span class="counter-value" data-target="<?php echo e($totalVotes); ?>">
                                    0<!-- <?php echo e(number_format($totalVotes)); ?> --></span>
                            </h4>
                            <p class="text-muted text-truncate">Total de votos emitidos</sppan>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-2 fs-2">
                                <i data-feather="archive" class="text-primary"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Mesas Reportadas</p>
                        </div>
                        <div class="flex-shrink-0">
                                        <h5 class="text-info fs-14 mb-0">
                                            <?php echo e($progressPercentage); ?>%
                                        </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="flex-grow-1">
                                <span class="counter-value" data-target="<?php echo e($reportedTables); ?>">
                                    0</span>/
                                <span class="text-muted fs-14"><?php echo e($totalTables); ?></span>
                            </h4>
                            <div class="progress progress-sm mb-2 mt-2" style="height: 5px;">
                                <div class="progress-bar bg-info" role="progressbar" 
                                                 style="width: <?php echo e($progressPercentage); ?>%"></div>
                            </div>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-info-subtle text-info rounded-2 fs-2">
                                <i class="bx bx-table text-info"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Candidato Líder</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-success fs-14 mb-0">
                                <i class="ri-arrow-right-up-line fs-13 align-middle"></i> #1
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                        <?php if(count($candidateStats) > 0): ?>
                            <?php $leadingCandidate = reset($candidateStats); ?>
                            <div class=d-flex>
                                <div class="flex-grow-1">
                                <?php if($leadingCandidate['candidate']->photo): ?>
                                    <img src="<?php echo e(asset('storage/' . $leadingCandidate['candidate']->photo)); ?>" 
                                        alt="<?php echo e($leadingCandidate['candidate']->name); ?>" 
                                        class="img-fluid rounded-circle border border-3 border-white shadow-sm"
                                        style="width:55px;height:55px;object-fit:cover;">
                                <?php else: ?>
                                    <lord-icon src="https://cdn.lordicon.com/dxjqoygy.json" trigger="loop"
                                        colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                                <?php endif; ?>
                                </div>
                                <h4 class="fs-16 fw-semibold ff-secondary mb-2">
                                    <?php echo e($leadingCandidate['candidate']->name ?? 'N/A'); ?>

                                </h4>

                            </div>
                                <p class="text-muted mb-0">
                                    <?php echo e(number_format($leadingCandidate['votes'])); ?> votos
                                    (<?php echo e($leadingCandidate['percentage']); ?>%)
                                </p>
                        <?php else: ?>
                            <h4 class="fs-20 fw-semibold ff-secondary mb-4">Sin votos aún</h4>
                        <?php endif; ?>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle rounded fs-3">
                                <i class="bx bx-trophy text-success"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1 overflow-hidden">
                            <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Promedio por Mesa</p>
                        </div>
                        <div class="flex-shrink-0">
                            <h5 class="text-warning fs-14 mb-0">
                                <?php echo e($reportedTables > 0 ? round($totalVotes / $reportedTables, 1) : 0); ?>

                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between mt-4">
                        <div>
                            <h4 class="flex-grow-1">
                                <span class="counter-value" data-target="<?php echo e($reportedTables > 0 ? number_format($totalVotes / $reportedTables, 1) : 0); ?>">0</span> %
                            </h4>
                            <p class="text-muted text-truncate">Votos por mesa</sppan>
                        </div>
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle rounded fs-3">
                                <i class="bx bx-stats text-warning"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Resultados por Candidato -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Resultados por Candidato</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Posición</th>
                                    <th>Candidato</th>
                                    <th>Partido</th>
                                    <th>Votos</th>
                                    <th>Porcentaje</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $candidateStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidateId => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $candidate = $stats['candidate']; ?>
                                    <tr>
                                        <td><span class="badge bg-primary"><?php echo e($stats['rank']); ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if($candidate->photo): ?>
                                                    <img src="<?php echo e(asset('storage/' . $candidate->photo)); ?>" 
                                                         alt="<?php echo e($candidate->name); ?>" 
                                                         class="rounded-circle avatar-xs me-2">
                                                <?php else: ?>
                                                    <div class="avatar-xs me-2">
                                                        <span class="avatar-title rounded-circle bg-primary">
                                                            <?php echo e(substr($candidate->name, 0, 1)); ?>

                                                        </span>
                                                    </div>
                                                    <?php endif; ?>
                                                <span><?php echo e($candidate->name); ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo e($candidate->party); ?></td>
                                        <td><?php echo e(number_format($stats['votes'])); ?></td>
                                        <td><?php echo e($stats['percentage']); ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar" role="progressbar" 
                                                     style="width: <?php echo e($stats['percentage']); ?>%;"
                                                     aria-valuenow="<?php echo e($stats['percentage']); ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    <div class="row mt-4">
        <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Estado de Mesas</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-around text-center">
                                    <div>
                                        <h4 class="text-primary"><?php echo e($totalTables); ?></h4>
                                        <p class="text-muted mb-0">Total de Mesas</p>
                                    </div>
                                    <div>
                                        <h4 class="text-success"><?php echo e($reportedTables); ?></h4>
                                        <p class="text-muted mb-0">Mesas Reportadas</p>
                                    </div>
                                    <div>
                                        <h4 class="text-warning"><?php echo e($totalTables - $reportedTables); ?></h4>
                                        <p class="text-muted mb-0">Mesas Pendientes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>
        <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Progreso General</h5>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3" style="height: 20px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: <?php echo e($progressPercentage); ?>%"
                                         aria-valuenow="<?php echo e($progressPercentage); ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo e($progressPercentage); ?>%
                                    </div>
                                </div>
                                <p class="text-muted mb-0 text-center">
                                    <?php echo e($reportedTables); ?> de <?php echo e($totalTables); ?> mesas reportadas
                                </p>
                            </div>
                        </div>
        </div>
    </div>
    

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <h5 class="card-title mb-0 flex-grow-1">Resultados Detallados por Municipio</h5>
                    <div class="flex-shrink-0">
                        <button class="btn btn-sm btn-primary" id="exportMunicipalityTable">
                            <i class="ri-download-line align-middle"></i> Exportar Reporte
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="municipality-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Municipio</th>
                                    <th>Mesas</th>
                                    <th>Reportadas</th>
                                    <th>Avance</th>
                                    <th>Votos Totales</th>
                                    <?php $__currentLoopData = $presidentialCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th><?php echo e($candidate->name); ?> (<?php echo e($candidate->party); ?>)</th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $municipalityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $municipalityData = $municipalityResults[$municipality->id] ?? null;
                                    $progress = $municipality->total_tables > 0 
                                        ? round(($municipality->reported_tables / $municipality->total_tables) * 100, 1)
                                        : 0;
                                ?>
                                <tr>
                                    <td><strong><?php echo e($municipality->name); ?></strong></td>
                                    <td><?php echo e($municipality->total_tables); ?></td>
                                    <td><?php echo e($municipality->reported_tables); ?></td>
                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: <?php echo e($progress); ?>%;"
                                                aria-valuenow="<?php echo e($progress); ?>" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small><?php echo e($progress); ?>%</small>
                                    </td>
                                    <td><strong><?php echo e($municipalityData['total_votes'] ?? 0); ?></strong></td>
                                    <?php $__currentLoopData = $presidentialCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $candidateVotes = 0;
                                        $candidatePercentage = 0;
                                        if ($municipalityData) {
                                            foreach ($municipalityData['candidates'] as $cand) {
                                                if ($cand['id'] == $candidate->id) {
                                                    $candidateVotes = $cand['votes'];
                                                    $candidatePercentage = $cand['percentage'];
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                    <td>
                                        <?php echo e($candidateVotes); ?><br>
                                        <small class="text-muted"><?php echo e($candidatePercentage); ?>%</small>
                                    </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="<?php echo e(5 + count($presidentialCandidates)); ?>" class="text-center text-muted py-4">
                                        No hay datos disponibles
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header border-0 align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Resultados por Candidato</h4>
                        <div>
                            <button type="button" class="btn btn-soft-secondary btn-sm">
                                TODOS
                            </button>
                            <button type="button" class="btn btn-soft-primary btn-sm">
                                PRINCIPALES
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="candidates_chart"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4">
                <div class="card card-height-100">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Distribución por Partido</h4>
                        <div class="flex-shrink-0">
                            <button type="button" class="btn btn-soft-primary btn-sm">
                                Exportar
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="party_distribution_chart"  class="e-charts" style="height: 300px;"></div>
                    </div>
                </div>
            </div>        
    </div>

    <div class="row">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header border-0 align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Resultados por Municipio</h4>
                    <div>
                        <button type="button" class="btn btn-soft-secondary btn-sm">Todos</button>
                        <?php $__currentLoopData = $municipalityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="btn btn-soft-secondary btn-sm" 
                                data-municipality="<?php echo e($municipality->id); ?>">
                            <?php echo e($municipality->name); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="card-header p-0 border-0 bg-light-subtle">
                    <div class="row g-0 text-center">
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="<?php echo e($totalVotes); ?>"><?php echo e($totalVotes); ?></span></h5>
                                <p class="text-muted mb-0">Total de Votos</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="<?php echo e($totalTables); ?>"><?php echo e($totalTables); ?></span></h5>
                                <p class="text-muted mb-0">Mesas Totales</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0">
                                <h5 class="mb-1"><span class="counter-value" data-target="<?php echo e($reportedTables); ?>"><?php echo e($reportedTables); ?></span></h5>
                                <p class="text-muted mb-0">Mesas Reportadas</p>
                            </div>
                        </div>
                        <div class="col-6 col-sm-3">
                            <div class="p-3 border border-dashed border-start-0 border-end-0">
                                <h5 class="mb-1 text-success"><span class="counter-value" data-target="<?php echo e($progressPercentage); ?>"><?php echo e($progressPercentage); ?></span>%</h5>
                                <p class="text-muted mb-0">Avance</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 pb-2">
                    <div>
                        <div id="projects-overview-chart" dir="ltr" ></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
    <div class="card card-height-100">
        <div class="card-header align-items-center d-flex">
            <h4 class="card-title mb-0 flex-grow-1">Votos por Municipalidades</h4>
            <div class="flex-shrink-0">
                <button type="button" class="btn btn-soft-primary btn-sm" id="exportMapData">
                    Exportar
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Add a wrapper div with explicit dimensions -->
            <div style="height: 269px; position: relative;">
                <div id="votes-by-locations" 
                     data-colors='["--vz-light", "--vz-success", "--vz-primary"]'
                     style="height: 100%; width: 100%;" 
                     dir="ltr"></div>
            </div>
            <div class="px-2 py-2 mt-1">
                <!-- Your existing progress bars -->
                <?php $__currentLoopData = $municipalityStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $municipalityData = $municipalityResults[$municipality->id] ?? null;
                    $progress = $municipality->total_tables > 0 
                    ? round(($municipality->reported_tables / $municipality->total_tables) * 100, 1)
                    : 0;
                ?>
                <p class="mb-1"><?php echo e($municipality->name); ?> 
                <span class="float-end"><?php echo e($progress); ?>%</span></p>                                    
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" 
                        style="width: <?php echo e($progress); ?>%;" aria-valuenow="<?php echo e($progress); ?>" 
                        aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</div>

    </div>    
<?php $__env->startSection('dashboard-scripts'); ?>
    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/jsvectormap/maps/quillacollo-merc.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const candidateNames = <?php echo json_encode($presidentialCandidates->pluck('name'), 15, 512) ?>;
            const candidateColors = <?php echo json_encode($presidentialCandidates->pluck('color'), 15, 512) ?>;
            const candidateVotes = <?php echo json_encode($presidentialCandidates->map(function($candidate) use ($candidateStats) {
                return $candidateStats[$candidate->id]['votes'] ?? 0;
            }), 15, 512) ?>;
            const candidatePercentages = <?php echo json_encode($presidentialCandidates->map(function($candidate) use ($candidateStats) {
                return $candidateStats[$candidate->id]['percentage'] ?? 0;
            }), 15, 512) ?>;            
            const candidateParties = <?php echo json_encode($presidentialCandidates->pluck('party'), 15, 512) ?>;
            const candidatePhotos = <?php echo json_encode($presidentialCandidates->map(function($candidate) {
                return $candidate->photo ? asset('storage/' . $candidate->photo) : '';
            }), 15, 512) ?>;

            var candidateOptions = {
                series: [{
                    name: 'Votos',
                    data: candidateVotes
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        distributed: true,
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: candidateNames,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                colors: candidateColors,
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val.toLocaleString() + " votos";
                        }
                    }
                }
            };
            var candidateChart = new ApexCharts(document.querySelector("#candidates_chart"), candidateOptions);
            candidateChart.render();
            
            var partyOptions = {
                tooltip: {
                    trigger: 'item'
                },
                series: candidateVotes,
                labels: candidateNames,
                colors:candidateColors,
                chart: {
                    type: 'donut',
                    height: 300,
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '50%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total Votos',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: {
                    enabled: false,
                    formatter: function (val, opts) {
                        return opts.w.globals.labels[opts.seriesIndex] + ': ' + val.toFixed(1) + '%';
                    },
                    dropShadow: {
                        enabled: true
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 200
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            var partyChart = new ApexCharts(document.querySelector("#party_distribution_chart"), partyOptions);
            partyChart.render();


            const municipalityResults = <?php echo json_encode($municipalityResults, 15, 512) ?>;
            const municipalities = Object.values(municipalityResults).map(m => m.name);
            const series = candidateNames.map(name => {
                return {
                    name: name,
                    type: 'bar',
                    data: Object.values(municipalityResults).map(m => {
                        const candidate = (m.candidates || []).find(c => c.name === name);
                        return candidate ? candidate.votes : 0;
                    })
                };
            });

            var options = {
                    series: series,
                    chart: {
                        type: 'bar',
                        stacked: false 
                    },
                    stroke: {
                        curve: 'smooth',
                    },
                    // fill: {
                    //     opacity: [1, 0.1, 1]
                    // },
                    markers: {
                        size: [0, 4, 0],
                        strokeWidth: 2,
                        hover: {
                            size: 4,
                        }
                    },
                    xaxis: {
                        categories: municipalities,
                        axisTicks: {
                            show: false
                        },
                        axisBorder: {
                            show: false
                        }
                    },
                    grid: {
                        show: true,
                        xaxis: {
                            lines: {
                                show: true,
                            }
                        },
                        yaxis: {
                            lines: {
                                show: false,
                            }
                        },
                        padding: {
                            top: 0,
                            right: -2,
                            bottom: 15,
                            left: 10
                        },
                    },
                    legend: {
                        show: true,
                        horizontalAlign: 'center',
                        offsetX: 0,
                        offsetY: -5,
                        markers: {
                            width: 9,
                            height: 9,
                            radius: 6,
                        },
                        itemMargin: {
                            horizontal: 10,
                            vertical: 0
                        },
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '90%',
                            barHeight: '50%'
                        }
                    },
                    colors: candidateColors,
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (val) {
                            return val + " votos";
                            }
                        }
                    }
            };
            var chart = new ApexCharts(document.querySelector("#projects-overview-chart"), options);
            chart.render();                

            const markers = Object.values(municipalityResults).map(m => {
                return {
                    name: m.name + " (" + m.total_votes + " votos)",
                    coords: [m.latitude, m.longitude],
                    votes: m.total_votes,
                    candidates: m.candidates
                };
            });

            var vectorMapColors = getChartColorsArray("votes-by-locations");
            if(vectorMapColors){
                document.getElementById("votes-by-locations").innerHTML = "";
                var boliviaMap = new jsVectorMap({
                    map: "quillacollo_merc",
                    selector: "#votes-by-locations",
                    zoomOnScroll: true,
                    zoomButtons: true,

                    selectedMarkers: [0, 5],
                    regionStyle: {
                        initial: {
                            stroke: "#9599ad",
                            strokeWidth: 0.25,
                            fill: vectorMapColors[0],
                            fillOpacity: 1,
                        },
                    },
                    markersSelectable: true,

                    markers: markers,
                    markerStyle: {
                        initial: {
                            fill: vectorMapColors[1],
                        },
                        hover: {
                            fill: vectorMapColors[2],
                        },
                        selected: {
                            fill: vectorMapColors[2],
                        },
                    },
                    labels: {
                        markers: {
                            render: function(marker) {
                                return marker.name;
                            }
                        }
                    },
                    onMarkerClick: function(event, index) {
                        const m = markers[index];
                        const popup = document.createElement('div');
                        popup.className = 'custom-map-popup';
                        popup.innerHTML = `
                            <div class="popup-header">
                                <h5>${m.name}</h5>
                                <button type="button" class="btn-close" aria-label="Close"></button>
                            </div>
                            <div class="popup-body">
                                <p><strong>Total votes:</strong> ${m.votes.toLocaleString()}</p>
                                <h6>Candidate Results:</h6>
                                <ul class="list-group">
                                    ${m.candidates.map(c => `
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            ${c.name} (${c.party})
                                            <span class="badge bg-primary rounded-pill">
                                                ${c.votes.toLocaleString()} (${c.percentage}%)
                                            </span>
                                        </li>
                                    `).join('')}
                                </ul>
                            </div>
                        `;
                        if (!document.querySelector('#map-popup-styles')) {
                            const styles = document.createElement('style');
                            styles.id = 'map-popup-styles';
                            styles.textContent = `
                                .custom-map-popup {
                                    position: absolute;
                                    top: 50%;
                                    left: 50%;
                                    transform: translate(-50%, -50%);
                                    background: white;
                                    border-radius: 8px;
                                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                                    z-index: 1000;
                                    width: 320px;
                                    max-width: 90vw;
                                }
                                .popup-header {
                                    padding: 12px 16px;
                                    border-bottom: 1px solid #dee2e6;
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                }
                                .popup-body {
                                    padding: 16px;
                                    max-height: 60vh;
                                    overflow-y: auto;
                                }
                            `;
                            document.head.appendChild(styles);
                        }
                        popup.querySelector('.btn-close').addEventListener('click', function() {
                            document.body.removeChild(popup);
                        });
                        document.body.appendChild(popup);
                        popup.addEventListener('click', function(e) {
                            if (e.target === this) {
                                document.body.removeChild(popup);
                            }
                        });
                    }
                });
            }
                         
            function getChartColorsArray(chartId) {
                if (document.getElementById(chartId) !== null) {
                    const colorAttr = "data-colors" + ("-" + document.documentElement.getAttribute("data-theme") ?? "");
                    var colors = document.getElementById(chartId).getAttribute(colorAttr) ?? document.getElementById(chartId).getAttribute("data-colors");
                    if (colors) {
                        colors = JSON.parse(colors);
                        return colors.map(function (value) {
                            var newValue = value.replace(" ", "");
                            if (newValue.indexOf(",") === -1) {
                                var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                                if (color) return color;
                                else return newValue;;
                            } else {
                                var val = value.split(',');
                                if (val.length == 2) {
                                    var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                                    rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                                    return rgbaColor;
                                } else {
                                    return newValue;
                                }
                            }
                        });
                    } else {
                        console.warn('data-colors attributes not found on', chartId);
                    }
                }
            }
            document.getElementById('exportMunicipalityTable').addEventListener('click', function() {
                const table = document.getElementById('municipality-table');
                let csvContent = "data:text/csv;charset=utf-8,";
                const headers = [];
                table.querySelectorAll('thead th').forEach(header => {
                    headers.push(header.textContent.trim());
                });
                csvContent += headers.join(',') + '\r\n';
                table.querySelectorAll('tbody tr').forEach(row => {
                    const rowData = [];
                    row.querySelectorAll('td').forEach(cell => {
                        let text = cell.textContent.trim();
                        if (text.includes('\n')) {
                            text = text.split('\n').join('; ');
                        }
                        rowData.push('"' + text + '"');
                    });
                    csvContent += rowData.join(',') + '\r\n';
                });
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement('a');
                link.setAttribute('href', encodedUri);
                link.setAttribute('download', 'municipality_results.csv');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
<?php $__env->stopSection(); ?><?php /**PATH D:\_Mine\corporate\resources\views/partials/dashboard-content.blade.php ENDPATH**/ ?>