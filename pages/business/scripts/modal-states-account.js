// Acción de ver estado de cuenta
document.addEventListener('click', function (e) {
	const btn = e.target.closest('.btn-ver-estado-cuenta');
	if (btn) {
		const url = decodeURIComponent(btn.getAttribute('data-url') || '');
		if (url) {
			const iframe = document.getElementById('iframePreviewEstadoCuenta');
			if (iframe) iframe.src = url;
			const modalEl = document.getElementById('modalPreviewEstadoCuenta');
			modalEl.style.zIndex = 1080;
			// Abrir sin backdrop para evitar superposición incorrecta
			const modal = new bootstrap.Modal(modalEl, { backdrop: false });
			modal.show();
		}
	}
});
document.addEventListener('DOMContentLoaded', function () {
	const modalEstadosCuenta = document.getElementById('modalEstadosCuenta');
	const modalConfirmDelete = document.getElementById('modalConfirmDeleteEstadoCuenta');
	// Restaurar z-index al cerrar el modal de vista previa
	const modalPreview = document.getElementById('modalPreviewEstadoCuenta');
	if (modalPreview) {
		modalPreview.addEventListener('hidden.bs.modal', function () {
			modalPreview.style.zIndex = '';
			// Restaurar blur de otros modales si quedó
			document.querySelectorAll('.modal-blur').forEach((m) => m.classList.remove('modal-blur'));
		});
	}
	// Restaurar clases y estilos al cerrar el modal de confirmación de eliminación
	if (modalConfirmDelete) {
		modalConfirmDelete.addEventListener('hidden.bs.modal', function () {
			document.querySelectorAll('.modal-blur').forEach((m) => m.classList.remove('modal-blur'));
		});
	}
	const btnNuevoEstadoCuenta = document.getElementById('btnNuevoEstadoCuenta');
	const btnCancelarEstadoCuenta = document.getElementById('btnCancelarEstadoCuenta');
	const formNuevoEstadoCuenta = document.getElementById('formNuevoEstadoCuenta');
	const alertNuevoEstadoCuenta = document.getElementById('alertNuevoEstadoCuenta');
	const inputIdEmpresa = document.getElementById('inputIdEmpresaEstadoCuenta');
	const btnConfirmDelete = document.getElementById('btnConfirmDeleteEstadoCuenta');
	let deleteParams = null;
	const originalNuevoEstadoCuentaDisplay = btnNuevoEstadoCuenta
		? btnNuevoEstadoCuenta.style.display || 'inline-block'
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
		if (btnNuevoEstadoCuenta) {
			btnNuevoEstadoCuenta.classList.toggle('d-none', !canWrite);
			btnNuevoEstadoCuenta.style.display = canWrite ? originalNuevoEstadoCuentaDisplay : 'none';
		}
		if (!canWrite) {
			ocultarFormulario(true);
		}
		if (formNuevoEstadoCuenta) {
			const submitBtn = formNuevoEstadoCuenta.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		if (modalEstadosCuenta) {
			modalEstadosCuenta.querySelectorAll('[data-requires="write"]').forEach((element) => {
				if (!element.dataset.originalDisplay) {
					element.dataset.originalDisplay = getComputedStyle(element).display || '';
				}
				element.classList.toggle('d-none', !canWrite);
				element.style.display = canWrite ? element.dataset.originalDisplay || '' : 'none';
			});
		}
	};

	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	function ocultarFormulario(reset = false, empresaId = '') {
		if (!formNuevoEstadoCuenta) return;
		let valorActual = empresaId;
		if (!valorActual && inputIdEmpresa) {
			valorActual = inputIdEmpresa.value;
		}
		formNuevoEstadoCuenta.style.display = 'none';
		if (reset) {
			formNuevoEstadoCuenta.reset();
			if (inputIdEmpresa) {
				inputIdEmpresa.value = valorActual || '';
			}
		}
	}

	function mostrarFormulario(event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		if (!formNuevoEstadoCuenta) return;
		formNuevoEstadoCuenta.style.display = 'block';
	}

	function mostrarAlerta(tipo, mensaje) {
		if (!alertNuevoEstadoCuenta) return;
		alertNuevoEstadoCuenta.className = `alert alert-${tipo} mt-2`;
		alertNuevoEstadoCuenta.textContent = mensaje;
		alertNuevoEstadoCuenta.style.display = 'block';
	}

	function ocultarAlerta() {
		if (!alertNuevoEstadoCuenta) return;
		alertNuevoEstadoCuenta.style.display = 'none';
	}

	function refrescarEstadosCuenta(empresaId) {
		if (!empresaId) return;
		if (typeof cargarDetalleEmpresa === 'function') {
			cargarDetalleEmpresa(
				empresaId,
				(detalle) => {
					if (typeof populateEstadosCuentaModal === 'function') {
						populateEstadosCuentaModal(
							detalle || {
								estadosCuenta: [],
							}
						);
					}
				},
				{ forceReload: true }
			);
		} else {
			// Fallback a petición directa
			fetch(`../../api/routes/apiEmpresaDetalle.php?id=${empresaId}`)
				.then((res) => res.json())
				.then((data) => {
					const detalle = data.data || { estadosCuenta: [] };
					if (typeof populateEstadosCuentaModal === 'function') {
						populateEstadosCuentaModal(detalle);
					}
				});
		}
	}

	if (modalEstadosCuenta) {
		modalEstadosCuenta.addEventListener('show.bs.modal', function (event) {
			ocultarFormulario(true);
			ocultarAlerta();
			const trigger = event.relatedTarget;
			const empresaId = trigger && trigger.getAttribute('data-id') ? trigger.getAttribute('data-id') : '';
			if (inputIdEmpresa) {
				inputIdEmpresa.value = empresaId;
			}
		});
	}

	if (btnNuevoEstadoCuenta) {
		btnNuevoEstadoCuenta.addEventListener('click', function (event) {
			mostrarFormulario(event);
			if (inputIdEmpresa && !inputIdEmpresa.value) {
				const empresaId = this.getAttribute('data-id') || '';
				inputIdEmpresa.value = empresaId;
			}
		});
	}

	if (btnCancelarEstadoCuenta) {
		btnCancelarEstadoCuenta.addEventListener('click', function (event) {
			if (!ensureWritePermission(event)) {
				return;
			}
			ocultarFormulario(true);
			ocultarAlerta();
		});
	}

	if (formNuevoEstadoCuenta) {
		formNuevoEstadoCuenta.addEventListener('submit', async function (e) {
			e.preventDefault();
			if (!ensureWritePermission(e)) {
				return;
			}

			const empresaId = inputIdEmpresa ? inputIdEmpresa.value : '';
			if (!empresaId) {
				mostrarAlerta('danger', 'No se pudo identificar la empresa.');
				return;
			}

			try {
				const formData = new FormData(this);
				formData.append('accion', 'agregarEstadoDeCuenta');
				formData.set('id', empresaId);

				const response = await fetch('../../api/routes/apiEmpresa.php', {
					method: 'POST',
					body: formData,
				});

				if (!response.ok) {
					throw new Error('Error en la respuesta del servidor');
				}

				const data = await response.json();

				if (data.estatus === 'Exito') {
					mostrarAlerta('success', 'Estado de cuenta guardado correctamente.');
					ocultarFormulario(true, empresaId);
					setTimeout(() => ocultarAlerta(), 3000);
					refrescarEstadosCuenta(empresaId);
				} else {
					throw new Error(data.mensaje || 'Error al guardar el estado de cuenta');
				}
			} catch (error) {
				console.error('Error:', error);
				mostrarAlerta('danger', 'Error al guardar el estado de cuenta: ' + error.message);
			}
		});
	}

	const estadosCuentaTbody = modalEstadosCuenta ? modalEstadosCuenta.querySelector('tbody') : null;
	if (estadosCuentaTbody) {
		estadosCuentaTbody.addEventListener('click', function (event) {
			const btn = event.target.closest('.btn-eliminar-estados-cuenta');
			if (!btn) return;
			if (!ensureWritePermission(event)) {
				return;
			}

			const elementoId = btn.getAttribute('data-id');
			const empresaId = inputIdEmpresa ? inputIdEmpresa.value : '';
			if (!elementoId || !empresaId) return;

			deleteParams = {
				elementoId,
				empresaId,
			};
			if (modalConfirmDelete) {
				const confirmModal = new bootstrap.Modal(modalConfirmDelete);
				confirmModal.show();
			}
		});
	}
	// Si hay lógica de renderizado de filas, asegúrate de omitir la columna 'Tipo' al construir las filas

	if (btnConfirmDelete && modalConfirmDelete) {
		btnConfirmDelete.addEventListener('click', async function () {
			if (!hasWritePermissions()) {
				alert('No tienes permisos para realizar esta acción.');
				return;
			}
			if (!deleteParams) return;

			try {
				const formData = new FormData();
				formData.append('accion', 'eliminarEstadoDeCuenta');
				formData.append('id', deleteParams.elementoId);

				const response = await fetch('../../api/routes/apiEmpresa.php', {
					method: 'POST',
					body: formData,
				});

				if (!response.ok) {
					throw new Error('Error en la respuesta del servidor');
				}

				const data = await response.json();

				if (data.estatus === 'Exito') {
					const confirmInstance = bootstrap.Modal.getInstance(modalConfirmDelete);
					if (confirmInstance) {
						confirmInstance.hide();
					}
					mostrarAlerta('success', 'Estado de cuenta eliminado correctamente.');
					setTimeout(() => ocultarAlerta(), 3000);
					refrescarEstadosCuenta(deleteParams.empresaId);
					deleteParams = null;
				} else {
					throw new Error(data.mensaje || 'Error al eliminar el estado de cuenta');
				}
			} catch (error) {
				console.error('Error:', error);
				mostrarAlerta('danger', 'Error al eliminar el estado de cuenta: ' + error.message);
			}
		});
	}
});
