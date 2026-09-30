<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteFiel');
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
<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<!-- Modal FIEL -->
<div class="modal fade" id="modalFIEL" tabindex="-1" aria-labelledby="modalFIELLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFIELLabel">
                    <i class="bx bx-id-card me-2"></i>
                    FIEL
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
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevaFIEL" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nueva FIEL</span>
                        </button>
                    </div>

                    <!-- Formulario para nueva FIEL -->
                    <form id="formNuevaFIEL" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" enctype="multipart/form-data" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nueva FIEL</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="nuevaFIELTipo" class="form-label">Tipo</label>
                                    <select class="form-select" name="tipo" id="nuevaFIELTipo" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="CER">CER - Certificado</option>
                                        <option value="KEY">KEY - Llave privada</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="nuevaFIELArchivo" class="form-label">Documento</label>
                                    <input type="file" class="form-control" name="documento" id="nuevaFIELArchivo" accept=".cer,.key,.pfx,.p12" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarFIEL" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="idEmpresa" id="inputIdEmpresaFIEL">
                            <div id="alertNuevaFIEL" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-fiel.js"></script>