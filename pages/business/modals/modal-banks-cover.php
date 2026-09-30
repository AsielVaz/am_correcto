<!-- Modal de vista previa para carátulas bancarias -->
<div class="modal fade" id="modalPreviewCaratulaBancaria" tabindex="-1" aria-labelledby="modalPreviewCaratulaBancariaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewCaratulaBancariaLabel">Vista previa carátula bancaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-0" style="height:80vh;">
                <iframe id="iframePreviewCaratulaBancaria" src="" style="border:0;width:100%;height:100%;" loading="lazy" title="Vista previa carátula bancaria"></iframe>
            </div>
        </div>
    </div>
</div>
<script>
    // Vista previa de carátula bancaria
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('modalPreviewCaratulaBancaria');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                var iframe = document.getElementById('iframePreviewCaratulaBancaria');
                if (iframe) iframe.src = '';
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
<!-- Modal Caratulas bancarias -->
<div class="modal fade" id="modalCaratulasBancarias" tabindex="-1" aria-labelledby="modalCaratulasBancariasLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCaratulasBancariasLabel">
                    <i class="bx bx-file me-2"></i>
                    Caratulas bancarias
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
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente -->
                        </tbody>
                    </table>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevaCaratula" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nueva carátula</span>
                        </button>
                    </div>

                    <!-- Formulario para nueva carátula bancaria -->
                    <form id="formNuevaCaratula" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" enctype="multipart/form-data" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nueva carátula bancaria</h6>
                            <div class="row g-2">
                                <div class="col-md-12">
                                    <label for="nuevaCaratulaArchivo" class="form-label">Documento</label>
                                    <input type="file" class="form-control" name="caratula" id="nuevaCaratulaArchivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarCaratula" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="id" id="inputIdEmpresaCaratula">
                            <div id="alertNuevaCaratula" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar carátula bancaria -->
<div class="modal fade" id="modalConfirmDeleteCaratula" tabindex="-1" aria-labelledby="modalConfirmDeleteCaratulaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteCaratulaLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta carátula bancaria? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteCaratula" data-requires="write">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-banks-cover.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteCaratula');
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