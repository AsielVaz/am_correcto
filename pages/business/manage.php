<?php
$pageTitle = 'Administrar empresas';
include '../../components/main.php';
?>

<!-- ==================================================== -->
<!-- Start right Content here -->
<!-- ==================================================== -->
<link rel="stylesheet" href="../business/styles/manage.css">

<div class="container-fluid pt-4 px-4">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="card-title mb-0">Simbología</h4>
                        <button type="button" class="btn btn-outline-info btn-sm" id="estados-info-btn" data-bs-toggle="popover" data-bs-placement="bottom" data-bs-trigger="hover focus" data-bs-html="true">
                            <i class="bx bx-info-circle"></i>
                        </button>
                    </div>
                    <form class="d-flex" id="search-form" onsubmit="return false;">
                        <input type="search" class="form-control form-control-md me-2 rounded-pill" id="search-input" placeholder="Buscar empresa..." style="max-width:320px;">
                        <button class="btn btn-primary btn-sm rounded-pill d-flex align-items-center gap-1" type="submit">
                            <i class="bx bx-search"></i>
                            Buscar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Incluir tabla de empresas -->
            <?php include 'components/empresas-table.php'; ?>

            <!-- Incluir paginación -->
            <?php include 'components/pagination.php'; ?>
        </div>
    </div>
</div>

<!-- Incluir todos los modales -->
<?php include 'modals/modal-proof-address.php'; ?>
<?php include 'modals/modal-constancy.php'; ?>
<?php include 'modals/modal-32d.php'; ?>
<?php include 'modals/modal-web.php'; ?>
<?php include 'modals/modal-accounts.php'; ?>
<?php include 'modals/modal-phone.php'; ?>
<?php include 'modals/modal-email.php'; ?>
<?php include 'modals/modal-end-domain.php'; ?>
<?php include 'modals/modal-password-sat.php'; ?>
<?php include 'modals/modal-stamps-sat.php'; ?>
<?php include 'modals/modal-fiel.php'; ?>
<?php include 'modals/modal-password-bank.php'; ?>
<?php include 'modals/modal-permanent-files.php'; ?>
<?php include 'modals/modal-states-account.php'; ?>
<?php include 'modals/modal-banks-cover.php'; ?>
<?php include 'modals/modal-imss.php'; ?>
<?php include 'modals/iofacturo.php'; ?>


<!-- Plantilla popover estados -->
<div id="estados-info-template" class="d-none">
    <div class="d-flex flex-column gap-2" style="min-width:220px;">
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-check-circle text-success fs-5"></i>
            <span class="small">Completado: toda la información está actualizada y verificada.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-search-alt text-info fs-5"></i>
            <span class="small">Revisión: el documento recién cargado se encuentra en espera de validación.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-time-five text-warning fs-5"></i>
            <span class="small">Archivo faltante: el documento reportado no se encuentra disponible o requiere complementarlo.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-error text-danger fs-5"></i>
            <span class="small">Rechazado: el documento en revisión fue rechazado y requiere reemplazo.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-x-circle text-danger fs-5"></i>
            <span class="small">Sin datos: no se ha registrado información para este campo.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bx bxs-circle pulse-red text-danger fs-5"></i>
            <span class="small">Urgente: requiere atención inmediata.</span>
        </div>
    </div>
</div>

<!-- JavaScript principal -->
<script src="scripts/manage.js"></script>

<?php include '../../components/footer.php'; ?>