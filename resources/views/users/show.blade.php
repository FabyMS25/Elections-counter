{{-- resources/views/users/show.blade.php --}}
@extends('layouts.master')
@section('title') {{ $user->name }} {{ $user->last_name }} @endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
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
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Usuarios @endslot
    @slot('li_2') <a href="{{ route('users.index') }}">Lista</a> @endslot
    @slot('title') {{ $user->name }} {{ $user->last_name }} @endslot
@endcomponent

@include('components.alerts')
<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <img src="{{ $user->avatar ? URL::asset('build/images/users/'.$user->avatar) : URL::asset('build/images/users/avatar-1.jpg') }}"
                     alt="" class="avatar-ring">
            </div>
            <div class="col">
                <h4 class="mb-0">{{ $user->name }} {{ $user->last_name }}</h4>
                <p class="text-muted small mb-1">
                    {{ $user->email }}
                    @if($user->id_card) &bull; CI: {{ $user->id_card }} @endif
                    @if($user->phone) &bull; {{ $user->phone }} @endif
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                    @foreach($user->roles->take(4) as $r)
                    <span class="badge bg-info-subtle text-info">{{ $r->display_name ?? $r->name }}</span>
                    @endforeach
                    @if($user->roles->count() > 4)
                    <span class="badge bg-secondary">+{{ $user->roles->count()-4 }} más</span>
                    @endif
                </div>
            </div>
            <div class="col-auto d-none d-lg-flex gap-2">
                <div class="stat-box"><div class="n">{{ $user->roles->count() }}</div><div class="l">Roles</div></div>
                <div class="stat-box"><div class="n">{{ $totalPermCount }}</div><div class="l">Permisos</div></div>
            </div>
            <div class="col-auto d-flex gap-2 flex-wrap">
                @if(auth()->user()->hasPermission('edit_users'))
                <a href="{{ route('users.edit', $user) }}" class="btn btn-soft-warning btn" title="Editar">
                    <i class="ri-pencil-line me-1"></i>
                </a>
                @endif
                @if(auth()->user()->hasPermission('assign_roles'))
                <a href="{{ route('users.delegaciones.form', $user) }}" class="btn btn-soft-primary btn" title="Delegaciones">
                    <i class="ri-shield-user-line me-1"></i>{{-- Delegaciones --}}
                </a>
                @endif
                @if($user->id !== auth()->id())
                    @if($user->is_active && auth()->user()->hasPermission('delete_users'))
                    <button class="btn btn-soft-danger btn" title="Desactivar"
                            onclick="confirmDeactivate({{ $user->id }},'{{ addslashes($user->name) }}')">
                        <i class="ri-user-unfollow-line me-1"></i>{{-- Desactivar --}}
                    </button>
                    @elseif(!$user->is_active && auth()->user()->hasPermission('activate_users'))
                    <form method="POST" action="{{ route('users.activate', $user) }}" class="d-inline">@csrf
                        <button class="btn btn-soft-success btn" title="Activar"><i class="ri-user-follow-line me-1"></i>{{-- Activar --}}</button>
                    </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
<div class="d-flex gap-2 mb-3 flex-wrap">
    <div class="tab-pill active" id="tpInfo"  onclick="showTab('info',this)"><i class="ri-user-line me-1"></i>Información</div>
    <div class="tab-pill"        id="tpRoles" onclick="showTab('roles',this)">
        <i class="ri-shield-user-line me-1"></i>Roles y Delegaciones
        @if($user->roles->count()) <span class="badge bg-primary ms-1">{{ $user->roles->count() }}</span> @endif
    </div>
    <div class="tab-pill"        id="tpPerms" onclick="showTab('perms',this)">
        <i class="ri-key-line me-1"></i>Permisos
        <span class="badge bg-secondary ms-1">{{ $totalPermCount }}</span>
    </div>
    <div class="tab-pill"        id="tpLog"   onclick="showTab('log',this)"><i class="ri-history-line me-1"></i>Actividad</div>
