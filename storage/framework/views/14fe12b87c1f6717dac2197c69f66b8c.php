
<div class="card">
    <div class="card-body py-2 px-2">
        <form method="GET" action="<?php echo e(route('voting-table-votes.index')); ?>" id="filterForm">
            <div class="row g-2 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small mb-1">
                        <i class="ri-building-2-line me-1 text-muted"></i>Recinto
                    </label>
                    <select name="institution_id" class="form-select form-select-sm" id="institutionFilter">
                        <option value="">Todos los recintos</option>
                        <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($inst->id); ?>"
                                <?php echo e(request('institution_id') == $inst->id ? 'selected' : ''); ?>>
                                <?php echo e($inst->name); ?> (<?php echo e($inst->code); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small mb-1">
                        <i class="ri-vote-line me-1 text-muted"></i>Tipo de Elección
                    </label>
                    <select name="election_type_id" class="form-select form-select-sm" id="electionTypeFilter">
                        <?php $__currentLoopData = $electionTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>"
                                <?php echo e(($electionTypeId ?? '') == $type->id ? 'selected' : ''); ?>>
                                <?php echo e($type->name); ?>

                                (<?php echo e(\Carbon\Carbon::parse($type->election_date)->format('d/m/Y')); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">
                        <i class="ri-flag-line me-1 text-muted"></i>Estado
                    </label>
                    <?php
                        $statusOptions = [
                            'configurada'   => ['label'=>'Configurada',  'icon'=>'⚙️'],
                            'en_espera'     => ['label'=>'En Espera',    'icon'=>'⏳'],
                            'votacion'      => ['label'=>'En Votación',  'icon'=>'🗳️'],
                            'en_escrutinio' => ['label'=>'Escrutinio',   'icon'=>'📊'],
                            'escrutada'     => ['label'=>'Escrutada',    'icon'=>'✅'],
                            'observada'     => ['label'=>'Observada',    'icon'=>'⚠️'],
                            'transmitida'   => ['label'=>'Transmitida',  'icon'=>'📡'],
                            'anulada'       => ['label'=>'Anulada',      'icon'=>'❌'],
                        ];
                    ?>
                    <select name="status" class="form-select form-select-sm" id="statusFilter">
                        <option value="">Todos los estados</option>
                        <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($val); ?>"
                                <?php echo e(request('status') === $val ? 'selected' : ''); ?>>
                                <?php echo e($opt['icon']); ?> <?php echo e($opt['label']); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">
                        <i class="ri-hashtag me-1 text-muted"></i>N° Mesa
                    </label>
                    <input type="number" name="table_number" class="form-control form-control-sm"
                           placeholder="Ej: 1, 2…" min="1" value="<?php echo e(request('table_number')); ?>">
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    <?php if(request()->hasAny(['institution_id','status','table_number','table_code','table_type','from_name','to_name','min_votes','max_votes','has_observations'])): ?>
                        <a href="<?php echo e(route('voting-table-votes.index', ['election_type_id' => request('election_type_id')])); ?>"
                           class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                            <i class="ri-close-line"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
                $hasActive = request()->hasAny(['institution_id','status','table_number','table_code','table_type','has_observations','min_votes','max_votes']);
            ?>
            <?php if($hasActive): ?>
            <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted" style="font-size:.78rem">Filtros activos:</span>

                <?php if(request('institution_id') && ($inst = $institutions->find(request('institution_id')))): ?>
                <span class="badge bg-primary d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-building-2-line"></i> <?php echo e($inst->name); ?>

                    <a href="<?php echo e(route('voting-table-votes.index', request()->except(['institution_id']))); ?>"
                       class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                <?php endif; ?>

                <?php if(request('status') && isset($statusOptions[request('status')])): ?>
                <span class="badge bg-secondary d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <?php echo e($statusOptions[request('status')]['icon']); ?> <?php echo e($statusOptions[request('status')]['label']); ?>

                    <a href="<?php echo e(route('voting-table-votes.index', request()->except(['status']))); ?>"
                       class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                <?php endif; ?>

                <?php if(request('table_number')): ?>
                <span class="badge bg-info d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-hashtag"></i> Mesa <?php echo e(request('table_number')); ?>

                    <a href="<?php echo e(route('voting-table-votes.index', request()->except(['table_number']))); ?>"
                       class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                <?php endif; ?>

                <?php if(request('table_code')): ?>
                <span class="badge bg-info d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-barcode-line"></i> <?php echo e(request('table_code')); ?>

                    <a href="<?php echo e(route('voting-table-votes.index', request()->except(['table_code']))); ?>"
                       class="text-white ms-1"><i class="ri-close-line"></i></a>
                </span>
                <?php endif; ?>

                <?php if(request('has_observations') !== null && request('has_observations') !== ''): ?>
                <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1" style="font-size:.75rem">
                    <i class="ri-alert-line"></i>
                    <?php echo e(request('has_observations') == '1' ? 'Con observaciones' : 'Sin observaciones'); ?>

                    <a href="<?php echo e(route('voting-table-votes.index', request()->except(['has_observations']))); ?>"
                       class="text-dark ms-1"><i class="ri-close-line"></i></a>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php
                $advancedKeys   = ['table_code','from_name','to_name','table_type','min_votes','max_votes','has_observations','sort_by'];
                $advancedActive = collect($advancedKeys)->filter(fn($k) => request($k))->count();
            ?>
            <div class="mt-2">
                <a class="text-muted small text-decoration-none" data-bs-toggle="collapse"
                   href="#advancedFilters" role="button"
                   aria-expanded="<?php echo e($advancedActive > 0 ? 'true' : 'false'); ?>">
                    <i class="ri-equalizer-line me-1"></i>Filtros avanzados
                    <?php if($advancedActive > 0): ?>
                        <span class="badge bg-primary rounded-pill ms-1" style="font-size:.65rem"><?php echo e($advancedActive); ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="collapse <?php echo e($advancedActive > 0 ? 'show' : ''); ?>" id="advancedFilters">
                <div class="border rounded p-3 mt-2 bg-light">
                    <div class="row g-2">
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Código Mesa</label>
                            <input type="text" name="table_code" class="form-control form-control-sm"
                                   placeholder="OEP o interno" value="<?php echo e(request('table_code')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Tipo de Mesa</label>
                            <select name="table_type" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="mixta"     <?php echo e(request('table_type') == 'mixta'     ? 'selected' : ''); ?>>Mixta</option>
                                <option value="masculina" <?php echo e(request('table_type') == 'masculina' ? 'selected' : ''); ?>>Masculina</option>
                                <option value="femenina"  <?php echo e(request('table_type') == 'femenina'  ? 'selected' : ''); ?>>Femenina</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Apellido desde</label>
                            <input type="text" name="from_name" class="form-control form-control-sm"
                                   placeholder="Apellido inicial" value="<?php echo e(request('from_name')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Apellido hasta</label>
                            <input type="text" name="to_name" class="form-control form-control-sm"
                                   placeholder="Apellido final" value="<?php echo e(request('to_name')); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small mb-1">Observaciones</label>
                            <select name="has_observations" class="form-select form-select-sm">
                                <option value="">Todas</option>
                                <option value="1" <?php echo e(request('has_observations') == '1' ? 'selected' : ''); ?>>Con obs.</option>
                                <option value="0" <?php echo e(request('has_observations') == '0' ? 'selected' : ''); ?>>Sin obs.</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small mb-1">Votos mín.</label>
                            <input type="number" name="min_votes" class="form-control form-control-sm"
                                   placeholder="0" min="0" value="<?php echo e(request('min_votes')); ?>">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label small mb-1">Votos máx.</label>
                            <input type="number" name="max_votes" class="form-control form-control-sm"
                                   placeholder="∞" min="0" value="<?php echo e(request('max_votes')); ?>">
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('electionTypeFilter')?.addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
    document.getElementById('institutionFilter')?.addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
    document.getElementById('statusFilter')?.addEventListener('change', function () {
        document.getElementById('filterForm').submit();
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views/voting-table-votes/partials/filters.blade.php ENDPATH**/ ?>