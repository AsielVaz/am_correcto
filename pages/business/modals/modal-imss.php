<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<!-- Modal IMSS -->
<div class="modal fade" id="modalIMSS" tabindex="-1" aria-labelledby="modalIMSSLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalIMSSLabel">
                    <i class="bx bx-health me-2"></i>
                    IMSS
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-centered">
                    <table class="table mb-0 table-bordered align-middle">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 py-2 px-2">Tipo</th>
                                <th class="border-0 py-2 px-2">Fecha</th>
                                <th class="border-0 py-2 px-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente -->
                        </tbody>
                    </table>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevoImss" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nuevo IMSS</span>
                        </button>
                    </div>

                    <!-- Formulario para nuevo documento IMSS -->
                    <form id="formNuevoImss" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" enctype="multipart/form-data" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nuevo documento IMSS</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="nuevoImssTipo" class="form-label">Tipo</label>
                                    <select class="form-select" name="tipo" id="nuevoImssTipo" required>
                                        <option value="">Seleccionar tipo de archivo</option>
                                        <option value="KEY">Clave privada (.key)</option>
                                        <option value="CSD">Certificado digital (.pfx)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nuevoImssArchivo" class="form-label">Documento</label>
                                    <input type="file" class="form-control" name="documento" id="nuevoImssArchivo" accept=".key,.pfx" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarImss" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="inputIdEmpresaImss">
                            <div id="alertNuevoImss" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar documento IMSS -->
<div class="modal fade" id="modalConfirmDeleteImss" tabindex="-1" aria-labelledby="modalConfirmDeleteImssLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteImssLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este documento IMSS? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteImss" data-requires="write">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-imss.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteImss');
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