let pagePermissions = window.appPermissions || {
	role: 'Usuario',
	canWrite: false,
	isAdmin: false,
};
let adminPageInitialized = false;
const detectedAppBase = window.location.pathname.includes('/pages/')
	? window.location.pathname.slice(0, window.location.pathname.indexOf('/pages/'))
	: '/am-cr';
const fallbackManageUrl =
	(document.querySelector('meta[name="app-base"]')?.content || detectedAppBase) + '/pages/business/manage.php';
let currentUsers = [];
let currentPage = 1;
let editHandlersBound = false;

function hasAdminAccess() {
	return !!(pagePermissions && pagePermissions.isAdmin);
}

function showAdminWarning() {
	const warning = document.getElementById('admin-access-warning');
	if (warning) {
		warning.classList.remove('d-none');
	}
}

function hideAdminWarning() {
	const warning = document.getElementById('admin-access-warning');
	if (warning) {
		warning.classList.add('d-none');
	}
}

function ensureAdminInitialized() {
	if (adminPageInitialized || !hasAdminAccess()) {
		return;
	}
	adminPageInitialized = true;
	initializeAdminPage();
}

function getSortableName(user) {
	if (!user) return '';
	return [user.nombre, user.apellidoPaterno, user.apellidoMaterno]
		.map((part) => (part || '').trim())
		.filter(Boolean)
		.join(' ');
}

function renderUsersTable(data) {
	const tbody = document.getElementById('accounts-tbody');
	tbody.innerHTML = '';
	const list = Array.isArray(data) ? data.slice() : [];
	list.sort((a, b) => {
		const nameA = getSortableName(a);
		const nameB = getSortableName(b);
		if (!nameA && !nameB) return 0;
		if (!nameA) return 1;
		if (!nameB) return -1;
		return nameA.localeCompare(nameB, 'es', {
			sensitivity: 'base',
		});
	});
	currentUsers = list;
	if (!list.length) {
		tbody.innerHTML = '<tr><td colspan="7" class="text-center">Sin resultados</td></tr>';
		return;
	}
	list.forEach((row) => {
		const tr = document.createElement('tr');
		tr.innerHTML = `
			   <td class='py-2 px-2'>${row.nombre || ''}</td>
			   <td class='py-2 px-2'>${row.email || ''}</td>
			   <td class='py-2 px-2 text-center'>${row.rol || row.tipoUsuario || ''}</td>
			   <td class='py-2 px-2 text-center'>${row.departamento ? row.departamento : 'Sin departamento'}</td>
			   <td class='py-2 px-2 text-center'>
				   <span class="user-status-circle" style="display:inline-block;width:16px;height:16px;border-radius:50%;background:${
						row.activo === false || row.activo === 0 || row.activo === '0' ? '#e74c3c' : '#27ae60'
					};"></span>
			   </td>
			   <td class='py-2 px-2 text-center'>
				   <div class="user-actions d-inline-flex align-items-center justify-content-center gap-1">
					   <button type="button" class="btn btn-outline-primary btn-sm action-icon-btn btn-editar-usuario" data-user-id='${
							row.id
						}' aria-label="Editar usuario" title="Editar usuario">
						   <i class="bx bx-edit-alt" aria-hidden="true"></i>
					   </button>
					   <button type="button" class="btn btn-outline-secondary btn-sm action-icon-btn btn-cambiar-contrasena" data-user-id='${
							row.id
						}' aria-label="Cambiar contraseña" title="Cambiar contraseña">
						   <i class="bx bx-key" aria-hidden="true"></i>
					   </button>
				   </div>
			   </td>
		   `;
		tbody.appendChild(tr);
	});
}

