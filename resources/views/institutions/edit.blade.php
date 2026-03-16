{{-- resources/views/institutions/edit.blade.php --}}
@extends('layouts.master')
@section('title') Editar {{ $institution->name }} @endsection

@section('css')
<link href="{{ URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"/>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('institutions.index') }}">Recintos</a> @endslot
    @slot('li_2') <a href="{{ route('institutions.show', $institution) }}">{{ $institution->name }}</a> @endslot
    @slot('title') Editar Recinto @endslot
@endcomponent

@include('components.alerts')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-pencil-line me-1"></i>Editando: <span class="text-primary">{{ $institution->name }}</span></h5>
        <div class="d-flex gap-2">
            <a href="{{ route('institutions.show', $institution) }}" class="btn btn-soft-info btn-sm">
                <i class="ri-eye-line me-1"></i>Ver
            </a>
            <a href="{{ route('institutions.index') }}" class="btn btn-soft-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('institutions.update', $institution) }}" method="POST" id="institutionForm">
            @csrf
            @method('PUT')
            @include('institutions.partials.form-fields', [
                'institution' => $institution,
                'departments' => $departments,
                'statusOptions' => $statusOptions,
            ])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('institutions.show', $institution) }}" class="btn btn-soft-secondary">
                    <i class="ri-close-line me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Actualizar Recinto
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
@include('institutions.scripts.institution-js')
@endsection
