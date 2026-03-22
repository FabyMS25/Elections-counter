    <div class="row">

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Resultados Detallados por Municipio</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Municipio</th>
                                    <th>Mesas</th>
                                    <th>Reportadas</th>
                                    <th>Avance</th>
                                    <?php $__currentLoopData = $presidentialCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <th><?php echo e($candidate->party); ?></th>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $municipalityResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><strong><?php echo e($municipality['name']); ?></strong></td>
                                    <td><?php echo e($municipalityStats->firstWhere('id', $municipalityId)->total_tables ?? 0); ?></td>
                                    <td><?php echo e($municipalityStats->firstWhere('id', $municipalityId)->reported_tables ?? 0); ?></td>
                                    <td>
                                        <?php
                                            $total = $municipalityStats->firstWhere('id', $municipalityId)->total_tables ?? 1;
                                            $reported = $municipalityStats->firstWhere('id', $municipalityId)->reported_tables ?? 0;
                                            $progress = $total > 0 ? ($reported / $total) * 100 : 0;
                                        ?>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: <?php echo e($progress); ?>%">
                                                <?php echo e(round($progress)); ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <?php $__currentLoopData = $presidentialCandidates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $candidateVotes = collect($municipality['candidates'])
                                                ->firstWhere('id', $candidate->id);
                                            $votes = $candidateVotes['votes'] ?? 0;
                                            $percentage = $municipality['total_votes'] > 0 
                                                ? round(($votes / $municipality['total_votes']) * 100, 1)
                                                : 0;
                                        ?>
                                        <td>
                                            <?php echo e(number_format($votes)); ?><br>
                                            <small class="text-muted"><?php echo e($percentage); ?>%</small>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card card-height-100">
                <div class="card-header align-items-center d-flex">
                    <h4 class="card-title mb-0 flex-grow-1">Resultados por Municipio</h4>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-soft-primary btn-sm">
                            Exportar Reporte
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <?php $__currentLoopData = $municipalityResults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <h6 class="mt-3 mb-2 text-muted"><?php echo e($municipality['name']); ?></h6>
                        <p class="small text-muted mb-1">Total votos: <?php echo e(number_format($municipality['total_votes'])); ?></p>
                        
                        <?php $__currentLoopData = $municipality['candidates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <p class="mb-1">
                                <?php echo e($candidate['name']); ?> (<?php echo e($candidate['party']); ?>)
                                <span class="float-end"><?php echo e($candidate['percentage']); ?>%</span>
                            </p>
                            <div class="progress mt-2 mb-3" style="height: 6px;">
                                <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                    style="width: <?php echo e($candidate['percentage']); ?>%" 
                                    aria-valuenow="<?php echo e($candidate['percentage']); ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <hr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if(empty($municipalityResults)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="ri-information-line ri-2x"></i>
                            <p class="mt-2">No hay datos disponibles</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
<?php $__env->startSection('dashboard-scripts'); ?>
    <!-- apexcharts -->
    <script src="<?php echo e(URL::asset('build/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/jsvectormap/maps/world-merc.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.js')); ?>"></script>
    <!-- dashboard init -->
    <script src="<?php echo e(URL::asset('build/js/pages/dashboard-ecommerce.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/app.js')); ?>"></script>

    
    <script src="<?php echo e(URL::asset('build/js/pages/dashboard-projects.init.js')); ?>"></script>

    
    <script src="<?php echo e(URL::asset('build/libs/chart.js/chart.umd.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/chartjs.init.js')); ?>"></script>

    
    <script src="<?php echo e(URL::asset('build/libs/echarts/echarts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/echarts.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('build/libs/jquery/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>

<?php $__env->stopSection(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\components\municipality-results.blade.php ENDPATH**/ ?>