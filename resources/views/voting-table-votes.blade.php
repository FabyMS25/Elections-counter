@extends('layouts.master')
@section('title')
    @lang('translation.management')
@endsection
@section('css')
    <link href="{{ URL::asset('build/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />
    <style>        
        .candidate-name {            
            vertical-align: middle !important;
            word-wrap: break-word;
            hyphens: auto;
            white-space: normal;
            overflow-wrap: break-word;
        }
        .table tbody td {
            padding: 5px 10px 5px 0;
            /* white-space: normal; */
        }
        .table thead th {
            padding: 2px 17px 0 0;
            white-space: normal;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1') Voting @endslot
        @slot('title') Administracion de Votos @endslot
    @endcomponent

    <!-- Offset Position -->
<!-- <button type="button" data-toast data-toast-text="Welcome Back ! This is a Toast Notification" data-toast-gravity="top" data-toast-position="right" data-toast-duration="3000" data-toast-offset data-toast-close="close" class="btn btn-light w-xs">Click Me</button> -->


    <div id="alert-container" class="position-fixed top-0 end-0 p-3" ></div>
    
    <div class="row">
      @foreach($candidates as $index => $candidate)
        @php
            $stats = $candidateStats[$candidate->id] ?? ['votes' => 0, 'percentage' => 0, 'trend' => 'neutral'];
            $votes = $stats['votes'];
            $percentage = $stats['percentage'];
            $trend = $stats['trend'];                
            $cardClass = 'info';                
            if ($trend === 'up') { $cardClass = 'success';
            }elseif ($percentage > 20){ $cardClass = 'primary';
            }elseif ($percentage > 10){ $cardClass = 'warning';
            }else { $cardClass = 'danger'; }
        @endphp        
        <div class="col-xxl-3 col-md-6" data-candidate-id="{{ $candidate->id }}">
            <div class="card card-animate">
                <div class="card-body {{$candidate->party ==='.'?'bg-warning-subtle':''}}">
                    <div class="d-flex mb-3">
                        <div class="flex-grow-1">
                          @if($candidate->photo)
                            <img src="{{ asset('storage/' . $candidate->photo) }}" 
                                alt="{{ $candidate->name }}" 
                                class="img-fluid rounded-circle border border-3 border-white shadow-sm"
                                style="width:55px;height:55px;object-fit:cover;">
                          @else
                            <lord-icon src="https://cdn.lordicon.com/dxjqoygy.json" trigger="loop"
                                colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
                          @endif
                        </div>
                        @if($candidate->party!=='.')
                        <div class="flex-shrink-0 me-2">
                          @if($trend === 'up')
                            <div class="avatar-xs d-inline-block">
                                <div class="avatar-title bg-success text-white rounded-circle fs-16">
                                    <i class="ri-arrow-right-up-fill"></i>
                                </div>
                            </div>
                          @elseif($trend === 'down' && $totalCount > 0)
                            <div class="avatar-xs d-inline-block">
                                <div class="avatar-title bg-danger text-white rounded-circle fs-16">
                                    <i class="ri-arrow-right-down-fill"></i>
                                </div>
                            </div>
                          @else
                            <div class="avatar-xs d-inline-block">
                                <div class="avatar-title bg-secondary text-white rounded-circle fs-16">
                                    <i class="ri-subtract-line"></i>
                                </div>
                            </div>
                          @endif                       
                        </div>
                        @endif
                        <div class="flex-shrink-0">
                            <span class="badge bg-{{$cardClass}}-subtle text-{{$cardClass}} badge-border">
                                #{{ $stats['rank'] ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                        <h4 class="mb-2" data-candidate-id="{{ $candidate->id }}">
                        <span class="counter-value" data-target="{{ $votes }}">0</span>
                        <small class="text-muted fs-13"> votos</small>
                        <small class="text-muted fs-13 badge bg-light">{{ number_format($percentage, 1) }}% del total</small>
                    </h4>
                    <h6 class="text-muted mb-0 ">{{ $candidate->name }}</h6>
                    @if($candidate->party && $candidate->party !== '.')
                        <p class="text-muted mb-0 fs-12">{{ $candidate->party }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row align-items-center">
    <form method="GET" action="{{ route('voting-table-votes.index') }}" class="row">
        <div class="col-md-4">
            <label for="institution_id" class="form-label text-muted">Filtrar por Institución</label>
            <select class="form-control" data-choices name="institution_id" id="institution_id">
                <option value="">-- Instituciones --</option>
                @foreach($institutions as $institution)
                <option value="{{ $institution->id }}" {{ $institutionId == $institution->id ? 'selected' : '' }}>
                    {{ $institution->name }} ({{ $institution->code }})
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <div class="mb-1"><label class="form-label">&nbsp;</label></div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <a href="{{ route('voting-table-votes.index') }}" class="btn btn-secondary">Limpiar</a>
        </div>
        <div class="col-md-6">
            <div class="mb-1"><label class="form-label">&nbsp;</label></div>
            <div class="alert alert-info">
                <i class="ri-information-line me-2"></i>
                Use the input fields to enter vote counts or the +/- buttons to adjust. Click "Register Votes" to save.
            </div>
        </div>
    </form>
    </div>
    
    <div class="card" id="tableList">
        <div class="card-header">
            <div class="row align-items-center g-3">
                <div class="col-md-3">
                    <h4 class="card-title mb-0">Registro de Votos por mesa</h4>
                </div>
                <div class="col-md-auto ms-auto">
                    <div class="d-flex gap-2">
                        <div class="search-box">
                            <input type="text" class="form-control search" placeholder="Buscar por Institución y/o Mesa...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                        <button class="btn btn-soft-primary"><i class="ri-equalizer-line align-bottom me-1"></i>
                            Filtrar</button>
                    </div>
                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif                    
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
        </div>
        <div class="card-body">            
            <div class="table table-responsive table-card">                    
                <table class="table align-middle table-nowrap" id="votingTableList">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="responsivetableCheck">
                                    <label class="form-check-label" for="responsivetableCheck"></label>
                                </div>
                            </th>
                            <th class="sort" data-sort="institution" scope="col">INSTITUCION</th>
                            <th class="sort" data-sort="votingTable" scope="col">MESA</th>
                            <th class="sort" data-sort="status" scope="col">ESTADO</th>
                            @foreach($candidates as $candidate)
                            <th data-sort="candidate-{{ $candidate->id }}" scope="col">
                                <div class="d-flex">
                                    @if($candidate->photo)
                                        <div class="avatar-xs flex-shrink-0 me-1">
                                            <img src="{{ asset('storage/' . $candidate->photo) }}" 
                                            alt="{{ $candidate->name }}" 
                                            class="img-fluid rounded-circle">
                                        </div>
                                    @else
                                        <div class="avatar-xs flex-shrink-0 me-1">
                                            <div class="avatar-title bg-soft-secondary text-secondary rounded-circle">
                                                <i class="ri-user-line"></i>
                                            </div>
                                        </div>
                                    @endif
                                    <div style="{{ $candidate->party !== '.' ? 'width: 130px;' : 'width: 90px;' }}">
                                        <h6 class="fs-13 candidate-name mb-0">{{ $candidate->name }}</h6>
                                        @if($candidate->party !==".")
                                        <small class="text-muted fw-medium" >({{$candidate->party }})</small> 
                                        @endif
                                    </div>
                                </div>
                            </th>
                            @endforeach
                            <th class="sort text-center" data-sort="totalCount" scope="col">Votos Contados</th>
                            <th class="sort text-center" data-sort="capacity" scope="col">Capacidad</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="list form-check-all">
                      @foreach($votingTables as $table)
                        @php
                            $tableVotes = [];
                            $totalVotes = 0;
                            foreach ($table->votes as $vote) {
                                $tableVotes[$vote->candidate_id] = $vote->quantity;
                                $totalVotes += $vote->quantity;
                            }
                            $unregisteredVotes = $table->capacity ? max(0, $table->capacity - $totalVotes) : 0;
                            $isCapacityWarning = $table->capacity && $totalVotes > 0 && $totalVotes < $table->capacity;
                            $isCapacityExceeded = $table->capacity && $totalVotes > $table->capacity;
                        @endphp
                        <tr id="table-{{ $table->id }}" data-table-id="{{ $table->id }}" class="{{ $isCapacityExceeded ? 'capacity-exceeded' : ($isCapacityWarning ? 'capacity-warning' : '') }}">
                            <td class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="responsivetableCheck01">
                                    <label class="form-check-label" for="responsivetableCheck01"></label>
                                </div>
                            </td>
                            <td class="institution">{{ $table->institution->name }}</td>
                            <td class="votingTable">
                                {{ $table->code }} ({{ $table->number }})
                                @if($table->from_name && $table->to_name)
                                    <small class="from-name fs-10"><br>{{ $table->from_name }}</small> 
                                    <small class="to-name fs-10"><br> {{ $table->to_name }}</small>
                                @endif
                            </td>
                            <td class="status text-center">
                                @php
                                $statusClasses = [
                                    'active' => 'success',
                                    'closed' => 'danger',
                                    'pending' => 'warning'
                                ];
                                $statusLabels = [
                                    'active' => 'Activo',
                                    'closed' => 'Cerrado',
                                    'pending' => 'Pendiente'
                                ];
                                @endphp
                                <span class="badge bg-{{ $statusClasses[$table->status] }}-subtle text-{{ $statusClasses[$table->status] }} status-badge">
                                    {{ $statusLabels[$table->status] }}
                                </span>
                            </td>
                            @foreach($candidates as $candidate)
                            <td class="candidate-{{ $candidate->id }} text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <button class="btn btn-sm btn-outline-secondary vote-btn subtract-vote" 
                                            data-table-id="{{ $table->id }}" 
                                            data-candidate-id="{{ $candidate->id }}">
                                        <i class="ri-subtract-line"></i>
                                    </button>
                                    <input type="number" 
                                           class="form-control form-control-sm vote-input mx-1 text-center" 
                                           name="votes[{{ $table->id }}][{{ $candidate->id }}]" 
                                           value="{{ $tableVotes[$candidate->id] ?? 0 }}" 
                                           min="0"
                                           data-table-id="{{ $table->id }}"
                                           data-candidate-id="{{ $candidate->id }}">
                                    <button class="btn btn-sm btn-outline-secondary vote-btn add-vote" 
                                            data-table-id="{{ $table->id }}" 
                                            data-candidate-id="{{ $candidate->id }}">
                                        <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            </td>
                            @endforeach
                            <td class="totalCount text-center">
                                <span class="total-votes" data-table-id="{{ $table->id }}">
                                    {{ $totalVotes }}
                                </span>
                            </td>
                            <td class="capacity text-center">{{ $table->capacity ?? 'N/A' }}</td>
                            <td class="text-center gap-1">
                                <button type="button" class="btn btn-success p-2 py-1 register-votes" 
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Registrar"
                                    data-table-id="{{ $table->id }}" {{ $table->status === 'closed' ? 'disabled' : '' }}>
                                    <i class="ri-save-line"></i> 
                                </button>
                                <button type="button" class="btn btn-outline-primary p-2 py-1 register-close" 
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Cerrar Mesa"
                                    data-table-id="{{ $table->id }}" {{ $table->status === 'closed' ? 'disabled' : '' }}>
                                    <i class="ri-close-circle-fill"></i>                                        
                                </button>
                                    <!-- <button class="btn btn-sm btn-primary p-1 register-votes" 
                                            data-table-id="{{ $table->id }}"
                                            {{ $table->status === 'closed' ? 'disabled' : '' }}>
                                        <i class="ri-save-line"></i> Registrar
                                    </button>
                                    <button class="btn btn-sm btn-outline-primary register-close" 
                                            data-table-id="{{ $table->id }}"
                                            {{ $table->status === 'closed' ? 'disabled' : '' }}>
                                        <i class="ri-save-line me-1"></i> 
                                        Cerrar Mesa
                                    </button> -->
                                    @if($table->status === 'closed')
                                        <div class="text-muted small mt-1">Cerrada</div>
                                    @endif
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Totales:</td>
                            @foreach($candidates as $candidate)
                            <td class="text-center fw-bold candidate-{{ $candidate->id }}">
                                <div class="d-flex gap-2 align-items-center">
                                    <span class="fs-14 fw-bold mb-1">{{ $candidateTotals[$candidate->id] ?? 0 }}</span>                                    
                                    @php
                                        $stats = $candidateStats[$candidate->id] ?? ['trend' => 'neutral', 'percentage' => 0];
                                        $trend = $stats['trend'];
                                        $percentage = $stats['percentage'];
                                    @endphp                                    
                                    @if($totalCount > 0)
                                        <small class="text-muted mb-1">{{ number_format($percentage, 1) }}%</small>
                                        @if($candidate->party !==".")                                    
                                        @if($trend === 'up')
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-success-subtle text-success rounded-circle fs-12">
                                                    <i class="ri-arrow-right-up-fill"></i>
                                                </div>
                                            </div>
                                        @elseif($trend === 'down')
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-danger-subtle text-danger rounded-circle fs-12">
                                                    <i class="ri-arrow-right-down-fill"></i>
                                                </div>
                                            </div>
                                        @else
                                            <div class="avatar-xs">
                                                <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle fs-12">
                                                    <i class="ri-subtract-line"></i>
                                                </div>
                                            </div>
                                        @endif
                                        @endif
                                    @else
                                        <small class="text-muted">0%</small>
                                        <div class="avatar-xs">
                                            <div class="avatar-title bg-secondary-subtle text-secondary rounded-circle fs-12">
                                                <i class="ri-subtract-line"></i>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            @endforeach
                            <td class="text-center fw-bold">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fs-14 fw-bold">{{ $totalCount }}</span>
                                    <small class="text-muted">votos</small>
                                </div>
                            </td>
                            <td class="text-center fw-bold">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="fs-14 fw-bold">{{ $totalCapacity }}</span>
                                    <!-- <small class="text-muted">capacidad</small> -->
                                    @if($totalCapacity > 0)
                                        <small class="text-{{ $totalCount > $totalCapacity ? 'danger' : ($totalCount < $totalCapacity * 0.8 ? 'warning' : 'success') }}">
                                            {{ $totalCapacity > 0 ? number_format(($totalCount / $totalCapacity) * 100, 1) : 0 }}%
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="gap-1">
                                    <button class="btn btn-sm btn-primary register-all-votes">
                                        <i class="ri-save-line"></i> Registrar Todos
                                    </button>
                                    <!-- <button class="btn btn-sm btn-outline-primary register-close-all">
                                        <i class="ri-save-line me-1"></i> Cerrar Todas
                                    </button> -->
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
                @if($votingTables->isEmpty())
                <div class="noresult" >
                    <div class="text-center">
                        <!-- <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                            colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon> -->
                        <i class="ri-inbox-line display-4 text-muted"></i>
                        <h5 class="mt-2">No se encontraron Mesas registradas</h5>
                        <p class="text-muted">Intenta seleccionando una institucion diferente o verifica su las mesas de votos fueron registrados correctamente.</p>
                    </div>
                </div>
                @endif
            </div>
            <div class="d-flex justify-content-end mt-3">
                <div class="pagination-wrap hstack gap-2">
                    <a class="page-item pagination-prev disabled" href="#">
                        Anterios
                    </a>
                    <ul class="pagination listjs-pagination mb-0"></ul>
                    <a class="page-item pagination-next" href="#">
                        Siguiente
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/prismjs/prism.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/swiper/swiper-bundle.min.js') }}"></script>
    <script src="{{ URL::asset('build/js/app.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var votingTableList = new List('tableList', {
                valueNames: ['institution','votingTable','status',
                    'totalCount','capacity',
                    @foreach($candidates as $candidate)
                        'candidate-{{ $candidate->id }}',
                        @endforeach
                    ],
                    page: 10,
                    pagination: true
                });

                document.querySelector('.search').addEventListener('input', function(e) {
                    var searchValue = e.target.value;
                    votingTableList.search(searchValue);
                });
                document.getElementById('institution_id').addEventListener('change', function(e) {
                    var institutionId = e.target.value;
                    var selectedOption = document.querySelector('option[value="' + institutionId + '"]');
                    if (institutionId && selectedOption) {
                        var institutionName = selectedOption.textContent.split(' (')[0];
                        votingTableList.search(institutionName);
                    } else {
                        votingTableList.search('');
                    }
                });
                document.querySelectorAll(".register-votes").forEach(btn => {
                    btn.addEventListener("click", function () {
                        let tableId = this.dataset.tableId;
                        registerTableVotes(tableId, false);
                    });
                });
                document.querySelectorAll(".register-close").forEach(btn => {
                    btn.addEventListener("click", function () {
                        let tableId = this.dataset.tableId;
                        if (confirm('¿Está seguro de que desea cerrar esta mesa? No podrá registrar más votos después.')) {
                            registerTableVotes(tableId, true);
                        }
                    });
                });
                document.querySelector(".register-all-votes").addEventListener("click", function () {
                    if (confirm('¿Está seguro de que desea registrar todos los votos de todas las mesas?')) {
                        registerAllVotes(false);
                    }
                });
                document.querySelector(".register-close-all").addEventListener("click", function () {
                    if (confirm('¿Está seguro de que desea registrar todos los votos y cerrar todas las mesas? No podrá registrar más votos después.')) {
                        registerAllVotes(true);
                    }
                });
                document.querySelectorAll(".add-vote").forEach(btn => {
                    btn.addEventListener("click", function () {
                        let tableId = this.dataset.tableId;
                        let candidateId = this.dataset.candidateId;
                        let input = document.querySelector(`input.vote-input[data-table-id="${tableId}"][data-candidate-id="${candidateId}"]`);
                        input.value = parseInt(input.value || 0) + 1;
                        updateTotal(tableId);
                    });
                });
                document.querySelectorAll(".subtract-vote").forEach(btn => {
                    btn.addEventListener("click", function () {
                        let tableId = this.dataset.tableId;
                        let candidateId = this.dataset.candidateId;
                        let input = document.querySelector(`input.vote-input[data-table-id="${tableId}"][data-candidate-id="${candidateId}"]`);
                        input.value = Math.max(0, parseInt(input.value || 0) - 1);
                        updateTotal(tableId);
                    });
                });
                document.querySelectorAll(".vote-input").forEach(input => {
                    input.addEventListener('input', function() {
                        let tableId = this.dataset.tableId;
                        updateTotal(tableId);
                    });
                });
                function registerTableVotes(tableId, closeTable = false) {
                    let inputs = document.querySelectorAll(`input.vote-input[data-table-id="${tableId}"]`);
                    let votes = {};                
                    inputs.forEach(input => {votes[input.dataset.candidateId] = parseInt(input.value || 0);});
                    let button = document.querySelector(closeTable ? `.register-close[data-table-id="${tableId}"]` : `.register-votes[data-table-id="${tableId}"]`);
                    let originalText = button.innerHTML;
                    button.innerHTML = '<i class="ri-loader-2-line me-1 spin"></i> Procesando...';
                    button.disabled = true;
                    fetch("{{ route('voting-table-votes.register') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            voting_table_id: tableId,
                            votes: votes,
                            close: closeTable
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        showAlert(data.success ? 'success' : 'danger', data.message);
                        
                        if (data.success) {
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
                        } else {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                        showAlert('danger', 'Error procesando la solicitud. Intente nuevamente.');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
                }
                function registerAllVotes(closeAll = false) {
                    let allTables = {};
                    document.querySelectorAll('tr[data-table-id]').forEach(row => {
                        let tableId = row.dataset.tableId;
                        let inputs = row.querySelectorAll('input.vote-input');
                        let votes = {};                    
                        inputs.forEach(input => {
                            votes[input.dataset.candidateId] = parseInt(input.value || 0);
                        });
                        allTables[tableId] = votes;
                    });
                    let button = document.querySelector(closeAll ? '.register-close-all' : '.register-all-votes');
                    let originalText = button.innerHTML;
                    button.innerHTML = '<i class="ri-loader-2-line me-1 spin"></i> Procesando...';
                    button.disabled = true;
                    fetch("{{ route('voting-table-votes.register-all') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            tables: allTables,
                            close_all: closeAll
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        showAlert(data.success ? 'success' : 'danger', data.message);
                        
                        if (data.success) {
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error("Error:", err);
                        showAlert('danger', 'Error procesando la solicitud. Intente nuevamente.');
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
                }
                function showAlert(type, message) {
                    let alertContainer = document.getElementById("alert-container");
                    let div = document.createElement("div");
                    div.className = `alert alert-${type} alert-dismissible fade show`;
                    div.innerHTML = `
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    alertContainer.appendChild(div);
                    setTimeout(() => {
                        if (div.parentNode) {
                            div.remove();
                        }
                    }, 5000);
                }
                document.querySelectorAll('.counter-value').forEach(counter => {
                let target = parseInt(counter.getAttribute('data-target')) || 0;
                counter.textContent = '0';
                setTimeout(() => animateCounter(counter, target), 500);
            });
        });
        function updateFooterTotals() {
            let candidateTotals = {};
            let grandTotal = 0;
            @foreach($candidates as $candidate)
            candidateTotals[{{ $candidate->id }}] = 0;
            @endforeach
            document.querySelectorAll('input.vote-input').forEach(input => {
                let candidateId = input.dataset.candidateId;
                let votes = parseInt(input.value || 0);
                candidateTotals[candidateId] += votes;
                grandTotal += votes;
            });
            @foreach($candidates as $candidate)
            let candidate{{ $candidate->id }}Total = candidateTotals[{{ $candidate->id }}];
            let candidate{{ $candidate->id }}Element = document.querySelector('.candidate-{{ $candidate->id }} .fs-14');
            if (candidate{{ $candidate->id }}Element) {
                candidate{{ $candidate->id }}Element.textContent = candidate{{ $candidate->id }}Total;
            }
            let candidate{{ $candidate->id }}Percentage = grandTotal > 0 ? (candidate{{ $candidate->id }}Total / grandTotal) * 100 : 0;
            let candidate{{ $candidate->id }}PercentElement = document.querySelector('.candidate-{{ $candidate->id }} .text-muted');
            if (candidate{{ $candidate->id }}PercentElement) {
                candidate{{ $candidate->id }}PercentElement.textContent = candidate{{ $candidate->id }}Percentage.toFixed(1) + '%';
            }        
            updateCandidateTrend({{ $candidate->id }}, candidate{{ $candidate->id }}Total, candidate{{ $candidate->id }}Percentage, grandTotal);
            @endforeach
            let grandTotalElement = document.querySelector('tfoot .fw-bold .fs-14');
            if (grandTotalElement && grandTotalElement.parentElement.parentElement.classList.contains('text-center')) {
                grandTotalElement.textContent = grandTotal;
            }        
            let totalCapacity = {{ $totalCapacity }};
            if (totalCapacity > 0) {
                let capacityPercentage = (grandTotal / totalCapacity) * 100;
                let capacityPercentElement = document.querySelector('tfoot td:nth-last-child(2) .text-success, tfoot td:nth-last-child(2) .text-warning, tfoot td:nth-last-child(2) .text-danger');
                if (capacityPercentElement) {
                    capacityPercentElement.textContent = capacityPercentage.toFixed(1) + '%';
                    capacityPercentElement.className = capacityPercentElement.className.replace(/text-(success|warning|danger)/, '');
                    if (grandTotal > totalCapacity) {
                        capacityPercentElement.classList.add('text-danger');
                    } else if (grandTotal < totalCapacity * 0.8) {
                        capacityPercentElement.classList.add('text-warning');
                    } else {
                        capacityPercentElement.classList.add('text-success');
                    }
                }
            }
        }
        function updateCandidateTrend(candidateId, votes, percentage, totalVotes) {
            let allTotals = [];
            @foreach($candidates as $candidate)
            let candidate{{ $candidate->id }}Votes = 0;
            document.querySelectorAll('input.vote-input[data-candidate-id="{{ $candidate->id }}"]').forEach(input => {
                candidate{{ $candidate->id }}Votes += parseInt(input.value || 0);
            });
            allTotals.push({id: {{ $candidate->id }}, votes: candidate{{ $candidate->id }}Votes});
            @endforeach
            let maxVotes = Math.max(...allTotals.map(c => c.votes));
            let trend = 'down';
            if (maxVotes > 0 && votes === maxVotes) {
                trend = 'up';
            } else if (totalVotes === 0) {
                trend = 'neutral';
            }
            let arrowContainer = document.querySelector(`.candidate-${candidateId} .avatar-xs`);
            if (arrowContainer) {
                let arrowElement = arrowContainer.querySelector('.avatar-title');
                if (arrowElement) {
                    arrowElement.className = 'avatar-title rounded-circle fs-12';            
                    if (trend === 'up') {
                        arrowElement.classList.add('bg-success-subtle', 'text-success');
                        arrowElement.innerHTML = '<i class="ri-arrow-right-up-fill"></i>';
                    } else if (trend === 'down') {
                        arrowElement.classList.add('bg-danger-subtle', 'text-danger');
                        arrowElement.innerHTML = '<i class="ri-arrow-right-down-fill"></i>';
                    } else {
                        arrowElement.classList.add('bg-secondary-subtle', 'text-secondary');
                        arrowElement.innerHTML = '<i class="ri-subtract-line"></i>';
                    }
                }
            }
        }
        function updateTotal(tableId) {
            let inputs = document.querySelectorAll(`input.vote-input[data-table-id="${tableId}"]`);
            let total = 0;
            inputs.forEach(i => total += parseInt(i.value || 0));
            let totalElement = document.querySelector(`.total-votes[data-table-id="${tableId}"]`);
            if (totalElement) {
                totalElement.textContent = total;
            }
            let row = document.querySelector(`tr[data-table-id="${tableId}"]`);
            if (row) {
                let capacityElement = row.querySelector('.capacity');
                let capacity = capacityElement ? parseInt(capacityElement.textContent) : null;
                row.classList.remove('capacity-warning', 'capacity-exceeded');
                if (capacity && !isNaN(capacity)) {
                    if (total > capacity) {
                        row.classList.add('capacity-exceeded');
                    } else if (total > 0 && total < capacity) {
                        row.classList.add('capacity-warning');
                    }
                }
            }
            updateFooterTotals();
            updateCandidateCards();
        }
        function updateCandidateCards() {
            let candidateTotals = {};
            let grandTotal = 0;
            @foreach($candidates as $candidate)
            candidateTotals[{{ $candidate->id }}] = 0;
            @endforeach
            document.querySelectorAll('input.vote-input').forEach(input => {
                let candidateId = input.dataset.candidateId;
                let votes = parseInt(input.value || 0);
                candidateTotals[candidateId] += votes;
                grandTotal += votes;
            });
            let sortedCandidates = Object.entries(candidateTotals).sort((a, b) => b[1] - a[1]);
            let maxVotes = sortedCandidates[0] ? sortedCandidates[0][1] : 0;
            @foreach($candidates as $candidate)
            updateCandidateCard({{ $candidate->id }}, candidateTotals[{{ $candidate->id }}], grandTotal, maxVotes, sortedCandidates);
            @endforeach
            updateSummaryCards(grandTotal);
        }
        function updateCandidateCard(candidateId, votes, totalVotes, maxVotes, sortedCandidates) {
            let percentage = totalVotes > 0 ? (votes / totalVotes) * 100 : 0;
            let rank = sortedCandidates.findIndex(([id, v]) => id == candidateId) + 1;
            let trend = 'neutral';
            if (maxVotes > 0 && votes === maxVotes) {
                trend = 'up';
            } else if (totalVotes > 0) {
                trend = 'down';
            }
            let voteElement = document.querySelector(`.candidate-votes[data-candidate-id="${candidateId}"] .counter-value`);
            if (voteElement) {
                voteElement.textContent = votes;
                voteElement.setAttribute('data-target', votes);
            }
            let percentageElement = document.querySelector(`.candidate-percentage[data-candidate-id="${candidateId}"]`);
            if (percentageElement) {
                percentageElement.textContent = `${percentage.toFixed(1)}% del total`;
            }
            
        }
        function updateSummaryCards(totalVotes) {
            let totalVotesElement = document.getElementById('summary-total-votes');
            if (totalVotesElement) {
                totalVotesElement.textContent = totalVotes;
            }
            let participationElement = document.getElementById('summary-participation');
            if (participationElement) {
                let totalCapacity = {{ $totalCapacity }};
                let participation = totalCapacity > 0 ? (totalVotes / totalCapacity) * 100 : 0;
                participationElement.textContent = `${participation.toFixed(1)}%`;
            }
        }
        function animateCounter(element, target) {
            let current = parseInt(element.textContent) || 0;
            let increment = target > current ? 1 : -1;
            let steps = Math.abs(target - current);
            let stepTime = Math.max(50, 300 / steps);    
            if (steps === 0) return;    
            let timer = setInterval(() => {
                current += increment;
                element.textContent = current;            
                if (current === target) {
                    clearInterval(timer);
                }
            }, stepTime);
        }
    </script>
@endsection