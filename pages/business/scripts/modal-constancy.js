if (!window.PostergarDocumentoManager) {
	window.PostergarDocumentoManager = (function () {
		const configs = new Map();
		const MAX_CHARS = 500;
		const API_BASE = '../../api/routes/';
		const DETAIL_ENDPOINT = 'apiCampoDetalle.php';
		const ACTION_ENDPOINT = 'apiEmpresa.php';

		const CLASS_SUCCESS = 'text-success';
		const CLASS_ERROR = 'text-danger';
		const CLASS_INFO = 'text-muted';

		const sanitize = (text) => {
			const value = text || '';
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#39;',
			};
			return value.replace(/[&<>"']/g, (char) => map[char] || char);
		};

		const formatDate = (value) => {
			if (!value) {
				return '';
			}
			const trimmed = value.toString().trim();
			if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(trimmed)) {
				return trimmed;
			}
			if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}$/.test(trimmed)) {
				return trimmed.replace('T', ' ');
			}
			return trimmed;
		};

		const getEl = (id) => document.getElementById(id);
		const canWrite = () => !!(window.appPermissions && window.appPermissions.canWrite);

		const applyPermissionState = (config) => {
			if (!config) {
				return;
			}
			if (config.sectionId) {
				const section = getEl(config.sectionId);
				if (section) {
					if (!section.dataset.postergarOriginalDisplay) {
						section.dataset.postergarOriginalDisplay = section.style.display || '';
					}
					section.style.display = canWrite() ? section.dataset.postergarOriginalDisplay : 'none';
				}
			}

			const textarea = getEl(config.textareaId);
			if (textarea) {
				textarea.disabled = !canWrite() || !config.hasDetalle;
			}

			const button = getEl(config.buttonId);
			if (button) {
				const postergaciones = config.lastPostergaciones;
				const restanteRaw =
					postergaciones && postergaciones.restante !== undefined ? postergaciones.restante : null;
				const restante = restanteRaw === undefined || restanteRaw === null ? null : Number(restanteRaw);
				const sinRestantes = restante !== null && !Number.isNaN(restante) && restante <= 0;
				const disabled = !canWrite() || !config.hasDetalle || sinRestantes;
				button.disabled = disabled;
				button.classList.toggle('disabled', disabled);
			}
		};

		const setMessage = (config, message, type = '') => {
			const el = getEl(config.messageId);
			if (!el) {
				return;
			}
			el.textContent = message || '';
			el.classList.remove(CLASS_SUCCESS, CLASS_ERROR, CLASS_INFO);
			if (type === 'success') {
				el.classList.add(CLASS_SUCCESS);
			} else if (type === 'error') {
				el.classList.add(CLASS_ERROR);
			} else if (type === 'info') {
				el.classList.add(CLASS_INFO);
			}
		};
		const updateCounter = (config) => {
			const textarea = getEl(config.textareaId);
			const counterEl = getEl(config.counterId);
			if (!textarea || !counterEl) return;
			const length = (textarea.value || '').length;
			counterEl.textContent = `${length}/${MAX_CHARS}`;
			counterEl.classList.toggle('text-danger', length > MAX_CHARS);
			counterEl.classList.toggle('text-muted', length <= MAX_CHARS);
		};

		const renderHistorial = (config, historial) => {
			const container = getEl(config.historialId);
			if (!container) return;
			if (!historial || !historial.length) {
				container.classList.add('text-muted');
				container.innerHTML = 'Sin postergaciones registradas.';
				return;
			}
			container.classList.remove('text-muted');
			const items = (historial || [])
				.map((item) => {
					const fecha = sanitize(formatDate(item.fecha));
					const observaciones = sanitize(item.observaciones || 'Sin observaciones');
					return `
						<div class="border rounded p-2 mb-2">
							<div class="fw-semibold">${fecha || 'Fecha no disponible'}</div>
							<div class="mt-1">${observaciones || 'Sin observaciones'}</div>
						</div>`;
				})
				.join('');
			container.innerHTML = items;
		};

		const setLoadingState = (config, isLoading) => {
			const button = getEl(config.buttonId);
			if (button) {
				const postergaciones = config.lastPostergaciones;
				const restanteRaw =
					postergaciones && postergaciones.restante !== undefined ? postergaciones.restante : null;
				const restante = restanteRaw === undefined || restanteRaw === null ? null : Number(restanteRaw);
				const sinRestantes = restante !== null && !Number.isNaN(restante) && restante <= 0;
				const disabled = isLoading || !canWrite() || !config.hasDetalle || sinRestantes;
				button.disabled = disabled;
				button.classList.toggle('disabled', disabled);
			}
			if (isLoading) {
				setMessage(config, 'Procesando...', 'info');
			}
		};

		const updateInfo = (config, detalle) => {
			const badge = getEl(config.badgeId);
			const remainingEl = getEl(config.remainingId);
			const infoEl = getEl(config.infoId);
			const postergaciones = (detalle && detalle.postergaciones) || {
				total: 0,
				maximo: 3,
				restante: 3,
			};
			config.lastPostergaciones = postergaciones;

			if (badge) {
				badge.textContent = `Postergaciones: ${postergaciones.total} / ${postergaciones.maximo}`;
			}
			if (remainingEl) {
				remainingEl.textContent = `Restantes: ${Math.max(0, postergaciones.restante)}`;
			}
			if (infoEl) {
				if (postergaciones.restante > 0) {
					infoEl.textContent = `Puedes postergar este requisito hasta ${postergaciones.maximo} veces. Te quedan ${postergaciones.restante}.`;
				} else {
					infoEl.textContent = 'Se alcanzó el número máximo de postergaciones para este requisito.';
				}
			}

			applyPermissionState(config);
		};

		const fetchDetalle = async (empresaId, campo) => {
			const url = `${API_BASE}${DETAIL_ENDPOINT}?id=${empresaId}&campo=${encodeURIComponent(campo)}`;
			const response = await fetch(url, {
				credentials: 'same-origin',
			});
			if (!response.ok) {
				throw new Error('No se pudo obtener la información del campo');
			}
			const data = await response.json();
			if (!data.success) {
				throw new Error(data.error || 'Error al obtener información del campo');
			}
			return data.data;
		};

		const submitPostergacion = async (config) => {
			if (!canWrite()) {
				setMessage(config, 'No tienes permisos para postergar este requisito.', 'error');
				return;
			}

			if (!config.empresaId) {
				setMessage(config, 'No se encontró la empresa seleccionada.', 'error');
				return;
			}

			if (!config.hasDetalle) {
				setMessage(config, 'Aún se está cargando la información de postergaciones.', 'info');
				return;
			}

			const restanteRaw =
				config.lastPostergaciones && config.lastPostergaciones.restante !== undefined
					? config.lastPostergaciones.restante
					: null;
			const restante = restanteRaw === undefined || restanteRaw === null ? null : Number(restanteRaw);
			if (restante !== null && !Number.isNaN(restante) && restante <= 0) {
				setMessage(config, 'Ya se alcanzó el máximo de postergaciones.', 'error');
				return;
			}

			const textarea = getEl(config.textareaId);
			if (!textarea) {
				return;
			}

			const observaciones = textarea.value.trim();
			if (observaciones.length > MAX_CHARS) {
				textarea.value = observaciones.slice(0, MAX_CHARS);
			}

			const formData = new FormData();
			formData.append('accion', 'postergarEmpresa');
			formData.append('id', config.empresaId);
			formData.append('campo', config.campo);
			formData.append('observaciones', textarea.value.trim());

			setLoadingState(config, true);
			try {
				const response = await fetch(`${API_BASE}${ACTION_ENDPOINT}`, {
					method: 'POST',
					body: formData,
					credentials: 'same-origin',
				});

				let payload = null;
				try {
					payload = await response.json();
				} catch (parseError) {
					throw new Error('Respuesta inesperada del servidor.');
				}

				if (!response.ok || !payload.success) {
					const message =
						payload && payload.message ? payload.message : 'No se pudo registrar la postergación.';
					throw new Error(message);
				}

				textarea.value = '';
				updateCounter(config);
				setMessage(config, 'Se registró la postergación correctamente.', 'success');
				await load(config.key, config.empresaId, true);
				if (typeof window.loadEmpresas === 'function') {
					try {
						await window.loadEmpresas();
					} catch (refreshError) {
						console.error('No se pudo actualizar la tabla de empresas después de postergar:', refreshError);
					}
				}
			} catch (error) {
				setMessage(config, error.message || 'No se pudo registrar la postergación.', 'error');
			} finally {
				setLoadingState(config, false);
			}
		};

		const load = async (key, empresaId, skipLoadingMessage = false) => {
			const config = configs.get(key);
			if (!config) {
				return;
			}

			config.empresaId = empresaId;
			config.hasDetalle = false;
			config.lastPostergaciones = null;
			config.lastData = null;
			const textarea = getEl(config.textareaId);
			if (textarea) {
				textarea.value = '';
			}
			updateCounter(config);

			if (!skipLoadingMessage) {
				setMessage(config, 'Cargando postergaciones…', 'info');
			}
			const button = getEl(config.buttonId);
			if (button) {
				button.disabled = true;
				button.classList.add('disabled');
			}
			applyPermissionState(config);
			try {
				const detalle = await fetchDetalle(empresaId, config.campo);
				config.hasDetalle = true;
				config.lastData = detalle;
				updateInfo(config, detalle);
				renderHistorial(config, detalle.postergaciones ? detalle.postergaciones.historial : []);
				if (!skipLoadingMessage) {
					setMessage(config, '', '');
				}
			} catch (error) {
				config.hasDetalle = false;
				config.lastData = null;
				setMessage(config, error.message || 'No se pudo cargar la información de postergaciones.', 'error');
				updateInfo(config, null);
				renderHistorial(config, []);
			}
			applyPermissionState(config);
			updateCounter(config);
		};

		const attachListeners = (config) => {
			const textarea = getEl(config.textareaId);
			if (textarea && !textarea.dataset.postergarListener) {
				textarea.addEventListener('input', () => {
					updateCounter(config);
					if (textarea.value.length <= MAX_CHARS) {
						setMessage(config, '', '');
					}
				});
				textarea.dataset.postergarListener = '1';
			}

			const button = getEl(config.buttonId);
			if (button && !button.dataset.postergarListener) {
				button.addEventListener('click', (event) => {
					event.preventDefault();
					submitPostergacion(config);
				});
				button.dataset.postergarListener = '1';
			}
		};

		const init = (config) => {
			if (!config || !config.key) {
				return;
			}
			const stored = configs.get(config.key);
			if (stored) {
				Object.assign(stored, config);
				attachListeners(stored);
				applyPermissionState(stored);
				updateCounter(stored);
				return;
			}
			const entry = {
				key: config.key,
				campo: config.campo,
				textareaId: config.textareaId,
				counterId: config.counterId,
				buttonId: config.buttonId,
				badgeId: config.badgeId,
				remainingId: config.remainingId,
				historialId: config.historialId,
				messageId: config.messageId,
				infoId: config.infoId,
				sectionId: config.sectionId || null,
				empresaId: null,
				lastData: null,
				lastPostergaciones: null,
				hasDetalle: false,
			};
			configs.set(entry.key, entry);
			attachListeners(entry);
			applyPermissionState(entry);
			updateCounter(entry);
		};

		window.addEventListener('permissions:updated', () => {
			configs.forEach((config) => applyPermissionState(config));
		});

		return {
			init,
			load,
		};
	})();
	window.dispatchEvent(new CustomEvent('postergar:manager-ready'));
}

