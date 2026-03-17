<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.profile'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img  alt="" class="profile-wid-img" />
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img src="<?php echo e($user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg')); ?>" 
                         alt="user-img" class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1"><?php echo e($user->name); ?> <?php echo e($user->last_name); ?></h3>
                    <p class="text-white text-opacity-75">
                        <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php echo e($role->display_name ?? $role->name); ?><?php if(!$loop->last): ?>, <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </p>
                    <div class="hstack text-white-50 gap-1">
                        <?php if($user->id_card): ?>
                        <div class="me-2">
                            <i class="ri-fingerprint-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>
                            <?php echo e($user->id_card); ?>

                        </div>
                        <?php endif; ?>
                        <?php if($user->phone): ?>
                        <div class="me-2">
                            <i class="ri-phone-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>
                            <?php echo e($user->phone); ?>

                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <div class="d-flex profile-wrapper">
                    <ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link fs-14 active" data-bs-toggle="tab" href="#overview-tab" role="tab">
                                <i class="ri-airplay-fill d-inline-block d-md-none"></i> 
                                <span class="d-none d-md-inline-block">Resumen</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#delegations" role="tab">
                                <i class="ri-user-settings-line d-inline-block d-md-none"></i> 
                                <span class="d-none d-md-inline-block">Delegaciones</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fs-14" data-bs-toggle="tab" href="#activities" role="tab">
                                <i class="ri-history-line d-inline-block d-md-none"></i> 
                                <span class="d-none d-md-inline-block">Actividades Recientes</span>
                            </a>
                        </li>
                    </ul>
                    <div class="flex-shrink-0">
                        <a href="<?php echo e(route('profile.settings')); ?>" class="btn btn-secondary">
                            <i class="ri-edit-box-line align-bottom"></i> Editar Perfil
                        </a>
                    </div>
                </div>
                
                <div class="tab-content pt-4 text-muted">
                    
                    <div class="tab-pane active" id="overview-tab" role="tabpanel">
                        <div class="row">
                            <div class="col-xxl-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Información Personal</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row" style="width: 120px;">Nombre:</th>
                                                        <td class="text-muted"><?php echo e($user->name); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Apellidos:</th>
                                                        <td class="text-muted"><?php echo e($user->last_name ?? '—'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Email:</th>
                                                        <td class="text-muted"><?php echo e($user->email); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Teléfono:</th>
                                                        <td class="text-muted"><?php echo e($user->phone ?? '—'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Carnet:</th>
                                                        <td class="text-muted"><?php echo e($user->id_card ?? '—'); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Dirección:</th>
                                                        <td class="text-muted"><?php echo e($user->address ?? '—'); ?></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xxl-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Roles y Permisos</h5>
                                        
                                        <?php $__empty_1 = true; $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="border rounded p-3 mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-2"><?php echo e($role->display_name ?? $role->name); ?></h6>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <?php echo e($role->permissions->count()); ?> permisos
                                                </span>
                                            </div>
                                            <?php if($role->description): ?>
                                                <p class="text-muted small mb-2"><?php echo e($role->description); ?></p>
                                            <?php endif; ?>
                                            <?php if($role->permissions->isNotEmpty()): ?>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <?php $__currentLoopData = $role->permissions->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <span class="badge bg-light text-dark"><?php echo e($perm->display_name ?? $perm->name); ?></span>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    <?php if($role->permissions->count() > 5): ?>
                                                        <span class="badge bg-light text-dark">+<?php echo e($role->permissions->count() - 5); ?> más</span>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <p class="text-muted mb-0">Sin roles asignados</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xxl-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Información del Sistema</h5>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Miembro desde</small>
                                                    <strong><?php echo e($user->created_at->format('d/m/Y H:i')); ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Última actualización</small>
                                                    <strong><?php echo e($user->updated_at->format('d/m/Y H:i')); ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Último acceso</small>
                                                    <strong><?php echo e($user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca'); ?></strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Estado</small>
                                                    <strong>
                                                        <span class="badge bg-<?php echo e($user->is_active ? 'success' : 'danger'); ?>">
                                                            <?php echo e($user->is_active ? 'Activo' : 'Inactivo'); ?>

                                                        </span>
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="delegations" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Mis Delegaciones</h5>
                                
                                <?php
                                    $assignments = $user->assignments()
                                        ->with(['institution', 'votingTable', 'assignedBy'])
                                        ->latest()
                                        ->get();
                                ?>
                                
                                <?php $__empty_1 = true; $__currentLoopData = $assignments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assignment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="border rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-auto">
                                            <span class="badge bg-primary mb-1 mb-md-0">
                                                <?php echo e($assignment->delegate_type_label); ?>

                                            </span>
                                        </div>
                                        <div class="col-md">
                                            <h6 class="mb-1"><?php echo e($assignment->institution->name); ?></h6>
                                            <?php if($assignment->votingTable): ?>
                                                <p class="text-muted small mb-0">
                                                    Mesa: <?php echo e($assignment->votingTable->code); ?> (<?php echo e($assignment->votingTable->number); ?>)
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-auto">
                                            <?php echo $assignment->status_badge; ?>

                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                Asignado: <?php echo e($assignment->assignment_date?->format('d/m/Y') ?? 'No especificada'); ?>

                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="ri-user-line me-1"></i>
                                                Por: <?php echo e($assignment->assignedBy?->name ?? 'Sistema'); ?>

                                            </small>
                                        </div>
                                    </div>
                                    <?php if($assignment->observations): ?>
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small><i class="ri-chat-1-line me-1"></i> <?php echo e($assignment->observations); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <div class="text-center py-5">
                                        <i class="ri-user-settings-line display-1 text-muted"></i>
                                        <h5 class="mt-3">No tienes delegaciones asignadas</h5>
                                        <p class="text-muted">Las delegaciones aparecerán aquí cuando sean asignadas por un administrador.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="tab-pane fade" id="activities" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Actividades Recientes</h5>
                                
                                <?php
                                    use Carbon\Carbon;
                                    $activities = collect();
                                    $auditLogs = $user->auditLogs()
                                        ->latest()
                                        ->take(10)
                                        ->get()
                                        ->map(function($log) {
                                            return [
                                                'type' => 'audit',
                                                'action' => $log->action,
                                                'description' => $log->description,
                                                'details' => $log->notes,
                                                'created_at' => $log->created_at, 
                                                'icon' => match($log->action) {
                                                    'created' => 'ri-add-line',
                                                    'updated' => 'ri-edit-line',
                                                    'deleted' => 'ri-delete-bin-line',
                                                    'login' => 'ri-login-circle-line',
                                                    default => 'ri-history-line'
                                                },
                                                'color' => match($log->action) {
                                                    'created' => 'success',
                                                    'updated' => 'info',
                                                    'deleted' => 'danger',
                                                    'login' => 'primary',
                                                    default => 'secondary'
                                                }
                                            ];
                                        });
                                    $loginActivities = $user->auditLogs()
                                        ->where('action', 'login')
                                        ->latest()
                                        ->take(5)
                                        ->get()
                                        ->map(function($log) {
                                            return [
                                                'type' => 'login',
                                                'action' => 'login',
                                                'description' => 'Inicio de sesión en el sistema',
                                                'details' => $log->ip_address ? "IP: {$log->ip_address}" : null,
                                                'created_at' => $log->created_at,
                                                'icon' => 'ri-login-circle-line',
                                                'color' => 'primary'
                                            ];
                                        });
                                    $activities = $auditLogs->merge($loginActivities)
                                        ->sortByDesc('created_at')
                                        ->take(15);
                                ?>
                                
                                <?php if($activities->isNotEmpty()): ?>
                                    <div class="acitivity-timeline">
                                        <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="acitivity-item d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div class="avatar-title bg-<?php echo e($activity['color']); ?>-subtle text-<?php echo e($activity['color']); ?> rounded-circle">
                                                        <i class="<?php echo e($activity['icon']); ?>"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1"><?php echo e($activity['description']); ?></h6>
                                                <?php if($activity['details']): ?>
                                                    <p class="text-muted mb-2 small"><?php echo e($activity['details']); ?></p>
                                                <?php endif; ?>
                                                <small class="mb-0 text-muted">
                                                    <i class="ri-time-line me-1"></i>
                                                    <?php echo e($activity['created_at'] instanceof Carbon ? $activity['created_at']->diffForHumans() : 'Recientemente'); ?>

                                                </small>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="ri-history-line display-1 text-muted"></i>
                                        <h5 class="mt-3">No hay actividades registradas</h5>
                                        <p class="text-muted">Las actividades como inicios de sesión y cambios en tu perfil aparecerán aquí.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/swiper/swiper-bundle.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/js/pages/profile.init.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\sistema_electoral\resources\views/profile/index.blade.php ENDPATH**/ ?>