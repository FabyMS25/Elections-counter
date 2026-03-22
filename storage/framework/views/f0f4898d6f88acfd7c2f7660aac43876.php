<?php $__env->startSection('title'); ?> <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
<link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet"/>
<style>
.avatar-ring{width:88px;height:88px;border-radius:50%;object-fit:cover;border:3px solid #fff;box-shadow:0 2px 12px rgba(0,0,0,.15)}
.stat-box{background:#f8f9fa;border-radius:.5rem;padding:.6rem 1rem;text-align:center;min-width:80px}
.stat-box .n{font-size:1.35rem;font-weight:700;line-height:1}
.stat-box .l{font-size:.7rem;color:#74788d;margin-top:.15rem}
.tab-pill{cursor:pointer;padding:.4rem .9rem;border-radius:.375rem;font-size:.82rem;font-weight:500;color:#74788d;white-space:nowrap;transition:all .15s}
.tab-pill.active{background:#0ab39c;color:#fff}
.tab-pill:hover:not(.active){background:#f3f6f9;color:#495057}
.info-row{display:flex;gap:.5rem;padding:.45rem 0;border-bottom:1px solid #f3f6f9}
.info-row:last-child{border-bottom:none}
.info-k{min-width:42%;font-weight:500;color:#6c757d;font-size:.82rem}
.info-v{font-size:.82rem;color:#212529}
.del-row{border:1px solid #e9e9ef;border-radius:.4rem;padding:.6rem .9rem;margin-bottom:.4rem;background:#fff;transition:box-shadow .15s}
.del-row:hover{box-shadow:0 2px 8px rgba(0,0,0,.08)}
.perm-chip{display:inline-block;background:#f0f4ff;color:#3b5bdb;border-radius:.25rem;padding:.1rem .38rem;font-size:.68rem;margin:.1rem;line-height:1.4}
.perm-chip.direct{background:#d4f5ec;color:#0f6e56}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php $__env->startComponent('components.breadcrumb'); ?>
    <?php $__env->slot('li_1'); ?> Usuarios <?php $__env->endSlot(); ?>
    <?php $__env->slot('li_2'); ?> <a href="<?php echo e(route('users.index')); ?>">Lista</a> <?php $__env->endSlot(); ?>
    <?php $__env->slot('title'); ?> <?php echo e($user->name); ?> <?php echo e($user->last_name); ?> <?php $__env->endSlot(); ?>
<?php echo $__env->renderComponent(); ?>

<?php echo $__env->make('components.alerts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <img src="<?php echo e($user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                     alt="" class="avatar-ring">
            </div>
            <div class="col">
                <h4 class="mb-0"><?php echo e($user->name); ?> <?php echo e($user->last_name); ?></h4>
                <p class="text-muted small mb-1">
                    <?php echo e($user->email); ?>

                    <?php if($user->id_card): ?> &bull; CI: <?php echo e($user->id_card); ?> <?php endif; ?>
                    <?php if($user->phone): ?> &bull; <?php echo e($user->phone); ?> <?php endif; ?>
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge <?php echo e($user->is_active ? 'bg-success' : 'bg-danger'); ?>">
                        <?php echo e($user->is_active ? 'Activo' : 'Inactivo'); ?>

                    </span>
                    <?php $__currentLoopData = $user->roles->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="badge bg-info-subtle text-info"><?php echo e($r->display_name ?? $r->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($user->roles->count() > 4): ?>
                    <span class="badge bg-secondary">+<?php echo e($user->roles->count()-4); ?> más</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-auto d-none d-lg-flex gap-2">
                <div class="stat-box"><div class="n"><?php echo e($user->roles->count()); ?></div><div class="l">Roles</div></div>
                <div class="stat-box"><div class="n"><?php echo e($totalPermCount); ?></div><div class="l">Permisos</div></div>
            </div>
            <div class="col-auto d-flex gap-2 flex-wrap">
                <?php if(auth()->user()->hasPermission('edit_users')): ?>
                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-soft-warning btn" title="Editar">
                    <i class="ri-pencil-line me-1"></i>
                </a>
                <?php endif; ?>
                <?php if(auth()->user()->hasPermission('assign_roles')): ?>
                <a href="<?php echo e(route('users.delegaciones.form', $user)); ?>" class="btn btn-soft-primary btn" title="Delegaciones">
                    <i class="ri-shield-user-line me-1"></i>
                </a>
                <?php endif; ?>
                <?php if($user->id !== auth()->id()): ?>
                    <?php if($user->is_active && auth()->user()->hasPermission('delete_users')): ?>
                    <button class="btn btn-soft-danger btn" title="Desactivar"
                            onclick="confirmDeactivate(<?php echo e($user->id); ?>,'<?php echo e(addslashes($user->name)); ?>')">
                        <i class="ri-user-unfollow-line me-1"></i>
                    </button>
                    <?php elseif(!$user->is_active && auth()->user()->hasPermission('activate_users')): ?>
                    <form method="POST" action="<?php echo e(route('users.activate', $user)); ?>" class="d-inline"><?php echo csrf_field(); ?>
                        <button class="btn btn-soft-success btn" title="Activar"><i class="ri-user-follow-line me-1"></i></button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2 mb-3 flex-wrap">
    <div class="tab-pill active" id="tpInfo"  onclick="showTab('info',this)"><i class="ri-user-line me-1"></i>Información</div>
    <div class="tab-pill"        id="tpRoles" onclick="showTab('roles',this)">
        <i class="ri-shield-user-line me-1"></i>Roles y Delegaciones
        <?php if($user->roles->count()): ?> <span class="badge bg-primary ms-1"><?php echo e($user->roles->count()); ?></span> <?php endif; ?>
    </div>
    <div class="tab-pill"        id="tpPerms" onclick="showTab('perms',this)">
        <i class="ri-key-line me-1"></i>Permisos
        <span class="badge bg-secondary ms-1"><?php echo e($totalPermCount); ?></span>
    </div>
    <div class="tab-pill"        id="tpLog"   onclick="showTab('log',this)"><i class="ri-history-line me-1"></i>Actividad</div>
</div>
<div id="tab-info">
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Datos del usuario</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row"><div class="info-k">Nombres</div><div class="info-v"><?php echo e($user->name); ?></div></div>
                    <div class="info-row"><div class="info-k">Apellidos</div><div class="info-v"><?php echo e($user->last_name ?? '—'); ?></div></div>
                    <div class="info-row"><div class="info-k">Carnet</div><div class="info-v"><?php echo e($user->id_card ?? '—'); ?></div></div>
                    <div class="info-row"><div class="info-k">Email</div><div class="info-v"><?php echo e($user->email); ?></div></div>
                    <div class="info-row"><div class="info-k">Teléfono</div><div class="info-v"><?php echo e($user->phone ?? '—'); ?></div></div>
                    <div class="info-row"><div class="info-k">Dirección</div><div class="info-v"><?php echo e($user->address ?? '—'); ?></div></div>
                </div>
                <div class="col-md-6">
                    <div class="info-row"><div class="info-k">Estado</div><div class="info-v"><span class="badge <?php echo e($user->is_active?'bg-success':'bg-danger'); ?>"><?php echo e($user->is_active?'Activo':'Inactivo'); ?></span></div></div>
                    <div class="info-row"><div class="info-k">Registrado</div><div class="info-v"><?php echo e($user->created_at->format('d/m/Y H:i')); ?></div></div>
                    <div class="info-row"><div class="info-k">Último acceso</div><div class="info-v"><?php echo e($user->last_login_at?->format('d/m/Y H:i') ?? '—'); ?></div></div>
                    <div class="info-row"><div class="info-k">Última IP</div><div class="info-v"><?php echo e($user->last_login_ip ?? '—'); ?></div></div>
                    <div class="info-row"><div class="info-k">Creado por</div><div class="info-v"><?php echo e($user->createdBy?->name ?? '—'); ?></div></div>
                    <div class="info-row"><div class="info-k">Actualizado por</div><div class="info-v"><?php echo e($user->updatedBy?->name ?? '—'); ?></div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="tab-roles" style="display:none">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Roles y Delegaciones</h5>
            <?php if(auth()->user()->hasPermission('assign_roles')): ?>
            <a href="<?php echo e(route('users.delegaciones.form', $user)); ?>" class="btn btn-soft-primary btn-sm">
                <i class="ri-edit-line me-1"></i>Gestionar
            </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php $__empty_1 = true; $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="del-row p-3 mb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <div class="fw-semibold"><?php echo e($role->display_name ?? $role->name); ?></div>
                        <small class="text-muted"><?php echo e($role->description); ?></small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">
                        <?php echo e($role->permissions->count()); ?> permisos
                    </span>

                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-5 text-muted">
                <i class="ri-shield-user-line d-block mb-2" style="font-size:2rem"></i>
                <p class="mb-1">Sin roles asignados</p>
                <?php if(auth()->user()->hasPermission('edit_users')): ?>
                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-primary mt-1">Asignar roles</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<div id="tab-perms" style="display:none">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-0">Permisos efectivos</h5>
                <small class="text-muted">
                    <span class="badge bg-dark"><?php echo e($totalPermCount); ?></span> permisos activos &nbsp;
                    <span class="badge bg-info-subtle text-info"><?php echo e(count($rolePermIds)); ?> de <?php echo e($user->roles->count()); ?> roles</span>
                </small>
            </div>
            <?php if(auth()->user()->hasPermission('edit_users')): ?>
            <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-sm btn-soft-warning">
                <i class="ri-pencil-line me-1"></i>Editar permisos
            </a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php
                $groupOrder = ['Usuarios','Roles y Permisos','Recintos','Mesas de Votación','Votos','Actas','Observaciones','Delegaciones','Auditoría','Configuración','Dashboard'];
                $allPerms = \App\Models\Permission::orderBy('display_name')->get()
                    ->groupBy('group')
                    ->sortBy(fn($_, $g) => ($k = array_search($g, $groupOrder)) !== false ? $k : 99);
            ?>
            <?php $__currentLoopData = $allPerms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $active = $perms->filter(fn($p)=>in_array($p->id,$allEffectivePermIds)); ?>
            <?php if($active->count()): ?>
            <div class="mb-3">
                <div class="text-uppercase fw-semibold small text-muted mb-1" style="font-size:.65rem;letter-spacing:.06em"><?php echo e($group); ?></div>
                <?php $__currentLoopData = $active; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="perm-chip" title="<?php echo e($p->display_name ?? $p->name); ?>">
                    <?php echo e($p->display_name ?? $p->name); ?>

                </span>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$totalPermCount): ?>
            <div class="text-center py-4 text-muted">Sin permisos asignados</div>
            <?php endif; ?>
            <div class="mt-3 pt-2 border-top small text-muted">
                <i class="ri-information-line me-1"></i>
                Estos son los permisos efectivos del usuario. Para modificarlos usa
                <a href="<?php echo e(route('users.edit', $user)); ?>">Editar Usuario</a>.
                Los roles son presets de selección — los permisos guardados son la fuente de verdad.
            </div>
        </div>
    </div>
</div>


<div id="tab-log" style="display:none">
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Historial de Actividad</h5></div>
        <div class="card-body">
            <?php
            $logs=\App\Models\AuditLog::where(function($q)use($user){
                $q->where('user_id',$user->id)->orWhere(function($q2)use($user){
                    $q2->where('model_type',\App\Models\User::class)->where('model_id',$user->id);
                });
            })->with('user')->latest()->take(20)->get();
            ?>
            <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="d-flex gap-3 py-2 border-bottom align-items-start">
                <div class="text-muted small text-nowrap" style="min-width:105px"><?php echo e(($log->performed_at??$log->created_at)?->format('d/m/Y H:i')); ?></div>
                <div style="min-width:80px">
                    <?php $c=match($log->action??''){'created'=>'success','updated'=>'primary','deleted'=>'danger','restored'=>'info',default=>'secondary'}; ?>
                    <span class="badge bg-<?php echo e($c); ?>"><?php echo e($log->action); ?></span>
                </div>
                <div class="flex-grow-1 small"><?php echo e($log->description); ?></div>
                <div class="text-muted small text-nowrap"><?php echo e($log->ip_address??'—'); ?></div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center text-muted py-3 mb-0">Sin actividad registrada</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
<script>
const CSRF='<?php echo e(csrf_token()); ?>';
function showTab(name,el){
    ['info','roles','perms','log'].forEach(t=>document.getElementById('tab-'+t).style.display=t===name?'':'none');
    document.querySelectorAll('.tab-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    history.replaceState(null,'','?tab='+name);
}
(function(){
    const t=new URLSearchParams(location.search).get('tab');
    if(t){const el=document.getElementById('tp'+t.charAt(0).toUpperCase()+t.slice(1));if(el)el.click();}
})();
function confirmDeactivate(id,name){
    Swal.fire({title:'¿Desactivar?',html:`Desactivar a <strong>${name}</strong>`,icon:'warning',showCancelButton:true,confirmButtonColor:'#d33',confirmButtonText:'Sí, desactivar',cancelButtonText:'Cancelar'})
    .then(r=>{if(!r.isConfirmed)return;const f=document.createElement('form');f.method='POST';f.action=`/users/${id}`;f.innerHTML=`<input type="hidden" name="_token" value="${CSRF}"><input type="hidden" name="_method" value="DELETE">`;document.body.appendChild(f);f.submit();});
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views\users\show.blade.php ENDPATH**/ ?>