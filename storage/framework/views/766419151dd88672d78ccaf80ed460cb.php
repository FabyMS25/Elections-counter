<?php $__env->startSection('title'); ?> Registro de Votos <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet"/>
<style>
.stat-card{background:#fff;border:1px solid #e9e9ef;border-radius:.5rem;padding:.75rem 1.1rem;display:flex;align-items:center;gap:.9rem}
.stat-card .icon{width:50px;height:50px;border-radius:.4rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
.stat-card .num{font-size:1.4rem;font-weight:700;line-height:1}
.stat-card .lbl{font-size:.72rem;color:#74788d}
.stats-toggle{cursor:pointer;user-select:none}
.stats-toggle i{transition:transform .3s}
.stats-toggle.collapsed i{transform:rotate(-90deg)}

.table-card { border-radius: .5rem; margin-bottom: 1.25rem !important; }
.table-card.status-observada    { border-left: 3px solid #f06548; }
.table-card.status-en_escrutinio{ border-left: 3px solid #f7b84b; }
.table-card.status-escrutada    { border-left: 3px solid #0ab39c; }
.table-card.status-transmitida  { border-left: 3px solid #405189; }
.table-card.status-anulada      { border-left: 3px solid #212529; opacity:.85; }
.table-card.status-votacion     { border-left: 3px solid #299cdb; }

.vote-input{width:70px;text-align:center;padding:2px 4px;font-size:.85rem}
.vote-input:focus{box-shadow:0 0 0 2px rgba(64,81,137,.2)}

.ballot-input{width:90px!important;height:32px;padding:2px 6px;font-size:.9rem;font-weight:700;
              border:1.5px solid #ced4da;border-radius:4px;text-align:center}
.ballot-input:focus{outline:none;border-color:#405189;box-shadow:0 0 0 2px rgba(64,81,137,.15)}
.ballot-data-section{font-size:.82rem}
.ballot-label{font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;
              color:#6c757d;display:flex;align-items:center;gap:3px;flex-wrap:wrap}
.ballot-value{font-weight:700;font-size:1.05rem;line-height:1.2}
.ballot-hint {font-size:.62rem;color:#9ca3af;line-height:1.4}
.badge-auto    {display:inline-block;font-size:.55rem;padding:1px 4px;background:#e9ecef;color:#6c757d;border-radius:3px;font-weight:600}
.badge-readonly{display:inline-block;font-size:.55rem;padding:1px 4px;background:#cff4fc;color:#055160;border-radius:3px;font-weight:600}
.badge-optional{display:inline-block;font-size:.55rem;padding:1px 4px;background:#f8d7da;color:#842029;border-radius:3px;font-weight:600}
.badge-input-lbl{display:inline-block;font-size:.55rem;padding:1px 4px;background:#fff3cd;color:#664d03;border-radius:3px;font-weight:600}
.ballot-formula{font-size:.73rem;color:#6c757d;padding:5px 10px;background:#f8f9fa;
                border-radius:4px;border:1px solid #e9ecef;display:flex;flex-wrap:wrap;align-items:center;gap:3px}
.badge-balance     {font-size:.68rem;padding:4px 8px;border-radius:4px;border:1px solid;white-space:normal;line-height:1.4}
.badge-balance-ok  {background:#d1e7dd;color:#0f5132;border-color:#a3cfbb}
.badge-balance-warn{background:#fff3cd;color:#664d03;border-color:#ffe69c}
.badge-balance-err {background:#f8d7da;color:#842029;border-color:#f5c2c7}

/* ── Quick actions sticky bar ───────────────────────────────────────────────── */
.quick-actions{position:sticky;bottom:16px;z-index:900}
.quick-actions .card{backdrop-filter:blur(8px);background-color:rgba(255,255,255,.96)!important;
                     border-top:2px solid #e2e8f0!important}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Elecciones <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> <?php echo e($electionType->name ?? 'Registro de Votos'); ?> <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <?php if(isset($electionType)): ?>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:.78rem">
            <i class="ri-calendar-line me-1"></i><?php echo e($electionType->name); ?>

            — <?php echo e(\Carbon\Carbon::parse($electionType->election_date)->format('d/m/Y')); ?>

        </span>
        <?php endif; ?>
    </div>
    <button class="btn btn-sm btn-light stats-toggle" id="statsToggle" onclick="toggleStats()">
        <i class="ri-arrow-down-s-line me-1"></i>
        <span id="statsToggleLabel">Mostrar estadísticas</span>
    </button>
</div>

<div id="statsContainer" class="d-none">
    <div class="row g-3 mb-2">
        <div class="col-6 col-md-3 col-xl-2">
            <div class="stat-card">
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="ri-table-line"></i></div>
                <div><div class="num"><?php echo e(number_format($tableStats['total'] ?? 0)); ?></div><div class="lbl">Mesas</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="stat-card">
                <div class="icon bg-info bg-opacity-10 text-info"><i class="ri-group-line"></i></div>
                <div><div class="num"><?php echo e(number_format($totals['expected'] ?? 0)); ?></div><div class="lbl">Habilitados</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="stat-card">
                <div class="icon bg-success bg-opacity-10 text-success"><i class="ri-inbox-line"></i></div>
                <div><div class="num"><?php echo e(number_format($totals['total'] ?? 0)); ?></div><div class="lbl">En ánfora</div></div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="stat-card">
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="ri-percent-line"></i></div>
                <div><div class="num"><?php echo e($totals['participation'] ?? 0); ?>%</div><div class="lbl">Participación</div></div>
            </div>
        </div>
        <?php $catColorsMap = ['ALC'=>'success','CON'=>'primary','GOB'=>'warning','AST'=>'info','ASP'=>'danger']; ?>
        <?php $__currentLoopData = $typeCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $code = $tc->electionCategory->code; $c = $catColorsMap[$code] ?? 'secondary'; ?>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="stat-card">
                <div class="icon bg-<?php echo e($c); ?> bg-opacity-10 text-<?php echo e($c); ?>"><i class="ri-bar-chart-line"></i></div>
                <div>
                    <div class="num"><?php echo e(number_format($totals['by_category'][$code] ?? 0)); ?></div>
                    <div class="lbl"><?php echo e($tc->electionCategory->name); ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = [
            'configurada'   => ['secondary','ri-settings-4-line'],
            'votacion'      => ['primary','ri-vote-line'],
            'en_escrutinio' => ['warning','ri-bar-chart-2-line'],
            'observada'     => ['danger','ri-alert-line'],
            'escrutada'     => ['success','ri-check-double-line'],
            'transmitida'   => ['success','ri-cloud-line'],
            'anulada'       => ['dark','ri-forbid-line'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s => [$sc, $si]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(($tableStats[$s] ?? 0) > 0): ?>
        <div class="col-6 col-md-3 col-xl-2">
            <div class="stat-card">
                <div class="icon bg-<?php echo e($sc); ?> bg-opacity-10 text-<?php echo e($sc); ?>"><i class="<?php echo e($si); ?>"></i></div>
                <div>
                    <div class="num"><?php echo e($tableStats[$s]); ?></div>
                    <div class="lbl"><?php echo e($statusLabels[$s] ?? $s); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php echo $__env->make('voting-table-votes.partials.quick-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<div class="row">
    <?php echo $__env->make('voting-table-votes.partials.filters', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $votingTables->items(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('voting-table-votes.partials.table', [
                'table'                => $table,
                'candidatesByCategory' => $candidatesByCategory,
                'statusLabels'         => $statusLabels,
                'validationLabels'     => $validationLabels,
                'permissions'          => $permissions,
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="card">
                <div class="card-body text-center">
                    <i class="ri-table-line d-block mb-2 text-muted" style="font-size:3rem"></i>
                    <h6 class="text-muted">No hay mesas disponibles</h6>
                    <p class="text-muted small mb-2">No se encontraron mesas con los filtros seleccionados.</p>
                    <?php if(request()->hasAny(['institution_id','status','table_number','table_code'])): ?>
                        <a href="<?php echo e(route('voting-table-votes.index', ['election_type_id' => $electionTypeId])); ?>"
                           class="btn btn-sm btn-outline-secondary">
                            <i class="ri-close-line me-1"></i>Limpiar filtros
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
</div>
<?php if($votingTables->hasPages()): ?>
<div class="card">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <small class="text-muted">
            Mostrando <?php echo e($votingTables->firstItem() ?? 0); ?>–<?php echo e($votingTables->lastItem() ?? 0); ?>

            de <?php echo e($votingTables->total()); ?> mesas
        </small>
        <?php echo e($votingTables->links()); ?>

    </div>
</div>
<?php endif; ?>
<?php echo $__env->make('voting-table-votes.partials.quick-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('voting-table-votes.partials.modals.observation-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('voting-table-votes.partials.modals.upload-acta-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('voting-table-votes.partials.modals.view-actas-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<script>
window.electionTypeId  = <?php echo e($electionTypeId ?? 'null'); ?>;
window.userPermissions = {
    register:   <?php echo e(($permissions['can_register']    ?? false) ? 'true' : 'false'); ?>,
    review:     <?php echo e(($permissions['can_review']      ?? false) ? 'true' : 'false'); ?>,
    correct:    <?php echo e(($permissions['can_correct']     ?? false) ? 'true' : 'false'); ?>,
    validate:   <?php echo e(($permissions['can_validate']    ?? false) ? 'true' : 'false'); ?>,
    observe:    <?php echo e(($permissions['can_observe']     ?? false) ? 'true' : 'false'); ?>,
    uploadActa: <?php echo e(($permissions['can_upload_acta'] ?? false) ? 'true' : 'false'); ?>,
    reopen:     <?php echo e(($permissions['can_reopen']      ?? false) ? 'true' : 'false'); ?>,
};
</script>
<?php echo $__env->make('voting-table-votes.scripts.votes-table-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('voting-table-votes.scripts.observations-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('voting-table-votes.scripts.observations-by-vote-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('voting-table-votes.scripts.view-toggle-js', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script>
function toggleStats() {
    const c = document.getElementById('statsContainer');
    const b = document.getElementById('statsToggle');
    const l = document.getElementById('statsToggleLabel');
    const hidden = c.classList.contains('d-none');
    c.classList.toggle('d-none', !hidden);
    b.classList.toggle('collapsed', !hidden);
    b.querySelector('i').className = hidden ? 'ri-arrow-down-s-line me-1' : 'ri-arrow-right-s-line me-1';
    l.textContent = hidden ? 'Ocultar estadísticas' : 'Mostrar estadísticas';
    localStorage.setItem('voteStatsVisible', String(hidden));
}
document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('voteStatsVisible') === 'true') {
        const c = document.getElementById('statsContainer');
        const b = document.getElementById('statsToggle');
        const l = document.getElementById('statsToggleLabel');
        if (c) c.classList.remove('d-none');
        if (b) { b.classList.remove('collapsed'); b.querySelector('i').className = 'ri-arrow-down-s-line me-1'; }
        if (l) l.textContent = 'Ocultar estadísticas';
    }
    if (typeof window.initVoteListeners        === 'function') window.initVoteListeners();
    if (typeof window.initObservationListeners === 'function') window.initObservationListeners();
    if (typeof window.initViewToggle           === 'function') window.initViewToggle();
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el =>
        new bootstrap.Popover(el, { sanitize: false })
    );
});
setTimeout(() => document.querySelectorAll('.alert-dismissible').forEach(a =>
    bootstrap.Alert.getOrCreateInstance(a)?.close()), 5000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\voting-table-votes\index.blade.php ENDPATH**/ ?>