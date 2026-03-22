<?php $__env->startSection('title'); ?> Gestión de Usuarios <?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet"/>
<style>
.stat-card{background:#fff;border:1px solid #e9e9ef;border-radius:.5rem;padding:.75rem 1.1rem;display:flex;align-items:center;gap:.9rem}
.stat-card .icon{width:42px;height:42px;border-radius:.4rem;display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0}
.stat-card .num{font-size:1.4rem;font-weight:700;line-height:1}
.stat-card .lbl{font-size:.72rem;color:#74788d}
.avatar-xs{width:36px;height:36px;border-radius:50%;object-fit:cover}
.sort-link{color:inherit;text-decoration:none;white-space:nowrap}
.sort-link:hover{color:#0ab39c}
.sort-link i{font-size:.7rem;vertical-align:middle}
.stats-toggle{cursor:pointer;user-select:none}
.stats-toggle i{transition:transform .3s}
.stats-toggle.collapsed i{transform:rotate(-90deg)}
.view-toggle {
    display: flex;
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 0.25rem;
}
.view-toggle .btn {
    border: none;
    padding: 0.4rem 1rem;
    font-size: 0.85rem;
}
.view-toggle .btn.active {
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    color: #0ab39c;
}
.delegate-badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.5rem;
    border-radius: 1rem;
    white-space: nowrap;
}
.delegate-badge.recinto {
    background: #e7f5ff;
    color: #0d6efd;
}
.delegate-badge.mesa {
    background: #e6f7e6;
    color: #198754;
}
.delegate-multiple {
    background: #fff3cd;
    color: #856404;
    font-size: 0.65rem;
    padding: 0.15rem 0.4rem;
    border-radius: 1rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Sistema <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> Gestión de Usuarios <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>
<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="view-toggle">
        <a href="<?php echo e(route('users.index')); ?>"
           class="btn <?php echo e(!$isDelegatesView ? 'active' : ''); ?>">
            <i class="ri-group-line me-1"></i>Todos los Usuarios
        </a>
        <a href="<?php echo e(route('users.delegates')); ?>"
           class="btn <?php echo e($isDelegatesView ? 'active' : ''); ?>">
            <i class="ri-shield-user-line me-1"></i>Delegados
            <span class="badge bg-primary ms-1"><?php echo e($stats['delegates']); ?></span>
        </a>
    </div>
    <div class="d-flex justify-content-end">
        <button class="btn btn-sm btn-light stats-toggle" id="statsToggle" onclick="toggleStats()">
            <i class="ri-arrow-down-s-line me-1"></i><span id="statsToggleLabel">Mostrar estadísticas</span>
        </button>
    </div>
</div>
<div id="statsContainer" class="d-none">
    <div class="row g-2 mb-2">
        <?php if($isDelegatesView): ?>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-primary bg-opacity-10 text-primary">
                        <i class="ri-shield-user-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['delegates']); ?></div>
                        <div class="lbl">Total Delegados</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-success bg-opacity-10 text-success">
                        <i class="ri-map-pin-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['active_assignments']); ?></div>
                        <div class="lbl">Asignaciones</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-info bg-opacity-10 text-info">
                        <i class="ri-building-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['recintos']); ?></div>
                        <div class="lbl">Recintos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-warning bg-opacity-10 text-warning">
                        <i class="ri-table-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['mesas']); ?></div>
                        <div class="lbl">Mesas</div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-primary bg-opacity-10 text-primary">
                        <i class="ri-team-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['total']); ?></div>
                        <div class="lbl">Total usuarios</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-success bg-opacity-10 text-success">
                        <i class="ri-user-follow-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['active']); ?></div>
                        <div class="lbl">Activos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-danger bg-opacity-10 text-danger">
                        <i class="ri-user-unfollow-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['inactive']); ?></div>
                        <div class="lbl">Inactivos</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-warning bg-opacity-10 text-warning">
                        <i class="ri-shield-user-line"></i>
                    </div>
                    <div>
                        <div class="num"><?php echo e($stats['delegates']); ?></div>
                        <div class="lbl">Delegados</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-2">
    <div class="card-body py-2">
        <form method="GET" action="<?php echo e(route($isDelegatesView ? 'users.delegates' : 'users.index')); ?>" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="Nombre, email, CI…" value="<?php echo e(request('search')); ?>">
                    </div>
                </div>

                <?php if(!$isDelegatesView): ?>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Rol</label>
                    <select name="role" class="form-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->name); ?>" <?php echo e(request('role') == $role->name ? 'selected' : ''); ?>>
                            <?php echo e($role->display_name ?? $role->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipo Delegado</label>
                    <select name="delegate_type" class="form-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $delegateTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($val); ?>" <?php echo e(request('delegate_type') == $val ? 'selected' : ''); ?>>
                            <?php echo e($label); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <?php if($isDelegatesView): ?>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Recinto</label>
                    <select name="institution_id" class="form-select">
                        <option value="">Todos</option>
                        <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($inst->id); ?>" <?php echo e(request('institution_id') == $inst->id ? 'selected' : ''); ?>>
                            <?php echo e($inst->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="col-md-2">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>
                            <?php echo e($isDelegatesView ? 'Activos' : 'Usuarios Activos'); ?>

                        </option>
                        <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>
                            <?php echo e($isDelegatesView ? 'Inactivos' : 'Usuarios Inactivos'); ?>

                        </option>
                    </select>
                </div>

                <div class="col-md-<?php echo e($isDelegatesView ? '3' : '3'); ?> d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    <a href="<?php echo e(route($isDelegatesView ? 'users.delegates' : 'users.index')); ?>"
                       class="btn btn-outline-secondary" title="Limpiar">
                        <i class="ri-close-line"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between py-2 px-3">
        <h5 class="card-title mb-0">
            <?php echo e($isDelegatesView ? 'Delegados' : 'Usuarios'); ?>

            <span class="badge bg-secondary ms-1"><?php echo e($users->total()); ?></span>
        </h5>
        <?php if(auth()->user()->hasPermission('create_users')): ?>
        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-success btn">
            <i class="ri-add-line me-1"></i>Nuevo Usuario
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="<?php echo e(route('users.index', array_merge(request()->query(),['sort'=>'name','order'=> request('sort')=='name'&&request('order')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                                Usuario
                                <?php if(request('sort')=='name'): ?><i class="ri-arrow-<?php echo e(request('order')=='asc'?'up':'down'); ?>-s-line"></i><?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                            </a>
                        </th>
                        <th>
                            <a href="<?php echo e(route('users.index', array_merge(request()->query(),['sort'=>'email','order'=> request('sort')=='email'&&request('order')=='asc'?'desc':'asc']))); ?>" class="sort-link">
                                Contacto
                                <?php if(request('sort')=='email'): ?><i class="ri-arrow-<?php echo e(request('order')=='asc'?'up':'down'); ?>-s-line"></i><?php else: ?><i class="ri-arrow-up-down-line text-muted opacity-50"></i><?php endif; ?>
                            </a>
                        </th>
                        <th>Roles</th>
                        <th><?php echo e($isDelegatesView ? 'Delegación Activa' : 'Delegación'); ?></th>
                        <th>Estado</th>
                        <?php if(!$isDelegatesView): ?>
                        <th>Últ. Acceso</th>
                        <?php endif; ?>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $activeAssignments = $user->assignments->where('status', 'activo');
                        $primaryAssignment = $activeAssignments->first();
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="<?php echo e($user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                                     alt="" class="avatar-xs">
                                <div>
                                    <div class="fw-semibold"><?php echo e($user->name); ?> <?php echo e($user->last_name); ?></div>
                                    <div class="text-muted small">
                                        <?php if($user->id_card): ?>
                                            <i class="ri-fingerprint-line me-1"></i><?php echo e($user->id_card); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><i class="ri-mail-line text-muted me-1"></i><?php echo e(Str::limit($user->email, 20)); ?></div>
                            <?php if($user->phone): ?>
                                <div><i class="ri-phone-line text-muted me-1"></i><?php echo e($user->phone); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <?php $__currentLoopData = $user->roles->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-info-subtle text-info" style="font-size:.65rem">
                                        <?php echo e(Str::limit($r->display_name ?? $r->name, 12)); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php if($user->roles->count() > 2): ?>
                                    <span class="badge bg-secondary" style="font-size:.65rem">+<?php echo e($user->roles->count()-2); ?></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <?php if($primaryAssignment): ?>
                                <div class="d-flex flex-column gap-1">
                                    <span class="delegate-badge <?php echo e($primaryAssignment->voting_table_id ? 'mesa' : 'recinto'); ?>">
                                        <i class="ri-<?php echo e($primaryAssignment->voting_table_id ? 'table' : 'building'); ?>-line me-1"></i>
                                        <?php echo e($delegateTypes[$primaryAssignment->delegate_type] ?? $primaryAssignment->delegate_type); ?>

                                    </span>
                                    <small class="text-muted">
                                        <?php echo e($primaryAssignment->institution->code ?? ''); ?>

                                        <?php if($primaryAssignment->voting_table_id): ?>
                                            - Mesa <?php echo e($primaryAssignment->votingTable?->number); ?>

                                        <?php endif; ?>
                                    </small>
                                    <?php if($activeAssignments->count() > 1): ?>
                                        <span class="delegate-multiple">
                                            +<?php echo e($activeAssignments->count()-1); ?> más
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($user->is_active): ?>
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>

                        <?php if(!$isDelegatesView): ?>
                        <td>
                            <small class="text-muted" title="<?php echo e($user->last_login_at?->format('d/m/Y H:i')); ?>">
                                <?php echo e($user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca'); ?>

                            </small>
                        </td>
                        <?php endif; ?>
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                <?php if(auth()->user()->hasPermission('view_users')): ?>
                                <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-sm btn-soft-info" title="Ver perfil"><i class="ri-eye-line"></i></a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasPermission('edit_users')): ?>
                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-soft-warning" title="Editar"><i class="ri-pencil-line"></i></a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasPermission('assign_roles')): ?>
                                    <a href="<?php echo e(route('users.delegaciones.form', $user)); ?>" class="btn btn-sm btn-soft-primary" title="Delegaciones"><i class="ri-shield-user-line"></i></a>
                                <?php endif; ?>
                                <?php if($user->id !== auth()->id()): ?>
                                    <?php if($user->is_active && auth()->user()->hasPermission('delete_users')): ?>
                                    <button class="btn btn-sm btn-soft-danger" onclick="confirmDeactivate(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>')"><i class="ri-user-unfollow-line"></i></button>
                                    <?php elseif(!$user->is_active && auth()->user()->hasPermission('activate_users')): ?>
                                    <form method="POST" action="<?php echo e(route('users.activate', $user)); ?>" class="d-inline"><?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-soft-success" title="Activar"><i class="ri-user-follow-line"></i></button>
                                    </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e($isDelegatesView ? '5' : '7'); ?>" class="text-center py-5">
                            <i class="ri-user-search-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                            <p class="text-muted mb-1">
                                No se encontraron <?php echo e($isDelegatesView ? 'delegados' : 'usuarios'); ?>

                            </p>
                            <?php if(request()->hasAny(['search','role','status','delegate_type','institution_id'])): ?>
                            <a href="<?php echo e(route($isDelegatesView ? 'users.delegates' : 'users.index')); ?>"
                               class="btn btn-outline-secondary mt-2">
                                <i class="ri-close-line me-1"></i>Limpiar filtros
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($users->hasPages()): ?>
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <small class="text-muted">Mostrando <?php echo e($users->firstItem()); ?>–<?php echo e($users->lastItem()); ?> de <?php echo e($users->total()); ?> usuarios</small>
        <?php echo e($users->appends(request()->query())->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<script>
const CSRF = '<?php echo e(csrf_token()); ?>';
function toggleStats() {
    const container = document.getElementById('statsContainer');
    const btn       = document.getElementById('statsToggle');
    const label     = document.getElementById('statsToggleLabel');
    const isHidden  = container.classList.contains('d-none');
    container.classList.toggle('d-none', !isHidden);
    btn.classList.toggle('collapsed', !isHidden);
    btn.querySelector('i').className = isHidden ? 'ri-arrow-down-s-line me-1' : 'ri-arrow-right-s-line me-1';
    label.textContent = isHidden ? 'Ocultar estadísticas' : 'Mostrar estadísticas';
    localStorage.setItem('userStatsVisible', String(isHidden));
}
document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('userStatsVisible') === 'true') {
        const container = document.getElementById('statsContainer');
        const btn       = document.getElementById('statsToggle');
        const label     = document.getElementById('statsToggleLabel');
        if (container) container.classList.remove('d-none');
        if (btn) { btn.classList.remove('collapsed'); btn.querySelector('i').className = 'ri-arrow-down-s-line me-1'; }
        if (label) label.textContent = 'Ocultar estadísticas';
    }
});
function confirmDeactivate(id, name) {
    Swal.fire({
        title: '¿Desactivar usuario?',
        html: `¿Desactivar a <strong>${name}</strong>? El usuario no podrá acceder al sistema.`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33', confirmButtonText: 'Sí, desactivar', cancelButtonText: 'Cancelar',
    }).then(r => {
        if (!r.isConfirmed) return;
        const f = document.createElement('form');
        f.method = 'POST'; f.action = `/users/${id}`;
        f.innerHTML = `<input type="hidden" name="_token" value="${CSRF}"><input type="hidden" name="_method" value="DELETE">`;
        document.body.appendChild(f); f.submit();
    });
}
setTimeout(() => document.querySelectorAll('.alert-dismissible').forEach(a => bootstrap.Alert.getOrCreateInstance(a)?.close()), 5000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\users\index.blade.php ENDPATH**/ ?>