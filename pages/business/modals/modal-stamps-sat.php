<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<!-- Modal Sellos SAT -->
<div class="modal fade" id="modalSellosSAT" tabindex="-1" aria-labelledby="modalSellosSATLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSellosSATLabel">
                    <i class="bx bx-certification me-2"></i>
                    Sellos SAT
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
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevoSelloSAT" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nuevo sello</span>
                        </button>
                    </div>

                    <!-- Formulario para nuevo sello SAT -->
                    <form id="formNuevoSelloSAT" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" enctype="multipart/form-data" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nuevo sello SAT</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="nuevoSelloTipo" class="form-label">Tipo</label>
                                    <select class="form-select" name="tipo" id="nuevoSelloTipo" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="CER">CER</option>
                                        <option value="KEY">KEY</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nuevoSelloArchivo" class="form-label">Documento</label>
                                    <input type="file" class="form-control" name="documento" id="nuevoSelloArchivo" accept=".cer,.key,.pfx,.p12" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarSelloSAT" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="idEmpresa" id="inputIdEmpresaSelloSAT">
                            <div id="alertNuevoSelloSAT" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar sello SAT -->
<div class="modal fade" id="modalConfirmDeleteSelloSAT" tabindex="-1" aria-labelledby="modalConfirmDeleteSelloSATLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteSelloSATLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este sello SAT? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteSelloSAT" data-requires="write">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-stamps-sat.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteSelloSAT');
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