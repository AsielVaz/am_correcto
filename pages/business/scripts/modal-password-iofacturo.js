document.addEventListener('DOMContentLoaded', function () {
	const btnNuevaContrasenaIOFacturo = document.getElementById('btnNuevaContrasenaIOFacturo');
	const formNuevaContrasenaIOFacturo = document.getElementById('formNuevaContrasenaIOFacturo');
	const alertNuevaContrasenaIOFacturo = document.getElementById('alertNuevaContrasenaIOFacturo');
	const btnCancelarContrasenaIOFacturo = document.getElementById('btnCancelarContrasenaIOFacturo');
	const modalContrasenaIOFacturo = document.getElementById('modalContrasenaIOFacturo');
	const originalNuevaContrasenaDisplay = btnNuevaContrasenaIOFacturo
		? btnNuevaContrasenaIOFacturo.style.display || 'inline-block'
		: 'inline-block';

	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);

	function applyPermissionVisibility() {
		const canWrite = hasWritePermissions();
		const requiresWriteElements = (modalContrasenaIOFacturo || document).querySelectorAll(
			'[data-requires="write"]'
		);
		requiresWriteElements.forEach((element) => {
			const mode = element.getAttribute('data-permission-mode');
			if (canWrite) {
				if (mode === 'disable') {
					const targets = element.matches('input, button, select, textarea')
						? [element]
						: element.querySelectorAll('input, button, select, textarea');
					targets.forEach((target) => (target.disabled = false));
				} else {
					const originalDisplay = element.dataset.originalDisplay;
					element.style.display = originalDisplay !== undefined ? originalDisplay : '';
				}
			} else {
				if (mode === 'disable') {
					const targets = element.matches('input, button, select, textarea')
						? [element]
						: element.querySelectorAll('input, button, select, textarea');
					targets.forEach((target) => (target.disabled = true));
				} else {
					if (!element.dataset.originalDisplay) {
						element.dataset.originalDisplay = getComputedStyle(element).display || '';
					}
					element.style.display = 'none';
				}
			}
		});
		if (btnNuevaContrasenaIOFacturo) {
			btnNuevaContrasenaIOFacturo.classList.toggle('d-none', !canWrite);
			btnNuevaContrasenaIOFacturo.style.display = canWrite ? originalNuevaContrasenaDisplay : 'none';
		}
		if (!canWrite && formNuevaContrasenaIOFacturo) {
			formNuevaContrasenaIOFacturo.style.display = 'none';
		}
		if (formNuevaContrasenaIOFacturo) {
			const submitBtn = formNuevaContrasenaIOFacturo.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
	}

	function ensureWritePermission(event) {
		if (hasWritePermissions()) {
			return true;
		}
		if (event && typeof event.preventDefault === 'function') {
			event.preventDefault();
		}
		alert('No tienes permisos para realizar esta acción.');
		return false;
	}

	btnNuevaContrasenaIOFacturo.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaContrasenaIOFacturo.style.display = 'block';
		btnNuevaContrasenaIOFacturo.style.display = 'none';
	});

	btnCancelarContrasenaIOFacturo.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaContrasenaIOFacturo.style.display = 'none';
		btnNuevaContrasenaIOFacturo.style.display = 'inline-block';
		formNuevaContrasenaIOFacturo.reset();
		alertNuevaContrasenaIOFacturo.style.display = 'none';
	});

	async function recargarTablaContrasenaIOFacturo(idEmpresa) {
		const tbody = document.querySelector('#modalContrasenaIOFacturo tbody');
		if (!idEmpresa) {
			tbody.innerHTML = '<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
			return;
		}
		try {
			const resDetalle = await fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`);
			const dataDetalle = await resDetalle.json();
			const detalle = dataDetalle.data || {};
			tbody.innerHTML = '';
			if (detalle.contrasIofacturo && detalle.contrasIofacturo.length) {
				detalle.contrasIofacturo.forEach((row) => {
					const accionHtml = hasWritePermissions()
						? `<div class="d-inline-flex align-items-center justify-content-center gap-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-iofacturo" data-id="${row.id}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>`
						: '<span class="text-muted small">Sin permisos</span>';
					tbody.innerHTML += `<tr>
                            <td>${row.usuario || ''}</td>
                            <td>${row.rfc || ''}</td>
                            <td>${row.contrasena || ''}</td>
                            <td class="text-center">${accionHtml}</td>
                        </tr>`;
				});
			} else {
				tbody.innerHTML = '<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
			}
			applyPermissionVisibility();
		} catch (error) {
			console.error('Error al recargar contraseñas IOFacturo:', error);
			tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error al cargar datos</td></tr>';
		}
	}

	// Guardar nueva contraseña IOFacturo
	formNuevaContrasenaIOFacturo.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		const formData = new FormData(formNuevaContrasenaIOFacturo);
		formData.append('accion', 'crear');

		const idEmpresa = document.getElementById('inputIdEmpresaContrasenaIOFacturo').value;
		formData.set('id', idEmpresa);

		try {
			const res = await fetch('../../api/routes/apiContrasIofacturo.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				alertNuevaContrasenaIOFacturo.textContent = 'Contraseña IOFacturo agregada correctamente.';
				alertNuevaContrasenaIOFacturo.className = 'alert alert-success mt-2';
				alertNuevaContrasenaIOFacturo.style.display = 'block';
				formNuevaContrasenaIOFacturo.reset();

				// Segunda llamada para obtener datos actualizados
				setTimeout(async () => {
					alertNuevaContrasenaIOFacturo.style.display = 'none';
					formNuevaContrasenaIOFacturo.style.display = 'none';
					btnNuevaContrasenaIOFacturo.style.display = 'inline-block';
					recargarTablaContrasenaIOFacturo(idEmpresa);
				}, 1500);
			} else {
				alertNuevaContrasenaIOFacturo.textContent = data.mensaje || 'Error al agregar contraseña IOFacturo.';
				alertNuevaContrasenaIOFacturo.className = 'alert alert-danger mt-2';
				alertNuevaContrasenaIOFacturo.style.display = 'block';
			}
		} catch (err) {
			console.error('Error al guardar contraseña IOFacturo:', err);
			alertNuevaContrasenaIOFacturo.textContent = 'Error de conexión.';
			alertNuevaContrasenaIOFacturo.className = 'alert alert-danger mt-2';
			alertNuevaContrasenaIOFacturo.style.display = 'block';
		}
	});

	// Cuando se abra el modal, poner el idEmpresa en el input oculto y recargar tabla
	document.getElementById('modalContrasenaIOFacturo').addEventListener('show.bs.modal', function (e) {
		const btn = e.relatedTarget;
		let idEmpresa = '';
		if (btn && btn.getAttribute('data-id')) {
			idEmpresa = btn.getAttribute('data-id');
		}
		document.getElementById('inputIdEmpresaContrasenaIOFacturo').value = idEmpresa;
		recargarTablaContrasenaIOFacturo(idEmpresa);
	});

	// Delegación para copiar y eliminar contraseña IOFacturo
	document.querySelector('#modalContrasenaIOFacturo tbody').addEventListener('click', async function (e) {
		// Copiar campo individual
		if (e.target.closest('.btn-copy-campo')) {
			const btn = e.target.closest('.btn-copy-campo');
			const valor = btn.getAttribute('data-copy') || '';
			if (valor) {
				try {
					await navigator.clipboard.writeText(valor);
					showCopyNotification && showCopyNotification('¡Copiado!');
				} catch {
					showCopyNotification && showCopyNotification('No se pudo copiar');
				}
				btn.classList.add('text-success');
				setTimeout(() => btn.classList.remove('text-success'), 800);
			}
			return;
		}
		// Copiar toda la fila
		if (e.target.closest('.btn-copy-fila')) {
			const tr = e.target.closest('tr');
			if (tr) {
				const ths = ['Usuario', 'RFC', 'Contraseña'];
				const tds = Array.from(tr.querySelectorAll('td'));
				let texto = '';
				for (let i = 0; i < 3; i++) {
					const valor = tds[i]?.querySelector('span')?.textContent?.trim() || '';
					texto += `${ths[i]}: ${valor}\n`;
				}
				try {
					await navigator.clipboard.writeText(texto.trim());
					showCopyNotification && showCopyNotification('¡Fila copiada!');
				} catch {
					showCopyNotification && showCopyNotification('No se pudo copiar');
				}
				const btn = e.target.closest('.btn-copy-fila');
				btn.classList.add('text-success');
				setTimeout(() => btn.classList.remove('text-success'), 800);
			}
			return;
		}
		// Notificación de copiado (si no existe global)
		if (typeof showCopyNotification !== 'function') {
			window.showCopyNotification = function (msg = '¡Copiado!') {
				let notif = document.getElementById('copy-toast-notif');
				if (!notif) {
					notif = document.createElement('div');
					notif.id = 'copy-toast-notif';
					notif.style.position = 'fixed';
					notif.style.bottom = '32px';
					notif.style.left = '50%';
					notif.style.transform = 'translateX(-50%)';
					notif.style.background = 'rgba(40,40,40,0.95)';
					notif.style.color = '#fff';
					notif.style.padding = '10px 24px';
					notif.style.borderRadius = '24px';
					notif.style.fontSize = '1rem';
					notif.style.zIndex = 9999;
					notif.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
					notif.style.opacity = '0';
					notif.style.pointerEvents = 'none';
					notif.style.transition = 'opacity 0.25s';
					document.body.appendChild(notif);
				}
				notif.textContent = msg;
				notif.style.opacity = '1';
				setTimeout(() => {
					notif.style.opacity = '0';
				}, 1200);
			};
		}
		// Eliminar contraseña IOFacturo
		if (e.target.closest('.btn-eliminar-contrasena-iofacturo')) {
			const btn = e.target.closest('.btn-eliminar-contrasena-iofacturo');
			const idContrasena = btn.getAttribute('data-id');
			const idEmpresa = document.getElementById('inputIdEmpresaContrasenaIOFacturo').value;
			mostrarModalConfirmacionIOFacturo(idContrasena, idEmpresa, e);
		}
	});

	// Modal de confirmación para eliminar contraseña IOFacturo
	const modalConfirmDeleteIOFacturo = document.createElement('div');
	modalConfirmDeleteIOFacturo.innerHTML = `
			<div class="modal fade" id="modalConfirmDeleteIOFacturo" tabindex="-1" aria-labelledby="modalConfirmDeleteIOFacturoLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalConfirmDeleteIOFacturoLabel">Confirmar eliminación</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
						</div>
						<div class="modal-body">
							<p>¿Estás seguro de que deseas eliminar esta contraseña IOFacturo? Esta acción no se puede deshacer.</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteIOFacturo" data-requires="write">Eliminar</button>
						</div>
					</div>
				</div>
			</div>
	`;
	document.body.appendChild(modalConfirmDeleteIOFacturo);

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	let deleteIOFacturoParams = null;

	function mostrarModalConfirmacionIOFacturo(idContrasena, idEmpresa, event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		deleteIOFacturoParams = {
			idContrasena,
			idEmpresa,
		};
		const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteIOFacturo'));
		modal.show();
	}

	document.getElementById('btnConfirmDeleteIOFacturo').addEventListener('click', async function () {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		if (!deleteIOFacturoParams) return;
		const { idContrasena, idEmpresa } = deleteIOFacturoParams;
		const formData = new FormData();
		formData.append('accion', 'eliminar');
		formData.append('id', idContrasena);
		try {
			const res = await fetch('../../api/routes/apiContrasIofacturo.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				await recargarTablaContrasenaIOFacturo(idEmpresa);
			} else {
				alert('Error al eliminar la contraseña IOFacturo.');
			}
		} catch (err) {
			alert('Error de conexión al eliminar la contraseña IOFacturo.');
		}
		const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteIOFacturo'));
		modal.hide();
		deleteIOFacturoParams = null;
	});

	applyPermissionVisibility();
});