function renderPagination(page, pages) {
	const pag = document.getElementById('accounts-pagination');
	pag.innerHTML = '';
	if (pages <= 1) return;
	pag.appendChild(createPageItem(1, page > 1, '<i class="bx bx-chevrons-left"></i>', false, 'Primera página'));
	pag.appendChild(createPageItem(page - 1, page > 1, '<i class="bx bx-left-arrow-alt"></i>', false, 'Anterior'));
	let start = Math.max(1, page - 2);
	let end = Math.min(pages, page + 2);
	if (page <= 3) {
		end = Math.min(5, pages);
	} else if (page >= pages - 2) {
		start = Math.max(1, pages - 4);
	}
	if (start > 1) {
		pag.appendChild(createPageItem(null, false, '...', false));
	}
	for (let i = start; i <= end; i++) {
		pag.appendChild(createPageItem(i, true, i, i === page));
	}
	if (end < pages) {
		pag.appendChild(createPageItem(null, false, '...', false));
	}
	pag.appendChild(
		createPageItem(page + 1, page < pages, '<i class="bx bx-right-arrow-alt"></i>', false, 'Siguiente')
	);
	pag.appendChild(
		createPageItem(pages, page < pages, '<i class="bx bx-chevrons-right"></i>', false, 'Última página')
	);
}

function createPageItem(page, enabled, label, active, title) {
	const li = document.createElement('li');
	li.className = 'page-item' + (active ? ' active' : '') + (!enabled ? ' disabled' : '');
	const a = document.createElement('a');
	a.className = 'page-link';
	a.href = '#';
	a.innerHTML = label;
	if (title) a.title = title;
	if (enabled && page) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			loadUsers(page);
		});
	}
	li.appendChild(a);
	return li;
}

function renderInfo(page, total, limit) {
	const info = document.getElementById('accounts-info');
	const showing = Math.min(total, page * limit) - (page - 1) * limit;
	info.innerHTML = `Mostrando <span class="fw-semibold">${showing}</span> de <span class="fw-semibold">${total}</span> resultados`;
}

async function loadUsers(page = 1) {
	const search = document.getElementById('search-input').value.trim();
	const limit = 100;
	let url = `../../api/routes/apiUsuariosListado.php?page=${page}&limit=${limit}`;
	if (search) url += `&search=${encodeURIComponent(search)}`;
	const tbody = document.getElementById('accounts-tbody');
	tbody.innerHTML = '<tr><td colspan="7" class="text-center">Cargando...</td></tr>';
	try {
		const res = await fetch(url);
		const data = await res.json();
		currentPage = data.page || 1;
		renderUsersTable(data.data);
		renderPagination(data.page, data.pages);
		renderInfo(data.page, data.total, data.limit);
	} catch (e) {
		tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error al cargar datos</td></tr>';
	}
}

document.addEventListener('DOMContentLoaded', function () {
	if (!hasAdminAccess()) {
		window.location.replace(fallbackManageUrl);
		return;
	}
	hideAdminWarning();
	ensureAdminInitialized();
});

window.addEventListener('permissions:updated', function (event) {
	if (event.detail) {
		pagePermissions = event.detail;
	}
	if (!hasAdminAccess()) {
		window.location.replace(fallbackManageUrl);
		return;
	}
	hideAdminWarning();
	ensureAdminInitialized();
});

function initializeAdminPage() {
	loadUsers();
	document.getElementById('search-form').addEventListener('submit', function (e) {
		e.preventDefault();
		const searchInput = document.getElementById('search-input');
		if (searchInput.value.trim() === '') {
			searchInput.value = '';
			loadUsers(1);
		} else {
			loadUsers(1);
		}
	});

	document.getElementById('search-input').addEventListener('input', function () {
		const value = this.value.trim();
		if (value.length === 0) {
			loadUsers(1);
		} else if (value.length >= 3) {
			loadUsers(1);
		} else {
			// Si hay menos de 3 caracteres, limpiar la tabla
			const tbody = document.getElementById('accounts-tbody');
			tbody.innerHTML =
				'<tr><td colspan="7" class="text-center align-middle">Escribe al menos 3 caracteres para buscar</td></tr>';
		}
	});
	bindNewUserModalHandlers();
	bindEditUserHandlers();
	bindPasswordChangeHandlers();
}

let newUserModalEl = null;
let newUserForm = null;
let newUserAlert = null;
let newUserSubmitBtn = null;
let newUserListenersBound = false;
let mainActionAlert = null;

