{{-- resources/views/users/index.blade.php --}}
@extends('layouts.master')
@section('title') Gestión de Usuarios @endsection

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
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
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Sistema @endslot
    @slot('title') Gestión de Usuarios @endslot
@endcomponent
@include('components.alerts')

<div class="d-flex justify-content-between align-items-center mb-2">
    <div class="view-toggle">
        <a href="{{ route('users.index') }}"
           class="btn {{ !$isDelegatesView ? 'active' : '' }}">
            <i class="ri-group-line me-1"></i>Todos los Usuarios
        </a>
        <a href="{{ route('users.delegates') }}"
           class="btn {{ $isDelegatesView ? 'active' : '' }}">
            <i class="ri-shield-user-line me-1"></i>Delegados
            <span class="badge bg-primary ms-1">{{ $stats['delegates'] }}</span>
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
        @if($isDelegatesView)
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-primary bg-opacity-10 text-primary">
                        <i class="ri-shield-user-line"></i>
                    </div>
                    <div>
                        <div class="num">{{ $stats['delegates'] }}</div>
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
                        <div class="num">{{ $stats['active_assignments'] }}</div>
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
                        <div class="num">{{ $stats['recintos'] }}</div>
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
                        <div class="num">{{ $stats['mesas'] }}</div>
                        <div class="lbl">Mesas</div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="icon bg-primary bg-opacity-10 text-primary">
                        <i class="ri-team-line"></i>
                    </div>
                    <div>
                        <div class="num">{{ $stats['total'] }}</div>
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
                        <div class="num">{{ $stats['active'] }}</div>
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
                        <div class="num">{{ $stats['inactive'] }}</div>
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
                        <div class="num">{{ $stats['delegates'] }}</div>
                        <div class="lbl">Delegados</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="card mb-2">
    <div class="card-body py-2">
        <form method="GET" action="{{ route($isDelegatesView ? 'users.delegates' : 'users.index') }}" id="filterForm">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Buscar</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="ri-search-line text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                               placeholder="Nombre, email, CI…" value="{{ request('search') }}">
                    </div>
                </div>

                @if(!$isDelegatesView)
                <div class="col-md-2">
                    <label class="form-label small mb-1">Rol</label>
                    <select name="role" class="form-select">
                        <option value="">Todos</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                            {{ $role->display_name ?? $role->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <label class="form-label small mb-1">Tipo Delegado</label>
                    <select name="delegate_type" class="form-select">
                        <option value="">Todos</option>
                        @foreach($delegateTypes as $val => $label)
                        <option value="{{ $val }}" {{ request('delegate_type') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>

                @if($isDelegatesView)
                <div class="col-md-2">
                    <label class="form-label small mb-1">Recinto</label>
                    <select name="institution_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                            {{ $inst->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <label class="form-label small mb-1">Estado</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                            {{ $isDelegatesView ? 'Activos' : 'Usuarios Activos' }}
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            {{ $isDelegatesView ? 'Inactivos' : 'Usuarios Inactivos' }}
                        </option>
                    </select>
                </div>

                <div class="col-md-{{ $isDelegatesView ? '3' : '3' }} d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    <a href="{{ route($isDelegatesView ? 'users.delegates' : 'users.index') }}"
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
            {{ $isDelegatesView ? 'Delegados' : 'Usuarios' }}
            <span class="badge bg-secondary ms-1">{{ $users->total() }}</span>
        </h5>
        @if(auth()->user()->hasPermission('create_users'))
        <a href="{{ route('users.create') }}" class="btn btn-success btn">
            <i class="ri-add-line me-1"></i>Nuevo Usuario
        </a>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <a href="{{ route('users.index', array_merge(request()->query(),['sort'=>'name','order'=> request('sort')=='name'&&request('order')=='asc'?'desc':'asc'])) }}" class="sort-link">
                                Usuario
                                @if(request('sort')=='name')<i class="ri-arrow-{{ request('order')=='asc'?'up':'down' }}-s-line"></i>@else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ route('users.index', array_merge(request()->query(),['sort'=>'email','order'=> request('sort')=='email'&&request('order')=='asc'?'desc':'asc'])) }}" class="sort-link">
                                Contacto
                                @if(request('sort')=='email')<i class="ri-arrow-{{ request('order')=='asc'?'up':'down' }}-s-line"></i>@else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                            </a>
                        </th>
                        <th>Roles</th>
                        <th>{{ $isDelegatesView ? 'Delegación Activa' : 'Delegación' }}</th>
                        <th>Estado</th>
                        @if(!$isDelegatesView)
                        <th>Últ. Acceso</th>
                        @endif
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $activeAssignments = $user->assignments->where('status', 'activo');
                        $primaryAssignment = $activeAssignments->first();
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg') }}"
                                     alt="" class="avatar-xs">
                                <div>
                                    <div class="fw-semibold">{{ $user->name }} {{ $user->last_name }}</div>
                                    <div class="text-muted small">
                                        @if($user->id_card)
                                            <i class="ri-fingerprint-line me-1"></i>{{ $user->id_card }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><i class="ri-mail-line text-muted me-1"></i>{{ Str::limit($user->email, 20) }}</div>
                            @if($user->phone)
                                <div><i class="ri-phone-line text-muted me-1"></i>{{ $user->phone }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($user->roles->take(2) as $r)
                                    <span class="badge bg-info-subtle text-info" style="font-size:.65rem">
                                        {{ Str::limit($r->display_name ?? $r->name, 12) }}
                                    </span>
                                @endforeach
                                @if($user->roles->count() > 2)
                                    <span class="badge bg-secondary" style="font-size:.65rem">+{{ $user->roles->count()-2 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($primaryAssignment)
                                <div class="d-flex flex-column gap-1">
                                    <span class="delegate-badge {{ $primaryAssignment->voting_table_id ? 'mesa' : 'recinto' }}">
                                        <i class="ri-{{ $primaryAssignment->voting_table_id ? 'table' : 'building' }}-line me-1"></i>
                                        {{ $delegateTypes[$primaryAssignment->delegate_type] ?? $primaryAssignment->delegate_type }}
                                    </span>
                                    <small class="text-muted">
                                        {{ $primaryAssignment->institution->code ?? '' }}
                                        @if($primaryAssignment->voting_table_id)
                                            - Mesa {{ $primaryAssignment->votingTable?->number }}
                                        @endif
                                    </small>
                                    @if($activeAssignments->count() > 1)
                                        <span class="delegate-multiple">
                                            +{{ $activeAssignments->count()-1 }} más
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                            @endif
                        </td>

                        @if(!$isDelegatesView)
                        <td>
                            <small class="text-muted" title="{{ $user->last_login_at?->format('d/m/Y H:i') }}">
                                {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Nunca' }}
                            </small>
                        </td>
                        @endif
                        <td>
                            <div class="d-flex gap-1 justify-content-end">
                                @if(auth()->user()->hasPermission('view_users'))
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-soft-info" title="Ver perfil"><i class="ri-eye-line"></i></a>
                                @endif
                                @if(auth()->user()->hasPermission('edit_users'))
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-soft-warning" title="Editar"><i class="ri-pencil-line"></i></a>
                                @endif
                                @if(auth()->user()->hasPermission('assign_roles'))
                                    <a href="{{ route('users.delegaciones.form', $user) }}" class="btn btn-sm btn-soft-primary" title="Delegaciones"><i class="ri-shield-user-line"></i></a>
                                @endif
                                @if($user->id !== auth()->id())
                                    @if($user->is_active && auth()->user()->hasPermission('delete_users'))
                                    <button class="btn btn-sm btn-soft-danger" onclick="confirmDeactivate({{ $user->id }},'{{ addslashes($user->name) }}')"><i class="ri-user-unfollow-line"></i></button>
                                    @elseif(!$user->is_active && auth()->user()->hasPermission('activate_users'))
                                    <form method="POST" action="{{ route('users.activate', $user) }}" class="d-inline">@csrf
                                        <button type="submit" class="btn btn-sm btn-soft-success" title="Activar"><i class="ri-user-follow-line"></i></button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isDelegatesView ? '5' : '7' }}" class="text-center py-5">
                            <i class="ri-user-search-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                            <p class="text-muted mb-1">
                                No se encontraron {{ $isDelegatesView ? 'delegados' : 'usuarios' }}
                            </p>
                            @if(request()->hasAny(['search','role','status','delegate_type','institution_id']))
                            <a href="{{ route($isDelegatesView ? 'users.delegates' : 'users.index') }}"
                               class="btn btn-outline-secondary mt-2">
                                <i class="ri-close-line me-1"></i>Limpiar filtros
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex align-items-center justify-content-between flex-wrap gap-2 py-2 px-3">
        <small class="text-muted">Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }} usuarios</small>
        {{ $users->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const CSRF = '{{ csrf_token() }}';
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
@endsection
