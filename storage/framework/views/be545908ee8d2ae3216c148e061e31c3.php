
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.list-voting-tables'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
    <style>
        .datalist-container {
            position: relative;
        }
        .datalist-suggestions {
            position: absolute;
            border: 1px solid #d1d5db;
            border-top: none;
            z-index: 1000;
            width: 100%;
            background: white;
            display: none;
            max-height: 200px;
            overflow-y: auto;
        }
        .datalist-suggestion {
            padding: 8px 12px;
            cursor: pointer;
        }
        .datalist-suggestion:hover {
            background-color: #f3f4f6;
        }
        input[list]::-webkit-calendar-picker-indicator {
            display: none !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Tables
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Mesas de Votación
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Administración de Mesas de Votación</h4>
                </div>
                
                <div class="card-body">
                    <?php if(session('success')): ?>
                        <div class="alert alert-success">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>
                    
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="listjs-table" id="votingTableList">
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
                                        <th class="sort code-column" data-sort="table_code">Código</th>
                                        <th class="sort" data-sort="table_number">Número</th>
                                        <th class="sort" data-sort="from_name">Desde</th>
                                        <th class="sort" data-sort="to_name">Hasta</th>
                                        <th class="sort" data-sort="capacity">Capacidad</th>
                                        <th class="sort" data-sort="institution">Institución</th>
                                        <th class="sort status-column" data-sort="status">Estado</th>
                                        <th class="sort actions-column" data-sort="action">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    <?php $__currentLoopData = $votingTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <th scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="chk_child" value="<?php echo e($table->id); ?>">
                                                </div>
                                            </th>
                                            <td class="table_code">
                                                <span class="badge bg-info-subtle text-info"><?php echo e($table->code); ?></span>
                                            </td>
                                            <td class="table_number"><?php echo e($table->number); ?></td>
                                            <td class="from_name"><?php echo e($table->from_name ?? 'N/A'); ?></td>
                                            <td class="to_name"><?php echo e($table->to_name ?? 'N/A'); ?></td>
                                            <td class="capacity"><?php echo e($table->capacity); ?></td>
                                            <td class="institution"><?php echo e($table->institution->name ?? 'N/A'); ?></td>
                                            <td class="status">
                                                <?php
                                                    $statusClasses = [
                                                        'active' => 'success',
                                                        'closed' => 'danger',
                                                        'pending' => 'warning'
                                                    ];
                                                    $statusLabels = [
                                                        'active' => 'Activa',
                                                        'closed' => 'Cerrada',
                                                        'pending' => 'Pendiente'
                                                    ];
                                                ?>
                                                <span class="badge bg-<?php echo e($statusClasses[$table->status]); ?>-subtle text-<?php echo e($statusClasses[$table->status]); ?>">
                                                    <?php echo e($statusLabels[$table->status]); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <div class="edit">
                                                        <button class="btn btn-sm btn-success edit-item-btn"
                                                            data-bs-toggle="modal" data-bs-target="#showModal"
                                                            data-id="<?php echo e($table->id); ?>"
                                                            data-code="<?php echo e($table->code); ?>"
                                                            data-number="<?php echo e($table->number); ?>"
                                                            data-from_name="<?php echo e($table->from_name); ?>"
                                                            data-to_name="<?php echo e($table->to_name); ?>"
                                                            data-capacity="<?php echo e($table->capacity); ?>"
                                                            data-status="<?php echo e($table->status); ?>"
                                                            data-institution_id="<?php echo e($table->institution_id); ?>"
                                                            data-institution_name="<?php echo e($table->institution->name ?? ''); ?>"
                                                            data-update-url="<?php echo e(route('voting-tables.update', $table->id)); ?>">
                                                            Editar
                                                        </button>
                                                    </div>
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteRecordModal"
                                                            data-id="<?php echo e($table->id); ?>"
                                                            data-delete-url="<?php echo e(route('voting-tables.destroy', $table->id)); ?>">
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            
                            <?php if($votingTables->isEmpty()): ?>
                                <div class="noresult">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                        </lord-icon>
                                        <h5 class="mt-2">Lo sentimos! No se encontraron resultados</h5>
                                        <p class="text-muted mb-0">No hay mesas de votación registradas en el sistema.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
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
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Nueva Mesa de Votación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form id="votingTableForm" method="POST" class="tablelist-form" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="method_field" name="_method" value="">
                    <input type="hidden" id="voting_table_id" name="id">
                    <input type="hidden" id="institution_id" name="institution_id" value="<?php echo e(old('institution_id')); ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="code-field" class="form-label">Código de Mesa <span class="text-danger">*</span></label>
                            <input type="text" id="code-field" name="code" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                placeholder="Ingrese el código de mesa (ej: MESA-001)" value="<?php echo e(old('code')); ?>" required />
                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor ingrese un código válido.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="number-field" class="form-label">Número de Mesa <span class="text-danger">*</span></label>
                            <input type="number" id="number-field" name="number" class="form-control <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                placeholder="Ingrese el número de mesa" value="<?php echo e(old('number')); ?>" min="1" required />
                            <?php $__errorArgs = ['number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor ingrese un número válido.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="from_name-field" class="form-label">Desde (Nombre)</label>
                                    <input type="text" id="from_name-field" name="from_name" class="form-control <?php $__errorArgs = ['from_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        placeholder="Ej: Juan" value="<?php echo e(old('from_name')); ?>" />
                                    <?php $__errorArgs = ['from_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="to_name-field" class="form-label">Hasta (Nombre)</label>
                                    <input type="text" id="to_name-field" name="to_name" class="form-control <?php $__errorArgs = ['to_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                        placeholder="Ej: Pedro" value="<?php echo e(old('to_name')); ?>" />
                                    <?php $__errorArgs = ['to_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="capacity-field" class="form-label">Capacidad <span class="text-danger">*</span></label>
                            <input type="number" id="capacity-field" name="capacity" class="form-control <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                placeholder="Ingrese la capacidad de la mesa" value="<?php echo e(old('capacity')); ?>" min="1" required />
                            <?php $__errorArgs = ['capacity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor ingrese una capacidad válida.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="institution-field" class="form-label">Institución <span class="text-danger">*</span></label>
                            <div class="datalist-container">
                                <input type="text" id="institution-field" name="institution_name" 
                                    class="form-control <?php $__errorArgs = ['institution_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                    placeholder="Escriba para buscar institución..." 
                                    value="<?php echo e(old('institution_name')); ?>"
                                    autocomplete="off"
                                    list="institutions-list"
                                    required>
                                <datalist id="institutions-list">
                                    <?php
                                        // Ensure we only show unique institutions
                                        $uniqueInstitutions = [];
                                    ?>
                                    <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $institutionIdentifier = $institution->name . ' (' . $institution->code . ')';
                                        ?>
                                        <?php if(!in_array($institutionIdentifier, $uniqueInstitutions)): ?>
                                            <option value="<?php echo e($institutionIdentifier); ?>" data-id="<?php echo e($institution->id); ?>">
                                            <?php $uniqueInstitutions[] = $institutionIdentifier; ?>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </datalist>
                                <div id="institution-suggestions" class="datalist-suggestions"></div>
                            </div>
                            <?php $__errorArgs = ['institution_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor seleccione una institución válida.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="status-field" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="status" id="status-field" required>
                                <option value="">Seleccione un estado</option>
                                <option value="pending" <?php echo e(old('status') == 'pending' ? 'selected' : ''); ?>>Pendiente</option>
                                <option value="active" <?php echo e(old('status') == 'active' ? 'selected' : ''); ?>>Activa</option>
                                <option value="closed" <?php echo e(old('status') == 'closed' ? 'selected' : ''); ?>>Cerrada</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor seleccione un estado.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                            <p class="text-muted mx-4 mb-0">¿Está seguro de que desea eliminar esta mesa de votación?</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                        <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <form id="deleteForm" method="POST" style="display: inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn w-sm btn-danger">Sí, eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end modal -->
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('build/libs/prismjs/prism.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/list.js/list.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/list.pagination.js/list.pagination.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.js')); ?>"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced datalist functionality
            const institutionInput = document.getElementById('institution-field');
            const institutionIdInput = document.getElementById('institution_id');
            const suggestionsContainer = document.getElementById('institution-suggestions');
            const datalistOptions = document.querySelectorAll('#institutions-list option');
            
            // Show custom suggestions dropdown
            institutionInput.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                suggestionsContainer.innerHTML = '';
                
                if (value.length > 1) {
                    let hasMatches = false;
                    let displayedValues = []; // Track already displayed values to prevent duplicates
                    
                    datalistOptions.forEach(option => {
                        const optionValue = option.value;
                        if (optionValue.toLowerCase().includes(value) && !displayedValues.includes(optionValue)) {
                            hasMatches = true;
                            displayedValues.push(optionValue);
                            
                            const div = document.createElement('div');
                            div.className = 'datalist-suggestion';
                            div.textContent = optionValue;
                            div.setAttribute('data-id', option.getAttribute('data-id'));
                            div.setAttribute('data-value', optionValue);
                            
                            div.addEventListener('click', function() {
                                institutionInput.value = this.getAttribute('data-value');
                                institutionIdInput.value = this.getAttribute('data-id');
                                suggestionsContainer.style.display = 'none';
                            });
                            
                            suggestionsContainer.appendChild(div);
                        }
                    });
                    
                    suggestionsContainer.style.display = hasMatches ? 'block' : 'none';
                } else {
                    suggestionsContainer.style.display = 'none';
                }
            });
            
            document.addEventListener('click', function(e) {
                if (e.target !== institutionInput) {
                    suggestionsContainer.style.display = 'none';
                }
            });
            
            // Validate institution selection on form submit
            document.getElementById('votingTableForm').addEventListener('submit', function(e) {
                // Verify that the selected institution exists
                let institutionValid = false;
                const enteredValue = institutionInput.value.trim();
                
                datalistOptions.forEach(option => {
                    if (option.value === enteredValue) {
                        institutionValid = true;
                        institutionIdInput.value = option.getAttribute('data-id');
                    }
                });
                
                if (!institutionValid) {
                    e.preventDefault();
                    institutionInput.classList.add('is-invalid');
                    institutionInput.focus();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Institución no válida',
                        text: 'Por favor seleccione una institución de la lista de sugerencias.',
                    });
                }
            });
            
            // Initialize list.js
            var options = {valueNames: ['table_code', 'table_number', 'from_name', 'to_name', 'capacity', 'institution', 'status']};
            var votingTableList = new List('votingTableList', options).on('updated', function(list) {
                // Reattach event listeners after list update
                attachEditEventListeners();
                attachDeleteEventListeners();
            });

            // Initialize checkAll functionality
            document.getElementById('checkAll').addEventListener('change', function() {
                var checkboxes = document.querySelectorAll('input[name="chk_child"]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = this.checked;
                }
            });

            document.getElementById('create-btn').addEventListener('click', function() {
                document.getElementById('exampleModalLabel').textContent = 'Agregar Nueva Mesa de Votación';
                document.getElementById('votingTableForm').action = "<?php echo e(route('voting-tables.store')); ?>";
                document.getElementById('method_field').value = '';            
                document.getElementById('votingTableForm').reset();
                document.getElementById('voting_table_id').value = '';
                document.getElementById('institution_id').value = '';
                document.getElementById('status-field').value = 'pending';
                document.getElementById('save-btn').textContent = 'Guardar';
                clearValidationErrors();
            });

            function attachEditEventListeners() {
                document.querySelectorAll('.edit-item-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const code = this.getAttribute('data-code');
                        const number = this.getAttribute('data-number');
                        const from_name = this.getAttribute('data-from_name');
                        const to_name = this.getAttribute('data-to_name');
                        const capacity = this.getAttribute('data-capacity');
                        const status = this.getAttribute('data-status');
                        const institution_id = this.getAttribute('data-institution_id');
                        const institution_name = this.getAttribute('data-institution_name');
                        const updateUrl = this.getAttribute('data-update-url');
                        
                        document.getElementById('exampleModalLabel').textContent = 'Editar Mesa de Votación';
                        document.getElementById('votingTableForm').action = updateUrl;
                        document.getElementById('method_field').value = 'PUT';
                        document.getElementById('voting_table_id').value = id;
                        document.getElementById('code-field').value = code;
                        document.getElementById('number-field').value = number;
                        document.getElementById('from_name-field').value = from_name;
                        document.getElementById('to_name-field').value = to_name;
                        document.getElementById('capacity-field').value = capacity;
                        document.getElementById('institution-field').value = institution_name;
                        document.getElementById('institution_id').value = institution_id;
                        document.getElementById('status-field').value = status;
                        document.getElementById('save-btn').textContent = 'Actualizar';
                        clearValidationErrors();
                    });
                });
            }

            function attachDeleteEventListeners() {
                document.querySelectorAll('.remove-item-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const deleteUrl = this.getAttribute('data-delete-url');
                        document.getElementById('deleteForm').action = deleteUrl;
                    });
                });
            }

            // Attach initial event listeners
            attachEditEventListeners();
            attachDeleteEventListeners();

            const form = document.getElementById('votingTableForm');
            form.addEventListener('submit', function(event) {
                let isValid = true;
                
                const codeField = document.getElementById('code-field');
                if (!codeField.value.trim()) {
                    codeField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    codeField.classList.remove('is-invalid');
                }
                
                const numberField = document.getElementById('number-field');
                if (!numberField.value.trim() || parseInt(numberField.value) < 1) {
                    numberField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    numberField.classList.remove('is-invalid');
                }
                
                const capacityField = document.getElementById('capacity-field');
                if (!capacityField.value.trim() || parseInt(capacityField.value) < 1) {
                    capacityField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    capacityField.classList.remove('is-invalid');
                }
                
                const institutionIdField = document.getElementById('institution_id');
                if (!institutionIdField.value) {
                    document.getElementById('institution-field').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('institution-field').classList.remove('is-invalid');
                }
                
                const statusField = document.getElementById('status-field');
                if (!statusField.value) {
                    statusField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    statusField.classList.remove('is-invalid');
                }
                
                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
            
            document.getElementById('showModal').addEventListener('hidden.bs.modal', function () {
                clearValidationErrors();
            });
            
            function clearValidationErrors() {
                document.getElementById('code-field').classList.remove('is-invalid');
                document.getElementById('number-field').classList.remove('is-invalid');
                document.getElementById('from_name-field').classList.remove('is-invalid');
                document.getElementById('to_name-field').classList.remove('is-invalid');
                document.getElementById('capacity-field').classList.remove('is-invalid');
                document.getElementById('institution-field').classList.remove('is-invalid');
                document.getElementById('status-field').classList.remove('is-invalid');
            }
        });
        
        function deleteMultiple() {
            alert('Función de eliminar múltiple - por implementar');
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\corporate\resources\views/tables-voting-tables.blade.php ENDPATH**/ ?>