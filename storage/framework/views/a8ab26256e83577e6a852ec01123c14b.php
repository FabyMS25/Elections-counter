
<?php if(isset($institutionProgress) && count($institutionProgress) > 0): ?>
<div class="card border-0 shadow-sm mb-2">
    <div class="card-header bg-white border-bottom">
        <div class="row align-items-center g-2">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle" style="width: 36px; height: 36px;">
                        <i class="ri-building-line fs-5"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0">Avance por Recinto</h5>
                        <p class="text-muted small mb-0"><?php echo e(count($institutionProgress)); ?> recintos · <?php echo e(collect($institutionProgress)->where('pending_tables','>',0)->count()); ?> con pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                    <div class="input-group" style="width: 220px;">
                        <span class="input-group-text bg-white border-end-0"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" id="institutionSearch" class="form-control border-start-0 ps-0" placeholder="Buscar recinto...">
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filterInstitutions('all')">Todos</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterInstitutions('pending')">Pendientes</button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="filterInstitutions('complete')">Completos</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleExpandAll()" id="expandAllBtn">
                        <i class="ri-add-line me-1"></i>Expandir
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div id="institutionAccordion" style="max-height: 550px; overflow-y: auto;">
            <?php
                $grouped = collect($institutionProgress)->groupBy('district');
            ?>            
            <?php $__currentLoopData = $grouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $districtName => $districtInstitutions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $districtId = Str::slug($districtName);
                    $districtTotalTables = $districtInstitutions->sum('total_tables');
                    $districtReportedTables = $districtInstitutions->sum('reported_tables');
                    $districtProgress = $districtTotalTables > 0 ? round(($districtReportedTables / $districtTotalTables) * 100, 1) : 0;
                    $districtProgressClass = $districtProgress >= 75 ? 'success' : ($districtProgress >= 50 ? 'warning' : 'danger');
                ?>
                <div class="border-bottom">
                    <div class="px-3 py-2 bg-light d-flex justify-content-between align-items-center" 
                         style="cursor: pointer;" onclick="toggleDistrict('<?php echo e($districtId); ?>')">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-arrow-right-s-line fs-6" id="icon-<?php echo e($districtId); ?>"></i>
                            <span class="fw-semibold"><?php echo e($districtName); ?></span>
                            <span class="badge bg-secondary"><?php echo e($districtInstitutions->count()); ?> recintos</span>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="small text-muted"><?php echo e($districtReportedTables); ?>/<?php echo e($districtTotalTables); ?> mesas</span>
                            <span class="fw-bold text-<?php echo e($districtProgressClass); ?>"><?php echo e($districtProgress); ?>%</span>
                        </div>
                    </div>
                    <div id="district-<?php echo e($districtId); ?>" class="district-content" style="display: none;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="small">
                                    <th style="padding-left: 2rem;">Recinto</th>
                                    <th>Localidad</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Reg.</th>
                                    <th class="text-center">Val.</th>
                                    <th class="text-center">Pend.</th>
                                    <th style="width: 12%;">Progreso</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $districtInstitutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $progressColor = $inst['progress'] >= 75 ? 'success' : ($inst['progress'] >= 50 ? 'warning' : ($inst['progress'] > 0 ? 'info' : 'secondary'));
                                        $rowClass = $inst['pending_tables'] > 0 ? 'table-warning' : '';
                                    ?>
                                    <tr class="<?php echo e($rowClass); ?>">
                                        <td style="padding-left: 2rem;">
                                            <i class="ri-building-line text-muted me-1" style="font-size: 12px;"></i>
                                            <?php echo e(Str::limit($inst['name'], 40)); ?>

                                        </td>
                                        <td>
                                            <i class="ri-map-pin-line text-muted me-1" style="font-size: 10px;"></i>
                                            <?php echo e($inst['locality']); ?>

                                        </td>
                                        <td class="text-center fw-semibold"><?php echo e($inst['total_tables']); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-info"><?php echo e($inst['reported_tables']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success"><?php echo e($inst['validated_tables']); ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if($inst['pending_tables'] > 0): ?>
                                                <span class="badge bg-danger"><?php echo e($inst['pending_tables']); ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-success">0</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 5px;">
                                                    <div class="progress-bar bg-<?php echo e($progressColor); ?>" style="width: <?php echo e($inst['progress']); ?>%;"></div>
                                                </div>
                                                <small class="text-muted" style="min-width: 35px;"><?php echo e($inst['progress']); ?>%</small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($inst['pending_tables'] > 0): ?>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary py-0 px-2"
                                                        onclick="loadInstitutionTables(<?php echo e($inst['id']); ?>, '<?php echo e(addslashes($inst['name'])); ?>')"
                                                        title="Ver mesas">
                                                    <i class="ri-eye-line"></i> Ver Mesas
                                                </button>
                                            <?php else: ?>
                                                <i class="ri-checkbox-circle-line text-success fs-5"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        
        
        <div class="px-3 py-2 border-top bg-light">
            <small class="text-muted">
                <i class="ri-information-line me-1"></i>
                Click en el recinto para ver detalles de mesas
            </small>
        </div>
    </div>