function ensureMainAlert() {
	if (!mainActionAlert) {
		mainActionAlert = document.getElementById('users-action-alert');
	}
	return mainActionAlert;
}

function showMainAlert(message, type = 'success') {
	const alertBox = ensureMainAlert();
	if (!alertBox) return;
	alertBox.textContent = message;
	alertBox.className = `alert alert-${type}`;
	alertBox.classList.remove('d-none');
	setTimeout(() => {
		alertBox.classList.add('d-none');
	}, 4000);
}

function ensureNewUserElements() {
	if (!newUserModalEl) {
		newUserModalEl = document.getElementById('modalNuevoUsuario');
	}
	if (!newUserForm) {
		newUserForm = document.getElementById('formNuevoUsuario');
	}
	if (!newUserAlert) {
		newUserAlert = document.getElementById('alertNuevoUsuario');
	}
	if (!newUserSubmitBtn) {
		newUserSubmitBtn = document.getElementById('btnGuardarNuevoUsuario');
	}
	return newUserModalEl && newUserForm && newUserSubmitBtn;
}

function setNewUserAlert(message, type = 'success') {
	ensureNewUserElements();
	if (!newUserAlert) return;
	if (!message) {
		newUserAlert.classList.add('d-none');
		newUserAlert.textContent = '';
		return;
	}
	newUserAlert.classList.remove('d-none');
	newUserAlert.textContent = message;
	newUserAlert.className = `alert alert-${type}`;
}

function resetNewUserForm() {
	ensureNewUserElements();
	if (!newUserForm) return;
	newUserForm.reset();
	const rolSelect = newUserForm.querySelector('select[name="rol"]');
	if (rolSelect) {
		rolSelect.value = 'Usuario';
	}
	setNewUserAlert('');
	if (newUserSubmitBtn) {
		newUserSubmitBtn.disabled = false;
		newUserSubmitBtn.innerHTML = '<i class="bx bx-save me-1"></i> Guardar';
	}
}

function bindNewUserModalHandlers() {
	if (newUserListenersBound) {
		return;
	}
	if (!ensureNewUserElements()) {
		return;
	}
	newUserListenersBound = true;

	newUserModalEl.addEventListener('show.bs.modal', function () {
		resetNewUserForm();
	});

	newUserModalEl.addEventListener('hidden.bs.modal', function () {
		resetNewUserForm();
	});

	newUserForm.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!hasAdminAccess()) {
			setNewUserAlert('No tienes permisos para realizar esta acción.', 'danger');
			return;
		}

		const requiredFields = ['nombre', 'appat', 'email', 'pass'];
		for (const field of requiredFields) {
			const input = newUserForm.querySelector(`[name="${field}"]`);
			if (input) {
				input.value = input.value.trim();
				if (!input.value) {
					input.focus();
					setNewUserAlert('Completa todos los campos obligatorios.', 'danger');
					return;
				}
			}
		}

		const submitOriginal = newUserSubmitBtn.innerHTML;
		newUserSubmitBtn.disabled = true;
		newUserSubmitBtn.innerHTML =
			'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Guardando...';
		setNewUserAlert('');

		const formData = new FormData(newUserForm);
		formData.append('accion', 'agregar');
		formData.append('empleado', '0');
		// Departamento
		const departamento = newUserForm.querySelector('[name="departamento"]').value;
		if (departamento) formData.append('departamento', departamento);

		const selectedRole = (formData.get('rol') || 'Usuario').trim();
		if (selectedRole === 'Admin') {
			formData.append('RolAdmin', '1');
		} else if (selectedRole === 'Capturista') {
			formData.append('RolCap', '1');
		}

		try {
			const response = await fetch('../../api/routes/apiUsuarios.php', {
				method: 'POST',
				body: formData,
			});

			const text = await response.text();
			const trimmed = text ? text.trim() : '';

			if (!response.ok) {
				throw new Error(trimmed || 'Error en la respuesta del servidor');
			}

			if (trimmed === '1') {
				const modalInstance =
					bootstrap.Modal.getInstance(newUserModalEl) || new bootstrap.Modal(newUserModalEl);
				modalInstance.hide();
				setNewUserAlert('');
				loadUsers(1);
				showMainAlert('Usuario creado correctamente.');
			} else {
				throw new Error(trimmed || 'No se pudo crear el usuario.');
			}
		} catch (error) {
			console.error('Error al crear usuario:', error);
			setNewUserAlert(error.message || 'Error al crear usuario.', 'danger');
		} finally {
			newUserSubmitBtn.disabled = false;
			newUserSubmitBtn.innerHTML = submitOriginal;
		}
	});
}

