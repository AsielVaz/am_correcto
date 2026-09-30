<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<!-- Modal Estados de cuenta -->
<div class="modal fade" id="modalEstadosCuenta" tabindex="-1" aria-labelledby="modalEstadosCuentaLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstadosCuentaLabel">
                    <i class="bx bx-receipt me-2"></i>
                    Estados de cuenta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-centered">
                    <table class="table mb-0 table-bordered align-middle">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 py-2 px-2">Fecha</th>
                                <th class="border-0 py-2 px-2">Acciones</th>
                                <!-- Modal de vista previa para estados de cuenta -->
                                <div class="modal fade" id="modalPreviewEstadoCuenta" tabindex="-1" aria-labelledby="modalPreviewEstadoCuentaLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalPreviewEstadoCuentaLabel">Vista previa estado de cuenta</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <div class="modal-body p-0" style="height:80vh;">
                                                <iframe id="iframePreviewEstadoCuenta" src="" style="border:0;width:100%;height:100%;" loading="lazy" title="Vista previa estado de cuenta"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    // Vista previa de estado de cuenta
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var modal = document.getElementById('modalPreviewEstadoCuenta');
                                        if (modal) {
                                            modal.addEventListener('hidden.bs.modal', function() {
                                                var iframe = document.getElementById('iframePreviewEstadoCuenta');
                                                if (iframe) iframe.src = '';
                                            });
                                        }
                                    });
                                </script>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente -->
                        </tbody>
                    </table>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevoEstadoCuenta" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nuevo estado</span>
                        </button>
                    </div>

                    <!-- Formulario para nuevo estado de cuenta -->
                    <form id="formNuevoEstadoCuenta" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" enctype="multipart/form-data" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nuevo estado de cuenta</h6>
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label for="nuevoEstadoCuentaArchivo" class="form-label">Documento</label>
                                    <input type="file" class="form-control" name="estado" id="nuevoEstadoCuentaArchivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarEstadoCuenta" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="inputIdEmpresaEstadoCuenta">
                            <div id="alertNuevoEstadoCuenta" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar estado de cuenta -->
<div class="modal fade" id="modalConfirmDeleteEstadoCuenta" tabindex="-1" aria-labelledby="modalConfirmDeleteEstadoCuentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteEstadoCuentaLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este estado de cuenta? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteEstadoCuenta" data-requires="write">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-states-account.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteEstadoCuenta');
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