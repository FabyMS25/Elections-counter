
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.list-institutions'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('build/libs/sweetalert2/sweetalert2.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Tables
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Instituciones
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Administración de Instituciones</h4>
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
                    
                    <div class="listjs-table" id="institutionList">
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
                                        <th class="sort code-column" data-sort="institution_code">Código</th>
                                        <th class="sort" data-sort="institution_name">Institución</th>
                                        <th class="sort" data-sort="institution_address">Dirección</th>
                                        <th class="sort" data-sort="municipality">Municipio</th>
                                        <th class="sort status-column" data-sort="status">Estado</th>
                                        <th class="sort actions-column" data-sort="action">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    <?php $__currentLoopData = $institutions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $institution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <th scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="chk_child" value="<?php echo e($institution->id); ?>">
                                                </div>
                                            </th>
                                            <td class="institution_code">
                                                <span class="badge bg-info-subtle text-info"><?php echo e($institution->code); ?></span>
                                            </td>
                                            <td class="institution_name"><?php echo e($institution->name); ?></td>
                                            <td class="institution_address"><?php echo e($institution->address ?? 'N/A'); ?></td>
                                            <td class="municipality"><?php echo e($institution->municipality->name ?? 'N/A'); ?></td>
                                            <td class="status">
                                                <span class="badge bg-<?php echo e($institution->active ? 'success' : 'danger'); ?>-subtle text-<?php echo e($institution->active ? 'success' : 'danger'); ?>">
                                                    <?php echo e($institution->active ? 'Activa' : 'Inactiva'); ?>

                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <div class="edit">
                                                        <button class="btn btn-sm btn-success edit-item-btn"
                                                            data-bs-toggle="modal" data-bs-target="#showModal"
                                                            data-id="<?php echo e($institution->id); ?>"
                                                            data-name="<?php echo e($institution->name); ?>"
                                                            data-address="<?php echo e($institution->address); ?>"
                                                            data-municipality-id="<?php echo e($institution->municipality_id); ?>"
                                                            data-active="<?php echo e($institution->active); ?>"
                                                            data-update-url="<?php echo e(route('institutions.update', $institution->id)); ?>">
                                                            Editar
                                                        </button>
                                                    </div>
                                                    <div class="remove">
                                                        <button class="btn btn-sm btn-danger remove-item-btn"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteRecordModal"
                                                            data-id="<?php echo e($institution->id); ?>"
                                                            data-delete-url="<?php echo e(route('institutions.destroy', $institution->id)); ?>">
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            
                            <?php if($institutions->isEmpty()): ?>
                                <div class="noresult">
                                    <div class="text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                            colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px">
                                        </lord-icon>
                                        <h5 class="mt-2">Lo sentimos! No se encontraron resultados</h5>
                                        <p class="text-muted mb-0">No hay instituciones registradas en el sistema.</p>
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
                    <h5 class="modal-title" id="exampleModalLabel">Agregar Nueva Institución</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <form id="institutionForm" method="POST" action="<?php echo e(route('institutions.store')); ?>" class="tablelist-form" autocomplete="off">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="method_field" name="_method" value="">
                    <input type="hidden" id="institution_id" name="id">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="ri-information-line align-middle me-1"></i> 
                            El código de la institución se generará automáticamente al guardar.
                        </div>

                        <div class="mb-3">
                            <label for="name-field" class="form-label">Nombre de la Institución <span class="text-danger">*</span></label>
                            <input type="text" id="name-field" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                placeholder="Ingrese el nombre de la institución" value="<?php echo e(old('name')); ?>" required />
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor ingrese un nombre válido.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <label for="address-field" class="form-label">Dirección</label>
                            <textarea id="address-field" name="address" class="form-control <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                placeholder="Ingrese la dirección de la institución" rows="2"><?php echo e(old('address')); ?></textarea>
                            <?php $__errorArgs = ['address'];
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

                        <div class="mb-3">
                            <label for="municipality-field" class="form-label">Municipio <span class="text-danger">*</span></label>
                            <select class="form-control <?php $__errorArgs = ['municipality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="municipality_id" id="municipality-field" required>
                                <option value="">Seleccione un municipio</option>
                                <?php $__currentLoopData = $municipalities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $municipality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($municipality->id); ?>" <?php echo e(old('municipality_id') == $municipality->id ? 'selected' : ''); ?>>
                                        <?php echo e($municipality->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['municipality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php else: ?>
                                <div class="invalid-feedback">Por favor seleccione un municipio.</div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="active-field" name="active" value="1" checked>
                                <label class="form-check-label" for="active-field">Institución Activa</label>
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
                            <p class="text-muted mx-4 mb-0">¿Está seguro de que desea eliminar esta institución?</p>
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
            var options = {valueNames: ['institution_code', 'institution_name', 'institution_address', 'municipality', 'status']};
            var institutionList = new List('institutionList', options);

            document.getElementById('create-btn').addEventListener('click', function() {
                document.getElementById('exampleModalLabel').textContent = 'Agregar Nueva Institución';
                document.getElementById('institutionForm').action = "<?php echo e(route('institutions.store')); ?>";
                document.getElementById('method_field').value = '';            
                document.getElementById('institutionForm').reset();
                document.getElementById('institution_id').value = '';
                document.getElementById('active-field').checked = true;
                document.getElementById('save-btn').textContent = 'Guardar';
                document.getElementById('name-field').classList.remove('is-invalid');
                document.getElementById('address-field').classList.remove('is-invalid');
                document.getElementById('municipality-field').classList.remove('is-invalid');
            });

            document.querySelectorAll('.edit-item-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const address = this.getAttribute('data-address');
                    const municipalityId = this.getAttribute('data-municipality-id');
                    const active = this.getAttribute('data-active');
                    const updateUrl = this.getAttribute('data-update-url');
                    document.getElementById('exampleModalLabel').textContent = 'Editar Institución';
                    document.getElementById('institutionForm').action = updateUrl;
                    document.getElementById('method_field').value = 'PUT';
                    document.getElementById('institution_id').value = id;
                    document.getElementById('name-field').value = name;
                    document.getElementById('address-field').value = address;
                    document.getElementById('municipality-field').value = municipalityId;
                    document.getElementById('active-field').checked = active === '1';
                    document.getElementById('save-btn').textContent = 'Actualizar';
                    document.getElementById('name-field').classList.remove('is-invalid');
                    document.getElementById('address-field').classList.remove('is-invalid');
                    document.getElementById('municipality-field').classList.remove('is-invalid');
                });
            });

            document.querySelectorAll('.remove-item-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const deleteUrl = this.getAttribute('data-delete-url');
                    document.getElementById('deleteForm').action = deleteUrl;
                });
            });

            const form = document.getElementById('institutionForm');
            form.addEventListener('submit', function(event) {
                let isValid = true;
                const nameField = document.getElementById('name-field');
                if (!nameField.value.trim()) {
                    nameField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    nameField.classList.remove('is-invalid');
                }
                const municipalityField = document.getElementById('municipality-field');
                if (!municipalityField.value) {
                    municipalityField.classList.add('is-invalid');
                    isValid = false;
                } else {
                    municipalityField.classList.remove('is-invalid');
                }
                if (!isValid) {
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
            
            document.getElementById('showModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('name-field').classList.remove('is-invalid');
                document.getElementById('address-field').classList.remove('is-invalid');
                document.getElementById('municipality-field').classList.remove('is-invalid');
            });
        });
        
        function deleteMultiple() {
            alert('Función de eliminar múltiple - por implementar');
        }
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\_Mine\corporate\resources\views/tables-institutions.blade.php ENDPATH**/ ?>