{{-- resources/views/candidates/partials/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:50px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th style="width:48px;">Foto</th>
                @php
                    $sortCols = [
                        'name'              => 'Nombre',
                        'party'             => 'Partido',
                        'election_type'     => 'Tipo Elección',
                        'election_category' => 'Categoría',
                    ];
                @endphp
                @foreach($sortCols as $col => $label)
                <th>
                    <a href="{{ route('candidates.index', array_merge(request()->query(), [
                            'sort'      => $col,
                            'direction' => (request('sort') === $col && request('direction') === 'asc') ? 'desc' : 'asc',
                        ])) }}" class="sort-link">
                        {{ $label }}
                        @if(request('sort') === $col)
                            <i class="ri-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }}-s-line"></i>
                        @else
                            <i class="ri-arrow-up-down-line text-muted opacity-50"></i>
                        @endif
                    </a>
                </th>
                @endforeach
                <th>Color</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($candidates as $candidate)
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input child-checkbox" type="checkbox"
                               name="selected_ids[]" value="{{ $candidate->id }}">
                    </div>
                </td>
                <td>
                    @if($candidate->photo)
                        <img src="{{ $candidate->photo_url }}" alt="{{ $candidate->name }}"
                             class="avatar-xs rounded-circle" style="object-fit:cover;">
                    @else
                        <div class="avatar-xs bg-light rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-user-line text-muted" style="font-size:.9rem"></i>
                        </div>
                    @endif
                </td>
                <td>
                    <div class="fw-semibold">{{ $candidate->name }}</div>
                    @if($candidate->list_name)
                        <small class="text-muted">{{ $candidate->list_name }}
                            @if($candidate->list_order) · #{{ $candidate->list_order }} @endif
                        </small>
                    @endif
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        @if($candidate->party_logo)
                            <img src="{{ $candidate->party_logo_url }}" alt="{{ $candidate->party }}"
                                 style="width:24px;height:24px;object-fit:contain;flex-shrink:0;">
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $candidate->party }}</div>
                            @if($candidate->party_full_name)
                                <small class="text-muted">{{ Str::limit($candidate->party_full_name, 30) }}</small>
                            @endif
                        </div>
                    </div>
                </td>
                <td>
                    <small class="text-muted">{{ $candidate->electionTypeCategory?->electionType?->name ?? '—' }}</small>
                </td>
                <td>
                    @if($candidate->electionTypeCategory?->electionCategory)
                        @php $cat = $candidate->electionTypeCategory->electionCategory; @endphp
                        <span class="badge bg-primary-subtle text-primary" style="font-size:.68rem;">
                            {{ $cat->code }}
                        </span>
                        <small class="text-muted d-block" style="font-size:.68rem;">
                            {{ $cat->name }}
                        </small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    @if($candidate->color)
                        <span class="color-dot" style="background-color:{{ $candidate->color }};"
                              title="{{ $candidate->color }}"></span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>

                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        @can('view_candidatos')
                        <button type="button" class="btn btn-sm btn-soft-info view-item-btn"
                            data-bs-toggle="modal" data-bs-target="#viewCandidateModal"
                            data-id="{{ $candidate->id }}"
                            data-name="{{ $candidate->name }}"
                            data-party="{{ $candidate->party }}"
                            data-party_full_name="{{ $candidate->party_full_name }}"
                            data-color="{{ $candidate->color }}"
                            data-election_type="{{ $candidate->electionTypeCategory?->electionType?->name ?? 'N/A' }}"
                            data-election_category="{{ $candidate->electionTypeCategory?->electionCategory?->name ?? 'N/A' }}"
                            data-election_category_code="{{ $candidate->electionTypeCategory?->electionCategory?->code ?? '' }}"
                            data-ballot_order="{{ $candidate->electionTypeCategory?->ballot_order ?? '' }}"
                            data-votes_per_person="{{ $candidate->electionTypeCategory?->votes_per_person ?? 1 }}"
                            data-list_order="{{ $candidate->list_order }}"
                            data-list_name="{{ $candidate->list_name }}"
                            data-department_name="{{ $candidate->department?->name ?? '' }}"
                            data-province_name="{{ $candidate->province?->name ?? '' }}"
                            data-municipality_name="{{ $candidate->municipality?->name ?? '' }}"
                            data-photo-url="{{ $candidate->photo_url }}"
                            data-party-logo-url="{{ $candidate->party_logo_url }}"
                            data-active="{{ $candidate->active ? '1' : '0' }}"
                            title="Ver detalles">
                            <i class="ri-eye-line"></i>
                        </button>
                        @endcan
                        @can('edit_candidatos')
                        <button type="button" class="btn btn-sm btn-soft-warning edit-item-btn"
                            data-bs-toggle="modal" data-bs-target="#candidateModal"
                            data-id="{{ $candidate->id }}"
                            data-update-url="{{ route('candidates.update', $candidate->id) }}"
                            data-name="{{ $candidate->name }}"
                            data-party="{{ $candidate->party }}"
                            data-party_full_name="{{ $candidate->party_full_name }}"
                            data-color="{{ $candidate->color }}"
                            data-election_type_category_id="{{ $candidate->election_type_category_id }}"
                            data-list_order="{{ $candidate->list_order }}"
                            data-list_name="{{ $candidate->list_name }}"
                            data-department_id="{{ $candidate->department_id }}"
                            data-province_id="{{ $candidate->province_id }}"
                            data-municipality_id="{{ $candidate->municipality_id }}"
                            data-photo-url="{{ $candidate->photo_url }}"
                            data-party-logo-url="{{ $candidate->party_logo_url }}"
                            data-active="{{ $candidate->active ? '1' : '0' }}"
                            title="Editar">
                            <i class="ri-pencil-line"></i>
                        </button>
                        @endcan
                        @can('delete_candidatos')
                        <button class="btn btn-sm btn-soft-danger remove-item-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                            data-id="{{ $candidate->id }}"
                            data-name="{{ $candidate->name }}"
                            data-delete-url="{{ route('candidates.destroy', $candidate->id) }}"
                            title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5">
                    <i class="ri-user-search-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                    <p class="text-muted mb-1">No hay candidatos que coincidan con los filtros aplicados.</p>
                    @if(request()->hasAny(['search','election_type_id','election_type_category_id','department_id','province_id']))
                    <a href="{{ route('candidates.index') }}" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-close-line me-1"></i>Limpiar filtros
                    </a>
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
