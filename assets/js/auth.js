// Verifica el token JWT y protege las páginas del frontend
(function () {
	// Páginas que no requieren autenticación
	const appBase = document.querySelector('meta[name="app-base"]')?.content || '/am/new';
	const publicPages = [appBase, appBase + '/', appBase + '/index.php', appBase + '/pages/auth/login.php', appBase + '/pages/auth/logout.php'];
	const currentPath = window.location.pathname.toLowerCase();
	if (publicPages.some((p) => currentPath === p.toLowerCase())) return;

	const session = window.sessionStorage;
	if (!session.getItem('token') && localStorage.getItem('token')) {
		['token', 'expire', 'role', 'departamento'].forEach((key) => {
			const value = localStorage.getItem(key);
			if (value !== null) session.setItem(key, value);
			localStorage.removeItem(key);
		});
	}

	function normalizeRole(role) {
		if (!role) return 'Usuario';
		const value = role.toString().trim().toLowerCase();
		if (value === 'capturista') return 'Capturista';
		if (value === 'admin' || value === 'administrador') return 'Admin';
		return 'Usuario';
	}

	function buildPermissions(role, departamento) {
		const normalizedRole = normalizeRole(role);
		return {
			role: normalizedRole,
			canWrite: normalizedRole === 'Capturista' || normalizedRole === 'Admin',
			isAdmin: normalizedRole === 'Admin',
			departamento: departamento || '',
		};
	}

	function setPermissions(role, departamento) {
		const permissions = buildPermissions(role, departamento);
		window.appPermissions = permissions;
		session.setItem('role', permissions.role);
		session.setItem('departamento', permissions.departamento);
		window.dispatchEvent(new CustomEvent('permissions:updated', { detail: permissions }));
	}

	function clearSessionAndRedirect() {
		session.clear();
		localStorage.removeItem('token');
		localStorage.removeItem('expire');
		localStorage.removeItem('role');
		localStorage.removeItem('departamento');
		const permissions = buildPermissions(null, null);
		window.appPermissions = permissions;
		window.dispatchEvent(new CustomEvent('permissions:updated', { detail: permissions }));
		window.location.href = appBase + '/pages/auth/login.php';
	}

	function extractRoleAndDepartamentoFromToken(token) {
		if (!token) return { rol: null, departamento: null };
		const parts = token.split('.');
		if (parts.length !== 3) return { rol: null, departamento: null };
		try {
			let payload = parts[1].replace(/-/g, '+').replace(/_/g, '/');
			while (payload.length % 4 !== 0) {
				payload += '=';
			}
			const decoded = atob(payload);
			const data = JSON.parse(decoded);
			return {
				rol: data && typeof data.rol === 'string' ? data.rol : null,
				departamento: data && typeof data.departamento === 'string' ? data.departamento : null,
			};
		} catch (error) {
			return { rol: null, departamento: null };
		}
	}

	const token = session.getItem('token');
	const expire = parseInt(session.getItem('expire'), 10);
	if (!token || !expire || Date.now() / 1000 > expire) {
		clearSessionAndRedirect();
		return;
	}

	const storedRole = session.getItem('role');
	const storedDepartamento = session.getItem('departamento');
	const { rol: tokenRole, departamento: tokenDepartamento } = extractRoleAndDepartamentoFromToken(token);
	const derivedRole = tokenRole || storedRole;
	const derivedDepartamento = tokenDepartamento || storedDepartamento;
	setPermissions(derivedRole, derivedDepartamento);
	window.refreshAppPermissions = setPermissions;

	// Interceptar fetch para agregar el header Authorization solo a la API propia
	const originalFetch = window.fetch;
	window.fetch = function (input, init = {}) {
		let url = input;
		if (typeof input === 'object' && input.url) url = input.url;
		const isApiRequest =
			typeof url === 'string' && (url.includes('/api/') || url.startsWith(window.location.origin + '/api/'));
		// Solo agregar el header si es una petición a la API local
		if (isApiRequest) {
			init.headers = init.headers || {};
			if (typeof init.headers.append === 'function') {
				init.headers.append('Authorization', 'Bearer ' + token);
			} else {
				init.headers['Authorization'] = 'Bearer ' + token;
			}
			const method = (init.method || (typeof input === 'object' && input.method) || 'GET')
				.toString()
				.toUpperCase();
			if (method !== 'GET' && !(window.appPermissions && window.appPermissions.canWrite)) {
				alert(
					'Tu rol actual no permite realizar cambios. Contacta a un administrador si necesitas más permisos.'
				);
				return Promise.reject(new Error('Permisos insuficientes'));
			}
		}
		return originalFetch(input, init).then((response) => {
			if (response.status === 401) {
				clearSessionAndRedirect();
				return Promise.reject(response);
			}
			return response;
		});
	};
})();
