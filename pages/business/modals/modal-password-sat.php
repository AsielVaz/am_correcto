<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<!-- Modal Contraseña SAT -->
<div class="modal fade" id="modalContrasenaSAT" tabindex="-1" aria-labelledby="modalContrasenaSATLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalContrasenaSATLabel">
                    <i class="bx bx-lock me-2"></i>
                    Contraseña SAT
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-centered">
                    <table class="table mb-0 table-bordered align-middle">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 py-2 px-2">Usuario</th>
                                <th class="border-0 py-2 px-2">Contraseña</th>
                                <th class="border-0 py-2 px-2">RFC</th>
                                <th class="border-0 py-2 px-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Datos cargados dinámicamente -->
                        </tbody>
                    </table>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevaContrasenaSAT" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nueva contraseña</span>
                        </button>
                    </div>

                    <!-- Formulario para nueva contraseña SAT -->
                    <form id="formNuevaContrasenaSAT" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nueva contraseña SAT</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label for="nuevoSATUsuario" class="form-label">Usuario</label>
                                    <input type="text" class="form-control" name="usuario" id="nuevoSATUsuario" placeholder="Usuario SAT" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="nuevoSATContrasena" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" name="contrasena" id="nuevoSATContrasena" placeholder="Contraseña" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="nuevoSATRFC" class="form-label">RFC</label>
                                    <input type="text" class="form-control" name="rfc" id="nuevoSATRFC" placeholder="RFC" required>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" data-requires="write">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarContrasenaSAT" data-requires="write">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="idEmpresa" id="inputIdEmpresaContrasenaSAT">
                            <div id="alertNuevaContrasenaSAT" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para eliminar contraseña SAT -->
<div class="modal fade" id="modalConfirmDeleteSAT" tabindex="-1" aria-labelledby="modalConfirmDeleteSATLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmDeleteSATLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta contraseña SAT? Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteSAT" data-requires="write">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-password-sat.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteSAT');
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