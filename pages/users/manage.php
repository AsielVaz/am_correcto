<?php
$pageTitle = "Administrar Usuarios";
include '../../components/main.php';
?>

<!-- ==================================================== -->
<!-- Start right Content here -->
<!-- ==================================================== -->
<link rel="stylesheet" href="../users/styles/manage.css">

<div class="container-fluid px-4 pt-4">
    <!-- ========== Page Title End ========== -->
    <div class="row">
        <div class="card" data-requires="admin">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">Administrar usuarios</h4>
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <form class="d-flex" id="search-form" onsubmit="return false;">
                            <input type="search" class="form-control form-control-md rounded-pill me-2" id="search-input" placeholder="Buscar..." style="max-width:320px;">
                            <button class="btn btn-primary btn-sm rounded-pill d-flex align-items-center gap-1" type="submit">
                                <i class="bx bx-search"></i>
                                Buscar
                            </button>
                        </form>
                        <button type="button" class="btn btn-success btn-md rounded-pill" id="btnNuevoUsuario" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
                            <i class="bx bx-plus me-1"></i> Nuevo usuario
                        </button>
                    </div>
                </div>
            </div>
            <div class="users-table-wrapper">
                <div id="users-action-alert" class="alert alert-success d-none" role="alert"></div>
                <div class="table-responsive table-centered">
                    <table class="principal table table-hover align-middle mb-0" id="accounts-table" style="position: relative; table-layout: fixed; width: 100%;">
                        <thead class="users-table-head">
                            <tr>
                                <th class="text-center" style="width: 12%;"><span class="heading-label">Nombre</span></th>
                                <th class="text-center" style="width: 12%;"><span class="heading-label">Email</span></th>
                                <th class="text-center" style="width: 12%;"><span class="heading-label">Rol</span></th>
                                <th class="text-center" style="width: 12%;"><span class="heading-label">Departamento</span></th>
                                <th class="text-center" style="width: 8%;"><span class="heading-label">Estado</span></th>
                                <th class="text-center" style="width: 12%;"><span class="heading-label">Acciones</span></th>
                            </tr>
                        </thead>
                        <tbody id="accounts-tbody">
                            <tr>
                                <td colspan="7" class="text-center loading-text">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="align-items-center justify-content-between row g-0 text-center text-sm-start p-3">
                <div class="col-sm">
                    <div class="text-muted" id="accounts-info">
                        Mostrando <span class="fw-semibold">0</span> de <span class="fw-semibold">0</span> resultados
                    </div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    <ul class="pagination pagination-rounded m-0" id="accounts-pagination"></ul>
                </div>
            </div>
            <div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true" data-requires="admin">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formNuevoUsuario" autocomplete="off" data-requires="admin" data-permission-mode="disable">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalNuevoUsuarioLabel"><i class="bx bx-user-plus me-2"></i>Nuevo usuario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div id="alertNuevoUsuario" class="alert d-none" role="alert"></div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="nuevoUsuarioNombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nuevoUsuarioNombre" name="nombre" required maxlength="80">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nuevoUsuarioEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="nuevoUsuarioEmail" name="email" required maxlength="120">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nuevoUsuarioRol" class="form-label">Rol</label>
                                        <select class="form-select" id="nuevoUsuarioRol" name="rol" required>
                                            <option value="Usuario" selected>Usuario</option>
                                            <option value="Capturista">Capturista</option>
                                            <option value="Admin">Administrador</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nuevoUsuarioDepartamento" class="form-label">Departamento</label>
                                        <select class="form-select" id="nuevoUsuarioDepartamento" name="departamento">
                                            <option value="Sin departamento" selected>Sin departamento</option>
                                            <option value="Bancario">Bancario</option>
                                            <option value="Contabilidad">Contabilidad</option>
                                            <option value="Contabilidad auxiliar">Contabilidad auxiliar</option>
                                            <option value="Facturación">Facturación</option>
                                            <option value="Documentos">Documentos</option>
                                            <option value="Dev">Dev</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nuevoUsuarioPass" class="form-label">Contraseña</label>
                                        <input type="password" class="form-control" id="nuevoUsuarioPass" name="pass" required minlength="6">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="btnGuardarNuevoUsuario">
                                    <i class="bx bx-save me-1"></i> Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true" data-requires="admin">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formEditarUsuario" autocomplete="off" data-requires="admin" data-permission-mode="disable">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalEditarUsuarioLabel"><i class="bx bx-edit-alt me-2"></i>Editar usuario</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div id="alertEditarUsuario" class="alert d-none" role="alert"></div>
                                <input type="hidden" name="id" value="">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label for="editarUsuarioNombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="editarUsuarioNombre" name="nombre" required maxlength="80">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editarUsuarioEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="editarUsuarioEmail" name="email" required maxlength="120">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editarUsuarioRol" class="form-label">Rol</label>
                                        <select class="form-select" id="editarUsuarioRol" name="rol" required>
                                            <option value="Usuario">Usuario</option>
                                            <option value="Capturista">Capturista</option>
                                            <option value="Admin">Administrador</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editarUsuarioDepartamento" class="form-label">Departamento</label>
                                        <select class="form-select" id="editarUsuarioDepartamento" name="departamento">
                                            <option value="Sin departamento">Sin departamento</option>
                                            <option value="Bancario">Bancario</option>
                                            <option value="Contabilidad">Contabilidad</option>
                                            <option value="Contabilidad auxiliar">Contabilidad auxiliar</option>
                                            <option value="Facturación">Facturación</option>
                                            <option value="Documentos">Documentos</option>
                                            <option value="Dev">Dev</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="editarUsuarioActivo" class="form-label">Estado</label>
                                        <select class="form-select" id="editarUsuarioActivo" name="activo">
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="btnGuardarEditarUsuario">
                                    <i class="bx bx-save me-1"></i> Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="modalCambiarContrasena" tabindex="-1" aria-labelledby="modalCambiarContrasenaLabel" aria-hidden="true" data-requires="admin">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formCambiarContrasena" autocomplete="off" data-requires="admin" data-permission-mode="disable">
                            <div class="modal-header">
                                <div>
                                    <h5 class="modal-title" id="modalCambiarContrasenaLabel"><i class="bx bx-key me-2"></i>Cambiar contraseña</h5>
                                    <p class="password-modal-subtitle mb-0" id="cambiarContrasenaUsuario"></p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div id="alertCambiarContrasena" class="alert d-none" role="alert"></div>
                                <input type="hidden" name="id" value="">
                                <div class="mb-3">
                                    <label for="nuevaContrasena" class="form-label">Nueva contraseña</label>
                                    <div class="input-group password-input-group">
                                        <input type="password" class="form-control" id="nuevaContrasena" name="pass" required minlength="6" maxlength="255" autocomplete="new-password" aria-describedby="passwordHelp">
                                        <button type="button" class="btn btn-outline-secondary password-toggle" data-password-target="nuevaContrasena" aria-label="Mostrar nueva contraseña" aria-pressed="false">
                                            <i class="bx bx-show" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <div id="passwordHelp" class="form-text">Usa al menos 6 caracteres.</div>
                                </div>
                                <div>
                                    <label for="confirmarContrasena" class="form-label">Confirmar contraseña</label>
                                    <div class="input-group password-input-group">
                                        <input type="password" class="form-control" id="confirmarContrasena" name="pass_confirmation" required minlength="6" maxlength="255" autocomplete="new-password">
                                        <button type="button" class="btn btn-outline-secondary password-toggle" data-password-target="confirmarContrasena" aria-label="Mostrar confirmación de contraseña" aria-pressed="false">
                                            <i class="bx bx-show" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary" id="btnGuardarContrasena">
                                    <i class="bx bx-check me-1"></i> Actualizar contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-warning mt-3 d-none" id="admin-access-warning">
            No tienes permisos para administrar usuarios. Contacta a un administrador si consideras que esto es un error.
        </div>
    </div>
</div>

<script src="scripts/manage.js"></script>

<?php include '../../components/footer.php'; ?>
