// Funciones específicas para el modal de teléfono
function showTelefonoAlert(msg) {
	const alertDiv = document.getElementById('telefono-alert');
	const alertText = document.getElementById('telefono-alert-text');
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
	const form = document.getElementById('telefonoForm');
	const input = document.getElementById('telefonoInput');
	const saveButton = form ? form.querySelector('button[type="submit"]') : null;
	const validationMessage = document.getElementById('telefonoValidationMessage');
	const postergarButton = document.getElementById('btnPostergarTelefono');
	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);
	let initialTelefonoValue = '';
	const telefonoRegex = /^\d{10}$/;

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

	const updateSaveButtonVisibility = () => {
		if (!saveButton || !input) {
			return;
		}
		const canWriteNow = hasWritePermissions();
		const currentValue = input.value.trim();
		const changed = currentValue !== initialTelefonoValue;
		const isValid = telefonoRegex.test(currentValue);
		const canSave = canWriteNow && changed && isValid;
		saveButton.style.display = canWriteNow && changed ? 'inline-block' : 'none';
		saveButton.disabled = !canSave;
		saveButton.classList.toggle('disabled', !canSave);
		if (validationMessage) {
			validationMessage.style.display = !isValid && changed ? 'block' : 'none';
		}
	};

	const applyPermissionVisibility = () => {
		const canWrite = hasWritePermissions();
		toggleElementVisibility(postergarButton, canWrite);
		updateSaveButtonVisibility();
	};

	window.setTelefonoInitialValue = (value) => {
		if (typeof value !== 'string') {
			value = value === undefined || value === null ? '' : String(value);
		}
		initialTelefonoValue = value.trim();
		if (input) {
			input.value = initialTelefonoValue;
		}
		updateSaveButtonVisibility();
	};

	if (saveButton) {
		saveButton.style.display = 'none';
	}
	if (input) {
		input.addEventListener('input', () => {
			input.value = input.value.replace(/\D/g, '').slice(0, 10);
			updateSaveButtonVisibility();
		});
	}
	if (validationMessage) {
		validationMessage.style.display = 'none';
	}
	applyPermissionVisibility();
	window.addEventListener('permissions:updated', applyPermissionVisibility);

	form.addEventListener('submit', async function (e) {
		if (!ensureWritePermission(e)) {
			return;
		}
		const id = this.getAttribute('data-id');
		const telefono = input ? input.value.trim() : '';

		if (!telefonoRegex.test(telefono)) {
			alert('Ingresa un teléfono válido de 10 dígitos.');
			return;
		}

		if (!id) return;

		const formData = new FormData();
		formData.append('accion', 'espModificarTelefono');
		formData.append('id', id);
		formData.append('telefono', telefono);

		try {
			await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});

			showTelefonoAlert('¡Teléfono guardado!');
			window.setTelefonoInitialValue(telefono);

			if (typeof loadEmpresas === 'function') {
				await loadEmpresas();
			}
			if (window.PostergarDocumentoManager) {
				await window.PostergarDocumentoManager.load('telefono', id, true);
			}
		} catch (err) {
			console.error('Error al guardar teléfono:', err);
			showTelefonoAlert('Error al guardar');
		}
	});

	const registerPostergarTelefono = () => {
		if (!window.PostergarDocumentoManager) {
			return;
		}
		window.PostergarDocumentoManager.init({
			key: 'telefono',
			campo: 'telefono',
			textareaId: 'telefonoPostergarObservaciones',
			counterId: 'telefonoPostergarCounter',
			buttonId: 'btnPostergarTelefono',
			badgeId: 'telefonoPostergarBadge',
			remainingId: 'telefonoPostergarRemaining',
			historialId: 'telefonoPostergarHistorial',
			messageId: 'telefonoPostergarStatus',
			infoId: 'telefonoPostergarInfo',
			sectionId: 'telefonoPostergarSection',
		});
	};

	if (window.PostergarDocumentoManager) {
		registerPostergarTelefono();
	} else {
		window.addEventListener('postergar:manager-ready', registerPostergarTelefono, {
			once: true,
		});
	}
});
