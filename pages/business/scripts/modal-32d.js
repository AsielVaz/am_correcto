// Funciones específicas para el modal 32D
function show32DAlert(msg) {
	const alertDiv = document.getElementById('d32-alert');
	const alertText = document.getElementById('d32-alert-text');
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
	const uploadBtn = document.getElementById('btnSubir32D');
	const saveBtn = document.getElementById('btnGuardar32D');
	const input = document.getElementById('input32DModal');
	const fileLabel = document.getElementById('nombre32DArchivo');

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

		if (!empresaId || !input.files.length) return;

		const formData = new FormData();
		formData.append('accion', 'subirPdf');
		formData.append('id', empresaId);
		formData.append('pdf', input.files[0]);

		try {
			await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});

			// Actualizar el iframe con el PDF recién subido
			const cont = document.getElementById('iframe-32d-container');
			const url = URL.createObjectURL(input.files[0]);
			cont.innerHTML = `<iframe src='${url}' width='100%' height='500px'></iframe>`;
			window.addEventListener('permissions:updated', applyPermissionVisibility);
			show32DAlert('¡32D guardado!');
			fileLabel.textContent = '';
			input.value = '';
			updateSaveButtonVisibility();

			if (typeof loadEmpresas === 'function') {
				await loadEmpresas();
			}
			if (window.PostergarDocumentoManager) {
				await window.PostergarDocumentoManager.load('32d', empresaId, true);
			}
		} catch (err) {
			console.error('Error al enviar 32D:', err);
			show32DAlert('Error al guardar');
		}
	});

	const registerPostergar32D = () => {
		if (!window.PostergarDocumentoManager) {
			return;
		}
		window.PostergarDocumentoManager.init({
			key: '32d',
			campo: '32D',
			textareaId: 'd32PostergarObservaciones',
			counterId: 'd32PostergarCounter',
			buttonId: 'btnPostergar32D',
			badgeId: 'd32PostergarBadge',
			remainingId: 'd32PostergarRemaining',
			historialId: 'd32PostergarHistorial',
			messageId: 'd32PostergarStatus',
			infoId: 'd32PostergarInfo',
			sectionId: 'd32PostergarSection',
		});
	};

	if (window.PostergarDocumentoManager) {
		registerPostergar32D();
	} else {
		window.addEventListener('postergar:manager-ready', registerPostergar32D, {
			once: true,
		});
	}

	window.addEventListener('permissions:updated', updateSaveButtonVisibility);
});
