<!-- Modal Correo contacto -->
<div class="modal fade" id="modalCorreoContacto" tabindex="-1" aria-labelledby="modalCorreoContactoLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCorreoContactoLabel">
                    <i class="bx bx-envelope me-2"></i>
                    Correo electrónico de contacto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="correoContactoForm" onsubmit="event.preventDefault();" data-requires="write" data-permission-mode="disable">
                    <div class="mb-3">
                        <label for="correoContactoInput" class="form-label">Correo contacto</label>
                        <input type="email" class="form-control" id="correoContactoInput" name="correo_contacto"
                            placeholder="Ej. contacto@empresa.com">
                        <div class="form-text text-danger" id="correoValidationMessage" style="display:none;">Ingresa un correo electrónico válido.</div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                            <i class="bx bx-save"></i>
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>

                <!-- Alerta de guardado -->
                <div id="correo-alert" style="display:none;position:fixed;top:30px;right:30px;z-index:9999;">
                    <div class="alert alert-success d-flex align-items-center shadow" role="alert" style="min-width:220px;">
                        <iconify-icon icon="solar:check-circle-bold" class="me-2 fs-20"></iconify-icon>
                        <span id="correo-alert-text">¡Guardado!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-email.js"></script>