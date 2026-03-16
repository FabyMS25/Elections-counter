{{-- resources/views/institutions/create.blade.php --}}
@extends('layouts.master')
@section('title') Nuevo Recinto Electoral @endsection

@section('css')
<link href="{{ URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"/>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('institutions.index') }}">Recintos</a> @endslot
    @slot('title') Nuevo Recinto Electoral @endslot
@endcomponent

@include('components.alerts')

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0"><i class="ri-add-line me-1"></i>Nuevo Recinto Electoral</h5>
        <a href="{{ route('institutions.index') }}" class="btn btn-soft-secondary btn-sm">
            <i class="ri-arrow-left-line me-1"></i>Volver
        </a>
    </div>
    <div class="card-body">
        <form action="{{ route('institutions.store') }}" method="POST" id="institutionForm">
            @csrf
            @include('institutions.partials.form-fields', [
                'institution' => null,
                'departments' => $departments,
                'statusOptions' => $statusOptions,
            ])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('institutions.index') }}" class="btn btn-soft-secondary">
                    <i class="ri-close-line me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Crear Recinto
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
