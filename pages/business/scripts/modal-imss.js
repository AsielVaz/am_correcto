document.addEventListener('DOMContentLoaded', function () {
	const modalIMSS = document.getElementById('modalIMSS');
	const btnNuevoImss = document.getElementById('btnNuevoImss');
	const btnCancelarImss = document.getElementById('btnCancelarImss');
	const formNuevoImss = document.getElementById('formNuevoImss');
	const alertNuevoImss = document.getElementById('alertNuevoImss');
	const inputIdEmpresa = document.getElementById('inputIdEmpresaImss');
	const modalConfirmDelete = document.getElementById('modalConfirmDeleteImss');
	const btnConfirmDelete = document.getElementById('btnConfirmDeleteImss');
	let deleteParams = null;
	const originalNuevoImssDisplay = btnNuevoImss ? btnNuevoImss.style.display || 'inline-block' : 'inline-block';

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
		if (btnNuevoImss) {
			btnNuevoImss.classList.toggle('d-none', !canWrite);
			btnNuevoImss.style.display = canWrite ? originalNuevoImssDisplay : 'none';
		}
		if (!canWrite && formNuevoImss) {
			formNuevoImss.style.display = 'none';
		}
		if (formNuevoImss) {
			const submitBtn = formNuevoImss.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		if (modalIMSS) {
			modalIMSS.querySelectorAll('[data-requires="write"]').forEach((element) => {
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
		if (!formNuevoImss) return;
		let valorActual = empresaId;
		if (!valorActual && inputIdEmpresa) {
			valorActual = inputIdEmpresa.value;
		}
		formNuevoImss.style.display = 'none';
		if (reset) {
			formNuevoImss.reset();
			if (inputIdEmpresa) {
				inputIdEmpresa.value = valorActual || '';
			}
		}
	}

	function mostrarFormulario(event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		if (!formNuevoImss) return;
		formNuevoImss.style.display = 'block';
	}

	function mostrarAlerta(tipo, mensaje) {
		if (!alertNuevoImss) return;
		alertNuevoImss.className = `alert alert-${tipo} mt-2`;
		alertNuevoImss.textContent = mensaje;
		alertNuevoImss.style.display = 'block';
	}

	function ocultarAlerta() {
		if (!alertNuevoImss) return;
		alertNuevoImss.style.display = 'none';
	}

	function refrescarImss(empresaId) {
		if (!empresaId || typeof cargarDetalleEmpresa !== 'function') return;
		cargarDetalleEmpresa(
			empresaId,
			(detalle) => {
				if (typeof populateIMSSModal === 'function') {
					populateIMSSModal(
						detalle || {
							imss: [],
						}
					);
				}
			},
			{
				forceReload: true,
			}
		);
		if (typeof loadEmpresas === 'function') {
			loadEmpresas();
		}
	}

	if (modalIMSS) {
		modalIMSS.addEventListener('show.bs.modal', function (event) {
			ocultarFormulario(true);
			ocultarAlerta();
			const trigger = event.relatedTarget;
			const empresaId = trigger && trigger.getAttribute('data-id') ? trigger.getAttribute('data-id') : '';
			if (inputIdEmpresa) {
				inputIdEmpresa.value = empresaId;
			}
		});
	}

	if (btnNuevoImss) {
		btnNuevoImss.addEventListener('click', function (event) {
			mostrarFormulario(event);
			if (inputIdEmpresa && !inputIdEmpresa.value) {
				const empresaId = this.getAttribute('data-id') || '';
				inputIdEmpresa.value = empresaId;
			}
		});
	}

	if (btnCancelarImss) {
		btnCancelarImss.addEventListener('click', function (event) {
			if (!ensureWritePermission(event)) {
				return;
			}
			ocultarFormulario(true);
			ocultarAlerta();
		});
	}

	if (formNuevoImss) {
		formNuevoImss.addEventListener('submit', async function (e) {
			e.preventDefault();
			if (!ensureWritePermission(e)) {
				return;
			}

			try {
				const formData = new FormData(this);
				const empresaId = inputIdEmpresa ? inputIdEmpresa.value : '';
				if (!empresaId) {
					mostrarAlerta('danger', 'No se pudo identificar la empresa.');
					return;
				}

				const tipo = formData.get('tipo');
				const archivo = formData.get('documento');
				if (!tipo) {
					mostrarAlerta('danger', 'Selecciona el tipo de archivo (KEY o CSD).');
					return;
				}
				if (!(archivo instanceof File) || archivo.size === 0) {
					mostrarAlerta('danger', 'Selecciona un archivo válido (.key o .pfx).');
					return;
				}

				formData.delete('documento');
				formData.append('accion', 'agregarImss');
				formData.set('id', empresaId);

				if (tipo === 'KEY') {
					formData.append('comprobante', archivo);
				} else if (tipo === 'CSD') {
					formData.append('archivo', archivo);
				} else {
					mostrarAlerta('danger', 'Tipo de archivo no soportado por el sistema.');
					return;
				}

				const response = await fetch('../../api/routes/apiEmpresa.php', {
					method: 'POST',
					body: formData,
				});

				if (!response.ok) {
					throw new Error('Error en la respuesta del servidor');
				}

				const data = await response.json();

				if (data.estatus === 'Exito') {
					mostrarAlerta('success', 'Documento IMSS guardado correctamente.');
					ocultarFormulario(true, empresaId);
					setTimeout(() => ocultarAlerta(), 3000);
					refrescarImss(empresaId);
				} else {
					throw new Error(data.mensaje || 'Error al guardar el documento IMSS');
				}
			} catch (error) {
				console.error('Error:', error);
				mostrarAlerta('danger', 'Error al guardar el documento IMSS: ' + error.message);
			}
		});
	}

	const imssTbody = modalIMSS ? modalIMSS.querySelector('tbody') : null;
	if (imssTbody) {
		imssTbody.addEventListener('click', function (event) {
			const btn = event.target.closest('.btn-eliminar-imss');
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

	if (btnConfirmDelete && modalConfirmDelete) {
		btnConfirmDelete.addEventListener('click', async function () {
			if (!hasWritePermissions()) {
				alert('No tienes permisos para realizar esta acción.');
				return;
			}
			if (!deleteParams) return;

			try {
				const formData = new FormData();
				formData.append('accion', 'eliminarImss');
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
					mostrarAlerta('success', 'Documento IMSS eliminado correctamente.');
					setTimeout(() => ocultarAlerta(), 3000);
					refrescarImss(deleteParams.empresaId);
					deleteParams = null;
				} else {
					throw new Error(data.mensaje || 'Error al eliminar el documento IMSS');
				}
			} catch (error) {
				console.error('Error:', error);
				mostrarAlerta('danger', 'Error al eliminar el documento IMSS: ' + error.message);
			}
		});
	}
});
