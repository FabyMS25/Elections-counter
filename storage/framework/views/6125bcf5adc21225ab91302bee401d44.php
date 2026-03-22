<header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box horizontal-logo">
                    <a href="<?php echo e(route('root')); ?>" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('build/images/logo_elections.png')); ?>" alt="" height="45">
                        </span>
                        <span class="logo-lg">
                            <img src="<?php echo e(URL::asset('build/images/logo_elections_large.png')); ?>" alt="" height="45">
                        </span>
                    </a>
                    <a href="<?php echo e(route('root')); ?>" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="<?php echo e(URL::asset('build/images/logo_elections.png')); ?>" alt="" height="45">
                        </span>
                        <span class="logo-lg">
                            <img src="<?php echo e(URL::asset('build/images/logo_elections_large.png')); ?>" alt="" height="45">
                        </span>
                    </a>
                </div>
                <?php if(auth()->guard()->check()): ?>
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger" id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center">
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"
                            data-toggle="fullscreen" id="fullscreen-btn">
                        <i class='bx bx-fullscreen fs-22'></i>
                    </button>
                </div>
                <div class="ms-1 header-item d-none d-sm-flex">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">
                        <i class='bx bx-moon fs-22'></i>
                    </button>
                </div>
                <?php if(auth()->guard()->check()): ?>
                <div class="dropdown topbar-head-dropdown ms-1 header-item" id="notificationDropdown">
                    <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-haspopup="true" aria-expanded="false">
                        <i class='bx bx-bell fs-22'></i>
                        <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger">
                            <?php echo e($totalNotifications ?? 0); ?>

                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">

                        <div class="dropdown-head bg-primary bg-pattern rounded-top">
                            <div class="p-3">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-0 fs-16 fw-semibold text-white"> Notificaciones </h6>
                                    </div>
                                    <div class="col-auto dropdown-tabs">
                                        <span class="badge bg-light-subtle text-body fs-13"> <?php echo e($totalNotifications ?? 0); ?> Nuevas</span>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs dropdown-tabs nav-tabs-custom" id="notificationItemsTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#alerts-tab" role="tab">Alertas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#actas-tab" role="tab">Actas Subidas</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#progress-tab" role="tab">Progreso</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content" id="notificationItemsTabContent">
                            <div class="tab-pane fade show active py-2 ps-2" id="alerts-tab" role="tabpanel">
                                <?php $__currentLoopData = $recentObservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $obs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-reset notification-item d-block dropdown-item">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-danger-subtle text-danger rounded-circle fs-16"><i class="ri-error-warning-line"></i></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Mesa <?php echo e($obs->votingTable->number); ?></h6>
                                            <p class="mb-1 fs-12 text-muted"><b><?php echo e($obs->votingTable->institution->name); ?></b>: <?php echo e($obs->description); ?></p>
                                            <p class="mb-0 fs-11 text-muted"><span><i class="mdi mdi-clock-outline"></i> <?php echo e($obs->created_at->diffForHumans()); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="actas-tab" role="tabpanel">
                                <?php $__currentLoopData = $recentActas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-reset notification-item d-block dropdown-item">
                                    <div class="d-flex">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-info-subtle text-info rounded-circle fs-16"><i class="ri-file-list-3-line"></i></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mt-0 mb-1 fs-13 fw-semibold">Acta Mesa <?php echo e($acta->votingTable->number); ?></h6>
                                            <p class="mb-1 fs-12 text-muted">Subida en: <?php echo e($acta->votingTable->institution->name); ?></p>
                                            <p class="mb-0 fs-11 text-muted"><span><i class="mdi mdi-clock-outline"></i> <?php echo e($acta->created_at->diffForHumans()); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="tab-pane fade py-2 ps-2" id="progress-tab" role="tabpanel">
                                <?php $__currentLoopData = $validatedByRecinto; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-reset notification-item d-block dropdown-item">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-3 flex-shrink-0">
                                            <span class="avatar-title bg-success-subtle text-success rounded-circle fs-16"><i class="ri-checkbox-circle-line"></i></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="m-0 fs-13 fw-semibold"><?php echo e($group->validated_count); ?> mesas validadas</h6>
                                            <span class="text-muted fs-11"><?php echo e($group->institution->name); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown ms-sm-3 header-item topbar-user">
                    <button type="button" class="btn" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                src="<?php echo e(Auth::user()->avatar
                                    ? URL::asset('build/images/users/' . Auth::user()->avatar)
                                    : URL::asset('build/images/users/avatar-1.jpg')); ?>"
                                alt="Header Avatar">
                            <span class="text-start ms-xl-2">
                                <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">
                                    <?php echo e(Auth::user()->name); ?>

                                </span>
                                <span class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">
                                    <?php echo e(Auth::user()->roles->first()?->name ?? ''); ?>

                                </span>
                            </span>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <h6 class="dropdown-header">Bienvenid@ <?php echo e(Auth::user()->name); ?>!</h6>
                        <a class="dropdown-item" href="<?php echo e(route('profile.index')); ?>">
                            <i class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle">Perfil</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo e(route('profile.settings')); ?>">
                            <i class="mdi mdi-cog-outline text-muted fs-16 align-middle me-1"></i>
                            <span class="align-middle">Configuraciones</span>
                        </a>
                        <a class="dropdown-item text-danger"
                        href="javascript:void(0);"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-power-off fs-16 align-middle me-1"></i>
                            <span><?php echo app('translator')->get('translation.logout'); ?></span>
                        </a>
                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                            <?php echo csrf_field(); ?>
                        </form>
                    </div>
                </div>
                <?php else: ?>
                <div class="ms-sm-3 header-item">
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-primary">
                        <i class="ri-login-box-line me-1"></i> Ingresar
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
</header>

<div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
            </div>
            <div class="modal-body">
                <div class="mt-2 text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4>Are you sure ?</h4>
                        <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                    <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\_Mine\sistema_electoral\resources\views\layouts\topbar.blade.php ENDPATH**/ ?>