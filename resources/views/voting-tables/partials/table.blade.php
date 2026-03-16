{{-- resources/views/voting-tables/partials/table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th style="width:50px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkAll">
                    </div>
                </th>
                <th>
                    <a href="{{ route('voting-tables.index', array_merge(request()->query(),['sort'=>'institution_id','direction'=>request('sort')=='institution_id'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Recinto
                        @if(request('sort')=='institution_id')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>
                        @else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th>
                    <a href="{{ route('voting-tables.index', array_merge(request()->query(),['sort'=>'oep_code','direction'=>request('sort')=='oep_code'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Código OEP
                        @if(request('sort')=='oep_code')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>
                        @else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th>Código Interno</th>
                <th>
                    <a href="{{ route('voting-tables.index', array_merge(request()->query(),['sort'=>'number','direction'=>request('sort')=='number'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        N° Mesa
                        @if(request('sort')=='number')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>
                        @else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th>
                    <a href="{{ route('voting-tables.index', array_merge(request()->query(),['sort'=>'expected_voters','direction'=>request('sort')=='expected_voters'&&request('direction')=='asc'?'desc':'asc'])) }}" class="sort-link">
                        Electores
                        @if(request('sort')=='expected_voters')<i class="ri-arrow-{{ request('direction')=='asc'?'up':'down' }}-s-line"></i>
                        @else<i class="ri-arrow-up-down-line text-muted opacity-50"></i>@endif
                    </a>
                </th>
                <th>Votaron</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
        @forelse($votingTables as $vt)
            @php
                $te = $vt->elections->first();
                $status = $te?->status ?? 'sin_configurar';
                $total = $te?->total_voters ?? 0;
                $expected = $vt->expected_voters ?? 0;
                $pct = $expected > 0 ? round(($total / $expected) * 100, 1) : 0;
                $stColors = [
                    'configurada' => 'secondary',
                    'en_espera' => 'info',
                    'votacion' => 'primary',
                    'en_escrutinio' => 'warning',
                    'escrutada' => 'success',
                    'observada' => 'danger',
                    'transmitida' => 'success',
                    'anulada' => 'dark',
                    'sin_configurar' => 'light',
                ];
                $stLabels = [
                    'configurada' => 'Configurada',
                    'en_espera' => 'En espera',
                    'votacion' => 'Votación',
                    'en_escrutinio' => 'Escrutinio',
                    'escrutada' => 'Escrutada',
                    'observada' => 'Observada',
                    'transmitida' => 'Transmitida',
                    'anulada' => 'Anulada',
                    'sin_configurar' => 'Sin config.',
                ];
                $typeLabels = ['mixta' => 'Mixta', 'masculina' => 'Masculina', 'femenina' => 'Femenina'];
            @endphp
            <tr>
                <td>
                    <div class="form-check">
                        <input class="form-check-input child-checkbox" type="checkbox"
                               name="selected_ids[]" value="{{ $vt->id }}">
                    </div>
                </td>
                <td>
                    <div class="fw-semibold">{{ $vt->institution->name ?? 'N/A' }}</div>
                    <small class="text-muted">{{ $vt->institution->code ?? '' }}</small>
                </td>
                <td>
                    <span class="badge bg-primary-subtle text-primary font-monospace">
                        {{ $vt->oep_code ?? '—' }}
                    </span>
                </td>
                <td>
                    <span class="badge bg-info-subtle text-info font-monospace">
                        {{ $vt->internal_code ?? '—' }}
                    </span>
                </td>
                <td>
                    <span class="fw-semibold">{{ $vt->number }}</span>{{ $vt->letter ? ' ' . $vt->letter : '' }}
                    <br><small class="text-muted">{{ $typeLabels[$vt->type] ?? $vt->type }}</small>
                </td>
                <td>
                    <span class="fw-semibold">{{ number_format($expected) }}</span>
                </td>
                <td>
                    <span class="fw-semibold">{{ number_format($total) }}</span>
                    <br><small class="text-{{ $pct >= 75 ? 'success' : ($pct >= 50 ? 'warning' : 'muted') }}">{{ $pct }}%</small>
                </td>
                <td>
                    <span class="badge bg-{{ $stColors[$status] ?? 'secondary' }}-subtle text-{{ $stColors[$status] ?? 'secondary' }}">
                        {{ $stLabels[$status] ?? $status }}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        @can('view_mesas')
                        <a href="{{ route('voting-tables.show', $vt) }}"
                           class="btn btn-sm btn-soft-info" title="Ver detalles">
                            <i class="ri-eye-line"></i>
                        </a>
                        @endcan
                        @can('edit_mesas')
                        <a href="{{ route('voting-tables.edit', $vt) }}"
                           class="btn btn-sm btn-soft-warning" title="Editar">
                            <i class="ri-pencil-line"></i>
                        </a>
                        <a href="{{ route('voting-tables.election-config', $vt) }}"
                           class="btn btn-sm btn-soft-primary" title="Configuración Electoral">
                            <i class="ri-settings-4-line"></i>
                        </a>
                        @endcan
                        @can('delete_mesas')
                        <button class="btn btn-sm btn-soft-danger remove-item-btn"
                            data-bs-toggle="modal" data-bs-target="#deleteRecordModal"
                            data-id="{{ $vt->id }}"
                            data-oep="{{ $vt->oep_code }}"
                            data-internal="{{ $vt->internal_code }}"
                            data-delete-url="{{ route('voting-tables.destroy', $vt) }}"
                            title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        @endcan
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <i class="ri-table-line d-block mb-2 text-muted" style="font-size:2.5rem"></i>
                    <p class="text-muted mb-1">No se encontraron mesas con los filtros aplicados.</p>
                    @if(request()->hasAny(['search','institution_id','status','type']))
                    <a href="{{ route('voting-tables.index') }}" class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-close-line me-1"></i>Limpiar filtros
                    </a>
                    @endif
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
