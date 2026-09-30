<?php

// Destruir sesión y eliminar cookies
session_start();
session_unset();
session_destroy();

// Elimina cookies de sesión personalizadas si existen
setcookie("id", "", time() - 3600, "/");
setcookie("permiso", "", time() - 3600, "/");
setcookie("accesos", "", time() - 3600, "/");

// Elimina el token JWT del localStorage (frontend)
echo '<script>sessionStorage.clear(); localStorage.removeItem("token"); localStorage.removeItem("expire"); localStorage.removeItem("role"); localStorage.removeItem("departamento");</script>';

$pageTitle = 'Cerrar sesión';
include '../../components/main2.php';
?>

<!-- ==================================================== -->
<!-- Start right Content here -->
<!-- ==================================================== -->


<!-- Start Container Fluid -->
<div class="container-fluid">
    <div class="account-pages py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-5">
                            <div class="text-center">
                                <div class="mx-auto mb-4 text-center auth-logo">
                                    <a href="<?php echo url('/'); ?>" class="logo-dark">
                                        <img src="<?php echo assetsUrl('images/logos/Logo-Codigo-Chips.png'); ?>" style="height: 100px;" alt="logo dark">
                                    </a>

                                    <a href="<?php echo url('/'); ?>" class="logo-light">
                                        <img src="<?php echo assetsUrl('images/logos/Logo-Codigo-Chips-Invertido.png'); ?>" style="height: 100px;" alt=" logo light">
                                    </a>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Hasta luego!</h4>
                                <p class="text-muted">Cerraste sesión exitosamente.</p>
                            </div>
                            <div class="mt-4">
                                <a href="<?php echo url('/'); ?>" class="btn btn-success w-100">Iniciar sesión nuevamente</a>
                            </div>
                            <div class="text-center mt-4 mb-2">
                                <small class="text-muted">&copy; <?php echo date('Y'); ?> EDWorld TCS - Todos los derechos reservados.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> <!-- End Container Fluid -->
