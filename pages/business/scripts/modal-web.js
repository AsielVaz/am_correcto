// Funciones específicas para el modal de sitio web
function showWebAlert(msg) {
	const alertDiv = document.getElementById('web-alert');
	const alertText = document.getElementById('web-alert-text');
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
	const form = document.getElementById('sitioWebForm');
	const input = document.getElementById('sitioWebInput');
	const saveButton = form ? form.querySelector('button[type="submit"]') : null;
	const validationMessage = document.getElementById('sitioWebValidationMessage');
	const postergarButton = document.getElementById('btnPostergarSitioWeb');
	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);
	let initialWebValue = '';
	const webRegex = /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/[^\s]*)?$/i;

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

	const normalizeUrl = (value) => {
		const trimmed = (value || '').trim();
		if (!trimmed) {
			return '';
		}
		if (/^https?:\/\//i.test(trimmed)) {
			return trimmed;
		}
		return `https://${trimmed}`;
	};

	const isValidWeb = (value) => {
		const trimmed = (value || '').trim();
		if (!trimmed) {
			return true;
		}
		return webRegex.test(trimmed);
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
		const changed = currentValue !== initialWebValue;
		const isValid = isValidWeb(currentValue);
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

	window.setSitioWebInitialValue = (value) => {
		if (typeof value !== 'string') {
			value = value === undefined || value === null ? '' : String(value);
		}
		initialWebValue = value.trim();
		if (input) {
			input.value = initialWebValue;
		}
		if (validationMessage) {
			validationMessage.style.display = 'none';
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
		const sitioWeb = input ? input.value.trim() : '';
		const isValid = isValidWeb(sitioWeb);

		if (!id) return;

		if (!isValid) {
			if (validationMessage) {
				validationMessage.style.display = 'block';
			}
			alert('Ingresa un sitio web válido (ej. https://www.empresa.com).');
			return;
		}

		const sanitizedSitioWeb = normalizeUrl(sitioWeb);

		const formData = new FormData();
		formData.append('accion', 'espModificarSitioWeb');
		formData.append('id', id);
		formData.append('web', sanitizedSitioWeb);

		try {
			await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});

			showWebAlert('¡Sitio web guardado!');
			window.setSitioWebInitialValue(sanitizedSitioWeb);

			if (typeof loadEmpresas === 'function') {
				await loadEmpresas();
			}
			if (window.PostergarDocumentoManager) {
				await window.PostergarDocumentoManager.load('sitioWeb', id, true);
			}
		} catch (err) {
			console.error('Error al guardar sitio web:', err);
			showWebAlert('Error al guardar');
		}
	});

	const registerPostergarSitioWeb = () => {
		if (!window.PostergarDocumentoManager) {
			return;
		}
		window.PostergarDocumentoManager.init({
			key: 'sitioWeb',
			campo: 'sitioWeb',
			textareaId: 'sitioWebPostergarObservaciones',
			counterId: 'sitioWebPostergarCounter',
			buttonId: 'btnPostergarSitioWeb',
			badgeId: 'sitioWebPostergarBadge',
			remainingId: 'sitioWebPostergarRemaining',
			historialId: 'sitioWebPostergarHistorial',
			messageId: 'sitioWebPostergarStatus',
			infoId: 'sitioWebPostergarInfo',
			sectionId: 'sitioWebPostergarSection',
		});
	};

	if (window.PostergarDocumentoManager) {
		registerPostergarSitioWeb();
	} else {
		window.addEventListener('postergar:manager-ready', registerPostergarSitioWeb, {
			once: true,
		});
	}
});
