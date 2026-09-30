// Acción de ver carátula bancaria
document.addEventListener('click', function (e) {
	const btn = e.target.closest('.btn-ver-caratula-bancaria');
	if (btn) {
		const url = decodeURIComponent(btn.getAttribute('data-url') || '');
		if (url) {
			const iframe = document.getElementById('iframePreviewCaratulaBancaria');
			if (iframe) iframe.src = url;
			const modalEl = document.getElementById('modalPreviewCaratulaBancaria');
			modalEl.style.zIndex = 1080;
			// Abrir sin backdrop para evitar superposición incorrecta
			const modal = new bootstrap.Modal(modalEl, { backdrop: false });
			modal.show();
		}
	}
});
document.addEventListener('DOMContentLoaded', function () {
	const modalCaratulasBancarias = document.getElementById('modalCaratulasBancarias');
	const modalConfirmDelete = document.getElementById('modalConfirmDeleteCaratula');
	// Restaurar z-index al cerrar el modal de vista previa
	const modalPreview = document.getElementById('modalPreviewCaratulaBancaria');
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
	const btnNuevaCaratula = document.getElementById('btnNuevaCaratula');
	const btnCancelarCaratula = document.getElementById('btnCancelarCaratula');
	const formNuevaCaratula = document.getElementById('formNuevaCaratula');
	const alertNuevaCaratula = document.getElementById('alertNuevaCaratula');
	const inputIdEmpresa = document.getElementById('inputIdEmpresaCaratula');
	const btnConfirmDelete = document.getElementById('btnConfirmDeleteCaratula');
	let deleteParams = null;
	const originalNuevaCaratulaDisplay = btnNuevaCaratula
		? btnNuevaCaratula.style.display || 'inline-block'
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
		if (btnNuevaCaratula) {
			btnNuevaCaratula.classList.toggle('d-none', !canWrite);
			btnNuevaCaratula.style.display = canWrite ? originalNuevaCaratulaDisplay : 'none';
		}
		if (!canWrite && formNuevaCaratula) {
			formNuevaCaratula.style.display = 'none';
		}
		if (formNuevaCaratula) {
			const submitBtn = formNuevaCaratula.querySelector('button[type="submit"]');
			if (submitBtn) {
				submitBtn.disabled = !canWrite;
				submitBtn.classList.toggle('disabled', !canWrite);
			}
		}
		if (modalCaratulasBancarias) {
			modalCaratulasBancarias.querySelectorAll('[data-requires="write"]').forEach((element) => {
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
		if (!formNuevaCaratula) return;
		let valorActual = empresaId;
		if (!valorActual && inputIdEmpresa) {
			valorActual = inputIdEmpresa.value;
		}
		formNuevaCaratula.style.display = 'none';
		if (reset) {
			formNuevaCaratula.reset();
			if (inputIdEmpresa) {
				inputIdEmpresa.value = valorActual || '';
			}
		}
	}

	function mostrarFormulario(event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		if (!formNuevaCaratula) return;
		formNuevaCaratula.style.display = 'block';
	}

	function mostrarAlerta(tipo, mensaje) {
		if (!alertNuevaCaratula) return;
		alertNuevaCaratula.className = `alert alert-${tipo} mt-2`;
		alertNuevaCaratula.textContent = mensaje;
		alertNuevaCaratula.style.display = 'block';
	}

	function ocultarAlerta() {
		if (!alertNuevaCaratula) return;
		alertNuevaCaratula.style.display = 'none';
	}

	function refrescarCaratulas(empresaId) {
		if (!empresaId || typeof cargarDetalleEmpresa !== 'function') return;
		cargarDetalleEmpresa(
			empresaId,
			(detalle) => {
				if (typeof populateCaratulasBancariasModal === 'function') {
					populateCaratulasBancariasModal(
						detalle || {
							caratulas: [],
						}
					);
				}
			},
			{ forceReload: true }
		);
	}

	if (modalCaratulasBancarias) {
		modalCaratulasBancarias.addEventListener('show.bs.modal', function (event) {
			ocultarFormulario(true);
			ocultarAlerta();
			const trigger = event.relatedTarget;
			const empresaId = trigger && trigger.getAttribute('data-id') ? trigger.getAttribute('data-id') : '';
			if (inputIdEmpresa) {
				inputIdEmpresa.value = empresaId;
			}
		});
	}

	if (btnNuevaCaratula) {
		btnNuevaCaratula.addEventListener('click', function (event) {
			mostrarFormulario(event);
			if (inputIdEmpresa && !inputIdEmpresa.value) {
				const empresaId = this.getAttribute('data-id') || '';
				inputIdEmpresa.value = empresaId;
			}
		});
	}

	if (btnCancelarCaratula) {
		btnCancelarCaratula.addEventListener('click', function (event) {
			if (!ensureWritePermission(event)) {
				return;
			}
			ocultarFormulario(true);
			ocultarAlerta();
		});
	}

	if (formNuevaCaratula) {
		formNuevaCaratula.addEventListener('submit', async function (e) {
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

				formData.append('accion', 'agregarCaratula');
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
					mostrarAlerta('success', 'Carátula bancaria guardada correctamente.');
					ocultarFormulario(true, empresaId);
					setTimeout(() => ocultarAlerta(), 3000);
					refrescarCaratulas(empresaId);
				} else {
					throw new Error(data.mensaje || 'Error al guardar la carátula bancaria');
				}
			} catch (error) {
				console.error('Error:', error);
				mostrarAlerta('danger', 'Error al guardar la carátula bancaria: ' + error.message);
			}
		});
	}

	const caratulasTbody = modalCaratulasBancarias ? modalCaratulasBancarias.querySelector('tbody') : null;
	if (caratulasTbody) {
		caratulasTbody.addEventListener('click', function (event) {
			const btn = event.target.closest('.btn-eliminar-caratula-bancaria');
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
				formData.append('accion', 'eliminarCaratula');
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
					mostrarAlerta('success', 'Carátula bancaria eliminada correctamente.');
					setTimeout(() => ocultarAlerta(), 3000);
					refrescarCaratulas(deleteParams.empresaId);
					deleteParams = null;
				} else {
					throw new Error(data.mensaje || 'Error al eliminar la carátula bancaria');
				}
			} catch (error) {
				console.error('Error:', error);
				mostrarAlerta('danger', 'Error al eliminar la carátula bancaria: ' + error.message);
			}
		});
	}
});
