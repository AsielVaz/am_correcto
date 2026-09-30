<!-- Topbar Start -->
<header class="app-topbar" style="padding-left: 0 !important;">
    <div class="container-fluid">
        <div class="navbar-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100">
                <a class="d-flex align-items-center gap-2 text-decoration-none topbar-brand ms-sm-2 ms-lg-3 gap-3" href="<?php echo pagesUrl('business/manage.php'); ?>">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 40px; height: 40px;">
                        <img src="<?php echo assetsUrl('images/logos/Logo-Codigo-Chips.png'); ?>" alt="Logo light" class="img-fluid p-1 rounded logo-light" style="max-width: 64px; display: none;">
                        <img src="<?php echo assetsUrl('images/logos/Logo-Codigo-Chips-Invertido.png'); ?>" alt="Logo dark" class="img-fluid p-1 rounded logo-dark" style="max-width: 64px; display: none;">
                    </span>
                    <span class="d-flex flex-column lh-1">
                        <span class="fw-semibold text-dark">Código & Chips</span>
                        <small class="text-muted"><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : ''; ?></small>
                    </span>
                </a>

                <div class="d-flex align-items-center justify-content-end gap-2">
                    <!-- Theme Color (Light/Dark) -->
                    <div class="topbar-item">
                        <button type="button" class="topbar-button rounded-pill px-3 py-2 shadow-sm" id="light-dark-mode">
                            <iconify-icon icon="solar:moon-outline" class="fs-20 align-middle light-mode"></iconify-icon>
                            <iconify-icon icon="solar:sun-2-outline" class="fs-20 align-middle dark-mode"></iconify-icon>
                        </button>
                    </div>

                    <!-- User -->
                    <div class="dropdown topbar-item">
                        <a type="button" class="topbar-button d-flex align-items-center rounded-pill px-3 py-2 shadow-sm" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <iconify-icon icon="solar:user-circle-bold" class="fs-20 align-middle me-2"></iconify-icon>
                            <span class="fw-semibold">Cuenta</span>
                            <iconify-icon icon="solar:alt-arrow-down-linear" class="fs-16 ms-1"></iconify-icon>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow rounded-3 border-0 py-2">
                            <!-- item-->
                            <h6 class="dropdown-header text-secondary">
                                <span class="fw-semibold">Bienvenido</span>
                                <small class="d-block text-muted mt-1 d-none" id="topbar-user-email"></small>
                                <small class="d-block text-muted mt-1 d-none" id="topbar-user-departamento"></small>
                            </h6>
                            <a class="dropdown-item d-flex align-items-center gap-2" href="<?php echo pagesUrl('business/manage.php'); ?>">
                                <iconify-icon icon="solar:buildings-2-outline" class="fs-18 text-primary"></iconify-icon>
                                <span>Administrar empresas</span>
                            </a>
                            <a class="dropdown-item d-flex align-items-center gap-2 d-none" id="topbar-manage-users-link" href="<?php echo pagesUrl('users/manage.php'); ?>" data-requires="admin" aria-hidden="true" tabindex="-1">
                                <iconify-icon icon="solar:users-group-rounded-outline" class="fs-18 text-primary"></iconify-icon>
                                <span>Administrar usuarios</span>
                            </a>
                            <div class="dropdown-divider my-2"></div>

                            <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="<?php echo pagesUrl('auth/logout.php'); ?>">
                                <iconify-icon icon="solar:logout-3-outline" class="fs-18"></iconify-icon>
                                <span>Cerrar sesión</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Topbar End -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logo dark/light
        function updateLogoTheme() {
            // Detectar el tema usando el atributo data-bs-theme en <html>
            const html = document.documentElement;
            const theme = html.getAttribute('data-bs-theme');
            const logoLight = document.querySelector('.logo-light');
            const logoDark = document.querySelector('.logo-dark');
            if (logoLight && logoDark) {
                if (theme === 'dark') {
                    logoLight.style.display = 'none';
                    logoDark.style.display = '';
                } else {
                    logoLight.style.display = '';
                    logoDark.style.display = 'none';
                }
            }
        }
        updateLogoTheme();
        // Escuchar cambios de tema si tienes un sistema de toggle
        document.addEventListener('theme:changed', updateLogoTheme);
        // --- resto del script original ---
        const manageUsersLink = document.getElementById('topbar-manage-users-link');
        const firstNameEl = document.getElementById('topbar-user-firstname');
        const emailEl = document.getElementById('topbar-user-email');
        const departamentoEl = document.getElementById('topbar-user-departamento');
        if (!manageUsersLink) {
            return;
        }
        const applyManageUsersVisibility = () => {
            const isAdmin = !!(window.appPermissions && window.appPermissions.isAdmin);
            manageUsersLink.classList.toggle('d-none', !isAdmin);
            if (isAdmin) {
                manageUsersLink.removeAttribute('aria-hidden');
                manageUsersLink.removeAttribute('tabindex');
            } else {
                manageUsersLink.setAttribute('aria-hidden', 'true');
                manageUsersLink.setAttribute('tabindex', '-1');
            }
        };
        applyManageUsersVisibility();
        window.addEventListener('permissions:updated', applyManageUsersVisibility);

        // Poblar saludo con primer nombre y correo desde el token JWT del almacenamiento local
        const decodeJwtPayload = (token) => {
            if (!token || typeof token !== 'string') return null;
            const parts = token.split('.');
            if (parts.length !== 3) return null;
            try {
                let payload = parts[1].replace(/-/g, '+').replace(/_/g, '/');
                while (payload.length % 4 !== 0) payload += '=';
                const json = atob(payload);
                return JSON.parse(json);
            } catch (e) {
                return null;
            }
        };

        const getFirstNameFromEmail = (email) => {
            if (!email || typeof email !== 'string') return '';
            const local = email.split('@')[0] || '';
            if (!local) return '';
            // Separar por . _ - o espacios y tomar la primera parte
            const raw = (local.split(/[._\-\s]+/)[0] || local).trim();
            if (!raw) return '';
            return raw.charAt(0).toUpperCase() + raw.slice(1).toLowerCase();
        };

        try {
            const token = localStorage.getItem('token');
            const payload = decodeJwtPayload(token) || {};
            const possibleId = payload && (payload.id ?? payload.userId ?? payload.user_id ?? payload.usuarioId ?? payload.usuario_id ?? null);
            let resolvedUserId = null;
            if (typeof possibleId === 'number' && Number.isFinite(possibleId)) {
                resolvedUserId = possibleId;
            } else if (typeof possibleId === 'string' && possibleId.trim() !== '') {
                const parsedId = parseInt(possibleId.trim(), 10);
                if (!Number.isNaN(parsedId)) {
                    resolvedUserId = parsedId;
                }
            }
            if (resolvedUserId !== null) {
                window.appCurrentUserId = resolvedUserId;
            } else if (typeof window.appCurrentUserId === 'undefined') {
                window.appCurrentUserId = null;
            }
            const email = typeof payload.email === 'string' ? payload.email : '';
            const firstName = getFirstNameFromEmail(email);
            const departamento = typeof payload.departamento === 'string' && payload.departamento.trim() !== '' ?
                payload.departamento :
                'Sin departamento';

            if (firstNameEl && firstName) {
                firstNameEl.textContent = ', ' + firstName;
            }
            if (emailEl && email) {
                emailEl.textContent = email;
                emailEl.classList.remove('d-none');
            }
            if (departamentoEl) {
                departamentoEl.textContent = departamento;
                departamentoEl.classList.remove('d-none');
            }
        } catch (err) {
            // Silencioso: si no hay token válido, dejamos solo "Bienvenido"
        } finally {
            if (typeof window.appCurrentUserId === 'undefined') {
                window.appCurrentUserId = null;
            }
        }
    });
</script>