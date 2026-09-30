document.addEventListener('DOMContentLoaded', function () {
	const btnNuevoSelloSAT = document.getElementById('btnNuevoSelloSAT');
	const formNuevoSelloSAT = document.getElementById('formNuevoSelloSAT');
	const alertNuevoSelloSAT = document.getElementById('alertNuevoSelloSAT');
	const btnCancelarSelloSAT = document.getElementById('btnCancelarSelloSAT');
	const btnConfirmDeleteSelloSAT = document.getElementById('btnConfirmDeleteSelloSAT');

	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);

	const storeOriginalDisplay = (element) => {
		if (!element || element.dataset.originalDisplay) {
			return;
		}
		const computed = getComputedStyle(element).display;
		element.dataset.originalDisplay = computed && computed !== 'none' ? computed : 'inline-block';
	};

	const toggleElementVisibility = (element, shouldShow) => {
		if (!element) {
			return;
		}
		storeOriginalDisplay(element);
		element.classList.toggle('d-none', !shouldShow);
		element.style.display = shouldShow ? element.dataset.originalDisplay : 'none';
	};

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
		toggleElementVisibility(btnNuevoSelloSAT, canWrite);
		if (!canWrite && formNuevoSelloSAT) {
			formNuevoSelloSAT.style.display = 'none';
		}
		if (formNuevoSelloSAT) {
			const submitBtn = formNuevoSelloSAT.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		if (btnConfirmDeleteSelloSAT) {
			toggleElementVisibility(btnConfirmDeleteSelloSAT, canWrite);
			btnConfirmDeleteSelloSAT.disabled = !canWrite;
			btnConfirmDeleteSelloSAT.classList.toggle('disabled', !canWrite);
		}
		const tbody = document.querySelector('#modalSellosSAT tbody');
		if (tbody) {
			tbody.querySelectorAll('[data-requires="write"]').forEach((element) => {
				toggleElementVisibility(element, canWrite);
			});
		}
		if (!canWrite && alertNuevoSelloSAT) {
			alertNuevoSelloSAT.style.display = 'none';
		}
	};

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	if (btnNuevoSelloSAT) {
		btnNuevoSelloSAT.addEventListener('click', function (event) {
			if (!ensureWritePermission(event)) {
				return;
			}
			formNuevoSelloSAT.style.display = 'block';
			btnNuevoSelloSAT.style.display = 'none';
		});
	}

	if (btnCancelarSelloSAT) {
		btnCancelarSelloSAT.addEventListener('click', function (event) {
			if (!ensureWritePermission(event)) {
				return;
			}
			formNuevoSelloSAT.style.display = 'none';
			if (btnNuevoSelloSAT) {
				storeOriginalDisplay(btnNuevoSelloSAT);
				btnNuevoSelloSAT.style.display = btnNuevoSelloSAT.dataset.originalDisplay || 'inline-block';
			}
			formNuevoSelloSAT.reset();
			alertNuevoSelloSAT.style.display = 'none';
		});
	}

	// Guardar nuevo sello SAT
	if (formNuevoSelloSAT) {
		formNuevoSelloSAT.addEventListener('submit', async function (e) {
			e.preventDefault();
			if (!ensureWritePermission(e)) {
				return;
			}
			const formData = new FormData(formNuevoSelloSAT);
			let tipo = document.getElementById('nuevoSelloTipo').value;
			// Si el tipo es CER, enviar como CSD
			if (tipo === 'CER') {
				tipo = 'CSD';
			}
			formData.append('tipo', tipo);
			formData.append('accion', 'agregarSelloSat');

			const idEmpresa = document.getElementById('inputIdEmpresaSelloSAT').value;
			formData.set('id', idEmpresa);

			try {
				const res = await fetch('../../api/routes/apiEmpresa.php', {
					method: 'POST',
					body: formData,
				});
				const data = await res.json();

				if (data.estatus === 'Exito') {
					alertNuevoSelloSAT.textContent = 'Sello SAT agregado correctamente.';
					alertNuevoSelloSAT.className = 'alert alert-success mt-2';
					alertNuevoSelloSAT.style.display = 'block';
					formNuevoSelloSAT.reset();

					setTimeout(() => {
						alertNuevoSelloSAT.style.display = 'none';
						formNuevoSelloSAT.style.display = 'none';
						if (btnNuevoSelloSAT) {
							storeOriginalDisplay(btnNuevoSelloSAT);
							btnNuevoSelloSAT.style.display = btnNuevoSelloSAT.dataset.originalDisplay || 'inline-block';
						}

						// Volver a pedir los sellos actualizados
						fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
							.then((res) => res.json())
							.then((data) => {
								const detalle = data.data;
								const tbody = document.querySelector('#modalSellosSAT tbody');
								tbody.innerHTML = '';
								if (detalle.sellosSat && detalle.sellosSat.length) {
									detalle.sellosSat.forEach((row) => {
										const tr = document.createElement('tr');
										tr.innerHTML = `
                                            <td>${row.tipo || ''}</td>
                                            <td>${row.fechaCreacion || ''}</td>
                                            <td class='text-center'>
                                                <div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">
                                                    ${
														row.documento
															? `<a href='${row.documento}' class='btn btn-outline-primary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center' download aria-label='Descargar sello SAT' title='Descargar sello SAT'><i class='bx bx-download'></i></a>`
															: '<span class="text-muted small">Sin archivo</span>'
													}
                                                    <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-sello-sat" data-id="${
														row.id
													}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        `;
										tbody.appendChild(tr);
									});
								} else {
									tbody.innerHTML = '<tr><td colspan="3" class="text-center">Sin sellos</td></tr>';
								}
								applyPermissionVisibility();
							});
						applyPermissionVisibility();
					}, 2000);
				} else {
					alertNuevoSelloSAT.textContent = 'Error al agregar sello SAT.';
					alertNuevoSelloSAT.className = 'alert alert-danger mt-2';
					alertNuevoSelloSAT.style.display = 'block';
				}
			} catch (err) {
				console.error('Error al guardar sello SAT:', err);
				alertNuevoSelloSAT.textContent = 'Error de conexión.';
				alertNuevoSelloSAT.className = 'alert alert-danger mt-2';
				alertNuevoSelloSAT.style.display = 'block';
			}
		});
	}

	let deleteSelloSATParams = null;

	function mostrarModalConfirmacionSelloSAT(idSello, idEmpresa, event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		deleteSelloSATParams = {
			idSello,
			idEmpresa,
		};
		const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteSelloSAT'));
		modal.show();
	}

	if (btnConfirmDeleteSelloSAT) {
		btnConfirmDeleteSelloSAT.addEventListener('click', async function () {
			if (!hasWritePermissions()) {
				alert('No tienes permisos para realizar esta acción.');
				return;
			}
			if (!deleteSelloSATParams) {
				return;
			}
			await eliminarSelloSAT(deleteSelloSATParams.idSello, deleteSelloSATParams.idEmpresa);
			const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteSelloSAT'));
			modal.hide();
			deleteSelloSATParams = null;
		});
	}

	// --- Eliminar sello SAT con modal ---
	async function eliminarSelloSAT(idSello, idEmpresa) {
		const formData = new FormData();
		formData.append('accion', 'eliminarSelloSat');
		formData.append('id', idSello);
		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				// Actualizar la tabla de sellos SAT
				fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
					.then((res) => res.json())
					.then((data) => {
						const detalle = data.data;
						const tbody = document.querySelector('#modalSellosSAT tbody');
						tbody.innerHTML = '';
						if (detalle.sellosSat && detalle.sellosSat.length) {
							detalle.sellosSat.forEach((row) => {
								tbody.innerHTML += `<tr>
                                        <td>${row.tipo || ''}</td>
                                        <td>${row.fechaCreacion || ''}</td>
                                        <td class='text-center'>
                                            <div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">
                                                ${
													row.documento
														? `<a href='${row.documento}' class='btn btn-outline-primary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center' download aria-label='Descargar sello SAT' title='Descargar sello SAT'><i class='bx bx-download'></i></a>`
														: '<span class="text-muted small">Sin archivo</span>'
												}
                                                <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-sello-sat" data-id="${
													row.id
												}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>`;
							});
						} else {
							tbody.innerHTML = '<tr><td colspan="3" class="text-center">Sin sellos</td></tr>';
						}
						applyPermissionVisibility();
					});
			} else {
				alert('Error al eliminar el sello SAT.');
			}
		} catch (err) {
			alert('Error de conexión al eliminar el sello SAT.');
		}
	}

	// --- Delegación de evento para eliminar sello SAT usando el modal ---
	const sellosTableBody = document.querySelector('#modalSellosSAT tbody');
	if (sellosTableBody) {
		sellosTableBody.addEventListener('click', function (e) {
			if (e.target.closest('.btn-eliminar-sello-sat')) {
				const btn = e.target.closest('.btn-eliminar-sello-sat');
				const idSello = btn.getAttribute('data-id');
				const idEmpresa = document.getElementById('inputIdEmpresaSelloSAT').value;
				if (!idSello) return;
				mostrarModalConfirmacionSelloSAT(idSello, idEmpresa, e);
			}
		});
	}

	// Cuando se abra el modal, poner el idEmpresa en el input oculto
	const modalSellosSAT = document.getElementById('modalSellosSAT');
	if (modalSellosSAT) {
		modalSellosSAT.addEventListener('show.bs.modal', function (e) {
			const btn = e.relatedTarget;
			let idEmpresa = '';
			if (btn && btn.getAttribute('data-id')) {
				idEmpresa = btn.getAttribute('data-id');
			}
			document.getElementById('inputIdEmpresaSelloSAT').value = idEmpresa;
		});
	}
});
