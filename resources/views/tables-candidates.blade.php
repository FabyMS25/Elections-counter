@extends('layouts.master')
@section('title')
    @lang('translation.list-candidates')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- <link href="{{ URL::asset('build/libs/choices.js/choices.js.min.css') }}" rel="stylesheet" type="text/css" /> -->
    <style>
        .image-preview-container {
            margin-top: 10px;
            text-align: center;
        }
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            margin: 5px;
        }
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Tables
        @endslot
        @slot('title')
            Candidatos
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Administración de Candidatos</h4>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="listjs-table" id="candidateList">
                        <div class="row g-4 mb-3">
                            <div class="col-sm-auto">
                                <div>
                                    <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal"
                                        id="create-btn" data-bs-target="#showModal"><i
                                            class="ri-add-line align-bottom me-1"></i> Agregar</button>
                                    <button class="btn btn-soft-danger" onClick="deleteMultiple()"><i
                                            class="ri-delete-bin-2-line"></i></button>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="d-flex justify-content-sm-end">
                                    <div class="search-box ms-2">
                                        <input type="text" class="form-control search" placeholder="Buscar...">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive table-card mt-3 mb-1">
                            <table class="table align-middle table-nowrap" id="customerTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll">
                                            </div>
                                        </th>
                                        <th class="sort" data-sort="photo">Foto</th>
                                        <th class="sort" data-sort="name">Nombre</th>
                                        <th class="sort" data-sort="party">Partido</th>
                                        <th data-sort="color">Color</th>
                                        <th class="sort" data-sort="position">Cargo</th>
                                        <th class="actions-column" >Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @foreach($candidates as $candidate)
                                        <tr>
                                            <th scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="chk_child" value="{{ $candidate->id }}">
                                                </div>
                                            </th>
                                            <td class="photo">
                                                <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}"  class="avatar-xs rounded-circle">
                                            </td>
                                            <td class="name">{{ $candidate->name }}</td>
                                            <td class="party">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <div class="flex-shrink-0">
                                                    @if($candidate->party_logo)
                                                        <img src="{{ $candidate->party_logo_url }}" alt="{{ $candidate->party }}"  class="avatar-xs rounded-circle">
                                                    @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        {{ $candidate->party }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="color">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <div class="flex-shrink-0">
                                                    @if($candidate->party_logo)
                                                        <img src="{{ $candidate->party_logo_url }}" alt="{{ $candidate->party }}"  class="avatar-xs rounded-circle">
                                                    @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        {{ $candidate->party }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="position">
                                                <span class="badge bg-primary-subtle text-primary">
                                                    {{ $candidate->position }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <div class="edit">
                                                        <button class="btn btn-sm btn-success edit-item-btn"
                                                            data-bs-toggle="modal" data-bs-target="#showModal"
                                                            data-id="{{ $candidate->id }}"
                                                            data-name="{{ $candidate->name }}"
                                                            data-party="{{ $candidate->party }}"
                                                            data-position="{{ $candidate->position }}"
                                                            data-photo="{{ $candidate->photo }}"
                                                            data-party_logo="{{ $candidate->party_logo }}"
                                                            data-photo-url="{{ $candidate->photo_url }}"
                                                            data-party-logo-url="{{ $candidate->party_logo_url }}"
                                                            data-update-url="{{ route('candidates.update', $candidate->id) }}">
                                                            Editar
                                                        </button>
                                                    </div>
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteRecordModal"
                                                            data-id="{{ $candidate->id }}"
                                                            data-delete-url="{{ route('candidates.destroy', $candidate->id) }}">
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            @if($candidates->isEmpty())
                                <div class="noresult">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                        </lord-icon>
                                        <h5 class="mt-2">Lo sentimos! No se encontraron resultados</h5>
                                        <p class="text-muted mb-0">No hay candidatos registrados en el sistema.</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="javascript:void(0);">
                                    Anterior
                                </a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="javascript:void(0);">
                                    Siguiente
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Nuevo Candidato</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form id="candidateForm" method="POST" class="tablelist-form" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="method_field" name="_method" value="">
                    <input type="hidden" id="candidate_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name-field" class="form-label">Nombre <span class="text-danger">*</span></label>
                                    <input type="text" id="name-field" name="name" class="form-control @error('name') is-invalid @enderror" 
                                        placeholder="Ingrese el nombre completo" value="{{ old('name') }}" required />
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Por favor ingrese un nombre válido.</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="party-field" class="form-label">Partido Político <span class="text-danger">*</span></label>
                                    <input type="text" id="party-field" name="party" class="form-control @error('party') is-invalid @enderror" 
                                        placeholder="Ingrese el partido político" value="{{ old('party') }}" required />
                                    @error('party')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Por favor ingrese un partido político.</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position-field" class="form-label">Cargo <span class="text-danger">*</span></label>
                                    <select class="form-control @error('position') is-invalid @enderror" name="position" id="position-field" required>
                                        <option value="">Seleccione un cargo</option>
                                        @foreach($positions as $position)
                                            <option value="{{ $position }}" {{ old('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                                        @endforeach
                                    </select>
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Por favor seleccione un cargo.</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="position-field" class="form-label">Color <span class="text-danger">*</span></label>
                                    <input type="color" class="form-control form-control-color w-100 @error('color') is-invalid @enderror" 
                                        id="color-field" name="color" value="#1b8af8ff">                                    
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <div class="invalid-feedback">Por favor ingrese un color.</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="photo-field" class="form-label">Foto del Candidato</label>
                                    <input type="file" id="photo-field" name="photo" class="form-control @error('photo') is-invalid @enderror" 
                                        accept="image/*" />
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="image-preview-container" id="photo-preview-container">
                                        <img id="photo-preview" class="image-preview" src="" style="display: none;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="party_logo-field" class="form-label">Logo del Partido</label>
                                    <input type="file" id="party_logo-field" name="party_logo" class="form-control @error('party_logo') is-invalid @enderror" 
                                        accept="image/*" />
                                    @error('party_logo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="image-preview-container" id="party-logo-preview-container">
                                        <img id="party-logo-preview" class="image-preview" src="" style="display: none;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success" id="save-btn">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mt-2 text-center">
                        <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                            colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                        <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                            <h4>¿Está seguro?</h4>
                            <p class="text-muted mx-4 mb-0">¿Está seguro de que desea eliminar este candidato?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <form id="deleteForm" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn w-sm btn-danger">Sí, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end modal -->
@endsection

@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- <script src="{{ URL::asset('build/libs/choices.js/choices.js.min.js') }}"></script> -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Init Choices.js
            const positionSelect = new Choices('#position-field', {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
            });

            // Utility: image preview
            function handleImagePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                input.addEventListener('change', e => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = ev => {
                            preview.src = ev.target.result;
                            preview.style.display = 'block';
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
            handleImagePreview('photo-field', 'photo-preview');
            handleImagePreview('party_logo-field', 'party-logo-preview');

            // List.js
            const candidateList = new List('candidateList', {
                valueNames: ['name', 'party', 'position']
            }).on('updated', () => {
                attachEditDeleteEvents();
            });

            // Select/Deselect all checkboxes
            document.getElementById('checkAll').addEventListener('change', function () {
                document.querySelectorAll('input[name="chk_child"]').forEach(cb => cb.checked = this.checked);
            });

            // Reset form for new candidate
            document.getElementById('create-btn').addEventListener('click', () => {
                document.getElementById('exampleModalLabel').textContent = 'Agregar Nuevo Candidato';
                const form = document.getElementById('candidateForm');
                form.action = "{{ route('candidates.store') }}";
                document.getElementById('method_field').value = '';
                form.reset();
                document.getElementById('candidate_id').value = '';
                document.getElementById('save-btn').textContent = 'Guardar';
                positionSelect.setChoiceByValue('');
                ['photo-preview', 'party-logo-preview'].forEach(id => document.getElementById(id).style.display = 'none');
                document.getElementById('color-field').value = '#1b8af8'; // reset color default
                clearValidationErrors();
            });

            // Edit & Delete
            function attachEditDeleteEvents() {
                // Edit buttons
                document.querySelectorAll('.edit-item-btn').forEach(btn => {
                    btn.onclick = () => {
                        document.getElementById('exampleModalLabel').textContent = 'Editar Candidato';
                        const form = document.getElementById('candidateForm');
                        form.action = btn.dataset.updateUrl;
                        document.getElementById('method_field').value = 'PUT';
                        document.getElementById('candidate_id').value = btn.dataset.id;
                        document.getElementById('name-field').value = btn.dataset.name;
                        document.getElementById('party-field').value = btn.dataset.party;
                        positionSelect.setChoiceByValue(btn.dataset.position);
                        document.getElementById('color-field').value = btn.dataset.color || '#1b8af8';
                        
                        const photoPreview = document.getElementById('photo-preview');
                        const logoPreview = document.getElementById('party-logo-preview');
                        photoPreview.style.display = btn.dataset.photoUrl ? 'block' : 'none';
                        logoPreview.style.display = btn.dataset.partyLogoUrl ? 'block' : 'none';
                        if (btn.dataset.photoUrl) photoPreview.src = btn.dataset.photoUrl;
                        if (btn.dataset.partyLogoUrl) logoPreview.src = btn.dataset.partyLogoUrl;

                        document.getElementById('save-btn').textContent = 'Actualizar';
                        clearValidationErrors();
                    };
                });

                // Delete buttons
                document.querySelectorAll('.remove-item-btn').forEach(btn => {
                    btn.onclick = () => document.getElementById('deleteForm').action = btn.dataset.deleteUrl;
                });
            }
            attachEditDeleteEvents();

            // Form validation
            const form = document.getElementById('candidateForm');
            form.addEventListener('submit', function (event) {
                let isValid = true;
                ['name', 'party', 'position', 'color'].forEach(field => {
                    const el = document.getElementById(field + '-field');
                    if (!el.value.trim()) {
                        el.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        el.classList.remove('is-invalid');
                    }
                });
                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });

            document.getElementById('showModal').addEventListener('hidden.bs.modal', clearValidationErrors);

            function clearValidationErrors() {
                document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            }
        });

        function deleteMultiple() {
            alert('Función de eliminar múltiple - por implementar');
        }
    </script>
@endsection