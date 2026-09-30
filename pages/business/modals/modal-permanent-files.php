<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<script>
    // Efecto blur al abrir modal de observaciones de rechazo
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalObservacionesRechazo');
        var lastModal = null;
        if (confirmModal) {
            confirmModal.addEventListener('show.bs.modal', function() {
                var modals = document.querySelectorAll('.modal.show');
                modals.forEach(function(m) {
                    if (m !== confirmModal) {
                        m.classList.add('modal-blur');
                        lastModal = m;
                    }
                });
            });
            confirmModal.addEventListener('hidden.bs.modal', function() {
                if (lastModal) {
                    lastModal.classList.remove('modal-blur');
                    lastModal = null;
                }
            });
        }
    });
</script>
<!-- Modal Documentos permanentes -->
<div class="modal fade" id="modalDocumentosPermanentes" tabindex="-1" aria-labelledby="modalDocumentosPermanentesLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered" id="modalDocumentosPermanentesDialog" style="min-height:95vh;max-height:95vh;">
        <div class="modal-content" style="height:100%;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDocumentosPermanentesLabel">
                    <i class="bx bx-folder me-2"></i>
                    Documentos permanentes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 align-items-stretch" style="height:calc(98vh - 110px);">
                    <div class="col-12 d-flex flex-column" id="listCol" style="height:100%;">
                        <div class="documentos-permanentes-wrapper grow d-flex flex-column gap-3 overflow-auto pe-1">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2" id="documentos-permanentes-header">
                                <span class="text-uppercase fw-semibold small text-muted">Documento</span>
                                <div class="d-inline-flex align-items-center gap-3 flex-wrap justify-content-end ms-auto">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <span class="text-uppercase fw-semibold small text-muted">Estado</span>
                                        <button type="button" class="btn btn-outline-info btn-sm estado-info-trigger" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" aria-label="Información sobre estados">
                                            <i class="bx bx-info-circle"></i>
                                        </button>
                                    </div>
                                    <div class="d-inline-flex align-items-center gap-2" id="documentos-permanentes-acciones-header">
                                        <span class="text-uppercase fw-semibold small text-muted">Acciones</span>
                                        <button type="button" class="btn btn-outline-info btn-sm acciones-info-trigger" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" aria-label="Información sobre acciones">
                                            <i class="bx bx-info-circle"></i>
                                        </button>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevoDocumentoPermanente" data-requires="write">
                                        <i class="bx bx-plus"></i>
                                        <span>Nuevo documento</span>
                                    </button>
                                </div>
                            </div>
                            <div id="documentos-permanentes-empty" class="text-center text-muted py-4 border border-light-subtle rounded-3 bg-light bg-opacity-25">
                                <i class="bx bx-file-blank fs-3 d-block mb-2"></i>
                                <span>Sin documentos permanentes</span>
                            </div>
                            <div class="accordion accordion-flush d-none" id="documentos-permanentes-accordion" aria-live="polite"></div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-5 flex-column gap-3 d-none" id="previewCol" style="height:100%;">
                        <div id="previewPanel" class="card h-100 grow">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bx bx-show"></i>
                                    <span class="fw-semibold" id="previewTitle">Vista previa</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div id="previewActions" class="d-none d-inline-flex align-items-center gap-2 me-2 flex-column" style="align-items: stretch !important;">
                                        <div class="d-flex align-items-center gap-2">
                                            <button type="button" class="btn btn-success btn-sm" id="btnPreviewApprove" title="Marcar como revisado" aria-label="Marcar como revisado">
                                                <i class="bx bx-check"></i>
                                                <span class="d-none d-sm-inline">Aprobar</span>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" id="btnPreviewReject" title="Rechazar revisión" aria-label="Rechazar revisión">
                                                <i class="bx bx-x"></i>
                                                <span class="d-none d-sm-inline">Rechazar</span>
                                            </button>
                                        </div>
                                        <div id="observacionesRechazoContainer" class="mt-2 d-none">
                                            <label for="inputObservacionesRechazo" class="form-label small">Observaciones (obligatorio al rechazar)</label>
                                            <textarea class="form-control" id="inputObservacionesRechazo" rows="2" placeholder="Escribe el motivo del rechazo..." required></textarea>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewLeft" title="Mover a la izquierda"><i class="bx bx-left-arrow-alt"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewRight" title="Mover a la derecha"><i class="bx bx-right-arrow-alt"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnPreviewClose" title="Ocultar"><i class="bx bx-x"></i></button>
                                </div>
                            </div>
                            <div class="card-body d-flex flex-column p-0" style="height:60vh;">
                                <iframe id="documentPreviewFrame" src="" style="border:0;width:100%;height:100%;" loading="lazy" title="Vista previa documento"></iframe>
                            </div>
                        </div>

                        <!-- Formulario con pestañas para nuevo documento permanente -->
                        <div id="formNuevoDocumentoPermanente" class="card shadow-sm border-0 grow flex-column d-none" style="overflow:hidden;" data-requires="write" data-permission-mode="disable">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">Agregar nuevo documento permanente</h6>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCancelarDocumentoPermanente" data-requires="write" title="Cerrar formulario">
                                        <i class="bx bx-x"></i>
                                    </button>
                                </div>

                                <!-- Botones tipo tabs -->
                                <div class="btn-group w-100 mb-2" id="tabsNuevoDocumentoPermanente" role="tablist" style="overflow-x:auto; overflow-y:hidden; white-space:nowrap; flex-wrap:nowrap;">
                                    <button class="btn btn-outline-primary flex-fill" id="tab-solicitar-tab" type="button" data-bs-target="#tab-solicitar" aria-controls="tab-solicitar" aria-selected="false">
                                        Solicitar documento
                                    </button>
                                    <button class="btn btn-outline-primary flex-fill" id="tab-subir-tab" type="button" data-bs-target="#tab-subir" aria-controls="tab-subir" aria-selected="false">
                                        Subir archivo
                                    </button>
                                    <button class="btn btn-outline-primary flex-fill" id="tab-resolver-tab" type="button" data-bs-target="#tab-resolver" aria-controls="tab-resolver" aria-selected="false">
                                        Resolver archivo faltante
                                    </button>
                                </div>
                                <style>
                                    #tabsNuevoDocumentoPermanente .tab-btn {
                                        border-radius: 0 !important;
                                        font-weight: 500;
                                        transition: background 0.15s, color 0.15s;
                                    }

                                    #tabsNuevoDocumentoPermanente .tab-btn.active,
                                    #tabsNuevoDocumentoPermanente .tab-btn:focus {
                                        background: #0d6efd;
                                        color: #fff;
                                        border-color: #0d6efd;
                                    }

                                    #tabsNuevoDocumentoPermanente .tab-btn:not(.active) {
                                        background: #fff;
                                        color: #0d6efd;
                                    }
                                </style>

                                <div class="tab-content grow overflow-auto pe-1 pt-3">
                                    <!-- Mensaje inicial -->
                                    <div class="tab-pane fade show active overflow-x-hidden" id="tab-inicial" role="tabpanel" aria-labelledby="tab-inicial-tab" tabindex="0">
                                        <div class="text-center text-muted py-4">
                                            <i class="bx bx-info-circle fs-2 d-block mb-2"></i>
                                            <span>Selecciona una pestaña para ver las opciones</span>
                                        </div>
                                    </div>
                                    <!-- Tab: Solicitar documento -->
                                    <div class="tab-pane fade overflow-x-hidden" id="tab-solicitar" role="tabpanel" aria-labelledby="tab-solicitar-tab" tabindex="0">
                                        <form id="formSolicitarDocumentoPermanente" autocomplete="off">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label for="selectTipoDocumentoSolicitar" class="form-label">Tipo de documento</label>
                                                    <select class="form-select" id="selectTipoDocumentoSolicitar" name="descripcion" required>
                                                        <option value="" selected>Selecciona un tipo...</option>
                                                        <option value="Acta constitutiva">Acta constitutiva</option>
                                                        <option value="Registro Público de la Propiedad y de Comercio (Acta)">Registro Público de la Propiedad y de Comercio (Acta)</option>
                                                        <option value="Registro Público de la Propiedad y de Comercio (Asamblea)">Registro Público de la Propiedad y de Comercio (Asamblea)</option>
                                                        <option value="Asamblea">Asamblea</option>
                                                        <option value="Poder">Poder</option>
                                                        <option value="INE del representante legal">INE del representante legal</option>
                                                        <option value="INE del socio">INE del socio</option>
                                                    </select>
                                                    <div class="form-text">Se registrará como documento pendiente.</div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col text-end">
                                                    <button type="submit" class="btn btn-warning rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                                        <i class="bx bx-time-five"></i>
                                                        <span>Solicitar</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Tab: Subir archivo -->
                                    <div class="tab-pane fade overflow-x-hidden" id="tab-subir" role="tabpanel" aria-labelledby="tab-subir-tab" tabindex="0">
                                        <form id="formSubirDocumentoPermanente" autocomplete="off" enctype="multipart/form-data">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <label for="selectTipoDocumentoSubir" class="form-label">Tipo de documento</label>
                                                    <select class="form-select" id="selectTipoDocumentoSubir" name="descripcion" required>
                                                        <option value="" selected>Selecciona un tipo...</option>
                                                        <option value="Acta constitutiva">Acta constitutiva</option>
                                                        <option value="RPPC Acta">Registro Público de la Propiedad y de Comercio (Acta)</option>
                                                        <option value="RPPC Asamblea">Registro Público de la Propiedad y de Comercio (Asamblea)</option>
                                                        <option value="Asamblea">Asamblea</option>
                                                        <option value="Poder">Poder</option>
                                                        <option value="INE del representante legal">INE del representante legal</option>
                                                        <option value="INE del socio">INE del socio</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="nuevoDocumentoPermanenteArchivo" class="form-label">Documento</label>
                                                    <input type="file" class="form-control" name="documento" id="nuevoDocumentoPermanenteArchivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                                    <div class="form-text">Formatos permitidos: PDF, JPG, PNG</div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col text-end">
                                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                                        <i class="bx bx-save"></i>
                                                        <span>Guardar</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Tab: Resolver archivo faltante -->
                                    <div class="tab-pane fade overflow-x-hidden" id="tab-resolver" role="tabpanel" aria-labelledby="tab-resolver-tab" tabindex="0">
                                        <form id="formResolverArchivoFaltante" autocomplete="off" enctype="multipart/form-data">
                                            <div class="row g-3">
                                                <div class="col-md-12" id="resolverArchivoFaltanteContainer" style="display:none;">
                                                    <label for="selectResolverArchivoFaltante" class="form-label">Selecciona el documento faltante a resolver</label>
                                                    <select class="form-select" id="selectResolverArchivoFaltante" required>
                                                        <option value="">Selecciona un documento...</option>
                                                    </select>
                                                    <div class="form-text">El tipo ya está definido por la alerta seleccionada.</div>
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="resolverArchivoFaltanteArchivo" class="form-label">Documento</label>
                                                    <input type="file" class="form-control" id="resolverArchivoFaltanteArchivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                                    <div class="form-text">Formatos permitidos: PDF, JPG, PNG</div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col text-end">
                                                    <button type="submit" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                                        <i class="bx bx-upload"></i>
                                                        <span>Resolver</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <input type="hidden" id="inputIdEmpresaDocumentoPermanente">
                                <div id="alertNuevoDocumentoPermanente" class="alert alert-success mt-3" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmDeleteDocumentoPermanente" tabindex="-1" aria-labelledby="modalConfirmDeleteDocumentoPermanenteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteDocumentoPermanenteLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este documento permanente? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteDocumentoPermanente" data-requires="write">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/modal-observaciones-rechazo.php'; ?>
<script src="scripts/modal-permanent-files.js"></script>
<script>
    // Lógica para oscurecer el modal anterior cuando se abre el de confirmación
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteDocumentoPermanente');
        var lastModal = null;
        if (confirmModal) {
            confirmModal.addEventListener('show.bs.modal', function() {
                // Buscar el modal visible anterior
                var modals = document.querySelectorAll('.modal.show');
                modals.forEach(function(m) {
                    if (m !== confirmModal) {
                        m.classList.add('modal-blur');
                        lastModal = m;
                    }
                });
            });
            confirmModal.addEventListener('hidden.bs.modal', function() {
                if (lastModal) {
                    lastModal.classList.remove('modal-blur');
                    lastModal = null;
                }
            });
        }
    });
</script>