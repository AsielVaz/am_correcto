document.addEventListener('DOMContentLoaded', function () {
	const btnNuevaCuenta = document.getElementById('btnNuevaCuenta');
	const formNuevaCuenta = document.getElementById('formNuevaCuenta');
	const alertNuevaCuenta = document.getElementById('alertNuevaCuenta');
	const btnCancelarCuenta = document.getElementById('btnCancelarCuenta');
	const originalNuevaCuentaDisplay = btnNuevaCuenta ? btnNuevaCuenta.style.display || 'inline-block' : 'inline-block';

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
		if (btnNuevaCuenta) {
			btnNuevaCuenta.classList.toggle('d-none', !canWrite);
			btnNuevaCuenta.style.display = canWrite ? originalNuevaCuentaDisplay : 'none';
		}
		if (!canWrite && formNuevaCuenta) {
			formNuevaCuenta.style.display = 'none';
		}
		if (formNuevaCuenta) {
			const submitBtn = formNuevaCuenta.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		document.querySelectorAll('#modalCuentas [data-requires="write"]').forEach((element) => {
			if (!element.dataset.originalDisplay) {
				element.dataset.originalDisplay = getComputedStyle(element).display || '';
			}
			element.classList.toggle('d-none', !canWrite);
			element.style.display = canWrite ? element.dataset.originalDisplay || '' : 'none';
		});
	};

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	btnNuevaCuenta.addEventListener('click', function () {
		if (!ensureWritePermission()) {
			return;
		}
		formNuevaCuenta.style.display = 'block';
		btnNuevaCuenta.style.display = 'none';
	});

	btnCancelarCuenta.addEventListener('click', function () {
		formNuevaCuenta.style.display = 'none';
		btnNuevaCuenta.style.display = 'inline-block';
		formNuevaCuenta.reset();
		alertNuevaCuenta.style.display = 'none';
	});

	// Guardar nueva cuenta
	formNuevaCuenta.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		const formData = new FormData(formNuevaCuenta);
		formData.append('accion', 'nuevaCuenta');

		const idEmpresa = document.getElementById('inputIdEmpresaCuenta').value;
		let razonSocial = '';
		if (window.empresasCache && window.empresasCache[idEmpresa]) {
			razonSocial = window.empresasCache[idEmpresa].razon;
		}

		formData.append('empresa', razonSocial);
		formData.set('id', idEmpresa);
		formData.append('estatus', 'activa');

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				alertNuevaCuenta.textContent = 'Cuenta agregada correctamente.';
				alertNuevaCuenta.className = 'alert alert-success mt-2';
				alertNuevaCuenta.style.display = 'block';
				formNuevaCuenta.reset();

				setTimeout(() => {
					alertNuevaCuenta.style.display = 'none';
					formNuevaCuenta.style.display = 'none';
					btnNuevaCuenta.style.display = 'inline-block';

					// Volver a pedir las cuentas actualizadas
					fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
						.then((res) => res.json())
						.then((data) => {
							const detalle = data.data;
							const tbody = document.getElementById('tbody-cuentas-bancarias');
							tbody.innerHTML = '';
							if (detalle.cuentas && detalle.cuentas.length) {
								detalle.cuentas.forEach((cuenta) => {
									const tr = document.createElement('tr');
									const banco = cuenta.banco || '';
									const numero = cuenta.numero_cuenta || cuenta.numero || cuenta.cuenta || '';
									const clabe = cuenta.clabe_interbancaria || cuenta.clave || '';
									const moneda = cuenta.moneda || '';
									tr.innerHTML = `
										<td>
											<span>${banco}</span>
											<button type="button" class="btn btn-link btn-sm btn-copy-campo ms-1 p-0 align-middle" data-copy="${banco}" title="Copiar banco"><i class="bx bx-copy align-middle"></i></button>
										</td>
										<td>
											<span>${numero}</span>
											<button type="button" class="btn btn-link btn-sm btn-copy-campo ms-1 p-0 align-middle" data-copy="${numero}" title="Copiar número de cuenta"><i class="bx bx-copy align-middle"></i></button>
										</td>
										<td>
											<span>${clabe}</span>
											<button type="button" class="btn btn-link btn-sm btn-copy-campo ms-1 p-0 align-middle" data-copy="${clabe}" title="Copiar CLABE"><i class="bx bx-copy align-middle"></i></button>
										</td>
										<td>
											<span>${moneda}</span>
											<button type="button" class="btn btn-link btn-sm btn-copy-campo ms-1 p-0 align-middle" data-copy="${moneda}" title="Copiar moneda"><i class="bx bx-copy align-middle"></i></button>
										</td>
										<td class="text-center">
											<div class="d-inline-flex align-items-center justify-content-center gap-2">
												<button type="button" class="btn btn-outline-secondary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-copy-fila" title="Copiar toda la fila" aria-label="Copiar fila">
													<i class="bx bx-copy align-middle"></i>
												</button>
												<button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-cuenta" data-id="${cuenta.id}" data-requires="write" title="Eliminar" aria-label="Eliminar">
													<i class="bx bx-trash align-middle"></i>
												</button>
											</div>
										</td>
									`;
									tbody.appendChild(tr);
								});
							} else {
								tbody.innerHTML = '<tr><td colspan="5" class="text-center">Sin cuentas</td></tr>';
							}
						});
				}, 2000);
			} else {
				alertNuevaCuenta.textContent = 'Error al agregar cuenta.';
				alertNuevaCuenta.className = 'alert alert-danger mt-2';
				alertNuevaCuenta.style.display = 'block';
			}
		} catch (err) {
			console.error('Error al guardar cuenta:', err);
			alertNuevaCuenta.textContent = 'Error de conexión.';
			alertNuevaCuenta.className = 'alert alert-danger mt-2';
			alertNuevaCuenta.style.display = 'block';
		}
	});

	// Cuando se abra el modal, poner el idEmpresa en el input oculto
	document.getElementById('modalCuentas').addEventListener('show.bs.modal', function (e) {
		const btn = e.relatedTarget;
		let idEmpresa = '';
		if (btn && btn.getAttribute('data-id')) {
			idEmpresa = btn.getAttribute('data-id');
		} else if (window.empresasCache && typeof window.empresaIdSeleccionada !== 'undefined') {
			idEmpresa = window.empresaIdSeleccionada;
		}
		document.getElementById('inputIdEmpresaCuenta').value = idEmpresa;
	});
	// Delegación de evento para eliminar cuenta usando modal de confirmación
	let deleteCuentaParams = null;

	function mostrarModalConfirmacionCuenta(idCuenta, idEmpresa) {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		deleteCuentaParams = {
			idCuenta,
			idEmpresa,
		};
		const modal = new bootstrap.Modal(document.getElementById('modalConfirmDeleteCuenta'));
		modal.show();
	}

	document.getElementById('tbody-cuentas-bancarias').addEventListener('click', function (e) {
		// Copiar campo individual
		if (e.target.closest('.btn-copy-campo')) {
			const btn = e.target.closest('.btn-copy-campo');
			const valor = btn.getAttribute('data-copy') || '';
			if (valor) {
				navigator.clipboard.writeText(valor);
				btn.classList.add('text-success');
				setTimeout(() => btn.classList.remove('text-success'), 800);
			}
			return;
		}
		// Copiar toda la fila
		if (e.target.closest('.btn-copy-fila')) {
			const tr = e.target.closest('tr');
			if (tr) {
				const ths = ['Banco', 'Número de cuenta', 'CLABE interbancaria', 'Moneda'];
				const tds = Array.from(tr.querySelectorAll('td'));
				let texto = '';
				// Obtener nombre de la empresa si está disponible en window.empresasCache
				let idEmpresa = document.getElementById('inputIdEmpresaCuenta')?.value || '';
				let razon = '';
				if (window.empresasCache && idEmpresa && window.empresasCache[idEmpresa]) {
					razon = window.empresasCache[idEmpresa].razon || '';
				}
				if (razon) {
					texto += razon + '\n';
				}
				for (let i = 0; i < 4; i++) {
					const label = ths[i];
					const valor =
						tds[i]?.querySelector('span')?.textContent?.trim() || tds[i]?.textContent?.trim() || '';
					texto += `${label}: ${valor}\n`;
				}
				navigator.clipboard.writeText(texto.trim());
				const btn = e.target.closest('.btn-copy-fila');
				btn.classList.add('text-success');
				setTimeout(() => btn.classList.remove('text-success'), 800);
			}
			return;
		}
		// Eliminar cuenta
		if (e.target.closest('.btn-eliminar-cuenta')) {
			if (!ensureWritePermission(e)) {
				return;
			}
			const btn = e.target.closest('.btn-eliminar-cuenta');
			const idCuenta = btn.getAttribute('data-id');
			const idEmpresa = document.getElementById('inputIdEmpresaCuenta').value;
			if (!idCuenta) return;
			mostrarModalConfirmacionCuenta(idCuenta, idEmpresa);
		}
	});

	document.getElementById('btnConfirmDeleteCuenta').addEventListener('click', async function () {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		if (!deleteCuentaParams) return;
		const { idCuenta, idEmpresa } = deleteCuentaParams;
		const formData = new FormData();
		formData.append('accion', 'eliminarCuenta');
		formData.append('id', idCuenta);
		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				// Actualizar la tabla de cuentas
				fetch(`../../api/routes/apiEmpresaDetalle.php?id=${idEmpresa}`)
					.then((res) => res.json())
					.then((data) => {
						const detalle = data.data;
						const tbody = document.getElementById('tbody-cuentas-bancarias');
						tbody.innerHTML = '';
						if (detalle.cuentas && detalle.cuentas.length) {
							detalle.cuentas.forEach((cuenta) => {
								const tr = document.createElement('tr');
								tr.innerHTML = `
                                        <td>${cuenta.banco}</td>
                                        <td>${cuenta.numero_cuenta || cuenta.numero || cuenta.cuenta}</td>
                                        <td>${cuenta.clabe_interbancaria || cuenta.clave}</td>
                                        <td>${cuenta.moneda}</td>
                                        <td class="text-center">
                                            <div class="d-inline-flex align-items-center justify-content-center gap-2">
                                                <button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-cuenta" data-id="${
													cuenta.id
												}" data-requires="write" title="Eliminar" aria-label="Eliminar">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    `;
								tbody.appendChild(tr);
							});
						} else {
							tbody.innerHTML = '<tr><td colspan="5" class="text-center">Sin cuentas</td></tr>';
						}
					});
			} else {
				alert('Error al eliminar la cuenta bancaria.');
			}
		} catch (err) {
			alert('Error de conexión al eliminar la cuenta.');
		}
		const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDeleteCuenta'));
		modal.hide();
		deleteCuentaParams = null;
	});
});
