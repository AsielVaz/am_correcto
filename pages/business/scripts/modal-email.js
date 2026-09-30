// Funciones específicas para el modal de correo
function showCorreoAlert(msg) {
	const alertDiv = document.getElementById('correo-alert');
	const alertText = document.getElementById('correo-alert-text');
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
	const form = document.getElementById('correoContactoForm');
	const input = document.getElementById('correoContactoInput');
	const saveButton = form ? form.querySelector('button[type="submit"]') : null;
	const validationMessage = document.getElementById('correoValidationMessage');
	const postergarButton = document.getElementById('btnPostergarCorreo');
	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);
	let initialCorreoValue = '';
	const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

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
		const changed = currentValue !== initialCorreoValue;
		const isValid = currentValue === '' || correoRegex.test(currentValue);
		const canSave = canWriteNow && changed && isValid;
		saveButton.style.display = canWriteNow && changed ? 'inline-block' : 'none';
		saveButton.disabled = !canSave;
		saveButton.classList.toggle('disabled', !canSave);
		if (validationMessage) {
			if (!isValid && currentValue !== '') {
				validationMessage.textContent = 'Correo inválido';
				validationMessage.style.display = 'block';
			} else {
				validationMessage.textContent = '';
				validationMessage.style.display = 'none';
			}
		}
	};

	const applyPermissionVisibility = () => {
		const canWrite = hasWritePermissions();
		toggleElementVisibility(postergarButton, canWrite);
		updateSaveButtonVisibility();
	};

	window.setCorreoContactoInitialValue = (value) => {
		if (typeof value !== 'string') {
			value = value === undefined || value === null ? '' : String(value);
		}
		initialCorreoValue = value.trim();
		if (input) {
			input.value = initialCorreoValue;
		}
		updateSaveButtonVisibility();
	};

	if (saveButton) {
		saveButton.style.display = 'none';
	}
	if (input) {
		input.addEventListener('input', () => {
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
		const correo = input ? input.value.trim() : '';

		if (correo && !correoRegex.test(correo)) {
			alert('Ingresa un correo electrónico válido.');
			return;
		}

		if (!id) return;

		const formData = new FormData();
		formData.append('accion', 'espModificarCorreo');
		formData.append('id', id);
		formData.append('correo', correo);
		formData.append('frecuencia', '');

		try {
			await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});

			showCorreoAlert('¡Correo guardado!');
			window.setCorreoContactoInitialValue(correo);

			if (typeof loadEmpresas === 'function') {
				await loadEmpresas();
			}
			if (window.PostergarDocumentoManager) {
				await window.PostergarDocumentoManager.load('correoContacto', id, true);
			}
		} catch (err) {
			console.error('Error al guardar correo:', err);
			showCorreoAlert('Error al guardar');
		}
	});

	const registerPostergarCorreo = () => {
		if (!window.PostergarDocumentoManager) {
			return;
		}
		window.PostergarDocumentoManager.init({
			key: 'correoContacto',
			campo: 'correoContacto',
			textareaId: 'correoPostergarObservaciones',
			counterId: 'correoPostergarCounter',
			buttonId: 'btnPostergarCorreo',
			badgeId: 'correoPostergarBadge',
			remainingId: 'correoPostergarRemaining',
			historialId: 'correoPostergarHistorial',
			messageId: 'correoPostergarStatus',
			infoId: 'correoPostergarInfo',
			sectionId: 'correoPostergarSection',
		});
	};

	if (window.PostergarDocumentoManager) {
		registerPostergarCorreo();
	} else {
		window.addEventListener('postergar:manager-ready', registerPostergarCorreo, {
			once: true,
		});
	}
});
