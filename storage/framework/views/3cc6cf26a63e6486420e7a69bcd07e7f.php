
<?php
    $parseIniSize = function(string $val): int {
        $val  = trim($val);
        $last = strtolower(substr($val, -1));
        $num  = (int) $val;
        return match($last) {
            'g' => $num * 1024 * 1024 * 1024,
            'm' => $num * 1024 * 1024,
            'k' => $num * 1024,
            default => $num,
        };
    };
    $phpUpload = $parseIniSize(ini_get('upload_max_filesize') ?: '8M');
    $phpPost   = $parseIniSize(ini_get('post_max_size')       ?: '8M');
    $phpLimit  = min($phpUpload, $phpPost);
    $appLimitBytes = min(10 * 1024 * 1024, $phpLimit);
    $appLimitMb    = round($appLimitBytes / (1024 * 1024), 1);
    $appLimitKb    = (int) floor($appLimitBytes / 1024);
?>

<div class="modal fade" id="uploadActaModal" tabindex="-1" aria-labelledby="uploadActaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="uploadActaModalLabel">
                    <i class="ri-upload-line me-2"></i>
                    <span id="uploadActaTitle">Subir Acta Electoral</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="uploadActaAlert" class="alert d-none" role="alert"></div>

                <form id="uploadActaForm" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="voting_table_id"  id="uploadTableId">
                    <input type="hidden" name="election_type_id" id="uploadElectionTypeId">
                    <div class="mb-2">
                        <label class="form-label fw-semibold">
                            N° de Acta <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="acta_number" id="uploadActaNumber"
                               class="form-control"
                               placeholder="Ej: 001"
                               maxlength="50" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">
                            Foto del Acta <span class="text-danger">*</span>
                        </label>
                        <input type="file"
                               name="photo"
                               id="uploadActaPhoto"
                               class="form-control"
                               accept="image/jpeg,image/png,image/jpg"
                               required>
                        <div class="form-text">
                            JPEG o PNG — máximo <strong><?php echo e($appLimitMb); ?> MB</strong>
                            <?php if($phpLimit < 10 * 1024 * 1024): ?>
                                <span class="text-warning">
                                    (límite de PHP: <?php echo e($appLimitMb); ?> MB —
                                    para aumentarlo edite <code>upload_max_filesize</code> y
                                    <code>post_max_size</code> en <code>php.ini</code>)
                                </span>
                            <?php endif; ?>
                        </div>
                        <div id="photoPreviewWrap" class="mt-2 d-none text-center">
                            <img id="photoPreview"
                                 src="" alt="Vista previa"
                                 class="img-fluid rounded border"
                                 style="max-height:200px;object-fit:contain;">
                            <div id="photoPreviewInfo" class="small text-muted mt-1"></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">
                            PDF del Acta <span class="text-muted">(opcional)</span>
                        </label>
                        <input type="file" name="pdf" id="uploadActaPdf"
                               class="form-control"
                               accept="application/pdf">
                        <div class="form-text">Máximo 20 MB</div>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox"
                               name="has_physical" id="uploadHasPhysical" checked>
                        <label class="form-check-label" for="uploadHasPhysical">
                            Tengo el acta física en mano
                        </label>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="submitUploadActaBtn">
                    <i class="ri-upload-line me-1"></i>Subir Acta
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const PHP_LIMIT_BYTES = <?php echo e($appLimitBytes); ?>;
    const PHP_LIMIT_MB    = <?php echo e($appLimitMb); ?>;
    window.openActaModal = function (tableId, electionTypeId, tableCode) {
        document.getElementById('uploadTableId').value        = tableId        ?? '';
        document.getElementById('uploadElectionTypeId').value = electionTypeId ?? '';
        const titleEl = document.getElementById('uploadActaTitle');
        if (titleEl) titleEl.textContent = 'Subir Acta — ' + String(tableCode ?? tableId);
        document.getElementById('uploadActaForm').reset();
        hideAlert();
        document.getElementById('photoPreviewWrap').classList.add('d-none');

        new bootstrap.Modal(document.getElementById('uploadActaModal')).show();
    };
    document.getElementById('uploadActaPhoto')?.addEventListener('change', function () {
        const file = this.files?.[0];
        const wrap = document.getElementById('photoPreviewWrap');
        const info = document.getElementById('photoPreviewInfo');
        const prev = document.getElementById('photoPreview');

        if (!file) { wrap.classList.add('d-none'); return; }

        const sizeMb = file.size / (1024 * 1024);

        if (file.size > PHP_LIMIT_BYTES) {
            wrap.classList.add('d-none');
            showAlert('danger',
                `⚠️ El archivo seleccionado pesa ${sizeMb.toFixed(2)} MB y supera el límite de ${PHP_LIMIT_MB} MB. ` +
                `Reduzca la resolución de la imagen y vuelva a intentarlo. ` +
                `Para aumentar el límite, edite <code>upload_max_filesize</code> y <code>post_max_size</code> en <code>php.ini</code>.`
            );
            this.value = '';
            return;
        }

        hideAlert();
        const reader = new FileReader();
        reader.onload = function (e) {
            prev.src = e.target.result;
            wrap.classList.remove('d-none');
            info.textContent = file.name + ' — ' + sizeMb.toFixed(2) + ' MB';
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('submitUploadActaBtn')?.addEventListener('click', async function () {
        const form = document.getElementById('uploadActaForm');
        const tableId      = document.getElementById('uploadTableId').value;
        const etId         = document.getElementById('uploadElectionTypeId').value;
        const actaNumber   = document.getElementById('uploadActaNumber').value.trim();
        const photoFile    = document.getElementById('uploadActaPhoto').files?.[0];

        if (!tableId || !etId) { showAlert('danger', 'Error: no se identificó la mesa. Cierre y abra el modal nuevamente.'); return; }
        if (!actaNumber)       { showAlert('danger', 'Ingrese el número de acta.'); return; }
        if (!photoFile)        { showAlert('danger', 'Seleccione la foto del acta.'); return; }

        if (photoFile.size > PHP_LIMIT_BYTES) {
            showAlert('danger',
                `El archivo pesa ${(photoFile.size / 1024 / 1024).toFixed(2)} MB y supera el límite de ${PHP_LIMIT_MB} MB.`
            );
            return;
        }

        const btn = this;
        btn.disabled  = true;
        btn.innerHTML = '<i class="ri-loader-4-line ri-spin me-1"></i>Subiendo…';
        hideAlert();
        showAlert('info', '<i class="ri-loader-4-line ri-spin me-1"></i>Subiendo acta, por favor espere…');

        try {
            const formData = new FormData(form);

            const resp = await fetch('/actas/upload', {
                method:  'POST',
                headers: {
                    'Accept':       'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });

            const data = await resp.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('uploadActaModal'))?.hide();
                Swal.fire({
                    icon: 'success', title: '✅ Acta subida',
                    text: data.message ?? 'Acta subida correctamente',
                    toast: true, position: 'top-end',
                    showConfirmButton: false, timer: 3000,
                });
                // Dispatch event so the table row can refresh without full reload
                document.dispatchEvent(new CustomEvent('actaUploaded', {
                    detail: { tableId, actaId: data.acta?.id }
                }));
                setTimeout(() => location.reload(), 1500);
            } else {
                let msg = data.message ?? 'Error al subir el acta';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                showAlert('danger', msg);
            }

        } catch (err) {
            showAlert('danger', 'Error de conexión: ' + err.message);
        } finally {
            btn.disabled  = false;
            btn.innerHTML = '<i class="ri-upload-line me-1"></i>Subir Acta';
        }
    });
    function showAlert(type, html) {
        const el = document.getElementById('uploadActaAlert');
        if (!el) return;
        el.className = `alert alert-${type}`;
        el.innerHTML = html;
        el.classList.remove('d-none');
    }
    function hideAlert() {
        const el = document.getElementById('uploadActaAlert');
        if (el) { el.classList.add('d-none'); el.innerHTML = ''; }
    }
})();
</script><?php /**PATH D:\_Mine\sistema_electoral\resources\views/voting-table-votes/partials/modals/upload-acta-modal.blade.php ENDPATH**/ ?>