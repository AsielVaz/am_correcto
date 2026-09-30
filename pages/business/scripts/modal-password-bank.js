document.addEventListener('DOMContentLoaded', function () {
	// Delegar eventos de copiar campo y copiar fila
	document.querySelector('#modalContrasenasBancos tbody').addEventListener('click', function (e) {
		// Copiar campo individual
		const btnCopyField = e.target.closest('.btn-copy-field');
		if (btnCopyField) {
			let value = btnCopyField.getAttribute('data-value');
			value = value && value.trim() ? value : 'Sin datos';
			navigator.clipboard.writeText(value);
			return;
		}
		// Copiar toda la fila
		const btnCopyRow = e.target.closest('.btn-copy-row');
		if (btnCopyRow) {
			let rowText = btnCopyRow.getAttribute('data-rowcopy') || '';
			// Reemplazar cualquier campo vacío por 'Sin datos' en el texto de la fila
			rowText = rowText.replace(/: ?(\s*)($|\n)/g, function (match, spaces, ending) {
				return ': Sin datos' + ending;
			});
			navigator.clipboard.writeText(rowText);
			return;
		}
	});
	const btnNuevaContrasenaBanco = document.getElementById('btnNuevaContrasenaBanco');
	const formNuevaContrasenaBanco = document.getElementById('formNuevaContrasenaBanco');
	const alertNuevaContrasenaBanco = document.getElementById('alertNuevaContrasenaBanco');
	const btnCancelarContrasenaBanco = document.getElementById('btnCancelarContrasenaBanco');
	const originalNuevaContrasenaBancoDisplay = btnNuevaContrasenaBanco
		? btnNuevaContrasenaBanco.style.display || 'inline-block'
		: 'inline-block';

	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);

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

	const applyPermissionVisibility = () => {
		const canWrite = hasWritePermissions();
		if (btnNuevaContrasenaBanco) {
			btnNuevaContrasenaBanco.classList.toggle('d-none', !canWrite);
			btnNuevaContrasenaBanco.style.display = canWrite ? originalNuevaContrasenaBancoDisplay : 'none';
		}
		if (!canWrite && formNuevaContrasenaBanco) {
			formNuevaContrasenaBanco.style.display = 'none';
		}
		if (formNuevaContrasenaBanco) {
			const submitBtn = formNuevaContrasenaBanco.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		document.querySelectorAll('#modalContrasenasBancos [data-requires="write"]').forEach((element) => {
			if (!element.dataset.originalDisplay) {
				element.dataset.originalDisplay = getComputedStyle(element).display || '';
			}
			element.classList.toggle('d-none', !canWrite);
			element.style.display = canWrite ? element.dataset.originalDisplay || '' : 'none';
		});
	};

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	btnNuevaContrasenaBanco.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaContrasenaBanco.style.display = 'block';
		btnNuevaContrasenaBanco.style.display = 'none';
	});

	btnCancelarContrasenaBanco.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaContrasenaBanco.style.display = 'none';
		btnNuevaContrasenaBanco.style.display = 'inline-block';
		formNuevaContrasenaBanco.reset();
		alertNuevaContrasenaBanco.style.display = 'none';
	});

	// Guardar nueva contraseña banco
	formNuevaContrasenaBanco.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		const formData = new FormData(formNuevaContrasenaBanco);
		formData.append('accion', 'agregarContraBanco');

		const idEmpresa = document.getElementById('inputIdEmpresaContrasenaBanco').value;
		formData.set('id', idEmpresa);
		formData.append('estatus', 'activa');

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				alertNuevaContrasenaBanco.textContent = 'Contraseña banco agregada correctamente.';
				alertNuevaContrasenaBanco.className = 'alert alert-success mt-2';
				alertNuevaContrasenaBanco.style.display = 'block';
				formNuevaContrasenaBanco.reset();

				setTimeout(() => {
					alertNuevaContrasenaBanco.style.display = 'none';
					formNuevaContrasenaBanco.style.display = 'none';
					btnNuevaContrasenaBanco.style.display = 'inline-block';
					// Recargar datos (forzando recarga desde API)
					recargarTablaContrasenasBanco(idEmpresa);
				}, 2000);
			} else {
				alertNuevaContrasenaBanco.textContent = 'Error al agregar contraseña banco.';
				alertNuevaContrasenaBanco.className = 'alert alert-danger mt-2';
				alertNuevaContrasenaBanco.style.display = 'block';
			}
		} catch (err) {
			console.error('Error al guardar contraseña banco:', err);
			alertNuevaContrasenaBanco.textContent = 'Error de conexión.';
			alertNuevaContrasenaBanco.className = 'alert alert-danger mt-2';
			alertNuevaContrasenaBanco.style.display = 'block';
		}
	});

	// Modal de confirmación para eliminar contraseña banco
	const modalConfirmDeleteBanco = document.createElement('div');
	modalConfirmDeleteBanco.innerHTML = `
			<div class="modal fade" id="modalConfirmDeleteBanco" tabindex="-1" aria-labelledby="modalConfirmDeleteBancoLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalConfirmDeleteBancoLabel">Confirmar eliminación</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
						</div>
						<div class="modal-body">
							<p>¿Estás seguro de que deseas eliminar esta contraseña de banco? Esta acción no se puede deshacer.</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteBanco" data-requires="write">Eliminar</button>
						</div>
					</div>
				</div>
			</div>
	`;
	document.body.appendChild(modalConfirmDeleteBanco);

	let deleteBancoParams = null;

	document.querySelector('#modalContrasenasBancos tbody').addEventListener('click', function (e) {
		const targetBtn = e.target.closest('.btn-eliminar-contrasena-banco');
		if (targetBtn) {
			if (!ensureWritePermission(e)) {
				return;
			}
			const id = targetBtn.getAttribute('data-id');
			const idEmpresa = document.getElementById('inputIdEmpresaContrasenaBanco').value;
			if (!id) return;
			deleteBancoParams = {
				id,
				idEmpresa,
			};
			const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteBanco'));
			modal.show();
		}
	});

	document.getElementById('btnConfirmDeleteBanco').addEventListener('click', async function () {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		if (!deleteBancoParams) return;
		await eliminarContrasenaBanco(deleteBancoParams.id, deleteBancoParams.idEmpresa);
		const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteBanco'));
		modal.hide();
		deleteBancoParams = null;
	});

	// Eliminar contraseña banco y refrescar tabla
	async function eliminarContrasenaBanco(idContrasena, idEmpresa) {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		const formData = new FormData();
		formData.append('accion', 'eliminarContraBanco');
		formData.append('id', idContrasena);
		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				// Actualizar la tabla de contraseñas banco usando función de recarga si existe
				if (typeof cargarDetalleEmpresa === 'function') {
					cargarDetalleEmpresa(
						idEmpresa,
						(detalle) => {
							const tbody = document.querySelector('#modalContrasenasBancos tbody');
							tbody.innerHTML = '';
							if (detalle.contrasBanco && detalle.contrasBanco.length) {
								detalle.contrasBanco.forEach((row) => {
									const banco = row.banco && row.banco.trim() ? row.banco : 'Sin datos';
									const usuario = row.usuario && row.usuario.trim() ? row.usuario : 'Sin datos';
									const contrasena =
										row.contrasena && row.contrasena.trim() ? row.contrasena : 'Sin datos';
									const nip = row.nip && row.nip.trim() ? row.nip : 'Sin datos';
									const claveOp = row.claveOp && row.claveOp.trim() ? row.claveOp : 'Sin datos';
									tbody.innerHTML += `<tr>
										<td>${banco}</td>
										<td>${usuario}</td>
										<td>${contrasena}</td>
										<td>${nip}</td>
										<td>${claveOp}</td>
										<td class="text-center">
											<div class="d-inline-flex align-items-center justify-content-center gap-2">
												<button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-banco" data-id="${row.id}" data-requires="write" title="Eliminar" aria-label="Eliminar">
													<i class="bx bx-trash"></i>
												</button>
											</div>
										</td>
									</tr>`;
								});
							} else {
								tbody.innerHTML = '<tr><td colspan="6" class="text-center">Sin contraseñas</td></tr>';
							}
						},
						{ forceReload: true }
					);
				} else {
					// Fallback a petición directa
					fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
						.then((res) => res.json())
						.then((data) => {
							const detalle = data.data;
							const tbody = document.querySelector('#modalContrasenasBancos tbody');
							tbody.innerHTML = '';
							if (detalle.contrasBanco && detalle.contrasBanco.length) {
								detalle.contrasBanco.forEach((row) => {
									const banco = row.banco && row.banco.trim() ? row.banco : 'Sin datos';
									const usuario = row.usuario && row.usuario.trim() ? row.usuario : 'Sin datos';
									const contrasena =
										row.contrasena && row.contrasena.trim() ? row.contrasena : 'Sin datos';
									const nip = row.nip && row.nip.trim() ? row.nip : 'Sin datos';
									const claveOp = row.claveOp && row.claveOp.trim() ? row.claveOp : 'Sin datos';
									tbody.innerHTML += `<tr>
										<td>${banco}</td>
										<td>${usuario}</td>
										<td>${contrasena}</td>
										<td>${nip}</td>
										<td>${claveOp}</td>
										<td class="text-center">
											<div class="d-inline-flex align-items-center justify-content-center gap-2">
												<button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-banco" data-id="${row.id}" data-requires="write" title="Eliminar" aria-label="Eliminar">
													<i class="bx bx-trash"></i>
												</button>
											</div>
										</td>
									</tr>`;
								});
							} else {
								tbody.innerHTML = '<tr><td colspan="6" class="text-center">Sin contraseñas</td></tr>';
							}
						});
				}
			} else {
				alert('Error al eliminar la contraseña de banco.');
			}
		} catch (err) {
			alert('Error de conexión al eliminar la contraseña de banco.');
		}
	}

	// Función para recargar la tabla de contraseñas banco
	function recargarTablaContrasenasBanco(idEmpresa) {
		if (typeof cargarDetalleEmpresa === 'function') {
			cargarDetalleEmpresa(
				idEmpresa,
				(detalle) => {
					const tbody = document.querySelector('#modalContrasenasBancos tbody');
					tbody.innerHTML = '';
					if (detalle.contrasBanco && detalle.contrasBanco.length) {
						detalle.contrasBanco.forEach((row) => {
							tbody.innerHTML += `<tr>
                                <td>${row.banco || ''}</td>
                                <td>${row.usuario || ''}</td>
                                <td>${row.contrasena || ''}</td>
                                <td>${row.nip || ''}</td>
                                <td>${row.claveOp || ''}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center justify-content-center gap-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-banco" data-id="${
											row.id
										}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
						});
					} else {
						tbody.innerHTML = '<tr><td colspan="6" class="text-center">Sin contraseñas</td></tr>';
					}
				},
				{ forceReload: true }
			);
		}
	}

	// Cuando se abra el modal, poner el idEmpresa en el input oculto
	document.getElementById('modalContrasenasBancos').addEventListener('show.bs.modal', function (e) {
		const btn = e.relatedTarget;
		let idEmpresa = '';
		if (btn && btn.getAttribute('data-id')) {
			idEmpresa = btn.getAttribute('data-id');
		}
		document.getElementById('inputIdEmpresaContrasenaBanco').value = idEmpresa;
	});

	// Forzar el ancho máximo del modal por si el CSS de Bootstrap es muy restrictivo
	const modal = document.getElementById('modalContrasenasBancos');
	if (modal) {
		modal.addEventListener('shown.bs.modal', function () {
			const dialog = modal.querySelector('.modal-dialog');
			if (dialog) {
				dialog.style.maxWidth = '55vw';
			}
		});
	}
});
