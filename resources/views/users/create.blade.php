{{-- resources/views/users/create.blade.php --}}
@extends('layouts.master')
@section('title') Crear Usuario @endsection
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
    @slot('li_2') <a href="{{ route('users.index') }}">Lista</a> @endslot
    @slot('title') Crear Nuevo Usuario @endslot
@endcomponent

<div class="card">
    <div class="card-header"><h5 class="card-title mb-0">Nuevo Usuario</h5></div>
    <div class="card-body">
        @include('components.alerts')
        <form action="{{ route('users.store') }}" method="POST" id="userForm">
            @csrf
            @include('users._form', [
                'user'             => null,
                'isEdit'           => false,
                'userRoleIds'      => [],
                'userDirectPermIds'=> [],
                'rolePermMap'      => $rolePermMap,
            ])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('users.index') }}" class="btn btn-soft-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
@include('users._form_js')
@endsection
