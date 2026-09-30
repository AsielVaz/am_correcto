<?php
$pageTitle = 'Iniciar sesión';
include "../../components/main2.php";
?>

<script>
    // Redirigir si ya está logueado
    document.addEventListener('DOMContentLoaded', function() {
        const token = sessionStorage.getItem('token');
        const expire = parseInt(sessionStorage.getItem('expire'), 10);
        if (token && expire && Date.now() / 1000 < expire) {
            window.location.href = '<?php echo url("/pages/business/manage.php"); ?>';
        }
    });
</script>

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
                                        <img src="../../assets/images/logos/Logo-Codigo-Chips.png" style="height: 100px;" alt="logo dark">
                                    </a>

                                    <a href="<?php echo url('/'); ?>" class="logo-light">
                                        <img src="../../assets/images/logos/Logo-Codigo-Chips-Invertido.png" style="height: 100px;" alt="logo light">
                                    </a>
                                </div>
                                <h4 class="fw-bold text-dark mb-2">Bienvenido!</h4>
                                <p class="text-muted">Inicia sesión en tu cuenta para continuar</p>
                            </div>
                            <div id="loginError" class="alert alert-danger d-none" role="alert"></div>
                            <form id="loginForm" class="mt-4" autocomplete="off">
                                <div class="mb-3">
                                    <label for="user" class="form-label">Usuario</label>
                                    <input type="text" class="form-control" id="user" placeholder="Ingresa tu usuario" required>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label for="password" class="form-label">Contraseña</label>
                                    </div>
                                    <input type="password" class="form-control" id="password" placeholder="Ingresa tu contraseña" required>
                                </div>
                                <div class="d-grid">
                                    <button class="btn btn-dark btn-lg fw-medium" type="submit">Iniciar Sesión</button>
                                </div>
                            </form>
                        </div> <!-- End Container Fluid -->

                        <script>
                            // Manejo de login con fetch
                            const loginForm = document.getElementById('loginForm');
                            const loginError = document.getElementById('loginError');
                            loginForm.addEventListener('submit', async function(e) {
                                e.preventDefault();
                                loginError.classList.add('d-none');
                                const username = document.getElementById('user').value.trim();
                                const password = document.getElementById('password').value;
                                try {
                                    const formData = new FormData();
                                    formData.append('accion', 'inicio');
                                    formData.append('email', username);
                                    formData.append('pass', password);
                                    const response = await fetch('../../api/routes/apiUsuarios.php', {
                                        method: 'POST',
                                        body: formData
                                    });
                                    const data = await response.json();
                                    if (data && data.token) {
                                        // Guardar token y expiración en localStorage
                                        sessionStorage.setItem('token', data.token);
                                        sessionStorage.setItem('expire', data.expire);
                                        if (data.rol) {
                                            sessionStorage.setItem('role', data.rol);
                                            if (typeof window.refreshAppPermissions === 'function') {
                                                window.refreshAppPermissions(data.rol);
                                            }
                                        }
                                        // Puedes guardar otros datos si lo necesitas
                                        window.location.href = '<?php echo url("/pages/business/manage.php"); ?>';
                                    } else if (data && data.error) {
                                        loginError.textContent = data.error;
                                        loginError.classList.remove('d-none');
                                    } else {
                                        loginError.textContent = 'Usuario o contraseña incorrectos';
                                        loginError.classList.remove('d-none');
                                    }
                                } catch (err) {
                                    loginError.textContent = 'Error de conexión con el servidor';
                                    loginError.classList.remove('d-none');
                                }
                            });
                        </script>
                        <div class="text-center mt-2 mb-2">
                            <small class="text-muted">&copy; <?php echo date('Y'); ?> EDWorld TCS - Todos los derechos reservados.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div> <!-- End Container Fluid -->
