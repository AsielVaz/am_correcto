document.addEventListener('DOMContentLoaded', function () {
	const btnNuevaFIEL = document.getElementById('btnNuevaFIEL');
	const formNuevaFIEL = document.getElementById('formNuevaFIEL');
	const alertNuevaFIEL = document.getElementById('alertNuevaFIEL');
	const btnCancelarFIEL = document.getElementById('btnCancelarFIEL');
	const originalNuevaFielDisplay = btnNuevaFIEL ? btnNuevaFIEL.style.display || 'inline-block' : 'inline-block';

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
		if (btnNuevaFIEL) {
			btnNuevaFIEL.classList.toggle('d-none', !canWrite);
			btnNuevaFIEL.style.display = canWrite ? originalNuevaFielDisplay : 'none';
		}
		if (!canWrite && formNuevaFIEL) {
			formNuevaFIEL.style.display = 'none';
		}
		if (formNuevaFIEL) {
			const submitBtn = formNuevaFIEL.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		document.querySelectorAll('#modalFIEL [data-requires="write"]').forEach((element) => {
			if (!element.dataset.originalDisplay) {
				element.dataset.originalDisplay = getComputedStyle(element).display || '';
			}
			element.classList.toggle('d-none', !canWrite);
			element.style.display = canWrite ? element.dataset.originalDisplay || '' : 'none';
		});
	};

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	btnNuevaFIEL.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaFIEL.style.display = 'block';
		btnNuevaFIEL.style.display = 'none';
	});

	btnCancelarFIEL.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		formNuevaFIEL.style.display = 'none';
		btnNuevaFIEL.style.display = 'inline-block';
		formNuevaFIEL.reset();
		alertNuevaFIEL.style.display = 'none';
	});

	// Guardar nueva FIEL
	formNuevaFIEL.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		const formData = new FormData(formNuevaFIEL);
		formData.append('accion', 'nuevaFIEL');

		const idEmpresa = document.getElementById('inputIdEmpresaFIEL').value;
		formData.set('id', idEmpresa);

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				alertNuevaFIEL.textContent = 'FIEL agregada correctamente.';
				alertNuevaFIEL.className = 'alert alert-success mt-2';
				alertNuevaFIEL.style.display = 'block';
				formNuevaFIEL.reset();

				setTimeout(() => {
					alertNuevaFIEL.style.display = 'none';
					formNuevaFIEL.style.display = 'none';
					btnNuevaFIEL.style.display = 'inline-block';

					// Recargar datos
					fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
						.then((res) => res.json())
						.then((data) => {
							const detalle = data.data;
							const tbody = document.querySelector('#modalFIEL tbody');
							tbody.innerHTML = '';
							if (detalle.fiel && detalle.fiel.length) {
								detalle.fiel.forEach((row) => {
									tbody.innerHTML += `<tr>
                                            <td>${row.tipo || ''}</td>
                                            <td>${row.fechaCreacion || ''}</td>
                                            <td class='text-center'>
                                                <div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">
                                                    ${
														row.documento
															? `<a href='${row.documento}' class='btn btn-outline-primary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center' download aria-label='Descargar documento' title='Descargar documento'><i class='bx bx-download'></i></a>`
															: ''
													}
                                                    <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-fiel" data-id="${
														row.id
													}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>`;
								});
							} else {
								tbody.innerHTML = '<tr><td colspan="3" class="text-center">Sin FIEL</td></tr>';
							}
						});
				}, 2000);
			} else {
				alertNuevaFIEL.textContent = 'Error al agregar FIEL.';
				alertNuevaFIEL.className = 'alert alert-danger mt-2';
				alertNuevaFIEL.style.display = 'block';
			}
		} catch (err) {
			console.error('Error al guardar FIEL:', err);
			alertNuevaFIEL.textContent = 'Error de conexión.';
			alertNuevaFIEL.className = 'alert alert-danger mt-2';
			alertNuevaFIEL.style.display = 'block';
		}
	});

	// Cuando se abra el modal, poner el idEmpresa en el input oculto
	document.getElementById('modalFIEL').addEventListener('show.bs.modal', function (e) {
		const btn = e.relatedTarget;
		let idEmpresa = '';
		if (btn && btn.getAttribute('data-id')) {
			idEmpresa = btn.getAttribute('data-id');
		}
		document.getElementById('inputIdEmpresaFIEL').value = idEmpresa;
	});

	// Delegación de evento para eliminar FIEL usando modal de confirmación
	let deleteFielParams = null;

	function mostrarModalConfirmacionFiel(idFiel, idEmpresa, event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		deleteFielParams = {
			idFiel,
			idEmpresa,
		};
		// Puedes crear un modal de confirmación igual que los otros modales
		const modalHtml = document.getElementById('modalConfirmDeleteFiel');
		const modal = new bootstrap.Modal(modalHtml);
		modal.show();
	}

	// Crear modal de confirmación si no existe
	if (!document.getElementById('modalConfirmDeleteFiel')) {
		const modalDiv = document.createElement('div');
		modalDiv.innerHTML = `
			<div class="modal fade" id="modalConfirmDeleteFiel" tabindex="-1" aria-labelledby="modalConfirmDeleteFielLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalConfirmDeleteFielLabel">Confirmar eliminación</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
						</div>
						<div class="modal-body">
							<p>¿Estás seguro de que deseas eliminar este registro FIEL? Esta acción no se puede deshacer.</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" data-bs-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-danger rounded-pill shadow-sm d-inline-flex align-items-center justify-content-center fw-semibold" id="btnConfirmDeleteFiel" data-requires="write">Eliminar</button>
						</div>
					</div>
				</div>
			</div>
			`;
		document.body.appendChild(modalDiv);
	}

	document.getElementById('btnConfirmDeleteFiel').addEventListener('click', async function () {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		if (!deleteFielParams) return;
		await eliminarFiel(deleteFielParams.idFiel, deleteFielParams.idEmpresa);
		const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteFiel'));
		modal.hide();
		deleteFielParams = null;
	});

	// Delegación de evento para eliminar FIEL
	document.querySelector('#modalFIEL tbody').addEventListener('click', function (e) {
		if (e.target.closest('.btn-eliminar-fiel')) {
			const btn = e.target.closest('.btn-eliminar-fiel');
			const idFiel = btn.getAttribute('data-id');
			const idEmpresa = document.getElementById('inputIdEmpresaFIEL').value;
			if (!idFiel) return;
			mostrarModalConfirmacionFiel(idFiel, idEmpresa, e);
		}
	});

	// Función para eliminar FIEL
	async function eliminarFiel(idFiel, idEmpresa) {
		const formData = new FormData();
		formData.append('accion', 'eliminarFIEL');
		formData.append('id', idFiel);
		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				// Actualizar la tabla de FIEL
				fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
					.then((res) => res.json())
					.then((data) => {
						const detalle = data.data;
						const tbody = document.querySelector('#modalFIEL tbody');
						tbody.innerHTML = '';
						if (detalle.fiel && detalle.fiel.length) {
							detalle.fiel.forEach((row) => {
								tbody.innerHTML += `<tr>
                                        <td>${row.tipo || ''}</td>
                                        <td>${row.fechaCreacion || ''}</td>
                                        <td class='text-center'>
                                            <div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">
                                                ${
													row.documento
														? `<a href='${row.documento}' class='btn btn-outline-primary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center' download aria-label='Descargar documento' title='Descargar documento'><i class='bx bx-download'></i></a>`
														: ''
												}
                                                <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-fiel" data-id="${
													row.id
												}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>`;
							});
						} else {
							tbody.innerHTML = '<tr><td colspan="3" class="text-center">Sin FIEL</td></tr>';
						}
					});
			} else {
				alert('Error al eliminar el registro FIEL.');
			}
		} catch (err) {
			alert('Error de conexión al eliminar el registro FIEL.');
		}
	}
});
