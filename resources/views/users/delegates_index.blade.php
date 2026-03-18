{{-- resources/views/users/delegates_index.blade.php --}}
@extends('layouts.master')
@section('title') Gestión de Delegados @endsection

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
<style>
.delegate-card {
    border: 1px solid #e9e9ef;
    border-radius: 0.5rem;
    padding: 1rem;
    transition: all 0.2s;
    background: white;
    height: 100%;
}
.delegate-card:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    border-color: #0ab39c;
}
.delegate-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.delegate-type-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #0ab39c;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 2px solid white;
}
.delegate-stats {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
}
.stat-item {
    text-align: center;
    padding: 0.5rem;
}
.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0ab39c;
    line-height: 1;
}
.stat-label {
    font-size: 0.7rem;
    color: #74788d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.assignment-item {
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    background: #f8f9fa;
    border-radius: 0.25rem;
    margin-bottom: 0.2rem;
    border-left: 3px solid #0ab39c;
}
.assignment-item.mesa {
    border-left-color: #0890c9;
}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Sistema @endslot
    @slot('title') Gestión de Delegados @endslot
@endcomponent

@include('components.alerts')

{{-- Stats Cards --}}
<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="delegate-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['total_delegates'] }}</div>
                <div class="stat-label">Total Delegados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="delegate-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['active_assignments'] }}</div>
                <div class="stat-label">Asignaciones Activas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="delegate-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['recinto_count'] }}</div>
                <div class="stat-label">Recintos</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="delegate-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $stats['mesa_count'] }}</div>
                <div class="stat-label">Mesas</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('users.delegates.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Buscar Delegado</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nombre, email, CI..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Tipo Delegado</label>
                    <select name="delegate_type" class="form-select">
                        <option value="">Todos</option>
                        @foreach($delegateTypes as $val => $label)
                        <option value="{{ $val }}" {{ request('delegate_type') == $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Recinto</label>
                    <select name="institution_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ request('institution_id') == $inst->id ? 'selected' : '' }}>
                            {{ $inst->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Estado</label>
                    <select name="assignment_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('assignment_status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="suspendido" {{ request('assignment_status') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="ri-filter-3-line me-1"></i>Filtrar
                    </button>
                    <a href="{{ route('users.delegates.index') }}" class="btn btn-soft-secondary">
                        <i class="ri-close-line"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Delegates Grid --}}
<div class="row g-3">
    @forelse($delegates as $delegate)
    <div class="col-xl-4 col-lg-6">
        <div class="delegate-card position-relative">
            {{-- Type Badge --}}
            @php
                $mainAssignment = $delegate->active_assignments->first();
                $typeIcon = match($mainAssignment?->delegate_type) {
                    'delegado_general' => 'ri-user-star-line',
                    'delegado_mesa' => 'ri-table-line',
                    'presidente' => 'ri-award-line',
                    'secretario' => 'ri-edit-2-line',
                    'vocal' => 'ri-mic-line',
                    'tecnico' => 'ri-tools-line',
                    'observador' => 'ri-eye-line',
                    default => 'ri-user-line'
                };
            @endphp
            <div class="delegate-type-badge" title="{{ $delegateTypes[$mainAssignment?->delegate_type] ?? 'Delegado' }}">
                <i class="{{ $typeIcon }}"></i>
            </div>

            {{-- Header --}}
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ $delegate->avatar ? URL::asset('build/images/users/'.$delegate->avatar) : URL::asset('build/images/users/avatar-1.jpg') }}"
                     alt="" class="delegate-avatar">
                <div class="flex-grow-1">
                    <h6 class="mb-1">{{ $delegate->name }} {{ $delegate->last_name }}</h6>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge {{ $delegate->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $delegate->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                        <small class="text-muted">{{ $delegate->email }}</small>
                    </div>
                </div>
            </div>

            {{-- Assignments --}}
            <div class="mb-3">
                <small class="text-muted text-uppercase fw-semibold d-block mb-2">
                    <i class="ri-map-pin-line me-1"></i>Asignaciones ({{ $delegate->active_assignments->count() }})
                </small>
                @foreach($delegate->active_assignments->take(2) as $assign)
                <div class="assignment-item {{ $assign->voting_table_id ? 'mesa' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">{{ $delegateTypes[$assign->delegate_type] ?? $assign->delegate_type }}</span>
                        <small class="text-muted">{{ $assign->institution->code ?? '' }}</small>
                    </div>
                    <div class="small">
                        @if($assign->voting_table_id)
                            <i class="ri-table-line text-info me-1"></i>Mesa {{ $assign->votingTable?->number }}
                        @else
                            <i class="ri-building-line text-success me-1"></i>{{ $assign->institution?->name }}
                        @endif
                    </div>
                </div>
                @endforeach
                @if($delegate->active_assignments->count() > 2)
                <small class="text-muted d-block text-center mt-1">
                    +{{ $delegate->active_assignments->count() - 2 }} más
                </small>
                @endif
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                <a href="{{ route('users.show', $delegate) }}" class="btn btn-sm btn-soft-info flex-grow-1">
                    <i class="ri-eye-line me-1"></i>Perfil
                </a>
                <a href="{{ route('users.delegaciones.form', $delegate) }}" class="btn btn-sm btn-soft-primary flex-grow-1">
                    <i class="ri-shield-user-line me-1"></i>Delegaciones
                </a>
                @if(auth()->user()->hasPermission('edit_users'))
                <a href="{{ route('users.edit', $delegate) }}" class="btn btn-sm btn-soft-warning">
                    <i class="ri-pencil-line"></i>
                </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <i class="ri-user-search-line d-block mb-3" style="font-size: 3rem; color: #adb5bd;"></i>
            <h5>No se encontraron delegados</h5>
            <p class="text-muted">No hay usuarios con asignaciones de delegado activas</p>
            @if(auth()->user()->hasPermission('create_users'))
            <a href="{{ route('users.create') }}" class="btn btn-primary mt-2">
                <i class="ri-add-line me-1"></i>Nuevo Usuario
            </a>
            @endif
        </div>
    </div>
    @endforelse
</div>

@if($delegates->hasPages())
<div class="mt-3">
    {{ $delegates->appends(request()->query())->links() }}
</div>
@endif
@endsection
