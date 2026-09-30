<!-- Modal Sitio web -->
<div class="modal fade" id="modalSitioWeb" tabindex="-1" aria-labelledby="modalSitioWebLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSitioWebLabel">
                    <i class="bx bx-globe me-2"></i>
                    Sitio web
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="sitioWebForm" onsubmit="event.preventDefault();" data-requires="write" data-permission-mode="disable">
                    <div class="mb-3">
                        <label for="sitioWebInput" class="form-label">Sitio web</label>
                        <input type="url" class="form-control" id="sitioWebInput" name="sitio_web"
                            placeholder="Ej. https://www.empresa.com" maxlength="200" inputmode="url">
                        <div class="form-text text-danger" id="sitioWebValidationMessage" style="display:none;">Ingresa un sitio web válido (ej. https://www.empresa.com).</div>
                    </div>
                    <div class="mb-3 text-end">
                        <button type="submit" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                            <i class="bx bx-save"></i>
                            <span>Guardar</span>
                        </button>
                    </div>
                </form>

                <!-- Alerta de guardado -->
                <div id="web-alert" style="display:none;position:fixed;top:30px;right:30px;z-index:9999;">
                    <div class="alert alert-success d-flex align-items-center shadow" role="alert" style="min-width:220px;">
                        <iconify-icon icon="solar:check-circle-bold" class="me-2 fs-20"></iconify-icon>
                        <span id="web-alert-text">¡Guardado!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-web.js"></script>