document.addEventListener('DOMContentLoaded', function () {
	const editContainer = document.getElementById('finDominioEditContainer');
	const editButton = document.getElementById('btnEditarFinDominio');
	const form = document.getElementById('finDominioForm');
	const dateInput = document.getElementById('finDominioInput');
	const cancelButton = document.getElementById('btnCancelarFinDominio');
	const messageEl = document.getElementById('finDominioMessage');
	const fechaLabel = document.getElementById('finDominioFecha');

	const canWrite = () => !!(window.appPermissions && window.appPermissions.canWrite);

	const setMessage = (text, type) => {
		if (!messageEl) {
			return;
		}
		messageEl.textContent = text || '';
		messageEl.className = 'small mt-2';
		if (!text) {
			return;
		}
		if (type === 'success') {
			messageEl.classList.add('text-success');
		} else if (type === 'error') {
			messageEl.classList.add('text-danger');
		} else {
			messageEl.classList.add('text-muted');
		}
	};

	const normalizeToInputDate = (value) => {
		if (!value) {
			return '';
		}
		if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
			return value;
		}
		if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
			const parts = value.split('/');
			return `${parts[2]}-${parts[1]}-${parts[0]}`;
		}
		return '';
	};

	const hideForm = (reset = false) => {
		if (form) {
			form.style.display = 'none';
			if (reset) {
				form.reset();
			}
		}
		if (editButton) {
			editButton.classList.remove('d-none');
		}
	};

	const showForm = () => {
		if (!form) {
			return;
		}
		form.style.display = 'block';
		if (editButton) {
			editButton.classList.add('d-none');
		}
		if (dateInput && !dateInput.value && editButton && editButton.dataset.currentValue) {
			dateInput.value = normalizeToInputDate(editButton.dataset.currentValue);
		}
		if (dateInput) {
			dateInput.focus();
		}
	};

	const updatePermissions = () => {
		if (!editContainer) {
			return;
		}
		if (!editContainer.dataset.originalDisplay) {
			editContainer.dataset.originalDisplay = editContainer.style.display || '';
		}
		if (canWrite()) {
			editContainer.style.display = editContainer.dataset.originalDisplay;
		} else {
			editContainer.style.display = 'none';
			hideForm(true);
		}
	};

	const handlePermissionsUpdated = () => updatePermissions();

	window.updateFinDominioFormData = (empresa) => {
		if (form && empresa && typeof empresa.id !== 'undefined') {
			form.setAttribute('data-id', empresa.id);
		}
		const valor = empresa && empresa.finDominio ? empresa.finDominio : '';
		if (dateInput) {
			dateInput.value = normalizeToInputDate(valor);
		}
		if (editButton) {
			editButton.dataset.currentValue = valor;
		}
		setMessage('');
		hideForm(false);
	};

	window.refreshFinDominioPermissions = updatePermissions;

	if (editButton) {
		editButton.addEventListener('click', function (event) {
			if (!canWrite()) {
				event.preventDefault();
				alert('No tienes permisos para realizar esta acción.');
				return;
			}
			event.preventDefault();
			showForm();
		});
	}

	if (cancelButton) {
		cancelButton.addEventListener('click', function (event) {
			event.preventDefault();
			hideForm(true);
			setMessage('');
		});
	}

	if (form) {
		form.addEventListener('submit', async function (event) {
			event.preventDefault();
			if (!canWrite()) {
				alert('No tienes permisos para realizar esta acción.');
				return;
			}

			const empresaId = form.getAttribute('data-id') || window.ultimaEmpresaId;
			const fecha = dateInput ? dateInput.value : '';

			if (!empresaId) {
				setMessage('No se encontró la empresa seleccionada.', 'error');
				return;
			}

			if (!fecha) {
				setMessage('Selecciona una fecha válida.', 'error');
				return;
			}

			const formData = new FormData();
			formData.append('accion', 'espModificarFinDominio');
			formData.append('id', empresaId);
			formData.append('finDominio', fecha);

			setMessage('Guardando fecha…', 'info');
			try {
				const response = await fetch('../../api/routes/apiEmpresa.php', {
					method: 'POST',
					body: formData,
				});

				const payload = await response.json().catch(() => null);

				if (!response.ok || !payload || payload.estatus !== 'Exito') {
					const msg =
						payload && payload.mensaje ? payload.mensaje : 'No se pudo actualizar el fin de dominio.';
					throw new Error(msg);
				}

				setMessage('Fin de dominio actualizado correctamente.', 'success');

				const actualizarVista = (valor) => {
					if (typeof setupFinDominioModal === 'function') {
						setupFinDominioModal({
							id: empresaId,
							finDominio: valor,
						});
					}
				};

				actualizarVista(fecha);

				if (typeof cargarDetalleEmpresa === 'function') {
					try {
						await cargarDetalleEmpresa(
							empresaId,
							() => {
								actualizarVista(fecha);
							},
							{
								forceReload: true,
							}
						);
					} catch (refreshError) {
						console.error('No se pudo refrescar el detalle de la empresa:', refreshError);
						actualizarVista(fecha);
					}
				}

				if (typeof loadEmpresas === 'function') {
					try {
						await loadEmpresas();
					} catch (listError) {
						console.error('No se pudo actualizar la tabla de empresas:', listError);
					}
				}

				hideForm(false);
			} catch (error) {
				console.error('Error al actualizar el fin de dominio:', error);
				setMessage(error.message || 'Ocurrió un error al guardar la fecha.', 'error');
			}
		});
	}

	updatePermissions();
	window.addEventListener('permissions:updated', handlePermissionsUpdated);
});
