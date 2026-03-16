{{-- resources/views/voting-tables/edit.blade.php --}}
@extends('layouts.master')
@section('title') Editar Mesa {{ $votingTable->number }} @endsection

@section('css')
<link href="{{ URL::asset('build/libs/choices.js/public/assets/styles/choices.min.css') }}" rel="stylesheet"/>
@endsection

@section('content')
@component('components.breadcrumb')
    @slot('li_1') <a href="{{ route('voting-tables.index') }}">Mesas</a> @endslot
    @slot('li_2') <a href="{{ route('voting-tables.show', $votingTable) }}">Mesa {{ $votingTable->number }}{{ $votingTable->letter ?? '' }}</a> @endslot
    @slot('title') Editar Mesa @endslot
@endcomponent

@include('components.alerts')
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">
            <i class="ri-pencil-line me-1"></i>Editando: <span class="text-primary">Mesa {{ $votingTable->number }}{{ $votingTable->letter ?? '' }}</span></h5>
        <div class="d-flex gap-2">
            <a href="{{ route('voting-tables.show', $votingTable) }}" class="btn btn-soft-info btn-sm">
                <i class="ri-eye-line me-1"></i>Ver
            </a>
            <a href="{{ route('voting-tables.index') }}" class="btn btn-soft-secondary btn-sm">
                <i class="ri-arrow-left-line me-1"></i>Volver
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('voting-tables.update', $votingTable) }}" method="POST" id="votingTableForm">
            @csrf
            @method('PUT')
            @include('voting-tables.partials.form-fields', [
                'votingTable' => $votingTable,
                'institutions' => $institutions,
                'users' => $users ?? [],
                'departments' => $departments ?? [],
            ])
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('voting-tables.show', $votingTable) }}" class="btn btn-soft-secondary">
                    <i class="ri-close-line me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line me-1"></i>Actualizar Mesa
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script src="{{ URL::asset('build/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
@include('voting-tables.scripts.voting-table-js')
@endsection