// Funciones específicas para el modal de constancia
function showConstanciaAlert(msg) {
	const alertDiv = document.getElementById('constancia-alert');
	const alertText = document.getElementById('constancia-alert-text');
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
	const hasWritePermissions = () => !!(window.appPermissions && window.appPermissions.canWrite);
	const saveBtn = document.getElementById('btnGuardarConstancia');
	const input = document.getElementById('inputConstanciaModal');
	const fileLabel = document.getElementById('nombreConstanciaArchivo');
	const uploadBtn = document.getElementById('btnSubirConstancia');

	// Estado y referencias para la pestaña "Física"
	const fisica = {
		tipo: 'rep', // 'rep' | 'socio'
		docs: { rep: [], socio: [] }, // arrays de { id, url, descripcion, fecha }
		selectedIndex: null,
		container: document.getElementById('iframe-constancia-fisica-container'),
		labelArchivo: document.getElementById('nombreConstanciaFisicaArchivo'),
		input: document.getElementById('inputConstanciaFisica'),
		btnGuardar: document.getElementById('btnGuardarConstanciaFisica'),
		btnSubir: document.getElementById('btnSubirConstanciaFisica'),
		btnToggle: document.getElementById('btnToggleFisicaTipo'),
		labelTipoActual: document.getElementById('labelFisicaTipoActual'),
		list: document.getElementById('listaConstanciasFisica'),
		countEl: document.getElementById('fisicaConteoItems'),
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

		// Controles de pestaña Física
		if (fisica.btnSubir) {
			fisica.btnSubir.classList.toggle('d-none', !canWrite);
			fisica.btnSubir.disabled = !canWrite;
		}
		if (fisica.btnGuardar) {
			const hasFileFisica = !!(fisica.input && fisica.input.files && fisica.input.files.length);
			const canShowFisica = hasFileFisica && canWrite;
			fisica.btnGuardar.classList.toggle('d-none', !canShowFisica);
			fisica.btnGuardar.disabled = !canShowFisica;
		}
	};

	if (uploadBtn) {
		uploadBtn.addEventListener('click', function (event) {
			if (!ensureWritePermission(event)) {
				return;
			}
			input.click();
		});
	}

	input.addEventListener('change', function (e) {
		const nombre = e.target.files.length ? e.target.files[0].name : '';
		fileLabel.textContent = nombre;
		updateSaveButtonVisibility();
	});

	applyPermissionVisibility();

	document.getElementById('btnGuardarConstancia').addEventListener('click', async function (event) {
		if (!ensureWritePermission()) {
			return;
		}
		const empresaId = this.getAttribute('data-id') || window.ultimaEmpresaId;

		if (!empresaId || !input.files.length) return;

		const formData = new FormData();
		formData.append('accion', 'constanciaSfMod');
		formData.append('id', empresaId);
		formData.append('CSF', input.files[0]);

		try {
			const response = await fetch('../../api/routes/apiEmpresa.php', {
				method: 'POST',
				body: formData,
			});
			const data = await response.json();
			if (!response.ok || data.estatus === 'Error') {
				throw new Error(data.mensaje || 'No se pudo guardar la constancia.');
			}

			// Actualizar el iframe con el PDF recién subido
			const cont = document.getElementById('iframe-constancia-container');
			const url = URL.createObjectURL(input.files[0]);
			cont.innerHTML = `<iframe src='${url}' width='100%' height='500px'></iframe>`;

			showConstanciaAlert('¡Constancia guardada!');
			fileLabel.textContent = '';
			input.value = '';
			updateSaveButtonVisibility();

			if (typeof loadEmpresas === 'function') {
				await loadEmpresas();
			}
			if (window.PostergarDocumentoManager) {
				await window.PostergarDocumentoManager.load('constancia', empresaId, true);
			}
		} catch (err) {
			console.error('Error al enviar constancia:', err);
			showConstanciaAlert('Error al guardar');
		}
	});

	// ================
	// Pestaña: Física
	// ================

	function setFisicaTipo(tipo) {
		fisica.tipo = tipo === 'socio' ? 'socio' : 'rep';
		if (fisica.btnToggle) {
			fisica.btnToggle.textContent = fisica.tipo === 'rep' ? 'Representante legal' : 'Socio';
		}
		if (fisica.labelTipoActual) {
			fisica.labelTipoActual.textContent = `Mostrando: ${
				fisica.tipo === 'rep' ? 'Representante legal' : 'Socio'
			}`;
		}
		// por defecto seleccionar el más reciente si hay lista
		fisica.selectedIndex = fisica.docs[fisica.tipo] && fisica.docs[fisica.tipo].length ? 0 : null;
		renderFisicaVista();
		updateFisicaSaveVisibility();
	}

	function updateFisicaSaveVisibility() {
		const canWrite = hasWritePermissions();
		const hasFile = !!(fisica.input && fisica.input.files && fisica.input.files.length);
		if (fisica.btnGuardar) {
			fisica.btnGuardar.classList.toggle('d-none', !(canWrite && hasFile));
			fisica.btnGuardar.disabled = !(canWrite && hasFile);
		}
	}

	function normalizeText(value) {
		return (value || '').toString().trim().toLowerCase();
	}

	function matchDescripcionToTipo(descripcion, target) {
		const d = normalizeText(descripcion);
		if (target === 'rep') {
			return (
				(d.includes('constancia') && d.includes('representante') && d.includes('legal')) ||
				d.includes('const sif. rep. legal')
			);
		}
		return (d.includes('constancia') && d.includes('socio')) || d.includes('const sif. socio.');
	}

	async function cargarDetalleEmpresaParaFisica(empresaId) {
		try {
			const res = await fetch(`../../api/routes/apiEmpresaDetalle.php?id=${empresaId}`);
			if (!res.ok) throw new Error('No se pudo obtener detalle de empresa');
			const data = await res.json();
			if (!data.success) throw new Error(data.error || 'Error de detalle de empresa');
			const detalle = data.data || {};
			const docs = Array.isArray(detalle.documentosPermanentes) ? detalle.documentosPermanentes : [];
			const mapDoc = (x) => ({
				id: x.id || x.idDocumento || null,
				url: x.documento || '',
				descripcion: x.descripcion || x.contenido || '',
				fecha: x.fechaCreacion || x.fecha_creacion || x.fec_creacion || x.fechaSubio || x.fecha_subio || '',
			});
			const repList = docs
				.filter((x) => matchDescripcionToTipo(x.descripcion || x.contenido || '', 'rep'))
				.map(mapDoc);
			const socioList = docs
				.filter((x) => matchDescripcionToTipo(x.descripcion || x.contenido || '', 'socio'))
				.map(mapDoc);
			const safeTime = (v) => Date.parse((v || '').toString().replace(' ', 'T')) || 0;
			repList.sort((a, b) => safeTime(b.fecha) - safeTime(a.fecha));
			socioList.sort((a, b) => safeTime(b.fecha) - safeTime(a.fecha));
			fisica.docs.rep = repList;
			fisica.docs.socio = socioList;
			fisica.selectedIndex = fisica.docs[fisica.tipo] && fisica.docs[fisica.tipo].length ? 0 : null;
			renderFisicaVista(); // Call to renderFisicaVista after loading data
		} catch (err) {
			console.error('Error cargando detalle para Física:', err);
			fisica.docs.rep = [];
			fisica.docs.socio = [];
			fisica.selectedIndex = null;
			renderFisicaVista(); // Call to renderFisicaVista in case of error
		}
	}

	function renderFisicaVista() {
		if (!fisica.container) return;
		const list = fisica.docs[fisica.tipo] || [];
		const idx = fisica.selectedIndex;
		const current = idx !== null && idx >= 0 && idx < list.length ? list[idx] : null;
		if (current && current.url) {
			fisica.container.innerHTML = `<iframe src='${current.url}' width='100%' height='500px'></iframe>`;
		} else {
			fisica.container.innerHTML = '<span class="text-muted">Sin archivo</span>';
		}

		// Render historial en lista con estilo de Documentos Permanentes
		if (fisica.list) {
			if (!list.length) {
				fisica.list.innerHTML = '<div class="list-group-item text-muted">Sin documentos</div>';
			} else {
				const hasFn = (fn) => typeof fn === 'function';
				const getBtnClass = hasFn(window.getActionButtonClass)
					? window.getActionButtonClass
					: (variant = 'outline-primary') =>
							`btn btn-${variant} btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center`;
				const renderGroup = hasFn(window.renderActionGroup)
					? window.renderActionGroup
					: (buttons) =>
							`<div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">${(
								buttons || []
							)
								.filter(Boolean)
								.join('')}</div>`;
				const getDownloadHtml = hasFn(window.getDownloadButtonHtml)
					? window.getDownloadButtonHtml
					: (url, label = 'Descargar') =>
							url
								? `<a href='${url}' class="${getBtnClass(
										'outline-primary'
								  )}" download title="${label}" aria-label="${label}"><i class='bx bx-download'></i></a>`
								: '';

				const canWrite = hasWritePermissions();
				const items = list
					.map((item, i) => {
						const isSelected = i === idx;
						const fecha = (item.fecha || '').toString().trim();
						const safeDesc = (item.descripcion || '').replace(/</g, '&lt;').replace(/>/g, '&gt;');
						const downloadBtn = getDownloadHtml(item.url, 'Descargar');
						const deleteBtn = canWrite
							? `<button type="button" class="${getBtnClass(
									'outline-danger'
							  )} btn-eliminar-constancia-fisica" data-index="${i}" title="Eliminar" aria-label="Eliminar"><i class="bx bx-trash"></i></button>`
							: '';
						const verBtn = `<button type="button" class="${getBtnClass(
							isSelected ? 'secondary' : 'outline-secondary'
						)} btn-ver-constancia-fisica" data-index="${i}" title="Ver" aria-label="Ver"><i class="bx bx-show"></i></button>`;
						const accionesHtml = renderGroup([verBtn, downloadBtn, deleteBtn]);
						// Mantener borde azul en el seleccionado, y hover en todos
						return `
								<div class="list-group-item d-flex justify-content-between align-items-center${
									isSelected ? ' border-primary' : ''
								}" data-index="${i}" style="background-color: transparent;${
							isSelected ? 'border-width:2px;border-style:solid;' : ''
						}">
									<div class="me-2" style="cursor:pointer;">
										<div class="fw-semibold">${safeDesc || 'Constancia PDF'}</div>
										<div class="text-muted small">${fecha || ''}</div>
									</div>
									<div class="shrink-0 ms-xl-auto">${accionesHtml}</div>
								</div>`;
					})
					.join('');
				fisica.list.innerHTML = items;

				// Selección al hacer click fuera de botones
				fisica.list.querySelectorAll('.list-group-item .me-2').forEach((el) => {
					el.addEventListener('click', (ev) => {
						const parent = el.closest('.list-group-item');
						const newIdx = Number(parent && parent.getAttribute('data-index'));
						if (!Number.isNaN(newIdx)) {
							fisica.selectedIndex = newIdx;
							renderFisicaVista();
						}
					});
				});
				// Selección al hacer click en botón Ver
				fisica.list.querySelectorAll('.btn-ver-constancia-fisica').forEach((btn) => {
					btn.addEventListener('click', (ev) => {
						ev.stopPropagation();
						const newIdx = Number(btn.getAttribute('data-index'));
						if (!Number.isNaN(newIdx)) {
							fisica.selectedIndex = newIdx;
							renderFisicaVista();
						}
					});
				});

				// Eliminar documento (solo permisos de escritura)
				if (canWrite) {
					fisica.list.querySelectorAll('.btn-eliminar-constancia-fisica').forEach((btn) => {
						btn.addEventListener('click', (ev) => {
							ev.stopPropagation();
							const index = Number(btn.getAttribute('data-index'));
							if (Number.isNaN(index)) return;
							const doc = list[index];
							if (!doc || !doc.id) return;
							// Usar el modal de confirmación de documentos permanentes
							window._constanciaDeleteParams = {
								id: doc.id,
								empresaId: (saveBtn && saveBtn.getAttribute('data-id')) || window.ultimaEmpresaId,
								callback: async function () {
									try {
										const fd = new FormData();
										fd.append('accion', 'eliminarDocumentoPermanente');
										fd.append('id', doc.id);
										const res = await fetch('../../api/routes/apiEmpresa.php', {
											method: 'POST',
											body: fd,
										});
										const data = await res.json();
										if (data.estatus === 'Exito') {
											showConstanciaAlert('Eliminado');
											// Refrescar la lista inmediatamente
											await cargarDetalleEmpresaParaFisica(
												window._constanciaDeleteParams.empresaId
											);
											fisica.selectedIndex = 0;
											renderFisicaVista();
										} else {
											showConstanciaAlert(data.mensaje || 'No se pudo eliminar.', 'error');
										}
									} catch (e) {
										console.error('Error eliminando constancia:', e);
										showConstanciaAlert('Error de conexión.', 'error');
									}
								},
							};
							const modal = document.getElementById('modalConfirmDeleteDocumentoPermanente');
							if (modal) {
								const bsModal = window.bootstrap
									? window.bootstrap.Modal.getOrCreateInstance(modal)
									: null;
								bsModal && bsModal.show();
								// Unbind para evitar duplicados
								const btnConfirm = document.getElementById('btnConfirmDeleteDocumentoPermanente');
								if (btnConfirm) {
									btnConfirm.onclick = async function () {
										bsModal.hide();
										if (
											window._constanciaDeleteParams &&
											typeof window._constanciaDeleteParams.callback === 'function'
										) {
											await window._constanciaDeleteParams.callback();
											window._constanciaDeleteParams = null;
										}
									};
								}
							}
						});
					});
				}
			}
		}

		if (fisica.countEl) {
			const count = list.length;
			fisica.countEl.textContent = `${count} elemento${count === 1 ? '' : 's'}`;
		}
	}

	// Toggle tipo (un solo botón conmutador)
	if (fisica.btnToggle) {
		fisica.btnToggle.addEventListener('click', () => {
			setFisicaTipo(fisica.tipo === 'rep' ? 'socio' : 'rep');
		});
	}

	// Botón subir en Física
	if (fisica.btnSubir && fisica.input) {
		fisica.btnSubir.addEventListener('click', (e) => {
			if (!ensureWritePermission(e)) return;
			fisica.input.click();
		});
		fisica.input.addEventListener('change', (e) => {
			const nombre = e.target.files.length ? e.target.files[0].name : '';
			if (fisica.labelArchivo) fisica.labelArchivo.textContent = nombre;
			updateFisicaSaveVisibility();
		});
	}

	// Toggle tipo (un solo botón conmutador)
	// Guardar Física (siempre crear nuevo documento)
	if (fisica.btnGuardar) {
		fisica.btnGuardar.addEventListener('click', async () => {
			if (!ensureWritePermission()) return;
			const empresaId = (saveBtn && saveBtn.getAttribute('data-id')) || window.ultimaEmpresaId;
			const fileFis = fisica.input && fisica.input.files && fisica.input.files[0];
			if (!empresaId || !fileFis) return;

			try {
				const descripcion =
					fisica.tipo === 'rep'
						? 'Constancia de situación fiscal representante legal'
						: 'Constancia de situación fiscal socio';
				const fd = new FormData();
				fd.append('accion', 'nuevoDocumentoPermanente');
				fd.append('id', empresaId);
				fd.append('descripcion', descripcion);
				fd.append('documento', fileFis);
				fd.append('estado_documento', 'completado');
				const res = await fetch('../../api/routes/apiEmpresa.php', { method: 'POST', body: fd });
				const data = await res.json();
				if (data.estatus === 'Exito') {
					showConstanciaAlert('¡Guardado!');
					const url = URL.createObjectURL(fileFis);
					if (fisica.container)
						fisica.container.innerHTML = `<iframe src='${url}' width='100%' height='500px'></iframe>`;
					fisica.input.value = '';
					if (fisica.labelArchivo) fisica.labelArchivo.textContent = '';
					updateFisicaSaveVisibility();
					await cargarDetalleEmpresaParaFisica(empresaId);
					// Seleccionar el más reciente
					fisica.selectedIndex = 0;
					renderFisicaVista();
				} else {
					showConstanciaAlert(data.mensaje || 'No se pudo guardar el documento.', 'error');
				}
			} catch (error) {
				console.error('Error guardando documento físico:', error);
				showConstanciaAlert('Error de conexión.', 'error');
			}
		});
	}

	// Al mostrar el modal, cargar detalle de documentos para la pestaña Física
	const modal = document.getElementById('modalConstancia');
	if (modal) {
		modal.addEventListener('shown.bs.modal', () => {
			const empresaId = (saveBtn && saveBtn.getAttribute('data-id')) || window.ultimaEmpresaId;
			if (empresaId) {
				setFisicaTipo('rep');
				cargarDetalleEmpresaParaFisica(empresaId);
			}
		});
	}

	const registerPostergarConstancia = () => {
		if (!window.PostergarDocumentoManager) {
			return;
		}
		window.PostergarDocumentoManager.init({
			key: 'constancia',
			campo: 'constanciaSituacionFiscal',
			textareaId: 'constanciaPostergarObservaciones',
			counterId: 'constanciaPostergarCounter',
			buttonId: 'btnPostergarConstancia',
			badgeId: 'constanciaPostergarBadge',
			remainingId: 'constanciaPostergarRemaining',
			historialId: 'constanciaPostergarHistorial',
			messageId: 'constanciaPostergarStatus',
			infoId: 'constanciaPostergarInfo',
			sectionId: 'constanciaPostergarSection',
		});
	};

	if (window.PostergarDocumentoManager) {
		registerPostergarConstancia();
	} else {
		window.addEventListener('postergar:manager-ready', registerPostergarConstancia, {
			once: true,
		});
	}

	window.addEventListener('permissions:updated', applyPermissionVisibility);
});
