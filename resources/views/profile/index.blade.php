@extends('layouts.master')
@section('title')
    @lang('translation.profile')
@endsection
@section('content')
    <div class="profile-foreground position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg">
            <img  alt="" class="profile-wid-img" />{{-- src="{{ URL::asset('build/images/profile-bg.jpg') }}" --}}
        </div>
    </div>
    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
        <div class="row g-4">
            <div class="col-auto">
                <div class="avatar-lg">
                    <img src="{{ $user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg') }}" 
                         alt="user-img" class="img-thumbnail rounded-circle" />
                </div>
            </div>
            <div class="col">
                <div class="p-2">
                    <h3 class="text-white mb-1">{{ $user->name }} {{ $user->last_name }}</h3>
                    <p class="text-white text-opacity-75">
                        @foreach($user->roles as $role)
                            {{ $role->display_name ?? $role->name }}@if(!$loop->last), @endif
                        @endforeach
                    </p>
                    <div class="hstack text-white-50 gap-1">
                        @if($user->id_card)
                        <div class="me-2">
                            <i class="ri-fingerprint-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>
                            {{ $user->id_card }}
                        </div>
                        @endif
                        @if($user->phone)
                        <div class="me-2">
                            <i class="ri-phone-line me-1 text-white text-opacity-75 fs-16 align-middle"></i>
                            {{ $user->phone }}
                        </div>
                        @endif
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
                        <a href="{{ route('profile.settings') }}" class="btn btn-secondary">
                            <i class="ri-edit-box-line align-bottom"></i> Editar Perfil
                        </a>
                    </div>
                </div>
                
                <div class="tab-content pt-4 text-muted">
                    {{-- Resumen Tab --}}
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
                                                        <td class="text-muted">{{ $user->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Apellidos:</th>
                                                        <td class="text-muted">{{ $user->last_name ?? '—' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Email:</th>
                                                        <td class="text-muted">{{ $user->email }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Teléfono:</th>
                                                        <td class="text-muted">{{ $user->phone ?? '—' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Carnet:</th>
                                                        <td class="text-muted">{{ $user->id_card ?? '—' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Dirección:</th>
                                                        <td class="text-muted">{{ $user->address ?? '—' }}</td>
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
                                        
                                        @forelse($user->roles as $role)
                                        <div class="border rounded p-3 mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-2">{{ $role->display_name ?? $role->name }}</h6>
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $role->permissions->count() }} permisos
                                                </span>
                                            </div>
                                            @if($role->description)
                                                <p class="text-muted small mb-2">{{ $role->description }}</p>
                                            @endif
                                            @if($role->permissions->isNotEmpty())
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($role->permissions->take(5) as $perm)
                                                        <span class="badge bg-light text-dark">{{ $perm->display_name ?? $perm->name }}</span>
                                                    @endforeach
                                                    @if($role->permissions->count() > 5)
                                                        <span class="badge bg-light text-dark">+{{ $role->permissions->count() - 5 }} más</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @empty
                                            <p class="text-muted mb-0">Sin roles asignados</p>
                                        @endforelse
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
                                                    <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Última actualización</small>
                                                    <strong>{{ $user->updated_at->format('d/m/Y H:i') }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Último acceso</small>
                                                    <strong>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca' }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="border rounded p-3">
                                                    <small class="text-muted d-block">Estado</small>
                                                    <strong>
                                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                                            {{ $user->is_active ? 'Activo' : 'Inactivo' }}
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

                    {{-- Delegaciones Tab --}}
                    <div class="tab-pane fade" id="delegations" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Mis Delegaciones</h5>
                                
                                @php
                                    $assignments = $user->assignments()
                                        ->with(['institution', 'votingTable', 'assignedBy'])
                                        ->latest()
                                        ->get();
                                @endphp
                                
                                @forelse($assignments as $assignment)
                                <div class="border rounded p-3 mb-3">
                                    <div class="row align-items-center">
                                        <div class="col-md-auto">
                                            <span class="badge bg-primary mb-1 mb-md-0">
                                                {{ $assignment->delegate_type_label }}
                                            </span>
                                        </div>
                                        <div class="col-md">
                                            <h6 class="mb-1">{{ $assignment->institution->name }}</h6>
                                            @if($assignment->votingTable)
                                                <p class="text-muted small mb-0">
                                                    Mesa: {{ $assignment->votingTable->code }} ({{ $assignment->votingTable->number }})
                                                </p>
                                            @endif
                                        </div>
                                        <div class="col-md-auto">
                                            {!! $assignment->status_badge !!}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                Asignado: {{ $assignment->assignment_date?->format('d/m/Y') ?? 'No especificada' }}
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="ri-user-line me-1"></i>
                                                Por: {{ $assignment->assignedBy?->name ?? 'Sistema' }}
                                            </small>
                                        </div>
                                    </div>
                                    @if($assignment->observations)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small><i class="ri-chat-1-line me-1"></i> {{ $assignment->observations }}</small>
                                        </div>
                                    @endif
                                </div>
                                @empty
                                    <div class="text-center py-5">
                                        <i class="ri-user-settings-line display-1 text-muted"></i>
                                        <h5 class="mt-3">No tienes delegaciones asignadas</h5>
                                        <p class="text-muted">Las delegaciones aparecerán aquí cuando sean asignadas por un administrador.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Actividades Recientes Tab --}}
                    <div class="tab-pane fade" id="activities" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Actividades Recientes</h5>
                                
                                @php
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
                                @endphp
                                
                                @if($activities->isNotEmpty())
                                    <div class="acitivity-timeline">
                                        @foreach($activities as $activity)
                                        <div class="acitivity-item d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div class="avatar-title bg-{{ $activity['color'] }}-subtle text-{{ $activity['color'] }} rounded-circle">
                                                        <i class="{{ $activity['icon'] }}"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">{{ $activity['description'] }}</h6>
                                                @if($activity['details'])
                                                    <p class="text-muted mb-2 small">{{ $activity['details'] }}</p>
                                                @endif
                                                <small class="mb-0 text-muted">
                                                    <i class="ri-time-line me-1"></i>
                                                    {{ $activity['created_at'] instanceof Carbon ? $activity['created_at']->diffForHumans() : 'Recientemente' }}
                                                </small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <i class="ri-history-line display-1 text-muted"></i>
                                        <h5 class="mt-3">No hay actividades registradas</h5>
                                        <p class="text-muted">Las actividades como inicios de sesión y cambios en tu perfil aparecerán aquí.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/pages/profile.init.js') }}"></script>
@endsection