{{-- resources/views/users/edit.blade.php --}}
@extends('layouts.master')
@section('title') Editar — {{ $user->name }} @endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"/>
<style>
.btn-xs{padding:.15rem .45rem;font-size:.75rem}
.perm-item{transition:background .15s;border-radius:.25rem}
.role-card{transition:all .15s}
.perm-columns{columns:2;column-gap:0}
.perm-group:last-child{border-bottom:none!important}
@media(min-width:1200px){.perm-columns{columns:3}}
</style>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') Usuarios @endslot
    @slot('li_2') <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a> @endslot
    @slot('title') Editar Usuario @endslot
@endcomponent

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Editar: {{ $user->name }} {{ $user->last_name }}</h5>
        <a href="{{ route('users.show', $user) }}" class="btn btn-soft-secondary btn">
            <i class="ri-arrow-left-line me-1"></i>Volver al perfil
        </a>
    </div>
    <div class="card-body">
        @include('components.alerts')
        <form action="{{ route('users.update', $user) }}" method="POST" id="userForm">
            @csrf
            @method('PUT')
            @include('users._form', [
                'user'             => $user,
                'isEdit'           => true,
                'userRoleIds'      => $userRoleIds,
                'userDirectPermIds'=> $userDirectPermIds,
                'rolePermMap'      => $rolePermMap,
            ])
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('users.delegaciones.form', $user) }}" class="btn btn-soft-primary btn">
                    <i class="ri-map-pin-line me-1"></i>Gestionar Delegaciones
                </a>
                <div class="d-flex gap-2">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-soft-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>Guardar Cambios
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
@include('users._form_js')
@endsection
