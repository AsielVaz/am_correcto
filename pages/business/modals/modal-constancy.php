<!-- Modal Constancia de situación fiscal -->
<div class="modal fade" id="modalConstancia" tabindex="-1" aria-labelledby="modalConstanciaLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConstanciaLabel">Constancia de situación fiscal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="tabsConstancia" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-constancia-moral" data-bs-toggle="tab" data-bs-target="#pane-constancia-moral" type="button" role="tab" aria-controls="pane-constancia-moral" aria-selected="true">Moral</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-constancia-fisica" data-bs-toggle="tab" data-bs-target="#pane-constancia-fisica" type="button" role="tab" aria-controls="pane-constancia-fisica" aria-selected="false">Física</button>
                    </li>
                </ul>
                <div class="tab-content pt-3">
                    <!-- Pestaña Moral: conserva la funcionalidad actual -->
                    <div class="tab-pane fade show active" id="pane-constancia-moral" role="tabpanel" aria-labelledby="tab-constancia-moral" tabindex="0">
                        <div id="iframe-constancia-container" class="iframe-container"></div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnSubirConstancia" data-requires="write">
                                <i class="bx bx-upload me-1"></i> Subir constancia
                            </button>
                            <span id="nombreConstanciaArchivo" class="ms-2 text-muted" style="font-size: 0.95em;"></span>
                            <form id="formConstanciaModal" enctype="multipart/form-data" style="display:none;" data-requires="write" data-permission-mode="disable">
                                <input type="file" name="CSF" id="inputConstanciaModal" accept="application/pdf" style="display:none;">
                                <input type="hidden" name="tipo" value="constancia">
                            </form>
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium d-none" id="btnGuardarConstancia" data-requires="write">
                                    <i class="bx bx-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pestaña Física: RL / Socio -->
                    <div class="tab-pane fade" id="pane-constancia-fisica" role="tabpanel" aria-labelledby="tab-constancia-fisica" tabindex="0">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-muted">Ver:</span>
                                <button type="button" id="btnToggleFisicaTipo" class="btn btn-outline-secondary btn-sm rounded-pill">
                                    Representante legal
                                </button>
                            </div>
                            <span id="labelFisicaTipoActual" class="text-muted small">Mostrando: Representante legal</span>
                        </div>
                        <div id="iframe-constancia-fisica-container" class="iframe-container"></div>
                        <div class="mt-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="mb-0">Historial</h6>
                                <small class="text-muted" id="fisicaConteoItems">0 elementos</small>
                            </div>
                            <div id="listaConstanciasFisica" class="list-group list-group-flush border rounded"></div>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-outline-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnSubirConstanciaFisica" data-requires="write">
                                <i class="bx bx-upload me-1"></i> Subir constancia
                            </button>
                            <span id="nombreConstanciaFisicaArchivo" class="ms-2 text-muted" style="font-size: 0.95em;"></span>
                            <input type="file" id="inputConstanciaFisica" accept="application/pdf" style="display:none;">
                            <div class="text-end mt-2">
                                <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium d-none" id="btnGuardarConstanciaFisica" data-requires="write">
                                    <i class="bx bx-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerta de guardado (se reutiliza para ambas pestañas) -->
                <div id="constancia-alert" style="display:none;position:fixed;top:30px;right:30px;z-index:9999;">
                    <div class="alert alert-success d-flex align-items-center shadow" role="alert" style="min-width:220px;">
                        <iconify-icon icon="solar:check-circle-bold" class="me-2 fs-20"></iconify-icon>
                        <span id="constancia-alert-text">¡Guardado!</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-constancy.js"></script>