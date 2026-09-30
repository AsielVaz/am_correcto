document.addEventListener('DOMContentLoaded', function () {
	const btnNuevaContrasenaSAT = document.getElementById('btnNuevaContrasenaSAT');
	const formNuevaContrasenaSAT = document.getElementById('formNuevaContrasenaSAT');
	const alertNuevaContrasenaSAT = document.getElementById('alertNuevaContrasenaSAT');
	const btnCancelarContrasenaSAT = document.getElementById('btnCancelarContrasenaSAT');
	const originalNuevaContrasenaSatDisplay = btnNuevaContrasenaSAT
		? btnNuevaContrasenaSAT.style.display || 'inline-block'
		: 'inline-block';

	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);

	const applyPermissionVisibility = () => {
		const canWrite = hasWritePermissions();
		if (btnNuevaContrasenaSAT) {
			btnNuevaContrasenaSAT.classList.toggle('d-none', !canWrite);
			btnNuevaContrasenaSAT.style.display = canWrite ? originalNuevaContrasenaSatDisplay : 'none';
		}
		if (!canWrite && formNuevaContrasenaSAT) {
			formNuevaContrasenaSAT.style.display = 'none';
		}
		if (formNuevaContrasenaSAT) {
			const submitBtn = formNuevaContrasenaSAT.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		document.querySelectorAll('#modalContrasenaSAT [data-requires="write"]').forEach((element) => {
			if (!element.dataset.originalDisplay) {
				element.dataset.originalDisplay = getComputedStyle(element).display || '';
			}
			element.classList.toggle('d-none', !canWrite);
			element.style.display = canWrite ? element.dataset.originalDisplay || '' : 'none';
		});
	};

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

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

	btnNuevaContrasenaSAT.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaContrasenaSAT.style.display = 'block';
		btnNuevaContrasenaSAT.style.display = 'none';
	});

	btnCancelarContrasenaSAT.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaContrasenaSAT.style.display = 'none';
		btnNuevaContrasenaSAT.style.display = 'inline-block';
		formNuevaContrasenaSAT.reset();
		alertNuevaContrasenaSAT.style.display = 'none';
	});

	// Guardar nueva contraseña SAT
	formNuevaContrasenaSAT.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		const formData = new FormData(formNuevaContrasenaSAT);
		formData.append('accion', 'agregarContraSat'); // <-- Corrección aquí

		const idEmpresa = document.getElementById('inputIdEmpresaContrasenaSAT').value;
		formData.set('id', idEmpresa);
		formData.append('estatus', 'activa');

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				alertNuevaContrasenaSAT.textContent = 'Contraseña SAT agregada correctamente.';
				alertNuevaContrasenaSAT.className = 'alert alert-success mt-2';
				alertNuevaContrasenaSAT.style.display = 'block';
				formNuevaContrasenaSAT.reset();

				setTimeout(() => {
					alertNuevaContrasenaSAT.style.display = 'none';
					formNuevaContrasenaSAT.style.display = 'none';
					btnNuevaContrasenaSAT.style.display = 'inline-block';

					// Recargar datos (forzando recarga desde API)
					if (typeof cargarDetalleEmpresa === 'function') {
						cargarDetalleEmpresa(
							idEmpresa,
							(detalle) => {
								const tbody = document.querySelector('#modalContrasenaSAT tbody');
								tbody.innerHTML = '';
								if (detalle.contrasSat && detalle.contrasSat.length) {
									detalle.contrasSat.forEach((row) => {
										tbody.innerHTML += `<tr>
                                            <td>${row.usuario || ''}</td>
                                            <td>${row.contrasena || ''}</td>
                                            <td>${row.rfc || ''}</td>
                                            <td class='text-center'>
                                                <div class="d-inline-flex align-items-center justify-content-center gap-2">
                                                    <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-sat" data-id="${
														row.id
													}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>`;
									});
								} else {
									tbody.innerHTML =
										'<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
								}
							},
							{ forceReload: true }
						);
					}
				}, 2000);
			} else {
				alertNuevaContrasenaSAT.textContent = 'Error al agregar contraseña SAT.';
				alertNuevaContrasenaSAT.className = 'alert alert-danger mt-2';
				alertNuevaContrasenaSAT.style.display = 'block';
			}
		} catch (err) {
			console.error('Error al guardar contraseña SAT:', err);
			alertNuevaContrasenaSAT.textContent = 'Error de conexión.';
			alertNuevaContrasenaSAT.className = 'alert alert-danger mt-2';
			alertNuevaContrasenaSAT.style.display = 'block';
		}
	});

	// Usar solo el modal HTML existente, no crear uno nuevo

	let deleteSATParams = null;

	function mostrarModalConfirmacionSAT(idContrasena, idEmpresa, event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		deleteSATParams = {
			idContrasena,
			idEmpresa,
		};
		const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteSAT'));
		modal.show();
	}

	document.getElementById('btnConfirmDeleteSAT').addEventListener('click', async function () {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		if (!deleteSATParams) return;
		await eliminarContrasenaSAT(deleteSATParams.idContrasena, deleteSATParams.idEmpresa);
		const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteSAT'));
		modal.hide();
		deleteSATParams = null;
	});

	// --- Eliminar contraseña SAT con modal ---
	async function eliminarContrasenaSAT(idContrasena, idEmpresa) {
		const formData = new FormData();
		formData.append('accion', 'eliminarContraSat');
		formData.append('id', idContrasena);
		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				// Actualizar la tabla de contraseñas SAT usando la caché con recarga forzada si está disponible
				if (typeof cargarDetalleEmpresa === 'function') {
					cargarDetalleEmpresa(
						idEmpresa,
						(detalle) => {
							const tbody = document.querySelector('#modalContrasenaSAT tbody');
							tbody.innerHTML = '';
							if (detalle.contrasSat && detalle.contrasSat.length) {
								detalle.contrasSat.forEach((row) => {
									tbody.innerHTML += `<tr>
										<td>${row.usuario || ''}</td>
										<td>${row.contrasena || ''}</td>
										<td>${row.rfc || ''}</td>
										<td class='text-center'>
											<div class="d-inline-flex align-items-center justify-content-center gap-2">
												<button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-sat" data-id="${
													row.id
												}" data-requires="write" title="Eliminar" aria-label="Eliminar">
													<i class="bx bx-trash"></i>
												</button>
											</div>
										</td>
									</tr>`;
								});
							} else {
								tbody.innerHTML = '<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
							}
						},
						{ forceReload: true }
					);
				} else {
					// Fallback a petición directa si no existe la función
					fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
						.then((res) => res.json())
						.then((data) => {
							const detalle = data.data;
							const tbody = document.querySelector('#modalContrasenaSAT tbody');
							tbody.innerHTML = '';
							if (detalle.contrasSat && detalle.contrasSat.length) {
								detalle.contrasSat.forEach((row) => {
									tbody.innerHTML += `<tr>
											<td>${row.usuario || ''}</td>
											<td>${row.contrasena || ''}</td>
											<td>${row.rfc || ''}</td>
											<td class='text-center'>
												<div class="d-inline-flex align-items-center justify-content-center gap-2">
													<button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-sat" data-id="${
														row.id
													}" data-requires="write" title="Eliminar" aria-label="Eliminar">
														<i class="bx bx-trash"></i>
													</button>
												</div>
											</td>
										</tr>`;
								});
							} else {
								tbody.innerHTML = '<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
							}
						});
				}
			} else {
				alert('Error al eliminar la contraseña SAT.');
			}
		} catch (err) {
			alert('Error de conexión al eliminar la contraseña SAT.');
		}
	}

	// Cuando se abra el modal, poner el idEmpresa en el input oculto
	document.getElementById('modalContrasenaSAT').addEventListener('show.bs.modal', function (e) {
		const btn = e.relatedTarget;
		let idEmpresa = '';
		if (btn && btn.getAttribute('data-id')) {
			idEmpresa = btn.getAttribute('data-id');
		}
		document.getElementById('inputIdEmpresaContrasenaSAT').value = idEmpresa;
	});

	// --- Delegación de evento para eliminar contraseña SAT usando el modal ---
	document.querySelector('#modalContrasenaSAT tbody').addEventListener('click', async function (e) {
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
				const ths = ['Usuario', 'Contraseña', 'RFC'];
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
		// Eliminar contraseña SAT
		if (e.target.closest('.btn-eliminar-contrasena-sat')) {
			const btn = e.target.closest('.btn-eliminar-contrasena-sat');
			const idContrasena = btn.getAttribute('data-id');
			const idEmpresa = document.getElementById('inputIdEmpresaContrasenaSAT').value;
			if (!idContrasena) return;
			mostrarModalConfirmacionSAT(idContrasena, idEmpresa, e);
		}
	});
});
