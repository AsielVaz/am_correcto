<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteBanco');
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
<!-- Modal Contraseñas bancos -->
<div class="modal fade" id="modalContrasenasBancos" tabindex="-1" aria-labelledby="modalContrasenasBancosLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalContrasenasBancosLabel">
                    <i class="bx bx-credit-card me-2"></i>
                    Contraseñas bancos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-centered">
                    <table class="table mb-0 table-bordered align-middle">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 py-2 px-2">Banco</th>
                                <th class="border-0 py-2 px-2">Usuario</th>
                                <th class="border-0 py-2 px-2">Contraseña</th>
                                <th class="border-0 py-2 px-2">NIP</th>
                                <th class="border-0 py-2 px-2">Clave Operativa</th>
                                <th class="border-0 py-2 px-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente -->
                        </tbody>
                    </table>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevaContrasenaBanco" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nueva contraseña</span>
                        </button>
                    </div>

                    <!-- Formulario para nueva contraseña banco -->
                    <form id="formNuevaContrasenaBanco" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nueva contraseña banco</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="nuevoBancoBanco" class="form-label">Banco</label>
                                    <input type="text" class="form-control" name="banco" id="nuevoBancoBanco" placeholder="Nombre del banco" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nuevoBancoUsuario" class="form-label">Usuario</label>
                                    <input type="text" class="form-control" name="usuario" id="nuevoBancoUsuario" placeholder="Usuario del banco" required>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-4">
                                    <label for="nuevoBancoContrasena" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" name="contrasena" id="nuevoBancoContrasena" placeholder="Contraseña" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="nuevoBancoNIP" class="form-label">NIP</label>
                                    <input type="password" class="form-control" name="nip" id="nuevoBancoNIP" placeholder="NIP" maxlength="4">
                                </div>
                                <div class="col-md-4">
                                    <label for="nuevoBancoClaveOp" class="form-label">Clave Operativa</label>
                                    <input type="text" class="form-control" name="claveOp" id="nuevoBancoClaveOp" placeholder="Clave operativa">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarContrasenaBanco" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="idEmpresa" id="inputIdEmpresaContrasenaBanco">
                            <div id="alertNuevaContrasenaBanco" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-password-bank.js"></script>