let editUserModalEl = null;
let editUserForm = null;
let editUserAlert = null;
let editUserSubmitBtn = null;
let editUserListenersBound = false;
let editingUserId = null;

function ensureEditUserElements() {
	if (!editUserModalEl) {
		editUserModalEl = document.getElementById('modalEditarUsuario');
	}
	if (!editUserForm) {
		editUserForm = document.getElementById('formEditarUsuario');
	}
	if (!editUserAlert) {
		editUserAlert = document.getElementById('alertEditarUsuario');
	}
	if (!editUserSubmitBtn) {
		editUserSubmitBtn = document.getElementById('btnGuardarEditarUsuario');
	}
	return editUserModalEl && editUserForm && editUserSubmitBtn;
}

function setEditUserAlert(message, type = 'success') {
	ensureEditUserElements();
	if (!editUserAlert) return;
	if (!message) {
		editUserAlert.classList.add('d-none');
		editUserAlert.textContent = '';
		return;
	}
	editUserAlert.classList.remove('d-none');
	editUserAlert.textContent = message;
	editUserAlert.className = `alert alert-${type}`;
}

function resetEditUserForm() {
	ensureEditUserElements();
	if (!editUserForm) return;
	editUserForm.reset();
	const rolSelect = editUserForm.querySelector('select[name="rol"]');
	if (rolSelect) {
		rolSelect.value = 'Usuario';
	}
	editingUserId = null;
	setEditUserAlert('');
	if (editUserSubmitBtn) {
		editUserSubmitBtn.disabled = false;
		editUserSubmitBtn.innerHTML = '<i class="bx bx-save me-1"></i> Guardar cambios';
	}
}

function populateEditForm(user) {
	ensureEditUserElements();
	if (!editUserForm || !user) return;
	editUserForm.querySelector('[name="id"]').value = user.id || '';
	const nombre = editUserForm.querySelector('[name="nombre"]');
	if (nombre) nombre.value = user.nombre || '';
	const email = editUserForm.querySelector('[name="email"]');
	if (email) email.value = user.email || '';
	const rol = editUserForm.querySelector('[name="rol"]');
	if (rol) rol.value = user.rol || 'Usuario';
	const departamento = editUserForm.querySelector('[name="departamento"]');
	if (departamento) departamento.value = user.departamento || '';
	const activo = editUserForm.querySelector('[name="activo"]');
	if (activo) activo.value = user.activo === false || user.activo === 0 || user.activo === '0' ? '0' : '1';
}

