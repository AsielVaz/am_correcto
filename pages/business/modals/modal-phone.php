<!-- Modal Teléfono -->
<div class="modal fade" id="modalTelefono" tabindex="-1" aria-labelledby="modalTelefonoLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTelefonoLabel">
                    <i class="bx bx-phone me-2"></i>
                    Teléfono
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="telefonoForm" onsubmit="event.preventDefault();" data-requires="write" data-permission-mode="disable">
                    <div class="mb-3">
                        <label for="telefonoInput" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefonoInput" name="telefono"
                            placeholder="Ej. 5551234567" maxlength="20">
                        <div class="form-text text-danger" id="telefonoValidationMessage" style="display:none;">Ingresa un teléfono válido de 10 dígitos.</div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                            <i class="bx bx-save"></i>
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>

                <!-- Alerta de guardado -->
                <div id="telefono-alert" style="display:none;position:fixed;top:30px;right:30px;z-index:9999;">
                    <div class="alert alert-success d-flex align-items-center shadow" role="alert" style="min-width:220px;">
                        <iconify-icon icon="solar:check-circle-bold" class="me-2 fs-20"></iconify-icon>
                        <span id="telefono-alert-text">¡Guardado!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-phone.js"></script>