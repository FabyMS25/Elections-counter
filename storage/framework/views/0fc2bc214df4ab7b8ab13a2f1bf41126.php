<?php
    $totalCandidates   = $candidates->total();
    $byCategory        = $stats['byCategory'];
    $byDepartment      = $stats['byDepartment'];
    $byElectionType    = $stats['byElectionType'];
    $geo               = $stats['geo'];
?>
<?php if($byCategory->isNotEmpty()): ?>
<div class="row">
    <div class="col-12">
        <div class="card mb-0">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">Candidatos por Tipo de Elección y Categoría</h6>
            </div>
            <div class="card-body p-0 px-1">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tipo de Elección</th>
                                <th>Categoría</th>
                                <th>Código</th>
                                <th class="text-center">Franja</th>
                                <th class="text-center">Votos/Persona</th>
                                <th class="text-center">Candidatos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $byCategory->sortByDesc('total'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->electionTypeCategory?->electionType?->name ?? 'N/A'); ?></td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary">
                                        <?php echo e($item->electionTypeCategory?->electionCategory?->name ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td><code><?php echo e($item->electionTypeCategory?->electionCategory?->code ?? '–'); ?></code></td>
                                <td class="text-center"><?php echo e($item->electionTypeCategory?->ballot_order ?? '–'); ?></td>
                                <td class="text-center"><?php echo e($item->electionTypeCategory?->votes_per_person ?? 1); ?></td>
                                <td class="text-center"><span class="badge bg-info"><?php echo e($item->total); ?></span></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if($byDepartment->isNotEmpty()): ?>
<div class="row mt-2">
    <div class="col-md-6">
        <div class="card mb-0">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">Por Departamento</h6>
            </div>
            <div class="card-body p-0 px-1">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr><th>Departamento</th><th class="text-center">Candidatos</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $byDepartment->sortByDesc('total'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item->department?->name ?? 'Sin departamento'); ?></td>
                                <td class="text-center"><span class="badge bg-success"><?php echo e($item->total); ?></span></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-0">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">Por Ámbito Geográfico</h6>
            </div>
            <div class="card-body p-0 px-1">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr><th>Ámbito</th><th class="text-center">Candidatos</th></tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = ['nacional' => ['label'=>'Nacional','color'=>'primary'], 'departamental' => ['label'=>'Departamental','color'=>'info'], 'provincial' => ['label'=>'Provincial','color'=>'warning'], 'municipal' => ['label'=>'Municipal','color'=>'success']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $cfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($cfg['label']); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?php echo e($cfg['color']); ?>"><?php echo e($geo[$key]); ?></span>
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
<?php endif; ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\candidates\partials\stats-cards.blade.php ENDPATH**/ ?>