function bindEditUserHandlers() {
	if (editHandlersBound) {
		return;
	}
	const accountsTable = document.getElementById('accounts-table');
	if (!accountsTable || !ensureEditUserElements()) {
		return;
	}
	editHandlersBound = true;

	accountsTable.addEventListener('click', function (e) {
		const btn = e.target.closest('.btn-editar-usuario');
		if (!btn) return;
		const userId = parseInt(btn.getAttribute('data-user-id'), 10);
		if (!userId) return;
		const user = currentUsers.find((u) => parseInt(u.id, 10) === userId);
		if (!user) {
			showMainAlert('No se encontró la información del usuario seleccionado.', 'danger');
			return;
		}
		editingUserId = user.id;
		populateEditForm(user);
		setEditUserAlert('');
		const modalInstance = bootstrap.Modal.getOrCreateInstance(editUserModalEl);
		modalInstance.show();
	});

	if (!editUserListenersBound) {
		editUserListenersBound = true;

		editUserModalEl.addEventListener('hidden.bs.modal', function () {
			resetEditUserForm();
		});

		editUserForm.addEventListener('submit', async function (e) {
			e.preventDefault();
			if (!hasAdminAccess()) {
				setEditUserAlert('No tienes permisos para realizar esta acción.', 'danger');
				return;
			}
			if (!editingUserId) {
				setEditUserAlert('Usuario inválido.', 'danger');
				return;
			}

			const requiredFields = ['nombre', 'appat', 'email'];
			for (const field of requiredFields) {
				const input = editUserForm.querySelector(`[name="${field}"]`);
				if (input) {
					input.value = input.value.trim();
					if (!input.value) {
						input.focus();
						setEditUserAlert('Completa todos los campos obligatorios.', 'danger');
						return;
					}
				}
			}

			const submitOriginal = editUserSubmitBtn.innerHTML;
			editUserSubmitBtn.disabled = true;
			editUserSubmitBtn.innerHTML =
				'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Guardando...';
			setEditUserAlert('');

			const formData = new FormData(editUserForm);
			formData.append('accion', 'actualizar');
			formData.set('id', editingUserId);
			// Departamento
			const departamento = editUserForm.querySelector('[name="departamento"]').value;
			if (departamento) formData.append('departamento', departamento);
			// Estado activo/inactivo
			const activo = editUserForm.querySelector('[name="activo"]').value;
			formData.append('activo', activo);

			const selectedRole = (formData.get('rol') || 'Usuario').trim();
			if (selectedRole === 'Admin') {
				formData.append('RolAdmin', '1');
			} else if (selectedRole === 'Capturista') {
				formData.append('RolCap', '1');
			}

			try {
				const response = await fetch('../../api/routes/apiUsuarios.php', {
					method: 'POST',
					body: formData,
				});
				const text = await response.text();
				const trimmed = text ? text.trim() : '';
				if (!response.ok) {
					throw new Error(trimmed || 'Error en la respuesta del servidor');
				}
				if (trimmed === '1') {
					// Recargar la lista de usuarios desde la API para reflejar los cambios
					const modalInstance = bootstrap.Modal.getInstance(editUserModalEl);
					if (modalInstance) {
						modalInstance.hide();
					}
					setEditUserAlert('');
					showMainAlert('Usuario actualizado correctamente.');
					loadUsers(1);
				} else {
					throw new Error(trimmed || 'No se pudo actualizar el usuario.');
				}
			} catch (error) {
				console.error('Error al actualizar usuario:', error);
				setEditUserAlert(error.message || 'Error al actualizar usuario.', 'danger');
			} finally {
				editUserSubmitBtn.disabled = false;
				editUserSubmitBtn.innerHTML = submitOriginal;
			}
		});
	}
}

let passwordModalEl = null;
let passwordForm = null;
let passwordAlert = null;
let passwordSubmitBtn = null;
let passwordHandlersBound = false;
let passwordUserId = null;

function ensurePasswordElements() {
	passwordModalEl ||= document.getElementById('modalCambiarContrasena');
	passwordForm ||= document.getElementById('formCambiarContrasena');
	passwordAlert ||= document.getElementById('alertCambiarContrasena');
	passwordSubmitBtn ||= document.getElementById('btnGuardarContrasena');
	return passwordModalEl && passwordForm && passwordSubmitBtn;
}

function setPasswordAlert(message, type = 'danger') {
	if (!ensurePasswordElements() || !passwordAlert) return;
	passwordAlert.textContent = message || '';
	passwordAlert.className = message ? `alert alert-${type}` : 'alert d-none';
}

function resetPasswordForm() {
	if (!ensurePasswordElements()) return;
	passwordForm.reset();
	passwordUserId = null;
	passwordForm.querySelector('[name="id"]').value = '';
	document.getElementById('cambiarContrasenaUsuario').textContent = '';
	setPasswordAlert('');
	passwordForm.querySelectorAll('.password-toggle').forEach((button) => {
		const input = document.getElementById(button.dataset.passwordTarget);
		if (input) input.type = 'password';
		button.setAttribute('aria-pressed', 'false');
		button.querySelector('i')?.classList.replace('bx-hide', 'bx-show');
	});
	passwordSubmitBtn.disabled = false;
	passwordSubmitBtn.innerHTML = '<i class="bx bx-check me-1"></i> Actualizar contraseña';
}

