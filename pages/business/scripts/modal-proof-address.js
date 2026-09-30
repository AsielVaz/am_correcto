// Funciones específicas para el modal de comprobante
function showComprobanteAlert(msg) {
	const alertDiv = document.getElementById('comprobante-alert');
	const alertText = document.getElementById('comprobante-alert-text');
	alertText.textContent = msg || '¡Guardado!';
	alertDiv.style.display = 'block';
	alertDiv.classList.add('animate__animated', 'animate__fadeInDown');
	setTimeout(function () {
		alertDiv.classList.remove('animate__fadeInDown');
		alertDiv.classList.add('animate__fadeOutUp');
		setTimeout(function () {
			alertDiv.style.display = 'none';
			alertDiv.classList.remove('animate__fadeOutUp');
		}, 600);
	}, 1200);
}

document.addEventListener('DOMContentLoaded', function () {
	const uploadBtn = document.getElementById('btnSubirComprobante');
	const saveBtn = document.getElementById('btnGuardarComprobante');
	const input = document.getElementById('inputComprobanteModal');
	const fileLabel = document.getElementById('nombreComprobanteArchivo');

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

	const updateSaveButtonVisibility = () => {
		if (!saveBtn) {
			return;
		}
		const hasFile = !!(input && input.files && input.files.length);
		const canShow = hasFile && hasWritePermissions();
		saveBtn.classList.toggle('d-none', !canShow);
		saveBtn.disabled = !canShow;
	};

	const applyPermissionVisibility = () => {
		const canWrite = hasWritePermissions();
		if (uploadBtn) {
			uploadBtn.classList.toggle('d-none', !canWrite);
			uploadBtn.disabled = !canWrite;
		}
		updateSaveButtonVisibility();
	};

	uploadBtn.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		input.click();
	});

	input.addEventListener('change', function (e) {
		const nombre = e.target.files.length ? e.target.files[0].name : '';
		fileLabel.textContent = nombre;
		updateSaveButtonVisibility();
	});

	applyPermissionVisibility();

	saveBtn.addEventListener('click', async function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		const empresaId = this.getAttribute('data-id') || window.ultimaEmpresaId;

		if (!empresaId || !input.files.length) {
			return;
		}

		const formData = new FormData();
		formData.append('accion', 'comprobanteMod');
		formData.append('id', empresaId);
		formData.append('comprobante', input.files[0]);

		try {
			await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});

			// Recargar solo el comprobante en el iframe
			const cont = document.getElementById('iframe-comprobante-container');
			const url = URL.createObjectURL(input.files[0]);
			cont.innerHTML = `<iframe src='${url}' width='100%' height='500px'></iframe>`;
			window.addEventListener('permissions:updated', applyPermissionVisibility);
			showComprobanteAlert('¡Comprobante guardado!');
			fileLabel.textContent = '';
			input.value = '';
			updateSaveButtonVisibility();

			// Recargar empresas para actualizar la tabla
			if (typeof loadEmpresas === 'function') {
				await loadEmpresas();
			}
			if (window.PostergarDocumentoManager) {
				await window.PostergarDocumentoManager.load('comprobante', empresaId, true);
			}
		} catch (err) {
			console.error('Error al enviar comprobante:', err);
			showComprobanteAlert('Error al guardar');
		}
	});

	const registerPostergarComprobante = () => {
		if (!window.PostergarDocumentoManager) {
			return;
		}
		window.PostergarDocumentoManager.init({
			key: 'comprobante',
			campo: 'comprobanteDomicilio',
			textareaId: 'comprobantePostergarObservaciones',
			counterId: 'comprobantePostergarCounter',
			buttonId: 'btnPostergarComprobante',
			badgeId: 'comprobantePostergarBadge',
			remainingId: 'comprobantePostergarRemaining',
			historialId: 'comprobantePostergarHistorial',
			messageId: 'comprobantePostergarStatus',
			infoId: 'comprobantePostergarInfo',
			sectionId: 'comprobantePostergarSection',
		});
	};

	if (window.PostergarDocumentoManager) {
		registerPostergarComprobante();
	} else {
		window.addEventListener('postergar:manager-ready', registerPostergarComprobante, {
			once: true,
		});
	}

	window.addEventListener('permissions:updated', updateSaveButtonVisibility);
});
