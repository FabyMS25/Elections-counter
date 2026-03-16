{{-- resources/views/institutions/partials/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:40px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th>
                    <a href="{{ route('institutions.index', array_merge(request()->query(),['sort'=>'code','direction'=>request('sort')=='code'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Código @if(request('sort')=='code')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>@else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th>
                    <a href="{{ route('institutions.index', array_merge(request()->query(),['sort'=>'name','direction'=>request('sort')=='name'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Recinto @if(request('sort')=='name')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>@else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th>Ubicación</th>
                <th>
                    <a href="{{ route('institutions.index', array_merge(request()->query(),['sort'=>'registered_citizens','direction'=>request('sort')=='registered_citizens'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Ciudadanos @if(request('sort')=='registered_citizens')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>@else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th class="text-center">Mesas</th>
                <th>
                    <a href="{{ route('institutions.index', array_merge(request()->query(),['sort'=>'status','direction'=>request('sort')=='status'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Estado @if(request('sort')=='status')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>@else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($institutions as $institution)
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input child-checkbox" type="checkbox"
                               name="selected_ids[]" value="{{ $institution->id }}">
                    </div>
                </td>
                <td>
                    <span class="badge bg-info-subtle text-info font-monospace">{{ $institution->code }}</span>
                </td>
                <td>
                    <div class="fw-semibold">{{ $institution->name }}</div>
                    @if($institution->short_name)
                        <small class="text-muted">{{ $institution->short_name }}</small>
                    @endif
                </td>
                <td>
                    <span class="fw-semibold">{{ $institution->locality->municipality->name ?? 'N/A' }}</span>
                    <br><small class="text-muted">{{ $institution->locality->name ?? '' }}</small>
                </td>
                <td>
                    <span class="fw-semibold">{{ number_format($institution->registered_citizens ?? 0) }}</span>
                </td>
                <td class="text-center">
                    <span class="badge bg-primary-subtle text-primary">{{ $institution->voting_tables_count ?? 0 }}</span>
                </td>
                <td>
                    @php
                        $stColors = ['activo'=>'success','inactivo'=>'danger','en_mantenimiento'=>'warning'];
                        $stLabels = ['activo'=>'Activo','inactivo'=>'Inactivo','en_mantenimiento'=>'Mantenimiento'];
                    @endphp
                    <span class="badge bg-{{ $stColors[$institution->status] ?? 'secondary' }}-subtle text-{{ $stColors[$institution->status] ?? 'secondary' }}">
                        {{ $stLabels[$institution->status] ?? $institution->status }}
                    </span>
                    @if(!$institution->is_operative)
                        <br><small class="text-warning"><i class="ri-alert-line"></i> No operativo</small>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        @can('view_recintos')
                        <a href="{{ route('institutions.show', $institution->id) }}"
                           class="btn btn-sm btn-soft-info" title="Ver detalles">
                            <i class="ri-eye-line"></i>
                        </a>
                        @endcan
                        @can('edit_recintos')
                        <a href="{{ route('institutions.edit', $institution->id) }}"
                           class="btn btn-sm btn-soft-warning" title="Editar">
                            <i class="ri-pencil-line"></i>
                        </a>
                        @endcan
                        @can('delete_recintos')
                        <button class="btn btn-sm btn-soft-danger remove-item-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                            data-id="{{ $institution->id }}"
                            data-name="{{ $institution->name }}"
                            data-delete-url="{{ route('institutions.destroy', $institution->id) }}"
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
                    <i class="ri-building-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                    <p class="text-muted mb-1">No se encontraron recintos con los filtros aplicados.</p>
                    @if(request()->hasAny(['search','department_id','status','operative']))
                    <a href="{{ route('institutions.index') }}" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-close-line me-1"></i>Limpiar filtros
                    </a>
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