function openPasswordModal(user) {
	if (!ensurePasswordElements() || !user) return;
	resetPasswordForm();
	passwordUserId = parseInt(user.id, 10);
	passwordForm.querySelector('[name="id"]').value = passwordUserId;
	const displayName = getSortableName(user) || user.email || 'Usuario';
	document.getElementById('cambiarContrasenaUsuario').textContent = `${displayName} · ${user.email || ''}`;
	bootstrap.Modal.getOrCreateInstance(passwordModalEl).show();
}

function bindPasswordChangeHandlers() {
	if (passwordHandlersBound || !ensurePasswordElements()) return;
	const accountsTable = document.getElementById('accounts-table');
	if (!accountsTable) return;
	passwordHandlersBound = true;

	accountsTable.addEventListener('click', function (event) {
		const button = event.target.closest('.btn-cambiar-contrasena');
		if (!button) return;
		const userId = parseInt(button.dataset.userId, 10);
		const user = currentUsers.find((item) => parseInt(item.id, 10) === userId);
		if (!user) {
			showMainAlert('No se encontró la información del usuario seleccionado.', 'danger');
			return;
		}
		openPasswordModal(user);
	});

	passwordModalEl.addEventListener('shown.bs.modal', function () {
		document.getElementById('nuevaContrasena')?.focus();
	});

	passwordModalEl.addEventListener('hidden.bs.modal', resetPasswordForm);

	passwordForm.querySelectorAll('.password-toggle').forEach((button) => {
		button.addEventListener('click', function () {
			const input = document.getElementById(button.dataset.passwordTarget);
			if (!input) return;
			const showing = input.type === 'text';
			input.type = showing ? 'password' : 'text';
			button.setAttribute('aria-pressed', showing ? 'false' : 'true');
			button.setAttribute('aria-label', `${showing ? 'Mostrar' : 'Ocultar'} contraseña`);
			const icon = button.querySelector('i');
			if (icon) icon.className = showing ? 'bx bx-show' : 'bx bx-hide';
			input.focus();
		});
	});

	passwordForm.addEventListener('submit', async function (event) {
		event.preventDefault();
		if (!hasAdminAccess() || !passwordUserId) {
			setPasswordAlert('No tienes permisos para realizar esta acción.');
			return;
		}

		const passInput = passwordForm.querySelector('[name="pass"]');
		const confirmationInput = passwordForm.querySelector('[name="pass_confirmation"]');
		const pass = passInput.value;
		const confirmation = confirmationInput.value;
		if (pass.length < 6) {
			setPasswordAlert('La contraseña debe tener al menos 6 caracteres.');
			passInput.focus();
			return;
		}
		if (pass !== confirmation) {
			setPasswordAlert('Las contraseñas no coinciden.');
			confirmationInput.focus();
			return;
		}

		const originalContent = passwordSubmitBtn.innerHTML;
		passwordSubmitBtn.disabled = true;
		passwordSubmitBtn.innerHTML =
			'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Actualizando...';
		setPasswordAlert('');

		const formData = new FormData();
		formData.append('accion', 'cambiar-contrasena');
		formData.append('id', String(passwordUserId));
		formData.append('pass', pass);

		try {
			const response = await fetch('../../api/routes/apiUsuarios.php', {
				method: 'POST',
				body: formData,
			});
			const data = await response.json().catch(() => ({}));
			if (!response.ok || !data.success) {
				throw new Error(data.error || 'No fue posible actualizar la contraseña.');
			}
			bootstrap.Modal.getInstance(passwordModalEl)?.hide();
			showMainAlert('Contraseña actualizada correctamente.');
		} catch (error) {
			console.error('Error al actualizar contraseña:', error);
			setPasswordAlert(error.message || 'No fue posible actualizar la contraseña.');
		} finally {
			passwordSubmitBtn.disabled = false;
			passwordSubmitBtn.innerHTML = originalContent;
		}
	});
}
