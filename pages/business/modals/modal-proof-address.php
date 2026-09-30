<!-- Modal Comprobante de domicilio -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-labelledby="modalComprobanteLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalComprobanteLabel">Comprobante de domicilio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div id="iframe-comprobante-container" class="iframe-container"></div>
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnSubirComprobante" data-requires="write">
                        <i class="bx bx-upload me-1"></i> Subir comprobante
                    </button>
                    <span id="nombreComprobanteArchivo" class="ms-2 text-muted" style="font-size: 0.95em;"></span>
                    <form id="formComprobanteModal" enctype="multipart/form-data" style="display:none;" data-requires="write" data-permission-mode="disable">
                        <input type="file" name="comprobante_pdf" id="inputComprobanteModal" accept="application/pdf" style="display:none;">
                        <input type="hidden" name="tipo" value="comprobante">
                    </form>
                    <div class="text-end mt-2">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium d-none" id="btnGuardarComprobante" data-requires="write">
                            <i class="bx bx-save me-1"></i> Guardar
                        </button>
                    </div>
                </div>

                <!-- Alerta de guardado -->
                <div id="comprobante-alert" style="display:none;position:fixed;top:30px;right:30px;z-index:9999;">
                    <div class="alert alert-success d-flex align-items-center shadow" role="alert" style="min-width:220px;">
                        <iconify-icon icon="solar:check-circle-bold" class="me-2 fs-20"></iconify-icon>
                        <span id="comprobante-alert-text">¡Guardado!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-proof-address.js"></script>