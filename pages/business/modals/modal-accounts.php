<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<style>
    .modal-blur {
        filter: blur(2px) brightness(0.7);
        pointer-events: none;
        transition: filter 0.2s;
    }
</style>
<!-- Modal Cuentas -->
<div class="modal fade" id="modalCuentas" tabindex="-1" aria-labelledby="modalCuentasLabel">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCuentasLabel">
                    <i class="bx bx-credit-card me-2"></i>
                    Cuentas bancarias
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive table-centered">
                    <table class="table mb-0 table-bordered align-middle" id="tabla-cuentas-bancarias">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="border-0 py-2 px-2">Banco</th>
                                <th class="border-0 py-2 px-2">Número de cuenta</th>
                                <th class="border-0 py-2 px-2">CLABE interbancaria</th>
                                <th class="border-0 py-2 px-2">Moneda</th>
                                <th class="border-0 py-2 px-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-cuentas-bancarias">
                            <!-- Las cuentas se renderizan dinámicamente -->
                        </tbody>
                    </table>
                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-primary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnNuevaCuenta" data-requires="write">
                            <i class="bx bx-plus"></i>
                            <span>Nueva cuenta bancaria</span>
                        </button>
                    </div>

                    <!-- Formulario para nueva cuenta -->
                    <form id="formNuevaCuenta" style="display:none;margin-top:20px; overflow-x:hidden" autocomplete="off" data-requires="write" data-permission-mode="disable">
                        <div class="modal-form-container">
                            <h6 class="mb-3">Agregar nueva cuenta bancaria</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="nuevaCuentaBanco" class="form-label">Banco</label>
                                    <input type="text" class="form-control" name="banco" id="nuevaCuentaBanco" placeholder="Banco" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="nuevaCuentaNumero" class="form-label">Número de cuenta</label>
                                    <input type="text" class="form-control" name="numero" id="nuevaCuentaNumero" placeholder="Número de cuenta" required>
                                </div>
                            </div>
                            <div class="row g-2 mt-2">
                                <div class="col-md-6">
                                    <label for="nuevaCuentaClave" class="form-label">CLABE interbancaria</label>
                                    <input type="text"
                                        class="form-control"
                                        name="clave"
                                        id="nuevaCuentaClave"
                                        placeholder="CLABE interbancaria"
                                        required
                                        inputmode="numeric"
                                        pattern="^\d{1,18}$"
                                        title="Solo números, máximo 18 dígitos"
                                        oninput="this.value = this.value.replace(/\D/g, '').slice(0,18);">
                                </div>
                                <div class="col-md-6">
                                    <label for="nuevaCuentaMoneda" class="form-label">Moneda</label>
                                    <select class="form-select" name="moneda" id="nuevaCuentaMoneda" required>
                                        <option value="MXN">MXN - Peso Mexicano</option>
                                        <option value="USD">USD - Dólar Estadounidense</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="GBP">GBP - Libra Esterlina</option>
                                        <option value="JPY">JPY - Yen Japonés</option>
                                        <option value="CAD">CAD - Dólar Canadiense</option>
                                        <option value="AUD">AUD - Dólar Australiano</option>
                                        <option value="CHF">CHF - Franco Suizo</option>
                                        <option value="CNY">CNY - Yuan Chino</option>
                                        <option value="BRL">BRL - Real Brasileño</option>
                                        <option value="ARS">ARS - Peso Argentino</option>
                                        <option value="CLP">CLP - Peso Chileno</option>
                                        <option value="PEN">PEN - Sol Peruano</option>
                                        <option value="COP">COP - Peso Colombiano</option>
                                        <option value="VES">VES - Bolívar Soberano</option>
                                        <option value="UYU">UYU - Peso Uruguayo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-success rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium">
                                        <i class="bx bx-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                    <button type="button" class="btn btn-secondary ms-2 rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center gap-2 fw-medium" id="btnCancelarCuenta">
                                        <i class="bx bx-x"></i>
                                        <span>Cancelar</span>
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="idEmpresa" id="inputIdEmpresaCuenta">
                            <div id="alertNuevaCuenta" class="alert alert-success mt-2" style="display:none;"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/modal-accounts.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteCuenta');
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var confirmModal = document.getElementById('modalConfirmDeleteCuenta');
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