</div>
<div id="tab-info">
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Datos del usuario</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row"><div class="info-k">Nombres</div><div class="info-v">{{ $user->name }}</div></div>
                    <div class="info-row"><div class="info-k">Apellidos</div><div class="info-v">{{ $user->last_name ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-k">Carnet</div><div class="info-v">{{ $user->id_card ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-k">Email</div><div class="info-v">{{ $user->email }}</div></div>
                    <div class="info-row"><div class="info-k">Teléfono</div><div class="info-v">{{ $user->phone ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-k">Dirección</div><div class="info-v">{{ $user->address ?? '—' }}</div></div>
                </div>
                <div class="col-md-6">
                    <div class="info-row"><div class="info-k">Estado</div><div class="info-v"><span class="badge {{ $user->is_active?'bg-success':'bg-danger' }}">{{ $user->is_active?'Activo':'Inactivo' }}</span></div></div>
                    <div class="info-row"><div class="info-k">Registrado</div><div class="info-v">{{ $user->created_at->format('d/m/Y H:i') }}</div></div>
                    <div class="info-row"><div class="info-k">Último acceso</div><div class="info-v">{{ $user->last_login_at?->format('d/m/Y H:i') ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-k">Última IP</div><div class="info-v">{{ $user->last_login_ip ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-k">Creado por</div><div class="info-v">{{ $user->createdBy?->name ?? '—' }}</div></div>
                    <div class="info-row"><div class="info-k">Actualizado por</div><div class="info-v">{{ $user->updatedBy?->name ?? '—' }}</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="tab-roles" style="display:none">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0">Roles y Delegaciones</h5>
            @if(auth()->user()->hasPermission('assign_roles'))
            <a href="{{ route('users.delegaciones.form', $user) }}" class="btn btn-soft-primary btn-sm">
                <i class="ri-edit-line me-1"></i>Gestionar
            </a>
            @endif
        </div>
        <div class="card-body">
            @forelse($user->roles as $role)
            <div class="del-row p-3 mb-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="flex-grow-1">
                        <div class="fw-semibold">{{ $role->display_name ?? $role->name }}</div>
                        <small class="text-muted">{{ $role->description }}</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">
                        {{ $role->permissions->count() }} permisos
                    </span>

                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <i class="ri-shield-user-line d-block mb-2" style="font-size:2rem"></i>
                <p class="mb-1">Sin roles asignados</p>
                @if(auth()->user()->hasPermission('edit_users'))
                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary mt-1">Asignar roles</a>
                @endif
            </div>
            @endforelse
        </div>
    </div>
</div>
<div id="tab-perms" style="display:none">
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="card-title mb-0">Permisos efectivos</h5>
                <small class="text-muted">
                    <span class="badge bg-dark">{{ $totalPermCount }}</span> permisos activos &nbsp;
                    <span class="badge bg-info-subtle text-info">{{ count($rolePermIds) }} de {{ $user->roles->count() }} roles</span>
                </small>
            </div>
            @if(auth()->user()->hasPermission('edit_users'))
            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-soft-warning">
                <i class="ri-pencil-line me-1"></i>Editar permisos
            </a>
            @endif
        </div>
        <div class="card-body">
            @php
                $groupOrder = ['Usuarios','Roles y Permisos','Recintos','Mesas de Votación','Votos','Actas','Observaciones','Delegaciones','Auditoría','Configuración','Dashboard'];
                $allPerms = \App\Models\Permission::orderBy('display_name')->get()
                    ->groupBy('group')
                    ->sortBy(fn($_, $g) => ($k = array_search($g, $groupOrder)) !== false ? $k : 99);
            @endphp
            @foreach($allPerms as $group => $perms)
            @php $active = $perms->filter(fn($p)=>in_array($p->id,$allEffectivePermIds)); @endphp
            @if($active->count())
            <div class="mb-3">
                <div class="text-uppercase fw-semibold small text-muted mb-1" style="font-size:.65rem;letter-spacing:.06em">{{ $group }}</div>
                @foreach($active as $p)
                <span class="perm-chip" title="{{ $p->display_name ?? $p->name }}">
                    {{ $p->display_name ?? $p->name }}
                </span>
                @endforeach
            </div>
            @endif
            @endforeach
            @if(!$totalPermCount)
            <div class="text-center py-4 text-muted">Sin permisos asignados</div>
            @endif
            <div class="mt-3 pt-2 border-top small text-muted">
                <i class="ri-information-line me-1"></i>
                Estos son los permisos efectivos del usuario. Para modificarlos usa
                <a href="{{ route('users.edit', $user) }}">Editar Usuario</a>.
                Los roles son presets de selección — los permisos guardados son la fuente de verdad.
            </div>
        </div>
    </div>
</div>

{{-- ══ TAB: ACTIVIDAD ══════════════════════════════════════════════════════ --}}
<div id="tab-log" style="display:none">
    <div class="card">
        <div class="card-header"><h5 class="card-title mb-0">Historial de Actividad</h5></div>
        <div class="card-body">
            @php
            $logs=\App\Models\AuditLog::where(function($q)use($user){
                $q->where('user_id',$user->id)->orWhere(function($q2)use($user){
                    $q2->where('model_type',\App\Models\User::class)->where('model_id',$user->id);
                });
            })->with('user')->latest()->take(20)->get();
            @endphp
            @forelse($logs as $log)
            <div class="d-flex gap-3 py-2 border-bottom align-items-start">
                <div class="text-muted small text-nowrap" style="min-width:105px">{{ ($log->performed_at??$log->created_at)?->format('d/m/Y H:i') }}</div>
                <div style="min-width:80px">
                    @php $c=match($log->action??''){'created'=>'success','updated'=>'primary','deleted'=>'danger','restored'=>'info',default=>'secondary'}; @endphp
                    <span class="badge bg-{{ $c }}">{{ $log->action }}</span>
                </div>
                <div class="flex-grow-1 small">{{ $log->description }}</div>
                <div class="text-muted small text-nowrap">{{ $log->ip_address??'—' }}</div>
            </div>
            @empty
            <p class="text-center text-muted py-3 mb-0">Sin actividad registrada</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
const CSRF='{{ csrf_token() }}';
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
@endsection
