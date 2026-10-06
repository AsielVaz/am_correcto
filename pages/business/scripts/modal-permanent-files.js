document.addEventListener('DOMContentLoaded', function () {
	// --- Declaración de variables DOM al inicio ---
	const modalDocumentos = document.getElementById('modalDocumentosPermanentes');
	const modalDialog = document.getElementById('modalDocumentosPermanentesDialog');
	const accordionDocumentos = document.getElementById('documentos-permanentes-accordion');
	const emptyStateDocumentos = document.getElementById('documentos-permanentes-empty');
	const listCol = document.getElementById('listCol');
	const previewCol = document.getElementById('previewCol');
	const btnNuevoDocumentoPermanente = document.getElementById('btnNuevoDocumentoPermanente');
	const formContainerNuevoDocumento = document.getElementById('formNuevoDocumentoPermanente');
	const formSolicitarDocumentoPermanente = document.getElementById('formSolicitarDocumentoPermanente');
	const formSubirDocumentoPermanente = document.getElementById('formSubirDocumentoPermanente');
	const formResolverArchivoFaltante = document.getElementById('formResolverArchivoFaltante');
	const alertNuevoDocumentoPermanente = document.getElementById('alertNuevoDocumentoPermanente');
	const btnCancelarDocumentoPermanente = document.getElementById('btnCancelarDocumentoPermanente');
	const inputIdEmpresa = document.getElementById('inputIdEmpresaDocumentoPermanente');
	const btnConfirmDeleteDocumentoPermanente = document.getElementById('btnConfirmDeleteDocumentoPermanente');
	const resolverArchivoFaltanteContainer = document.getElementById('resolverArchivoFaltanteContainer');
	const selectResolverArchivoFaltante = document.getElementById('selectResolverArchivoFaltante');
	const inputResolverArchivoFaltanteArchivo = document.getElementById('resolverArchivoFaltanteArchivo');
	// Pestañas y botones
	const tabsNuevoDocumento = document.getElementById('tabsNuevoDocumentoPermanente');
	const tabInicial = document.getElementById('tab-inicial');
	const tabSolicitar = document.getElementById('tab-solicitar');
	const tabSubir = document.getElementById('tab-subir');
	const tabResolver = document.getElementById('tab-resolver');
	const btnTabSolicitar = document.getElementById('tab-solicitar-tab');
	const btnTabSubir = document.getElementById('tab-subir-tab');
	const btnTabResolver = document.getElementById('tab-resolver-tab');
	const observacionesRechazoContainer = document.getElementById('observacionesRechazoContainer');
	const inputObservacionesRechazo = document.getElementById('inputObservacionesRechazo');
	const documentosApiUrl = '../../api/routes/apiEmpresa.php';

	async function solicitarAccionDocumento(formData) {
		const response = await fetch(documentosApiUrl, {
			method: 'POST',
			body: formData,
		});
		const rawResponse = await response.text();
		let data;
		try {
			data = rawResponse ? JSON.parse(rawResponse) : {};
		} catch (_) {
			throw new Error('El servidor devolvió una respuesta inválida.');
		}
		if (!response.ok || data.estatus === 'Error' || data.error) {
			throw new Error(data.mensaje || data.error || data.message || 'No fue posible completar la operación.');
		}
		return data;
	}

	function activarTab(tabId) {
		// Oculta todas las pestañas
		[tabInicial, tabSolicitar, tabSubir, tabResolver].forEach(function (tab) {
			if (tab) {
				tab.classList.remove('show', 'active');
			}
		});
		// Quita activo a todos los botones
		[btnTabSolicitar, btnTabSubir, btnTabResolver].forEach(function (btn) {
			if (btn) {
				btn.classList.remove('active');
				btn.setAttribute('aria-selected', 'false');
			}
		});
		// Activa la pestaña correspondiente
		if (tabId === 'tab-solicitar' && tabSolicitar) {
			tabSolicitar.classList.add('show', 'active');
			btnTabSolicitar.classList.add('active');
			btnTabSolicitar.setAttribute('aria-selected', 'true');
		} else if (tabId === 'tab-subir' && tabSubir) {
			tabSubir.classList.add('show', 'active');
			btnTabSubir.classList.add('active');
			btnTabSubir.setAttribute('aria-selected', 'true');
		} else if (tabId === 'tab-resolver' && tabResolver) {
			tabResolver.classList.add('show', 'active');
			btnTabResolver.classList.add('active');
			btnTabResolver.setAttribute('aria-selected', 'true');
		} else if (tabInicial) {
			tabInicial.classList.add('show', 'active');
		}
	}

	// Tabs como botones: activar visual y funcionalmente
	function activarVisualTab(tabBtn) {
		[btnTabSolicitar, btnTabSubir, btnTabResolver].forEach(function (btn) {
			if (btn) btn.classList.remove('active');
		});
		if (tabBtn) tabBtn.classList.add('active');
	}
	if (btnTabSolicitar) {
		btnTabSolicitar.addEventListener('click', function (e) {
			e.preventDefault();
			activarTab('tab-solicitar');
			activarVisualTab(btnTabSolicitar);
		});
	}
	if (btnTabSubir) {
		btnTabSubir.addEventListener('click', function (e) {
			e.preventDefault();
			activarTab('tab-subir');
			activarVisualTab(btnTabSubir);
		});
	}
	if (btnTabResolver) {
		btnTabResolver.addEventListener('click', function (e) {
			e.preventDefault();
			activarTab('tab-resolver');
			activarVisualTab(btnTabResolver);
		});
	}
	// Al abrir el modal, dejar todas inactivas y mostrar mensaje inicial
	if (modalDocumentos) {
		modalDocumentos.addEventListener('show.bs.modal', function () {
			[btnTabSolicitar, btnTabSubir, btnTabResolver].forEach(function (btn) {
				if (btn) btn.classList.remove('active');
			});
		});
	}

	// Cuando se abra el modal, mostrar la pestaña inicial
	if (modalDocumentos) {
		modalDocumentos.addEventListener('show.bs.modal', function () {
			activarTab();
		});
	}
	let deleteDocumentoParams = null;

	// --- Vista previa ---
	const previewPanel = document.getElementById('previewPanel');
	const previewFrame = document.getElementById('documentPreviewFrame');
	const previewTitle = document.getElementById('previewTitle');
	const btnPreviewClose = document.getElementById('btnPreviewClose');
	const btnPreviewLeft = document.getElementById('btnPreviewLeft');
	const btnPreviewRight = document.getElementById('btnPreviewRight');
	const previewActions = document.getElementById('previewActions');
	const btnPreviewApprove = document.getElementById('btnPreviewApprove');
	const btnPreviewReject = document.getElementById('btnPreviewReject');
	const previewApproveDefaultHtml = btnPreviewApprove ? btnPreviewApprove.innerHTML : '';
	const previewRejectDefaultHtml = btnPreviewReject ? btnPreviewReject.innerHTML : '';
	let currentPreviewDocument = null;
	let currentPreviewDocumentId = null;
	let activePreviewRowElement = null;
	let activePreviewButtonElement = null;
	let isFormVisible = false;

	function isPreviewVisible() {
		return !!(previewCol && !previewCol.classList.contains('d-none'));
	}

	function isPreviewOnRight() {
		if (!listCol || !previewCol || !listCol.parentElement || listCol.parentElement !== previewCol.parentElement) {
			return true; // por defecto consideramos derecha
		}
		const siblings = Array.from(listCol.parentElement.children);
		const idxList = siblings.indexOf(listCol);
		const idxPrev = siblings.indexOf(previewCol);
		return idxPrev > idxList;
	}

	function updatePreviewMoveButtons() {
		if (!btnPreviewLeft || !btnPreviewRight) return;
		if (!isPreviewVisible()) {
			btnPreviewLeft.style.display = 'none';
			btnPreviewRight.style.display = 'none';
			return;
		}
		const right = isPreviewOnRight();
		btnPreviewLeft.style.display = right ? '' : 'none';
		btnPreviewRight.style.display = right ? 'none' : '';
	}

	function getDocumentEntryById(id) {
		if (!id) return null;
		const entries = window.documentosPermanentesEntries;
		if (!entries || typeof entries !== 'object') return null;
		const key = String(id);
		if (Object.prototype.hasOwnProperty.call(entries, key)) {
			return entries[key];
		}
		return null;
	}

	function resetPreviewActionButtons() {
		setPreviewActionsDisabled(false);
		if (btnPreviewApprove) {
			btnPreviewApprove.innerHTML = previewApproveDefaultHtml;
			delete btnPreviewApprove.dataset.docId;
		}
		if (btnPreviewReject) {
			btnPreviewReject.innerHTML = previewRejectDefaultHtml;
			delete btnPreviewReject.dataset.docId;
		}
	}

	function setPreviewActionsDisabled(disabled) {
		const value = !!disabled;
		if (btnPreviewApprove) {
			btnPreviewApprove.disabled = value;
		}
		if (btnPreviewReject) {
			btnPreviewReject.disabled = value;
		}
	}

	function clearActivePreviewIndicators() {
		if (activePreviewRowElement) {
			activePreviewRowElement.classList.remove('border-primary', 'shadow-sm');
			activePreviewRowElement = null;
		}
		if (activePreviewButtonElement) {
			activePreviewButtonElement.classList.remove('active');
			activePreviewButtonElement.setAttribute('aria-pressed', 'false');
			activePreviewButtonElement = null;
		}
	}

	function getSafeSelector(value) {
		const str = value !== null && value !== undefined ? String(value) : '';
		if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') {
			return CSS.escape(str);
		}
		return str.replace(/["]|\\/g, '\\$&');
	}

	function setActivePreviewIndicators(docId, rowHint) {
		clearActivePreviewIndicators();
		if (!accordionDocumentos) return;
		if (docId === null || docId === undefined || docId === '') {
			return;
		}
		const safeId = getSafeSelector(docId);
		const targetRow =
			rowHint && rowHint instanceof HTMLElement
				? rowHint
				: accordionDocumentos.querySelector(`.group-item[data-doc-id="${safeId}"]`);
		if (targetRow) {
			targetRow.classList.add('border-primary', 'shadow-sm');
			activePreviewRowElement = targetRow;
		}
		const buttonTarget = targetRow
			? targetRow.querySelector(`.btn-ver-documento-permanente[data-doc-id="${safeId}"]`)
			: accordionDocumentos.querySelector(`.btn-ver-documento-permanente[data-doc-id="${safeId}"]`);
		if (buttonTarget) {
			buttonTarget.classList.add('active');
			buttonTarget.setAttribute('aria-pressed', 'true');
			activePreviewButtonElement = buttonTarget;
		}
	}

	function syncPreviewIndicators() {
		if (!accordionDocumentos) return;
		if (!currentPreviewDocumentId && currentPreviewDocumentId !== 0) {
			clearActivePreviewIndicators();
			return;
		}
		const safeId = getSafeSelector(currentPreviewDocumentId);
		const row = accordionDocumentos.querySelector(`.group-item[data-doc-id="${safeId}"]`);
		if (!row) {
			clearActivePreviewIndicators();
			return;
		}
		const collapseEl = row.closest('.accordion-collapse');
		if (collapseEl && !collapseEl.classList.contains('show')) {
			if (window.bootstrap && typeof window.bootstrap.Collapse === 'function') {
				window.bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }).show();
			} else {
				collapseEl.classList.add('show');
			}
		}
		setActivePreviewIndicators(currentPreviewDocumentId, row);
	}

	function shouldShowPreviewActions(entry) {
		if (!entry) return false;
		const estado = entry.estado;
		const currentUserId = typeof window.appCurrentUserId !== 'undefined' ? window.appCurrentUserId : null;
		const isAdmin = window.appPermissions && window.appPermissions.isAdmin;
		const notUploader =
			currentUserId !== null &&
			currentUserId !== undefined &&
			entry.idUsuarioSubio !== undefined &&
			entry.idUsuarioSubio !== null &&
			String(currentUserId) !== String(entry.idUsuarioSubio);

		// Caso 1: Estado revisión (lógica original)
		if (estado === 'revision' && entry.puedeRevisar && canMarkReviewed()) {
			return true;
		}

		// Caso 2: Admin puede cambiar entre completado <-> rechazado si no es el uploader
		if (isAdmin && notUploader && (estado === 'completado' || estado === 'rechazado')) {
			return true;
		}
		return false;
	}

	function configurePreviewActions(entry) {
		if (!previewActions) return;
		resetPreviewActionButtons();
		previewActions.classList.add('d-none');
		if (!shouldShowPreviewActions(entry)) {
			return;
		}
		previewActions.classList.remove('d-none');
		setPreviewActionsDisabled(false);
		const docId = entry.idString || entry.id || '';
		const estado = entry.estado;
		const isAdmin = window.appPermissions && window.appPermissions.isAdmin;
		const currentUserId = typeof window.appCurrentUserId !== 'undefined' ? window.appCurrentUserId : null;
		const notUploader =
			currentUserId !== null &&
			currentUserId !== undefined &&
			entry.idUsuarioSubio !== undefined &&
			entry.idUsuarioSubio !== null &&
			String(currentUserId) !== String(entry.idUsuarioSubio);

		// Por defecto, mostrar ambos botones
		let showApprove = true;
		let showReject = true;

		// Si es Admin y no es el uploader y el estado es completado o rechazado, solo mostrar el botón opuesto
		if (isAdmin && notUploader && (estado === 'completado' || estado === 'rechazado')) {
			if (estado === 'completado') {
				showApprove = false;
				showReject = true;
			} else if (estado === 'rechazado') {
				showApprove = true;
				showReject = false;
			}
		}

		if (btnPreviewApprove) {
			btnPreviewApprove.dataset.docId = docId;
			btnPreviewApprove.style.display = showApprove ? '' : 'none';
		}
		if (btnPreviewReject) {
			btnPreviewReject.dataset.docId = docId;
			btnPreviewReject.style.display = showReject ? '' : 'none';
		}
		// Ocultar campo de observaciones por defecto
		if (observacionesRechazoContainer) {
			observacionesRechazoContainer.classList.add('d-none');
			if (inputObservacionesRechazo) inputObservacionesRechazo.value = '';
		}
	}

	function setCurrentPreviewDocument(entry) {
		currentPreviewDocument = entry || null;
		if (currentPreviewDocument) {
			const rawId = currentPreviewDocument.idString || currentPreviewDocument.id;
			const normalized = rawId !== undefined && rawId !== null ? String(rawId) : '';
			currentPreviewDocumentId = normalized ? normalized : null;
		} else {
			currentPreviewDocumentId = null;
		}
		configurePreviewActions(currentPreviewDocument);
		syncPreviewIndicators();
		if (typeof updateSidePanelLayout === 'function') {
			updateSidePanelLayout();
		}
	}

	function refreshCurrentPreviewDocument() {
		if (!currentPreviewDocumentId) return;
		const updated = getDocumentEntryById(currentPreviewDocumentId);
		if (updated) {
			setCurrentPreviewDocument(updated);
			return;
		}
		const entryMap = window.documentosPermanentesEntries;
		if (!entryMap || typeof entryMap !== 'object') {
			return;
		}
		if (isPreviewVisible()) {
			hidePreview();
		} else {
			setCurrentPreviewDocument(null);
		}
	}

	function showPreview(entryOrUrl, title) {
		if (!previewPanel || !previewFrame) return;
		toggleFormDocumento(false);
		let entry = null;
		let src = '';
		let resolvedTitle = title || 'Vista previa';
		if (entryOrUrl && typeof entryOrUrl === 'object' && !Array.isArray(entryOrUrl)) {
			entry = entryOrUrl;
			src = entry.documento || entry.url || '';
			resolvedTitle = entry.descripcion || entry.title || resolvedTitle;
		} else {
			src = entryOrUrl || '';
		}
		if (!src) {
			mostrarAlerta('No se encontró el archivo para vista previa.', 'danger');
			return;
		}
		if (typeof window.resolveAppPath === 'function') {
			src = window.resolveAppPath(src);
		}
		setCurrentPreviewDocument(entry);
		previewFrame.src = src;
		if (previewTitle) previewTitle.textContent = resolvedTitle || 'Vista previa';
		updateSidePanelLayout();
	}

	function hidePreview() {
		setCurrentPreviewDocument(null);
		if (previewFrame) previewFrame.src = '';
		updateSidePanelLayout();
	}

	if (btnPreviewClose) {
		btnPreviewClose.addEventListener('click', hidePreview);
	}

	// Mover panel a izquierda/derecha intercambiando columnas
	if (btnPreviewLeft || btnPreviewRight) {
		btnPreviewLeft?.addEventListener('click', () => {
			if (!listCol || !previewCol) return;
			const parent = listCol.parentElement;
			if (parent && parent.firstElementChild !== previewCol) {
				parent.insertBefore(previewCol, listCol);
			}
			updatePreviewMoveButtons();
		});
		btnPreviewRight?.addEventListener('click', () => {
			if (!listCol || !previewCol) return;
			const parent = listCol.parentElement;
			if (parent && previewCol.nextSibling !== null) {
				parent.insertBefore(listCol, previewCol);
				parent.insertBefore(previewCol, listCol.nextSibling);
			} else if (parent) {
				parent.appendChild(previewCol);
			}
			updatePreviewMoveButtons();
		});
	}

	async function handlePreviewAction(actionType, event) {
		if (!canMarkReviewed()) {
			if (actionType === 'approve') {
				mostrarAlerta('Solo Administrador puede completar la revisión.', 'danger');
			} else {
				mostrarAlerta('Solo Administrador puede rechazar la revisión.', 'danger');
			}
			return;
		}
		const targetButton = actionType === 'approve' ? btnPreviewApprove : btnPreviewReject;
		if (!targetButton) {
			return;
		}
		const docId = targetButton.dataset.docId || currentPreviewDocumentId;
		const empresaId = getEmpresaId();
		if (!docId || !empresaId) {
			mostrarAlerta('Selecciona una empresa válida antes de actualizar el estado.', 'danger');
			return;
		}
		// Si es rechazo, validar observaciones
		if (actionType === 'reject') {
			if (!inputObservacionesRechazo || !inputObservacionesRechazo.value.trim()) {
				if (inputObservacionesRechazo) inputObservacionesRechazo.focus();
				mostrarAlerta('Debes ingresar una observación para rechazar.', 'danger');
				return;
			}
		}
		const currentUserId = typeof window.appCurrentUserId !== 'undefined' ? window.appCurrentUserId : null;
		if (currentUserId !== null && currentUserId !== undefined) {
			const entryData = getDocumentEntryById(docId) || currentPreviewDocument;
			if (entryData && entryData.idUsuarioSubio !== undefined && entryData.idUsuarioSubio !== null) {
				if (String(entryData.idUsuarioSubio) === String(currentUserId)) {
					mostrarAlerta(
						'No puedes verificar tu propio documento. Debe hacerlo otro Administrador.',
						'danger'
					);
					configurePreviewActions(entryData);
					return;
				}
			}
		}
		setPreviewActionsDisabled(true);
		let success = false;
		try {
			if (actionType === 'approve') {
				success = await marcarDocumentoPermanenteRevisado(docId, empresaId, targetButton);
			} else if (actionType === 'reject') {
				success = await marcarDocumentoPermanenteRechazado(docId, empresaId, targetButton);
			}
		} catch (err) {
			mostrarAlerta('Ocurrió un error al actualizar el estado.', 'danger');
		}
		setPreviewActionsDisabled(false);
		configurePreviewActions(currentPreviewDocument);
	}

	if (btnPreviewApprove) {
		btnPreviewApprove.addEventListener('click', (event) => {
			handlePreviewAction('approve', event);
		});
	}

	// --- Declaración de variables DOM al inicio ---
	const modalObservacionesRechazo = document.getElementById('modalObservacionesRechazo');
	const inputModalObservacionesRechazo = document.getElementById('inputModalObservacionesRechazo');
	const btnConfirmarRechazoDocumento = document.getElementById('btnConfirmarRechazoDocumento');
	let docIdRechazoPendiente = null;
	let empresaIdRechazoPendiente = null;
	let triggerBtnRechazoPendiente = null;

	if (btnPreviewReject) {
		btnPreviewReject.addEventListener('click', (event) => {
			// Guardar el id y empresa actual para el rechazo
			const docId = btnPreviewReject.dataset.docId || currentPreviewDocumentId;
			const empresaId = getEmpresaId();
			if (!docId || !empresaId) {
				mostrarAlerta('Selecciona una empresa válida antes de actualizar el estado.', 'danger');
				return;
			}
			docIdRechazoPendiente = docId;
			empresaIdRechazoPendiente = empresaId;
			triggerBtnRechazoPendiente = btnPreviewReject;
			if (inputModalObservacionesRechazo) inputModalObservacionesRechazo.value = '';
			const modal = new bootstrap.Modal(modalObservacionesRechazo);
			modal.show();
		});
	}

	if (btnConfirmarRechazoDocumento) {
		btnConfirmarRechazoDocumento.addEventListener('click', async function () {
			if (!docIdRechazoPendiente || !empresaIdRechazoPendiente) return;
			const motivo = inputModalObservacionesRechazo ? inputModalObservacionesRechazo.value.trim() : '';
			await marcarDocumentoPermanenteRechazadoModal(
				docIdRechazoPendiente,
				empresaIdRechazoPendiente,
				triggerBtnRechazoPendiente,
				motivo
			);
			const modal = bootstrap.Modal.getInstance(modalObservacionesRechazo);
			if (modal) modal.hide();
			docIdRechazoPendiente = null;
			empresaIdRechazoPendiente = null;
			triggerBtnRechazoPendiente = null;
		});
	}

	async function marcarDocumentoPermanenteRechazadoModal(idDocumento, idEmpresa, triggerButton, motivo) {
		if (!hasWritePermissions() || !canMarkReviewed()) {
			mostrarAlerta('No tienes permisos para rechazar la revisión.', 'danger');
			return false;
		}
		limpiarAlerta();
		let originalHtml = '';
		if (triggerButton) {
			originalHtml = triggerButton.innerHTML;
			triggerButton.disabled = true;
			triggerButton.innerHTML =
				'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';
		}
		let success = false;
		const formData = new FormData();
		formData.append('accion', 'marcarDocumentoPermanenteRechazado');
		formData.append('id', idDocumento);
		if (motivo) {
			formData.append('observaciones', motivo);
		}
		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();
			if (data.estatus === 'Exito') {
				mostrarAlerta(data.mensaje || 'Documento marcado como rechazado.', 'success');
				await refrescarTablaDocumentos(idEmpresa);
				const targetId = String(idDocumento);
				if (currentPreviewDocumentId && currentPreviewDocumentId === targetId) {
					refreshCurrentPreviewDocument();
				}
				success = true;
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo rechazar el documento.', 'danger');
			}
		} catch (err) {
			console.error('Error al rechazar revisión de documento permanente:', err);
			mostrarAlerta('Error de conexión al rechazar el documento.', 'danger');
		} finally {
			if (triggerButton) {
				triggerButton.disabled = false;
				triggerButton.innerHTML = originalHtml;
			}
		}
		return success;
	}

	const originalNuevoDocumentoDisplay = btnNuevoDocumentoPermanente
		? btnNuevoDocumentoPermanente.style.display || 'inline-block'
		: 'inline-block';

	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);
	const canMarkReviewed = () => {
		const permisos = window.appPermissions || {};
		return permisos.isAdmin === true; // Solo Admin
	};

	const updateSidePanelLayout = () => {
		const previewActive = currentPreviewDocumentId !== null && currentPreviewDocumentId !== undefined;
		const shouldShowColumn = previewActive || isFormVisible;
		if (previewCol) {
			previewCol.classList.toggle('d-none', !shouldShowColumn);
			previewCol.classList.toggle('d-flex', shouldShowColumn);
		}
		if (listCol) {
			listCol.classList.toggle('col-xl-7', shouldShowColumn);
		}
		if (modalDialog) {
			modalDialog.classList.toggle('modal-xl', shouldShowColumn);
		}
		if (previewPanel) {
			previewPanel.classList.toggle('d-none', !previewActive);
		}
		if (formContainerNuevoDocumento) {
			const canWrite = hasWritePermissions();
			const showForm = isFormVisible && canWrite;
			formContainerNuevoDocumento.classList.toggle('d-none', !showForm);
			formContainerNuevoDocumento.classList.toggle('d-flex', showForm);
		}
		updatePreviewMoveButtons();
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

	const getEmpresaId = () => {
		const idFromDataset = modalDocumentos?.dataset?.empresaId || '';
		const currentValue = inputIdEmpresa.value;
		if (idFromDataset) return idFromDataset;
		if (currentValue) return currentValue;
		return '';
	};

	// Flag para evitar doble fetch durante show.bs.modal
	let modalFetchInFlight = false;

	const setEmpresaId = (empresaId) => {
		if (modalDocumentos) {
			modalDocumentos.dataset.empresaId = empresaId || '';
		}
		inputIdEmpresa.value = empresaId || '';
	};

	const toggleFormDocumento = (mostrar) => {
		const puedeEscribir = hasWritePermissions();
		isFormVisible = !!(mostrar && puedeEscribir);
		if (btnNuevoDocumentoPermanente) {
			const showButton = puedeEscribir && !isFormVisible;
			btnNuevoDocumentoPermanente.classList.toggle('d-none', !showButton);
			btnNuevoDocumentoPermanente.style.display = showButton ? originalNuevoDocumentoDisplay : 'none';
		}
		updateSidePanelLayout();
	};

	toggleFormDocumento(false);

	const limpiarAlerta = () => {
		alertNuevoDocumentoPermanente.style.display = 'none';
		alertNuevoDocumentoPermanente.textContent = '';
	};

	const mostrarAlerta = (mensaje, tipo = 'success') => {
		alertNuevoDocumentoPermanente.textContent = mensaje;
		alertNuevoDocumentoPermanente.className = `alert alert-${tipo} mt-2`;
		alertNuevoDocumentoPermanente.style.display = 'block';
		// Ocultar automáticamente después de 3 segundos si es error de tipo único
		if (mensaje.includes('Solo puede haber un documento de tipo')) {
			setTimeout(() => {
				alertNuevoDocumentoPermanente.style.display = 'none';
			}, 8000);
		}
	};

	function actualizarSelectArchivoFaltante(registros) {
		if (!selectResolverArchivoFaltante || !resolverArchivoFaltanteContainer) {
			return;
		}

		const opciones = ['<option value="">No afiliar (mantener alerta)</option>'];
		let hayOpciones = false;

		(registros || []).forEach((row) => {
			const estado = (row.estadoDocumento || row.estado_documento || row.estado || '').toString().toLowerCase();
			const id = row.id || row.idDocumento || null;
			if (estado === 'archivo_faltante' && id) {
				hayOpciones = true;
				const descripcion = row.descripcion || row.contenido || 'Documento sin descripción';
				const fecha = row.fechaCreacion || row.fec_creacion || '';
				const label = fecha ? `${descripcion} (${fecha})` : descripcion;
				opciones.push(`<option value="${id}">${label}</option>`);
			}
		});

		selectResolverArchivoFaltante.innerHTML = opciones.join('');
		selectResolverArchivoFaltante.value = '';
		resolverArchivoFaltanteContainer.style.display = hayOpciones ? 'block' : 'none';
	}

	window.actualizarSelectArchivoFaltante = actualizarSelectArchivoFaltante;

	const refrescarTablaDocumentos = async (idEmpresa) => {
		const empresaId = idEmpresa || getEmpresaId();
		if (!empresaId) return;
		if (accordionDocumentos) {
			accordionDocumentos.classList.remove('d-none');
			accordionDocumentos.innerHTML = `
				<div class="text-center text-muted py-4 small">
					<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
					Cargando documentos...
				</div>
			`;
		}
		if (emptyStateDocumentos) {
			emptyStateDocumentos.classList.add('d-none');
		}

		if (typeof recargarDocumentosPermanentes === 'function') {
			await recargarDocumentosPermanentes(empresaId);
		} else if (typeof cargarDetalleEmpresa === 'function') {
			await cargarDetalleEmpresa(empresaId, (detalle) => {
				if (typeof populateDocumentosPermanentesModal === 'function') {
					populateDocumentosPermanentesModal(
						detalle || {
							documentosPermanentes: [],
						}
					);
				}
				actualizarSelectArchivoFaltante(
					detalle && Array.isArray(detalle.documentosPermanentes) ? detalle.documentosPermanentes : []
				);
			});
		}
		setEmpresaId(empresaId);
		if (currentPreviewDocumentId) {
			refreshCurrentPreviewDocument();
		}
	};

	btnNuevoDocumentoPermanente.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		limpiarAlerta();
		setCurrentPreviewDocument(null);
		if (previewFrame) {
			previewFrame.src = '';
		}
		// resetear ambos formularios
		try {
			formSolicitarDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formSubirDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formResolverArchivoFaltante?.reset();
			if (selectResolverArchivoFaltante) selectResolverArchivoFaltante.value = '';
		} catch (_) {}
		toggleFormDocumento(true);
		// Al abrir el formulario, mostrar la pestaña inicial
		activarTab();
	});

	btnCancelarDocumentoPermanente.addEventListener('click', function (event) {
		if (!ensureWritePermission(event)) {
			return;
		}
		toggleFormDocumento(false);
		try {
			formSolicitarDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formSubirDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formResolverArchivoFaltante?.reset();
			if (selectResolverArchivoFaltante) selectResolverArchivoFaltante.value = '';
		} catch (_) {}
		limpiarAlerta();
		setEmpresaId(getEmpresaId());
		// Al cancelar, mostrar la pestaña inicial
		activarTab();
	});

	// Envío: Subir archivo
	formSubirDocumentoPermanente.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		limpiarAlerta();

		const idEmpresa = getEmpresaId();
		if (!idEmpresa) {
			mostrarAlerta('Selecciona una empresa válida antes de agregar documentos.', 'danger');
			return;
		}

		const submitButton = formSubirDocumentoPermanente.querySelector('button[type="submit"]');
		submitButton.disabled = true;
		const submitButtonOriginal = submitButton.innerHTML;
		submitButton.innerHTML =
			'<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';

		const formData = new FormData(formSubirDocumentoPermanente);
		formData.append('accion', 'nuevoDocumentoPermanente');
		formData.set('id', idEmpresa);

		// Validar selección única de tipo
		const tipo = (formData.get('descripcion') || '').toString().trim();
		if (!tipo) {
			mostrarAlerta('Selecciona un tipo de documento.', 'danger');
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
			return;
		}
		// Validación: solo uno del tipo específico por empresa
		const entries = window.documentosPermanentesEntries || {};
		let existe = false;
		if (tipo === 'Acta constitutiva') {
			existe = Object.values(entries).some((entry) => entry.descripcion === 'Acta constitutiva');
		} else if (tipo === 'Registro Público de la Propiedad y de Comercio (Acta)' || tipo === 'RPPC Acta') {
			existe = Object.values(entries).some(
				(entry) =>
					entry.descripcion === 'Registro Público de la Propiedad y de Comercio (Acta)' ||
					entry.descripcion === 'RPPC Acta'
			);
		}
		if (existe) {
			mostrarAlerta(`Solo puede haber un documento de tipo "${tipo}" por empresa.`, 'danger');
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
			return;
		}

		try {
			const data = await solicitarAccionDocumento(formData);

			if (data.estatus === 'Exito') {
				mostrarAlerta(data.mensaje || 'Documento subido correctamente.', 'success');
				try {
					formSubirDocumentoPermanente.reset();
				} catch (_) {}
				toggleFormDocumento(false);
				setEmpresaId(idEmpresa);
				await refrescarTablaDocumentos(idEmpresa);
			} else {
				mostrarAlerta(data.mensaje || 'Error al subir el documento.', 'danger');
			}
		} catch (err) {
			console.error('Error al subir documento permanente:', err);
			mostrarAlerta(err.message || 'No fue posible subir el documento.', 'danger');
		} finally {
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
		}
	});

	// Envío: Solicitar documento (crea registro pendiente sin archivo)
	formSolicitarDocumentoPermanente.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		limpiarAlerta();

		const idEmpresa = getEmpresaId();
		if (!idEmpresa) {
			mostrarAlerta('Selecciona una empresa válida antes de solicitar un documento.', 'danger');
			return;
		}

		const submitButton = formSolicitarDocumentoPermanente.querySelector('button[type="submit"]');
		submitButton.disabled = true;
		const submitButtonOriginal = submitButton.innerHTML;
		submitButton.innerHTML =
			'<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';

		const formData = new FormData(formSolicitarDocumentoPermanente);
		formData.append('accion', 'solicitarDocumentoPermanente');
		formData.set('id', idEmpresa);

		const tipo = (formData.get('descripcion') || '').toString().trim();
		if (!tipo) {
			mostrarAlerta('Selecciona un tipo de documento.', 'danger');
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
			return;
		}
		// Validación: solo uno del tipo específico por empresa
		const entries = window.documentosPermanentesEntries || {};
		let existe = false;
		if (tipo === 'Acta constitutiva') {
			existe = Object.values(entries).some((entry) => entry.descripcion === 'Acta constitutiva');
		} else if (tipo === 'Registro Público de la Propiedad y de Comercio (Acta)' || tipo === 'RPPC Acta') {
			existe = Object.values(entries).some(
				(entry) =>
					entry.descripcion === 'Registro Público de la Propiedad y de Comercio (Acta)' ||
					entry.descripcion === 'RPPC Acta'
			);
		}
		if (existe) {
			mostrarAlerta(`Solo puede haber un documento de tipo "${tipo}" por empresa.`, 'danger');
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
			return;
		}

		try {
			const data = await solicitarAccionDocumento(formData);

			if (data.estatus === 'Exito') {
				const mensajePrincipal = data.mensaje || 'Solicitud registrada.';
				const mensajeDetalle = data.subMensaje ? ` ${data.subMensaje}` : '';
				mostrarAlerta(`${mensajePrincipal}${mensajeDetalle}`, 'success');
				try {
					formSolicitarDocumentoPermanente.reset();
				} catch (_) {}
				toggleFormDocumento(false);
				setEmpresaId(idEmpresa);
				await refrescarTablaDocumentos(idEmpresa);
			} else {
				mostrarAlerta(data.mensaje || 'Error al registrar la solicitud.', 'danger');
			}
		} catch (err) {
			console.error('Error al solicitar documento permanente:', err);
			mostrarAlerta('Error de conexión.', 'danger');
		} finally {
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
		}
	});

	// Envío: Resolver archivo faltante (sube archivo al registro seleccionado)
	formResolverArchivoFaltante.addEventListener('submit', async function (e) {
		e.preventDefault();
		if (!ensureWritePermission(e)) {
			return;
		}
		limpiarAlerta();

		const idEmpresa = getEmpresaId();
		if (!idEmpresa) {
			mostrarAlerta('Selecciona una empresa válida antes de resolver un archivo faltante.', 'danger');
			return;
		}

		const selectedId = selectResolverArchivoFaltante ? selectResolverArchivoFaltante.value : '';
		const file = inputResolverArchivoFaltanteArchivo ? inputResolverArchivoFaltanteArchivo.files[0] : null;
		if (!selectedId) {
			mostrarAlerta('Selecciona una alerta de archivo faltante para resolver.', 'danger');
			return;
		}
		if (!file) {
			mostrarAlerta('Selecciona un archivo para subir.', 'danger');
			return;
		}

		const submitButton = formResolverArchivoFaltante.querySelector('button[type="submit"]');
		submitButton.disabled = true;
		const submitButtonOriginal = submitButton.innerHTML;
		submitButton.innerHTML =
			'<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>';

		const formData = new FormData();
		formData.append('accion', 'reemplazarDocumentoPermanente');
		formData.append('id', selectedId);
		formData.append('documento', file);

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				mostrarAlerta(data.mensaje || 'Documento subido y enviado a revisión.', 'success');
				try {
					formResolverArchivoFaltante.reset();
					if (selectResolverArchivoFaltante) selectResolverArchivoFaltante.value = '';
				} catch (_) {}
				toggleFormDocumento(false);
				setEmpresaId(idEmpresa);
				await refrescarTablaDocumentos(idEmpresa);
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo resolver el archivo faltante.', 'danger');
			}
		} catch (err) {
			console.error('Error al resolver archivo faltante:', err);
			mostrarAlerta('Error de conexión.', 'danger');
		} finally {
			submitButton.disabled = false;
			submitButton.innerHTML = submitButtonOriginal;
		}
	});

	modalDocumentos.addEventListener('show.bs.modal', function (e) {
		if (modalFetchInFlight) {
			// Evita correr dos veces si por alguna razón Bootstrap dispara el evento duplicado
			return;
		}
		const btn = e.relatedTarget;
		const idEmpresa = btn && btn.getAttribute('data-id') ? btn.getAttribute('data-id') : '';
		try {
			formSolicitarDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formSubirDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formResolverArchivoFaltante?.reset();
		} catch (_) {}
		setEmpresaId(idEmpresa);
		limpiarAlerta();
		toggleFormDocumento(false);
		actualizarSelectArchivoFaltante([]);
		if (idEmpresa) {
			modalFetchInFlight = true;
			Promise.resolve(refrescarTablaDocumentos(idEmpresa)).finally(() => {
				modalFetchInFlight = false;
			});
		} else {
			if (accordionDocumentos) {
				accordionDocumentos.innerHTML = '';
				accordionDocumentos.classList.add('d-none');
			}
			if (emptyStateDocumentos) {
				emptyStateDocumentos.classList.remove('d-none');
			}
		}
	});

	modalDocumentos.addEventListener('hidden.bs.modal', () => {
		try {
			formSolicitarDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formSubirDocumentoPermanente?.reset();
		} catch (_) {}
		try {
			formResolverArchivoFaltante?.reset();
		} catch (_) {}
		limpiarAlerta();
		toggleFormDocumento(false);
		setEmpresaId('');
		if (selectResolverArchivoFaltante) {
			selectResolverArchivoFaltante.value = '';
			resolverArchivoFaltanteContainer.style.display = 'none';
		}
		hidePreview();
	});

	if (accordionDocumentos) {
		accordionDocumentos.addEventListener('click', function (e) {
			// Abrir vista previa
			const btnVer = e.target.closest('.btn-ver-documento-permanente');
			if (btnVer) {
				const docId = btnVer.getAttribute('data-doc-id') || '';
				const entryFromMap = docId ? getDocumentEntryById(docId) : null;
				const fallbackUrl = btnVer.getAttribute('data-url') || '';
				const fallbackName = btnVer.getAttribute('data-name') || '';
				const estadoDataset = btnVer.getAttribute('data-estado') || '';
				const canReviewDataset = btnVer.getAttribute('data-can-review') === '1';
				const previewEntry = entryFromMap
					? Object.assign({}, entryFromMap)
					: {
							id: docId || null,
							idString: docId || null,
							documento: fallbackUrl,
							descripcion: fallbackName,
							estado: estadoDataset,
							puedeRevisar: estadoDataset === 'revision' && canReviewDataset,
							puedeRevisarRaw: canReviewDataset,
					  };
				const uploaderAttr = btnVer.getAttribute('data-uploader-id');
				if (
					uploaderAttr &&
					(previewEntry.idUsuarioSubio === undefined || previewEntry.idUsuarioSubio === null)
				) {
					previewEntry.idUsuarioSubio = uploaderAttr;
				}
				const previewUrl = previewEntry.documento || fallbackUrl;
				if (!previewUrl) {
					mostrarAlerta('No se encontró el archivo para vista previa.', 'danger');
					return;
				}
				previewEntry.documento = previewUrl;
				if (!previewEntry.descripcion) {
					previewEntry.descripcion = fallbackName;
				}
				if (!previewEntry.estado) {
					previewEntry.estado = estadoDataset;
				}
				if (!previewEntry.idString && docId) {
					previewEntry.idString = docId;
				}
				showPreview(previewEntry);
				return;
			}
			// Acción "marcar archivo faltante" eliminada: ahora se solicita desde la pestaña Solicitar.

			const btnRevisado = e.target.closest('.btn-marcar-revisado-documento');
			if (btnRevisado) {
				if (!ensureWritePermission(e) || !canMarkReviewed()) {
					if (!canMarkReviewed()) {
						mostrarAlerta('Solo Administrador puede completar la revisión.', 'danger');
					}
					return;
				}
				const idDocumentoRevisado = btnRevisado.getAttribute('data-id');
				const idEmpresaRevisado = getEmpresaId();
				if (!idDocumentoRevisado || !idEmpresaRevisado) {
					mostrarAlerta('Selecciona una empresa válida antes de actualizar el estado.', 'danger');
					return;
				}
				// Evitar que el mismo usuario que subió verifique su propio documento
				try {
					const entryCheck = getDocumentEntryById(idDocumentoRevisado);
					const currentUserId =
						typeof window.appCurrentUserId !== 'undefined' ? window.appCurrentUserId : null;
					if (
						currentUserId !== null &&
						currentUserId !== undefined &&
						entryCheck &&
						entryCheck.idUsuarioSubio !== undefined &&
						entryCheck.idUsuarioSubio !== null &&
						String(entryCheck.idUsuarioSubio) === String(currentUserId)
					) {
						mostrarAlerta(
							'No puedes verificar tu propio documento. Debe hacerlo otro Administrador.',
							'danger'
						);
						return;
					}
				} catch (_) {}
				marcarDocumentoPermanenteRevisado(idDocumentoRevisado, idEmpresaRevisado, btnRevisado);
				return;
			}

			const btn = e.target.closest('.btn-eliminar-documento-permanente');
			if (!btn) {
				const btnRechazar = e.target.closest('.btn-rechazar-revision-documento');
				if (btnRechazar) {
					if (!ensureWritePermission(e) || !canMarkReviewed()) {
						if (!canMarkReviewed()) {
							mostrarAlerta('Solo Administrador puede rechazar la revisión.', 'danger');
						}
						return;
					}
					const idDoc = btnRechazar.getAttribute('data-id');
					const empresaId = getEmpresaId();
					if (!idDoc || !empresaId) {
						mostrarAlerta('Selecciona una empresa válida antes de actualizar el estado.', 'danger');
						return;
					}
					// Evitar que el mismo usuario que subió rechace su propio documento
					try {
						const entryCheck = getDocumentEntryById(idDoc);
						const currentUserId =
							typeof window.appCurrentUserId !== 'undefined' ? window.appCurrentUserId : null;
						if (
							currentUserId !== null &&
							currentUserId !== undefined &&
							entryCheck &&
							entryCheck.idUsuarioSubio !== undefined &&
							entryCheck.idUsuarioSubio !== null &&
							String(entryCheck.idUsuarioSubio) === String(currentUserId)
						) {
							mostrarAlerta(
								'No puedes rechazar tu propio documento. Debe hacerlo otro Administrador.',
								'danger'
							);
							return;
						}
					} catch (_) {}
					marcarDocumentoPermanenteRechazado(idDoc, empresaId, btnRechazar);
					return;
				}

				const inputReemplazar = e.target.closest('.input-reemplazar-documento');
				if (inputReemplazar) {
					if (!ensureWritePermission(e)) {
						return;
					}
					const idDoc = inputReemplazar.getAttribute('data-id');
					const empresaId = getEmpresaId();
					if (inputReemplazar.files && inputReemplazar.files.length && idDoc && empresaId) {
						reemplazarDocumentoPermanente(idDoc, empresaId, inputReemplazar.files[0], inputReemplazar);
					}
					return;
				}
			}
			if (!btn) return;
			if (!ensureWritePermission(e)) {
				return;
			}
			const idDocumento = btn.getAttribute('data-id');
			const idEmpresa = getEmpresaId();
			if (!idDocumento) return;
			deleteDocumentoParams = {
				idDocumento,
				idEmpresa,
			};
			const modalConfirm = new bootstrap.Modal(document.getElementById('modalConfirmDeleteDocumentoPermanente'));
			modalConfirm.show();
		});

		// Delegación para cambios en inputs de archivo (reemplazar documento)
		accordionDocumentos.addEventListener('change', function (e) {
			const inputReemplazar = e.target.closest('.input-reemplazar-documento');
			if (!inputReemplazar) return;
			if (!ensureWritePermission(e)) {
				return;
			}
			const idDoc = inputReemplazar.getAttribute('data-id');
			const empresaId = getEmpresaId();
			if (inputReemplazar.files && inputReemplazar.files.length && idDoc && empresaId) {
				reemplazarDocumentoPermanente(idDoc, empresaId, inputReemplazar.files[0], inputReemplazar);
			}
		});
	}

	btnConfirmDeleteDocumentoPermanente.addEventListener('click', async function () {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		if (!deleteDocumentoParams) return;
		const { idDocumento, idEmpresa } = deleteDocumentoParams;
		try {
			await eliminarDocumentoPermanente(idDocumento, idEmpresa);
		} finally {
			const modalConfirm = bootstrap.Modal.getInstance(
				document.getElementById('modalConfirmDeleteDocumentoPermanente')
			);
			modalConfirm.hide();
			deleteDocumentoParams = null;
		}
	});

	window.addEventListener('permissions:updated', () => {
		toggleFormDocumento(false);
		configurePreviewActions(currentPreviewDocument);
	});

	window.cerrarVistaPreviaDocumentos = hidePreview;
	window.syncPreviewHighlight = syncPreviewIndicators;
	window.getCurrentDocumentPreviewId = () => currentPreviewDocumentId;

	async function eliminarDocumentoPermanente(idDocumento, idEmpresa) {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}
		limpiarAlerta();

		if (accordionDocumentos) {
			accordionDocumentos.querySelectorAll('button').forEach((btn) => (btn.disabled = true));
		}

		const formData = new FormData();
		formData.append('accion', 'eliminarDocumentoPermanente');
		formData.append('id', idDocumento);

		try {
			const data = await solicitarAccionDocumento(formData);

			if (data.estatus === 'Exito') {
				mostrarAlerta('Documento permanente eliminado correctamente.', 'success');
				const empresaId = idEmpresa || getEmpresaId();
				if (empresaId) {
					await refrescarTablaDocumentos(empresaId);
				} else {
					setEmpresaId('');
				}
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo eliminar el documento permanente.', 'danger');
			}
		} catch (err) {
			console.error('Error al eliminar documento permanente:', err);
			mostrarAlerta(err.message || 'No fue posible eliminar el documento permanente.', 'danger');
		} finally {
			if (accordionDocumentos) {
				accordionDocumentos.querySelectorAll('button').forEach((btn) => (btn.disabled = false));
			}
		}
	}

	async function marcarDocumentoPermanenteArchivoFaltante(idDocumento, idEmpresa, triggerButton) {
		if (!hasWritePermissions()) {
			alert('No tienes permisos para realizar esta acción.');
			return;
		}

		limpiarAlerta();

		let originalHtml = '';
		if (triggerButton) {
			originalHtml = triggerButton.innerHTML;
			triggerButton.disabled = true;
			triggerButton.innerHTML =
				'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Actualizando...';
		}

		const formData = new FormData();
		formData.append('accion', 'marcarArchivoFaltanteDocumentoPermanente');
		formData.append('id', idDocumento);

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				mostrarAlerta('Documento permanente marcado como archivo faltante.', 'success');
				await refrescarTablaDocumentos(idEmpresa);
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo marcar el documento como archivo faltante.', 'danger');
			}
		} catch (err) {
			console.error('Error al marcar documento permanente como archivo faltante:', err);
			mostrarAlerta('Error de conexión al actualizar el estado.', 'danger');
		} finally {
			if (triggerButton) {
				triggerButton.disabled = false;
				triggerButton.innerHTML = originalHtml;
			}
		}
	}

	async function marcarDocumentoPermanenteRevisado(idDocumento, idEmpresa, triggerButton) {
		if (!hasWritePermissions() || !canMarkReviewed()) {
			mostrarAlerta('No tienes permisos para completar la revisión.', 'danger');
			return false;
		}

		limpiarAlerta();

		let originalHtml = '';
		if (triggerButton) {
			originalHtml = triggerButton.innerHTML;
			triggerButton.disabled = true;
			triggerButton.innerHTML =
				'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';
		}

		let success = false;

		const formData = new FormData();
		formData.append('accion', 'marcarDocumentoPermanenteRevisado');
		formData.append('id', idDocumento);

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				mostrarAlerta('Revisión completada. Documento marcado como actualizado.', 'success');
				await refrescarTablaDocumentos(idEmpresa);
				const targetId = String(idDocumento);
				if (currentPreviewDocumentId && currentPreviewDocumentId === targetId) {
					refreshCurrentPreviewDocument();
				}
				success = true;
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo completar la revisión del documento.', 'danger');
			}
		} catch (err) {
			console.error('Error al completar revisión de documento permanente:', err);
			mostrarAlerta('Error de conexión al completar la revisión.', 'danger');
		} finally {
			if (triggerButton) {
				triggerButton.disabled = false;
				triggerButton.innerHTML = originalHtml;
			}
		}
		return success;
	}

	async function marcarDocumentoPermanenteRechazado(idDocumento, idEmpresa, triggerButton) {
		if (!hasWritePermissions() || !canMarkReviewed()) {
			mostrarAlerta('No tienes permisos para rechazar la revisión.', 'danger');
			return false;
		}

		limpiarAlerta();

		let originalHtml = '';
		if (triggerButton) {
			originalHtml = triggerButton.innerHTML;
			triggerButton.disabled = true;
			triggerButton.innerHTML =
				'<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>';
		}

		let success = false;

		const formData = new FormData();
		formData.append('accion', 'marcarDocumentoPermanenteRechazado');
		formData.append('id', idDocumento);
		// Adjuntar observaciones si existen
		if (inputObservacionesRechazo && inputObservacionesRechazo.value.trim()) {
			formData.append('observaciones', inputObservacionesRechazo.value.trim());
		}

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				mostrarAlerta(data.mensaje || 'Documento marcado como rechazado.', 'success');
				await refrescarTablaDocumentos(idEmpresa);
				const targetId = String(idDocumento);
				if (currentPreviewDocumentId && currentPreviewDocumentId === targetId) {
					refreshCurrentPreviewDocument();
				}
				success = true;
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo rechazar el documento.', 'danger');
			}
		} catch (err) {
			console.error('Error al rechazar revisión de documento permanente:', err);
			mostrarAlerta('Error de conexión al rechazar el documento.', 'danger');
		} finally {
			if (triggerButton) {
				triggerButton.disabled = false;
				triggerButton.innerHTML = originalHtml;
			}
		}
		return success;
	}

	async function reemplazarDocumentoPermanente(idDocumento, idEmpresa, file, inputEl) {
		if (!hasWritePermissions()) {
			mostrarAlerta('No tienes permisos para reemplazar el documento.', 'danger');
			return;
		}

		limpiarAlerta();

		if (inputEl) inputEl.disabled = true;

		const formData = new FormData();
		formData.append('accion', 'reemplazarDocumentoPermanente');
		formData.append('id', idDocumento);
		formData.append('documento', file);

		try {
			const res = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await res.json();

			if (data.estatus === 'Exito') {
				mostrarAlerta(data.mensaje || 'Documento reemplazado y enviado a revisión.', 'success');
				if (inputEl) {
					try {
						inputEl.value = '';
					} catch (e) {}
				}
				await refrescarTablaDocumentos(idEmpresa);
			} else {
				mostrarAlerta(data.mensaje || 'No se pudo reemplazar el documento.', 'danger');
			}
		} catch (err) {
			console.error('Error al reemplazar documento permanente:', err);
			mostrarAlerta('Error de conexión al reemplazar el documento.', 'danger');
		} finally {
			if (inputEl) inputEl.disabled = false;
		}
	}
});