</div>
<script>
let allExpanded = false;
function toggleDistrict(districtId) {
    const content = document.getElementById(`district-${districtId}`);
    const icon = document.getElementById(`icon-${districtId}`);
    if (content.style.display === 'none' || content.style.display === '') {
        content.style.display = 'block';
        icon.className = 'ri-arrow-down-s-line fs-6';
    } else {
        content.style.display = 'none';
        icon.className = 'ri-arrow-right-s-line fs-6';
    }
}
function toggleExpandAll() {
    const districts = document.querySelectorAll('[id^="district-"]');
    const icons = document.querySelectorAll('[id^="icon-"]');
    allExpanded = !allExpanded;    
    districts.forEach((district, index) => {
        district.style.display = allExpanded ? 'block' : 'none';
    });
    icons.forEach(icon => {
        icon.className = allExpanded ? 'ri-arrow-down-s-line fs-6' : 'ri-arrow-right-s-line fs-6';
    });    
    const btn = document.getElementById('expandAllBtn');
    btn.innerHTML = allExpanded ? '<i class="ri-subtract-line me-1"></i>Colapsar' : '<i class="ri-add-line me-1"></i>Expandir';
}
function filterInstitutions(type) {
    const rows = document.querySelectorAll('#institutionAccordion tbody tr');
    const districts = document.querySelectorAll('[id^="district-"]');
    rows.forEach(row => {
        const isPending = row.classList.contains('table-warning');
        if (type === 'all') {
            row.style.display = '';
        } else if (type === 'pending') {
            row.style.display = isPending ? '' : 'none';
        } else if (type === 'complete') {
            row.style.display = !isPending ? '' : 'none';
        }
    });
    districts.forEach(district => {
        const visibleRows = district.querySelectorAll('tbody tr:not([style*="display: none"])').length;
        if (visibleRows === 0 && type !== 'all') {
            district.style.display = 'none';
            const iconId = district.id.replace('district-', 'icon-');
            const icon = document.getElementById(iconId);
            if (icon) icon.className = 'ri-arrow-right-s-line fs-6';
        } else if (visibleRows > 0) {
            district.style.display = 'block';
            const iconId = district.id.replace('district-', 'icon-');
            const icon = document.getElementById(iconId);
            if (icon && district.style.display === 'block') {
                icon.className = 'ri-arrow-down-s-line fs-6';
            }
        }
    });
}

document.getElementById('institutionSearch')?.addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#institutionAccordion tbody tr');
    const districts = document.querySelectorAll('[id^="district-"]');
    if (searchTerm === '') {
        rows.forEach(row => row.style.display = '');
        districts.forEach(district => {
            district.style.display = 'none';
            const iconId = district.id.replace('district-', 'icon-');
            const icon = document.getElementById(iconId);
            if (icon) icon.className = 'ri-arrow-right-s-line fs-6';
        });
        return;
    }
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
    districts.forEach(district => {
        const visibleRows = district.querySelectorAll('tbody tr:not([style*="display: none"])').length;
        if (visibleRows > 0) {
            district.style.display = 'block';
            const iconId = district.id.replace('district-', 'icon-');
            const icon = document.getElementById(iconId);
            if (icon) icon.className = 'ri-arrow-down-s-line fs-6';
        }
    });
});
</script>
<?php endif; ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views/partials/institution-avance-table.blade.php ENDPATH**/ ?>