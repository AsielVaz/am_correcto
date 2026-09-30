<!-- Modal Fin dominio -->
<div class="modal fade" id="modalFinDominio" tabindex="-1" aria-labelledby="modalFinDominioLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFinDominioLabel">
                    <i class="bx bx-calendar me-2"></i>
                    Fecha de finalización de dominio
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-column align-items-center">
                    <div class="mb-1" id="finDominioEstado"></div>
                    <div class="small text-muted mb-3" id="finDominioEstadoHint"></div>
                    <div class="mb-2 text-center">
                        <i class="bx bx-calendar fs-4 text-primary mb-2"></i>
                        <div class="fw-semibold mb-1">Fecha de finalización:</div>
                        <div class="text-dark fs-5" id="finDominioFecha"></div>
                    </div>
                    <div class="w-100 mt-3" id="finDominioEditContainer">
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnEditarFinDominio" data-requires="write">
                                <i class="bx bx-edit-alt"></i>
                                <span>Editar</span>
                            </button>
                        </div>
                        <form id="finDominioForm" class="border rounded-3 p-3 bg-light" style="display:none;" data-requires="write" data-permission-mode="disable">
                            <div class="mb-2">
                                <label for="finDominioInput" class="form-label mb-1">Nueva fecha de finalización</label>
                                <input type="date" class="form-control" id="finDominioInput" name="fin_dominio" required>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary btn-sm rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarFinDominio">
                                    <i class="bx bx-x"></i>
                                    <span>Cancelar</span>
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium">
                                    <i class="bx bx-save"></i>
                                    <span>Guardar</span>
                                </button>
                            </div>
                        </form>
                        <div class="small mt-2" id="finDominioMessage"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-end-domain.js"></script>