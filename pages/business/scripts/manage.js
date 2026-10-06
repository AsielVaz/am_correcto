// Renderiza el encabezado de la tabla de empresas según las columnas visibles
function renderEmpresasTableHeader() {
	const theadRow = document.getElementById('empresas-thead-row');
	if (!theadRow) return;
	theadRow.innerHTML = '';
	const columnasVisibles = getColumnasVisibles();
	// Definición de metadatos de columnas
	const meta = [
		{ key: 'logo', label: '', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'razon', label: '', style: 'min-width: 150px; max-width: 150px;' },
		{
			key: 'documentos_permanentes',
			label: 'Documentos<br>permanentes',
			style: 'min-width: 60px; max-width: 60px;',
		},
		{ key: 'comprobanteDom', label: 'Comprobante de<br>domicilio', style: 'min-width: 60px; max-width: 60px;' },
		{
			key: 'constanciaSf',
			label: 'Constancia de<br>situación<br>fiscal',
			style: 'min-width: 60px; max-width: 60px;',
		},
		{ key: 'pdf', label: '32D', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'finDominio', label: 'Fin<br>dominio', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'telefono', label: 'Teléfono', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'correo', label: 'Correo<br>contacto', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'sitioWeb', label: 'Sitio<br>web', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'cuentas_bancarias', label: 'Cuentas<br>bancarias', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'contrasena_iofacturo', label: 'Contraseña<br>IOFacturo', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'contrasenas_bancos', label: 'Contraseñas<br>bancos', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'estados_cuenta', label: 'Estados de<br>cuenta', style: 'min-width: 60px; max-width: 60px;' },
		{ key: 'caratulas_bancarias', label: 'Carátulas<br>bancarias', style: 'min-width: 60px; max-width: 60px;' },
	];
	function colVisible(key) {
		if (!columnasVisibles) return true;
		return columnasVisibles.includes(key);
	}
	meta.forEach((col) => {
		if (!colVisible(col.key)) return;
		const th = document.createElement('th');
		th.className = 'text-center rotate-header';
		th.style = col.style;
		th.innerHTML = `<span class="heading-label" style="display: inline-block; transform: rotate(-60deg); white-space: nowrap;">${col.label}</span>`;
		theadRow.appendChild(th);
	});
}
// Cache de empresas por id para acceso rápido en modales
window.empresasCache = {};
const empresaDetalleCache = {};
const empresaStatusCache = {};
const statusFetchQueue = new Set();

const empresaDetalleInflight = {};
let appPermissions = window.appPermissions || { role: 'Usuario', canWrite: false, isAdmin: false };

// Notificación de copiado
function showCopyNotification(msg = '¡Copiado!') {
	let notif = document.getElementById('copy-toast-notif');
	if (!notif) {
		notif = document.createElement('div');
		notif.id = 'copy-toast-notif';
		notif.style.position = 'fixed';
		notif.style.bottom = '32px';
		notif.style.left = '50%';
		notif.style.transform = 'translateX(-50%)';
		notif.style.background = 'rgba(40,40,40,0.95)';
		notif.style.color = '#fff';
		notif.style.padding = '10px 24px';
		notif.style.borderRadius = '24px';
		notif.style.fontSize = '1rem';
		notif.style.zIndex = 9999;
		notif.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
		notif.style.opacity = '0';
		notif.style.pointerEvents = 'none';
		notif.style.transition = 'opacity 0.25s';
		document.body.appendChild(notif);
	}
	notif.textContent = msg;
	notif.style.opacity = '1';
	setTimeout(() => {
		notif.style.opacity = '0';
	}, 1200);
}

// Utilidad para crear un span clickeable que copia el texto
function buildCopyableSpan(text, tooltip = 'Copiar', notification = '¡Copiado!') {
	const safeText = (text || '').toString();
	return `<span class="copyable-text" style="cursor:pointer;user-select:all;" title="${tooltip}"
		onclick="navigator.clipboard.writeText('${safeText.replace(
			/'/g,
			"\\'"
		)}').then(() => { if (typeof showCopyNotification === 'function') showCopyNotification('${notification}'); })"
	>${safeText}</span>`;
}

const DETAIL_SECTIONS = [
	{
		section: 'cuentas_bancarias',
		label: 'Cuentas',
		modal: '#modalCuentas',
		tipo: 'cuentas',
		populate: populateCuentasModal,
		evaluator: (detalle) => detalle && Array.isArray(detalle.cuentas) && detalle.cuentas.length,
	},
	{
		section: 'contrasena_iofacturo',
		label: 'IOFacturo',
		modal: '#modalContrasenaIOFacturo',
		tipo: 'iofacturo',
		populate: populateContrasenaIOFacturoModal,
		evaluator: (detalle) => detalle && Array.isArray(detalle.contrasIofacturo) && detalle.contrasIofacturo.length,
	},
	{
		section: 'contrasenas_bancos',
		label: 'Bancos',
		modal: '#modalContrasenasBancos',
		tipo: 'bancos',
		populate: populateContrasenasBancosModal,
		evaluator: (detalle) => detalle && Array.isArray(detalle.contrasBanco) && detalle.contrasBanco.length,
	},
	{
		section: 'documentos_permanentes',
		label: 'Documentos',
		modal: '#modalDocumentosPermanentes',
		tipo: 'documentos',
		populate: populateDocumentosPermanentesModal,
		evaluator: (detalle) =>
			detalle && Array.isArray(detalle.documentosPermanentes) && detalle.documentosPermanentes.length,
	},
	{
		section: 'estados_cuenta',
		label: 'Estados',
		modal: '#modalEstadosCuenta',
		tipo: 'estados',
		populate: populateEstadosCuentaModal,
		evaluator: (detalle) =>
			detalle &&
			Array.isArray(detalle.estadosCuenta) &&
			detalle.estadosCuenta.some((row) => row.documento || row.documento1),
	},
	{
		section: 'caratulas_bancarias',
		label: 'Carátulas',
		modal: '#modalCaratulasBancarias',
		tipo: 'caratulas',
		populate: populateCaratulasBancariasModal,
		evaluator: (detalle) =>
			detalle &&
			Array.isArray(detalle.caratulas) &&
			detalle.caratulas.some((row) => row.documento || row.documento1),
	},
];
// Si hay lógica de renderizado de filas para estados de cuenta o carátulas bancarias, omitir la columna 'Tipo' y su valor

const DETAIL_SECTION_MAP = DETAIL_SECTIONS.reduce((acc, item) => {
	acc[item.section] = item;
	return acc;
}, {});

const DETAIL_MODAL_MAP = DETAIL_SECTIONS.reduce((acc, item) => {
	acc[item.modal] = item;
	return acc;
}, {});

window.addEventListener('permissions:updated', (event) => {
	appPermissions = event.detail || window.appPermissions || appPermissions;
});

// Helpers para placeholder de logo
function getInitialsFromName(name) {
	const text = (name || '').toString().trim();
	if (!text) return '?';
	const parts = text.replace(/\s+/g, ' ').split(' ').filter(Boolean);
	if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
	return (parts[0][0] + parts[1][0]).toUpperCase();
}

function stringToHslColor(str, s = 65, l = 48) {
	let hash = 0;
	for (let i = 0; i < str.length; i++) {
		hash = str.charCodeAt(i) + ((hash << 5) - hash);
	}
	const h = Math.abs(hash) % 360;
	return `hsl(${h}, ${s}%, ${l}%)`;
}

function generateLogoPlaceholderHtml(name) {
	const initials = getInitialsFromName(name);
	const bg = stringToHslColor(name || initials);
	const fg = '#fff';
	return `
		<div style="width:44px;height:44px;border-radius:10px;background:${bg};color:${fg};
					display:flex;align-items:center;justify-content:center;
					font-weight:700;font-size:16px;letter-spacing:0.5px;user-select:none;">
			${initials}
		</div>
	`;
}

// Helpers de nombres y mini avatar para celdas de auditoría
function extractFirstName(fullName) {
	const text = (fullName || '').toString().trim().replace(/\s+/g, ' ');
	if (!text) return '';
	const [first] = text.split(' ');
	return first || '';
}

function generateMiniAvatarHtml(name) {
	const text = (name || '').toString().trim();
	const initial = text ? text[0].toUpperCase() : '?';
	const bg = stringToHslColor(text || initial, 60, 45);
	const fg = '#fff';
	return `
		<span class="d-inline-flex align-items-center justify-content-center rounded-circle"
			style="width:24px;height:24px;background:${bg};color:${fg};font-size:12px;font-weight:700;line-height:1;">
			${initial}
		</span>
	`;
}

function ensureCanWrite(actionDescription) {
	if (appPermissions && appPermissions.canWrite) {
		return true;
	}
	const message = actionDescription
		? `No tienes permisos para ${actionDescription}.`
		: 'No tienes permisos para realizar esta acción.';
	alert(message);
	return false;
}

function canUserWrite() {
	return !!(appPermissions && appPermissions.canWrite);
}

function canUserMarkReviewed() {
	if (!appPermissions) return false;
	return appPermissions.isAdmin === true; // Solo Admin puede verificar
}

const ESTADO_VISUAL_CONFIG = {
	completado: {
		icon: 'bx bx-check-circle',
		className: 'text-success',
		label: 'Completado',
	},
	revision: {
		icon: 'bx bx-search-alt',
		className: 'text-info',
		label: 'Revisión',
	},
	rechazado: {
		icon: 'bx bx-error',
		className: 'text-danger',
		label: 'Rechazado',
	},
	archivo_faltante: {
		icon: 'bx bx-time-five',
		className: 'text-warning',
		label: 'Archivo faltante',
	},
	sin_datos: {
		icon: 'bx bx-x-circle',
		className: 'text-danger',
		label: 'Sin datos',
	},
	urgente: {
		icon: 'bx bxs-circle pulse-red',
		className: 'text-danger',
		label: 'Urgente',
	},
};

function escapeAttribute(value) {
	return (value || '').toString().replace(/"/g, '&quot;');
}

function escapeHtml(value) {
	if (value === null || value === undefined) return '';
	return String(value)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#39;');
}

function getDownloadButtonHtml(url, label = 'Descargar') {
	if (!url) {
		return '';
	}
	const resolvedUrl = typeof window.resolveAppPath === 'function' ? window.resolveAppPath(url) : url;
	const safeLabel = escapeAttribute(label);
	return `
		<a href='${escapeAttribute(resolvedUrl)}' class="${getActionButtonClass(
		'outline-primary'
	)}" download aria-label="${safeLabel}" title="${safeLabel}">
			<i class='bx bx-download'></i>
		</a>
	`;
}

function getMissingDocumentBadgeHtml() {
	return '<span class="badge bg-warning-subtle text-warning" title="El archivo físico no existe">Archivo no disponible</span>';
}

function renderActionGroup(buttons, fallback = '<span class="text-muted small">Sin acciones</span>') {
	const validButtons = (buttons || []).filter(Boolean);
	if (!validButtons.length) {
		return fallback;
	}
	return `<div class="d-inline-flex flex-wrap align-items-center justify-content-center gap-2">${validButtons.join(
		''
	)}</div>`;
}

function getActionButtonClass(variant = 'outline-primary') {
	return `btn btn-${variant} btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center`;
}

function normalizarEstadoDocumentoPermanente(estado) {
	const valor = (estado || '').toString().toLowerCase();
	switch (valor) {
		case 'archivo_faltante':
		case 'urgente':
		case 'documento pendiente':
		case 'documento_pendiente':
		case 'pendiente_documento':
			return 'archivo_faltante';
		case 'pendiente de revision':
		case 'pendiente_de_revision':
		case 'pendiente revision':
		case 'pendiente-revision':
		case 'pendiente':
		case 'en revision':
		case 'en_revison':
		case 'en-revision':
		case 'revision pendiente':
		case 'revision_pendiente':
		case 'revision-pendiente':
			return 'revision';
		case 'revision':
			return 'revision';
		case 'rechazado':
			return 'rechazado';
		case 'completado':
		case 'normal':
			return 'completado';
		case 'sin_datos':
			return 'sin_datos';
		default:
			return 'completado';
	}
}

/**
 * Construye el bloque HTML para mostrar el estado
 * @param {string} estado
 * @param {string} tipo
 * @returns {string}
 */
const ESTADO_ICON_SIZE_CLASS = 'fs-18'; // tamaño aumentado de iconos de estado
function getEstadoIcono(estado, tipo) {
	const config = ESTADO_VISUAL_CONFIG[estado] || ESTADO_VISUAL_CONFIG.sin_datos;
	return `
		<span class="estado-icono d-inline-flex align-items-center justify-content-center" data-estado="${estado}" data-tipo="${tipo}" style="min-width:42px;min-height:42px;">
			<i class='${config.icon} ${config.className} ${ESTADO_ICON_SIZE_CLASS}'></i>
		</span>
	`;
}

// Helpers Fin de Dominio
function parseFechaFlexibleToDateEndOfDay(value) {
	if (!value) return null;
	let d = null;
	if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
		d = new Date(value + 'T23:59:59');
	} else if (/^\d{2}\/\d{2}\/\d{4}$/.test(value)) {
		const [dd, mm, yyyy] = value.split('/');
		d = new Date(`${yyyy}-${mm}-${dd}T23:59:59`);
	}
	return isNaN(d?.getTime?.()) ? null : d;
}

function getFinDominioEstado(fechaStr) {
	if (!fechaStr || fechaStr === '0000-00-00') return 'sin_datos';
	const fecha = parseFechaFlexibleToDateEndOfDay(fechaStr);
	if (!fecha) return 'sin_datos';
	const ahora = new Date();
	const hoy = new Date(ahora.getFullYear(), ahora.getMonth(), ahora.getDate(), 23, 59, 59);
	const diffMs = fecha - hoy;
	const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
	if (diffDays < 0) return 'sin_datos'; // vencido (solicitado: no usar 'rechazado')
	if (diffDays <= 30) return 'urgente'; // último mes
	return 'completado';
}

function formatFechaHoraCorta(valor, { emptyText = 'Sin registro' } = {}) {
	if (!valor) return emptyText;
	const rawStr = String(valor).trim();
	if (!rawStr) return emptyText;
	const isoCandidate = rawStr.includes('T') ? rawStr : rawStr.replace(' ', 'T');
	let formatted = null;
	const fecha = new Date(isoCandidate);
	if (!Number.isNaN(fecha.getTime())) {
		formatted = fecha.toLocaleString('es-MX', { dateStyle: 'short', timeStyle: 'short' });
	} else {
		const normalized = rawStr.replace('T', ' ').replace(/\s+/g, ' ').trim();
		const [fechaPart = '', horaPartRaw = ''] = normalized.split(' ');
		let horaPart = horaPartRaw.replace(/[^0-9:]/g, '');
		if (horaPart.length >= 5) {
			horaPart = horaPart.slice(0, 5);
		} else {
			horaPart = '';
		}
		let fechaFormateada = fechaPart;
		if (/^\d{4}-\d{2}-\d{2}$/.test(fechaPart)) {
			const [yyyy, mm, dd] = fechaPart.split('-');
			fechaFormateada = `${dd}/${mm}/${yyyy}`;
		} else if (/^\d{2}\/\d{2}\/\d{4}$/.test(fechaPart)) {
			fechaFormateada = fechaPart;
		}
		formatted = horaPart ? `${fechaFormateada} ${horaPart}` : fechaFormateada;
	}
	return formatted || emptyText;
}

/**
 * Renderiza la tabla de empresas con los datos proporcionados
 * @param {Array} data - Array de empresas
 */
const BASE_STATE_COLUMNS = [
	{ key: 'comprobanteDom', tipo: 'comprobante', modal: '#modalComprobante', label: 'Comprobante' },
	{ key: 'constanciaSf', tipo: 'constancia', modal: '#modalConstancia', label: 'Constancia' },
	{ key: 'pdf', tipo: '32d', modal: '#modal32D', label: '32D' },
	{ key: 'finDominio', tipo: 'finDominio', modal: '#modalFinDominio', label: 'Fin dominio' },
	{ key: 'telefono', tipo: 'telefono', modal: '#modalTelefono', label: 'Teléfono' },
	{ key: 'correo', tipo: 'correo', modal: '#modalCorreoContacto', label: 'Correo' },
	{ key: 'sitioWeb', tipo: 'web', modal: '#modalSitioWeb', label: 'Sitio web' },
];

function getEstadoInicialDetalle(empresaId, section) {
	const status = empresaStatusCache[empresaId];
	if (status && status[section]) return status[section];
	return 'cargando';
}

function buildEstadoAnchor(row, config, estadoHtml) {
	const anchor = document.createElement('a');
	anchor.href = '#';
	anchor.dataset.bsToggle = 'modal';
	anchor.dataset.bsTarget = config.modal;
	anchor.dataset.id = row.id;
	anchor.className = 'estado-link d-inline-flex align-items-center justify-content-center text-decoration-none';
	anchor.innerHTML = estadoHtml;
	return anchor;
}

function buildEstadoWrapper(row, config) {
	const estados = row.estadosCampos || {};
	let estado = estados[config.key] || 'sin_datos';
	// Cálculo especial para Fin de Dominio: urgente en el último mes, rechazado si vencido
	if (config.key === 'finDominio') {
		const fecha = row.finDominio || row.fin_dominio || '';
		if (fecha) {
			estado = getFinDominioEstado(fecha);
		}
	}
	// Cálculo automático de urgente por periodo vencido para campos clave
	const now = new Date();
	const hoy = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59);
	const vencimientoMap = {
		comprobanteDom: row.periodo_comprobanteDom || row.periodoComprobanteDom,
		constanciaSf: row.periodo_constancia || row.periodoConstancia,
		pdf: row.periodo_32d || row.periodo32d,
		telefono: row.periodo_telefono || row.periodoTel,
		correo: row.periodo_correo || row.periodoCorreo,
		sitioWeb: row.periodo_sitiow || row.periodoSitio,
	};
	if (vencimientoMap[config.key]) {
		let fechaVenc = vencimientoMap[config.key];
		// Extraer fecha si viene con prefijo (ej: cadaMes-2022-12-27)
		const match = fechaVenc && fechaVenc.match(/(\d{4}-\d{2}-\d{2})$/);
		if (match) {
			fechaVenc = match[1];
		}
		let fechaObj = null;
		if (/^\d{4}-\d{2}-\d{2}$/.test(fechaVenc)) {
			fechaObj = new Date(fechaVenc + 'T23:59:59');
		} else if (/^\d{2}\/\d{2}\/\d{4}$/.test(fechaVenc)) {
			const [dd, mm, yyyy] = fechaVenc.split('/');
			fechaObj = new Date(`${yyyy}-${mm}-${dd}T23:59:59`);
		}
		if (fechaObj && fechaObj <= hoy) {
			estado = 'urgente';
		}
	}
	const estadoHtml = getEstadoIcono(estado, config.tipo);
	return buildEstadoAnchor(row, config, estadoHtml);
}

function buildDetalleEstadoWrapper(row, config) {
	const estadoInicial = getEstadoInicialDetalle(row.id, config.section);
	const estadoHtml = `
		<span class="estado-wrapper" data-empresa-id="${row.id}" data-section="${config.section}" data-tipo="${config.tipo}">
			${getEstadoIcono(estadoInicial, config.tipo)}
		</span>
	`;
	return buildEstadoAnchor(row, config, estadoHtml);
}

// Definición de columnas permitidas por departamento (solo para rol Usuario, según requerimiento)
const DEPARTAMENTO_COLUMNAS = {
	Documentos: ['logo', 'razon', 'documentos_permanentes', 'comprobanteDom', 'constanciaSf', 'pdf'],
	Dev: [
		'logo',
		'razon',
		'documentos_permanentes',
		'comprobanteDom',
		'constanciaSf',
		'pdf',
		'finDominio',
		'telefono',
		'correo',
		'sitioWeb',
		'cuentas_bancarias',
		'contrasena_iofacturo',
		'contrasenas_bancos',
		'estados_cuenta',
		'caratulas_bancarias',
	],
	Bancario: [
		'logo',
		'razon',
		'documentos_permanentes',
		'comprobanteDom',
		'constanciaSf',
		'pdf',
		'cuentas_bancarias',
		'contrasenas_bancos',
		'caratulas_bancarias',
		'estados_cuenta',
	],
	Contabilidad: [
		'logo',
		'razon',
		'documentos_permanentes',
		'contrasena_iofacturo',
		'caratulas_bancarias',
		'estados_cuenta',
	],
	'Contabilidad auxiliar': ['logo', 'razon', 'documentos_permanentes', 'contrasena_iofacturo'],
	Facturación: [
		'logo',
		'razon',
		'documentos_permanentes',
		'contrasena_iofacturo',
		'caratulas_bancarias',
		'estados_cuenta',
	],
	'Sin departamento': [
		'logo',
		'razon',
		'documentos_permanentes',
		'comprobanteDom',
		'constanciaSf',
		'pdf',
		'telefono',
		'correo',
		'sitioWeb',
		'cuentas_bancarias',
		'contrasena_iofacturo',
		'contrasenas_bancos',
		'estados_cuenta',
		'caratulas_bancarias',
	],
};

function getColumnasVisibles() {
	if (!appPermissions || appPermissions.role !== 'Usuario') {
		// Si no es Usuario, mostrar todas las columnas
		return null;
	}
	// Columnas base visibles para todos los departamentos
	const columnasBase = [
		'logo',
		'razon',
		'documentos_permanentes',
		'comprobanteDom',
		'constanciaSf',
		'pdf',
		'telefono',
		'correo',
		'sitioWeb',
	];
	const depto = appPermissions.departamento || '';
	if (depto === 'Dev') {
		return null;
	}
	if (depto && DEPARTAMENTO_COLUMNAS[depto]) {
		// Unir columnas base con las del departamento, sin duplicados
		return [...columnasBase, ...DEPARTAMENTO_COLUMNAS[depto].filter((c) => !columnasBase.includes(c))];
	}
	// Si el departamento no existe, solo mostrar las columnas base
	return columnasBase;
}

function renderEmpresasTable(data) {
	renderEmpresasTableHeader();
	const tbody = document.getElementById('empresas-tbody');
	tbody.innerHTML = '';
	window.empresasCache = {};
	Object.keys(empresaStatusCache).forEach((key) => delete empresaStatusCache[key]);

	// Determinar columnas visibles
	const columnasVisibles = getColumnasVisibles();

	if (!data.length) {
		tbody.innerHTML = '<tr><td colspan="19" class="text-center">Sin resultados</td></tr>';
		return;
	}

	data.forEach((row, index) => {
		window.empresasCache[row.id] = row;
		if (row.detalleEstados && typeof row.detalleEstados === 'object') {
			empresaStatusCache[row.id] = row.detalleEstados;
		}
		const tr = document.createElement('tr');

		// Helper para saber si mostrar una columna
		function colVisible(key) {
			if (!columnasVisibles) return true;
			return columnasVisibles.includes(key);
		}

		// Logo
		if (colVisible('logo')) {
			const tdLogo = document.createElement('td');
			tdLogo.className = 'py-2 px-2 text-center';
			const wrapperOuter = document.createElement('div');
			wrapperOuter.style.display = 'flex';
			wrapperOuter.style.alignItems = 'center';
			wrapperOuter.style.justifyContent = 'center';
			const wrapper = document.createElement('div');
			wrapper.style.background = '#f8f9fa';
			wrapper.style.borderRadius = '12px';
			wrapper.style.padding = '6px';
			wrapper.style.boxShadow = '0 1px 4px rgba(0,0,0,0.04)';
			wrapper.style.minWidth = '56px';
			wrapper.style.minHeight = '56px';
			wrapper.style.display = 'flex';
			wrapper.style.alignItems = 'center';
			wrapper.style.justifyContent = 'center';
			const usePlaceholder = !row.logo;
			if (usePlaceholder) {
				wrapper.innerHTML = generateLogoPlaceholderHtml(row.razon || row.nombre || '');
			} else {
				const img = document.createElement('img');
				img.src = row.logo;
				img.alt = 'Logo';
				img.style.maxWidth = '44px';
				img.style.maxHeight = '44px';
				img.style.objectFit = 'contain';
				img.style.display = 'block';
				img.addEventListener('error', () => {
					wrapper.innerHTML = generateLogoPlaceholderHtml(row.razon || row.nombre || '');
				});
				wrapper.appendChild(img);
			}
			wrapperOuter.appendChild(wrapper);
			tdLogo.appendChild(wrapperOuter);
			tr.appendChild(tdLogo);
		}

		// Razón social
		if (colVisible('razon')) {
			const tdRazon = document.createElement('td');
			tdRazon.className = 'py-2 px-2 text-start fw-medium sticky-col razon-col';
			tdRazon.style.maxWidth = '200px';
			tdRazon.style.minWidth = '200px';
			tdRazon.style.wordBreak = 'break-word';
			let razonSocial = row.razon || '';
			const span = document.createElement('span');
			span.className = 'razon-social-ellipsis';
			span.title = razonSocial;
			span.textContent = razonSocial;
			span.addEventListener('click', async function (e) {
				if (!razonSocial) return;
				try {
					await navigator.clipboard.writeText(razonSocial);
					showCopyNotification('¡Copiado!');
				} catch {
					showCopyNotification('No se pudo copiar');
				}
			});
			tdRazon.appendChild(span);
			tr.appendChild(tdRazon);
		}

		// Documentos permanentes
		if (colVisible('documentos_permanentes')) {
			const documentosConfig = DETAIL_SECTIONS.find((section) => section.section === 'documentos_permanentes');
			if (documentosConfig) {
				const tdDocumentos = document.createElement('td');
				tdDocumentos.className = 'py-2 px-2 text-center';
				tdDocumentos.appendChild(buildDetalleEstadoWrapper(row, documentosConfig));
				tr.appendChild(tdDocumentos);
			}
		}

		// Columnas base con estado
		BASE_STATE_COLUMNS.forEach((config) => {
			if (!colVisible(config.key)) return;
			const td = document.createElement('td');
			td.className = 'py-2 px-2 text-center';
			td.appendChild(buildEstadoWrapper(row, config));
			if (typeof config.afterContent === 'function') {
				const extraNode = config.afterContent(row);
				if (extraNode) td.appendChild(extraNode);
			}
			tr.appendChild(td);
		});

		// Secciones que dependen de detalle
		DETAIL_SECTIONS.forEach((config) => {
			if (config.section === 'documentos_permanentes') return;
			if (!colVisible(config.section)) return;
			const td = document.createElement('td');
			td.className = 'py-2 px-2 text-center';
			// Lógica especial para IMSS: solo mostrar si aplica_imss == 1
			if (config.section === 'imss') {
				if (row.aplica_imss === 1 || row.aplica_imss === '1') {
					td.appendChild(buildDetalleEstadoWrapper(row, config));
				} else {
					td.innerHTML = '';
				}
			} else {
				td.appendChild(buildDetalleEstadoWrapper(row, config));
			}
			tr.appendChild(td);
		});

		tbody.appendChild(tr);

		// Encolar carga de detalle solo cuando no haya información precargada
		if (!empresaStatusCache[row.id]) {
			enqueueStatusFetch(row.id, index * 100);
		}
	});
}

function enqueueStatusFetch(empresaId, delay = 0) {
	if (empresaStatusCache[empresaId] || statusFetchQueue.has(empresaId)) {
		return;
	}
	statusFetchQueue.add(empresaId);
	setTimeout(async () => {
		try {
			const detalle = await fetchEmpresaDetalle(empresaId);
			actualizarEstadoSecciones(empresaId, detalle);
		} catch (error) {
			console.error('Error al precargar estado de empresa:', error);
			DETAIL_SECTIONS.forEach((config) => {
				setEstadoIconoSeccion(empresaId, config.section, 'sin_datos', config.tipo);
			});
		} finally {
			statusFetchQueue.delete(empresaId);
		}
	}, delay);
}

async function fetchEmpresaDetalle(empresaId) {
	if (empresaDetalleCache[empresaId]) {
		return empresaDetalleCache[empresaId];
	}
	if (empresaDetalleInflight[empresaId]) {
		return empresaDetalleInflight[empresaId];
	}
	const promise = (async () => {
		const res = await fetch(`../../api/routes/apiEmpresaDetalle.php?id=${empresaId}`);
		if (!res.ok) {
			throw new Error('No se pudo obtener el detalle de la empresa');
		}
		const data = await res.json();
		if (!data.success) {
			throw new Error(data.error || 'Error al obtener detalle de la empresa');
		}
		empresaDetalleCache[empresaId] = data.data;
		return data.data;
	})();
	empresaDetalleInflight[empresaId] = promise;
	return promise.finally(() => {
		delete empresaDetalleInflight[empresaId];
	});
}

function actualizarEstadoSecciones(empresaId, detalle) {
	if (!detalle) return;
	const statusRecord = empresaStatusCache[empresaId] || {};
	DETAIL_SECTIONS.forEach((config) => {
		const hasData = typeof config.evaluator === 'function' ? Boolean(config.evaluator(detalle)) : false;
		let estado = hasData ? 'completado' : 'sin_datos';
		const estadoPrevio = statusRecord[config.section];
		if (config.section === 'documentos_permanentes' && hasData) {
			const docs = Array.isArray(detalle.documentosPermanentes) ? detalle.documentosPermanentes : [];
			const estadosNorm = docs.map((doc) =>
				normalizarEstadoDocumentoPermanente(doc.estadoDocumento || doc.estado_documento || doc.estado)
			);
			if (estadosNorm.includes('rechazado')) {
				estado = 'rechazado';
			} else if (estadosNorm.includes('archivo_faltante')) {
				estado = 'archivo_faltante';
			} else if (estadosNorm.includes('revision')) {
				estado = 'revision';
			} else if (estadosNorm.length) {
				estado = 'completado';
			}
		}
		if (hasData && estadoPrevio === 'archivo_faltante') {
			estado = 'archivo_faltante';
		} else if (hasData && estadoPrevio === 'rechazado') {
			estado = 'rechazado';
		} else if (hasData && estadoPrevio === 'revision' && estado !== 'archivo_faltante' && estado !== 'rechazado') {
			estado = 'revision';
		}
		statusRecord[config.section] = estado;
		setEstadoIconoSeccion(empresaId, config.section, estado, config.tipo);
	});
	empresaStatusCache[empresaId] = statusRecord;
}

function setEstadoIconoSeccion(empresaId, section, estado, tipo) {
	const wrappers = document.querySelectorAll(
		`.estado-wrapper[data-empresa-id="${empresaId}"][data-section="${section}"]`
	);
	wrappers.forEach((wrapper) => {
		const iconHtml = getEstadoIcono(estado, tipo || wrapper.dataset.tipo || 'default');
		wrapper.innerHTML = iconHtml;
	});
}

/**
 * Renderiza la paginación
 * @param {number} page - Página actual
 * @param {number} pages - Total de páginas
 */
function renderPagination(page, pages) {
	const pag = document.getElementById('empresas-pagination');
	pag.innerHTML = '';
	if (pages <= 1) return;

	pag.appendChild(createPageItem(1, page > 1, '<i class="bx bx-chevrons-left"></i>', false, 'Primera página'));
	pag.appendChild(createPageItem(page - 1, page > 1, '<i class="bx bx-left-arrow-alt"></i>', false, 'Anterior'));

	let start = Math.max(1, page - 2);
	let end = Math.min(pages, page + 2);

	if (page <= 3) {
		end = Math.min(5, pages);
	} else if (page >= pages - 2) {
		start = Math.max(1, pages - 4);
	}

	if (start > 1) {
		pag.appendChild(createPageItem(null, false, '...', false));
	}

	for (let i = start; i <= end; i++) {
		pag.appendChild(createPageItem(i, true, i, i === page));
	}

	if (end < pages) {
		pag.appendChild(createPageItem(null, false, '...', false));
	}

	pag.appendChild(
		createPageItem(page + 1, page < pages, '<i class="bx bx-right-arrow-alt"></i>', false, 'Siguiente')
	);
	pag.appendChild(
		createPageItem(pages, page < pages, '<i class="bx bx-chevrons-right"></i>', false, 'Última página')
	);
}

/**
 * Crea un elemento de paginación
 * @param {number|null} page - Número de página
 * @param {boolean} enabled - Si el elemento está habilitado
 * @param {string} label - Etiqueta del elemento
 * @param {boolean} active - Si el elemento está activo
 * @param {string} title - Título del elemento
 * @returns {HTMLElement} Elemento li de paginación
 */
function createPageItem(page, enabled, label, active, title) {
	const li = document.createElement('li');
	li.className = 'page-item' + (active ? ' active' : '') + (!enabled ? ' disabled' : '');
	const a = document.createElement('a');
	a.className = 'page-link';
	a.href = '#';
	a.innerHTML = label;
	if (title) a.title = title;

	if (enabled && page) {
		a.addEventListener('click', function (e) {
			e.preventDefault();
			loadEmpresas(page);
		});
	}

	li.appendChild(a);
	return li;
}

/**
 * Renderiza la información de resultados
 * @param {number} page - Página actual
 * @param {number} total - Total de resultados
 * @param {number} limit - Límite por página
 */
function renderInfo(page, total, limit) {
	const info = document.getElementById('empresas-info');
	const showing = Math.min(total, page * limit) - (page - 1) * limit;
	info.innerHTML = `Mostrando <span class="fw-semibold">${showing}</span> de <span class="fw-semibold">${total}</span> resultados`;
}

/**
 * Carga las empresas desde la API
 * @param {number} page - Página a cargar
 */
async function loadEmpresas(page = 1) {
	const search = document.getElementById('search-input').value.trim();
	const limit = 100;
	let url = `../../api/routes/apiEmpresasListado.php?page=${page}&limit=${limit}`;
	if (search) url += `&search=${encodeURIComponent(search)}`;

	const tbody = document.getElementById('empresas-tbody');
	tbody.innerHTML = '<tr><td colspan="19" class="text-center">Cargando...</td></tr>';

	try {
		const res = await fetch(url);
		const data = await res.json();
		renderEmpresasTable(data.data);
		renderPagination(data.page, data.pages);
		renderInfo(data.page, data.total, data.limit);
	} catch (e) {
		tbody.innerHTML = '<tr><td colspan="19" class="text-center text-danger">Error al cargar datos</td></tr>';
	}
}

/**
 * Carga los detalles de una empresa
 * @param {string} id - ID de la empresa
 * @param {Function} callback - Función callback a ejecutar con los detalles
 */
async function cargarDetalleEmpresa(id, callback, options = {}) {
	const { forceReload = false } = options;
	try {
		if (forceReload) {
			delete empresaDetalleCache[id];
			delete empresaStatusCache[id];
		}
		const detalle = await fetchEmpresaDetalle(id);
		actualizarEstadoSecciones(id, detalle);
		if (callback) callback(detalle);
		return detalle;
	} catch (error) {
		console.error('Error al cargar detalle de empresa:', error);
		DETAIL_SECTIONS.forEach((config) => {
			setEstadoIconoSeccion(id, config.section, 'sin_datos', config.tipo);
		});
		return null;
	}
}

/**
 * Inicializa los event listeners y carga inicial
 */
function initializeApp() {
	loadEmpresas();
	assignEstadoPopoverContent();
	initializeEstadoPopovers();
	initializeGestionPopovers();

	// Event listener para búsqueda
	document.getElementById('search-form').addEventListener('submit', function (e) {
		e.preventDefault();
		const searchInput = document.getElementById('search-input');
		if (searchInput.value.trim() === '') {
			searchInput.value = '';
			loadEmpresas(1);
		} else {
			loadEmpresas(1);
		}
	});

	// Event listener para input de búsqueda
	document.getElementById('search-input').addEventListener('input', function (e) {
		const value = this.value.trim();
		if (value.length === 0) {
			loadEmpresas(1);
		} else if (value.length >= 3) {
			loadEmpresas(1);
		} else {
			// Si hay menos de 3 caracteres, limpiar la tabla
			const tbody = document.getElementById('empresas-tbody');
			if (tbody) {
				tbody.innerHTML =
					'<tr><td colspan="19" class="text-center align-middle" style="height:120px;">Escribe al menos 3 caracteres para buscar</td></tr>';
			}
		}
	});

	// Event listener principal para clicks en la tabla
	setupTableEventListeners();

	// Delegación de eventos para copiar y eliminar en cuentas bancarias
	document.getElementById('tbody-cuentas-bancarias').addEventListener('click', async function (e) {
		if (e.target.closest('.btn-eliminar-cuenta')) {
			const btn = e.target.closest('.btn-eliminar-cuenta');
			const idCuenta = btn.getAttribute('data-id');
			const idEmpresa = document.getElementById('inputIdEmpresaCuenta').value;
			if (!idCuenta) return;
			mostrarModalConfirmacion('cuentas_bancarias', idCuenta, idEmpresa);
		}
		// Copiar campo
		if (e.target.closest('.btn-copy-campo')) {
			const btn = e.target.closest('.btn-copy-campo');
			const value = btn.getAttribute('data-copy') || '';
			try {
				await navigator.clipboard.writeText(value);
				showCopyNotification('¡Copiado!');
			} catch {
				showCopyNotification('No se pudo copiar');
			}
		}
		// Copiar fila
		if (e.target.closest('.btn-copy-fila')) {
			const tr = e.target.closest('tr');
			if (tr) {
				const ths = ['Banco', 'Número de cuenta', 'CLABE interbancaria', 'Moneda'];
				const tds = Array.from(tr.querySelectorAll('td'));
				let texto = '';
				// Obtener nombre de la empresa si está disponible en window.empresasCache
				let idEmpresa = document.getElementById('inputIdEmpresaCuenta')?.value || '';
				let razon = '';
				if (window.empresasCache && idEmpresa && window.empresasCache[idEmpresa]) {
					razon = window.empresasCache[idEmpresa].razon || '';
				} else {
					// Fallback: buscar en la fila si hay una celda con la razón social
					const table = tr.closest('table');
					if (table) {
						const headerCells = Array.from(table.querySelectorAll('thead th'));
						const razonIdx = headerCells.findIndex((th) => th.textContent?.toLowerCase().includes('razon'));
						if (razonIdx >= 0) {
							const razonCell = tr.querySelectorAll('td')[razonIdx];
							if (razonCell) razon = razonCell.textContent.trim();
						}
					}
				}
				if (razon) {
					texto += razon + '\n';
				}
				for (let i = 0; i < 4; i++) {
					const label = ths[i];
					const valor = tds[i]?.innerText?.trim() || '';
					texto += `${label}: ${valor}\n`;
				}
				try {
					await navigator.clipboard.writeText(texto.trim());
					showCopyNotification('¡Fila copiada!');
				} catch {
					showCopyNotification('No se pudo copiar');
				}
			}
		}
	});

	// Delegación para copiar en otros modales (SAT, IOFacturo, Bancos, etc)
	document.body.addEventListener('click', async function (e) {
		// SAT, IOFacturo, Bancos, etc: copiar campo
		if (e.target.closest('.btn-copy-field')) {
			const btn = e.target.closest('.btn-copy-field');
			const value = btn.getAttribute('data-value') || btn.getAttribute('data-copy') || '';
			try {
				await navigator.clipboard.writeText(value);
				showCopyNotification('¡Copiado!');
			} catch {
				showCopyNotification('No se pudo copiar');
			}
		}
		// Copiar fila/row
		if (e.target.closest('.btn-copy-row')) {
			const btn = e.target.closest('.btn-copy-row');
			const value = btn.getAttribute('data-rowcopy');
			if (value) {
				try {
					await navigator.clipboard.writeText(value);
					showCopyNotification('¡Fila copiada!');
				} catch {
					showCopyNotification('No se pudo copiar');
				}
			}
		}
	});
	setupEstadosPopover();
}

/**
 * Configura los event listeners para la tabla
 */
function setupTableEventListeners() {
	document.getElementById('empresas-table').addEventListener('click', function (e) {
		const target = e.target.closest('a[data-bs-toggle="modal"]');
		if (!target) return;

		const empresaId = target.getAttribute('data-id');
		const empresa = empresasCache[empresaId];
		if (!empresa) return;

		// Manejar cada tipo de modal
		handleModalClick(target, empresa);
	});
}

/**
 * Maneja los clicks en modales según el tipo
 * @param {HTMLElement} target - Elemento clickeado
 * @param {Object} empresa - Datos de la empresa
 */
function handleModalClick(target, empresa) {
	const modalTarget = target.dataset.bsTarget;

	let detallePromise = null;
	// Evitar precarga si el modal es Documentos permanentes para no duplicar peticiones
	const shouldPrefetch = empresa && empresa.id && modalTarget !== '#modalDocumentosPermanentes';
	if (shouldPrefetch) {
		detallePromise = cargarDetalleEmpresa(empresa.id, null, { forceReload: true }).catch((error) => {
			console.error('Error al refrescar detalle de empresa para modal:', error);
			return null;
		});
	}

	switch (modalTarget) {
		case '#modalComprobante':
			setupComprobanteModal(empresa);
			break;
		case '#modalConstancia':
			setupConstanciaModal(empresa);
			break;
		case '#modal32D':
			setup32DModal(empresa);
			break;
		case '#modalFinDominio':
			setupFinDominioModal(empresa);
			break;
		case '#modalTelefono':
			setupTelefonoModal(empresa);
			break;
		case '#modalCorreoContacto':
			setupCorreoContactoModal(empresa);
			break;
		case '#modalSitioWeb':
			setupSitioWebModal(empresa);
			break;
		case '#modalDocumentosPermanentes':
			// Evitar precarga y dejar que el script del modal maneje la carga en show.bs.modal
			break;
		default:
			// Para modales que requieren lazy loading
			setupLazyLoadModal(modalTarget, empresa, detallePromise);
			break;
	}
}

/**
 * Configura el modal de comprobante
 * @param {Object} empresa - Datos de la empresa
 */
function resetUploadControls(config) {
	if (!config) {
		return;
	}
	const inputEl = document.getElementById(config.inputId);
	if (inputEl) {
		inputEl.value = '';
	}
	const labelEl = document.getElementById(config.labelId);
	if (labelEl) {
		labelEl.textContent = '';
	}
	const buttonEl = document.getElementById(config.buttonId);
	if (buttonEl) {
		buttonEl.classList.add('d-none');
		buttonEl.disabled = true;
	}
}

function setupComprobanteModal(empresa) {
	const cont = document.getElementById('iframe-comprobante-container');
	let comprobante = empresa.comprobanteDom;

	if (comprobante && comprobante.includes('/Documentos/Comprobantes/')) {
		comprobante = comprobante.substring(comprobante.indexOf('/Documentos/Comprobantes/'));
	}

	cont.innerHTML = comprobante
		? `<iframe src='${comprobante}' width='100%' height='500px'></iframe>`
		: '<span class="text-muted">Sin archivo</span>';

	const saveButton = document.getElementById('btnGuardarComprobante');
	const inputEl = document.getElementById('inputComprobanteModal');
	if (saveButton) {
		saveButton.setAttribute('data-id', empresa.id);
	}
	if (inputEl) {
		inputEl.setAttribute('data-id', empresa.id);
	}
	resetUploadControls({
		inputId: 'inputComprobanteModal',
		labelId: 'nombreComprobanteArchivo',
		buttonId: 'btnGuardarComprobante',
	});

	if (window.PostergarDocumentoManager) {
		window.PostergarDocumentoManager.load('comprobante', empresa.id);
	} else {
		const handler = () => {
			if (window.PostergarDocumentoManager) {
				window.PostergarDocumentoManager.load('comprobante', empresa.id);
			}
		};
		window.addEventListener('postergar:manager-ready', handler, { once: true });
	}
}

/**
 * Configura el modal de constancia
 * @param {Object} empresa - Datos de la empresa
 */
function setupConstanciaModal(empresa) {
	const cont = document.getElementById('iframe-constancia-container');
	cont.innerHTML = empresa.constanciaSf
		? `<iframe src='${empresa.constanciaSf}' width='100%' height='500px'></iframe>`
		: '<span class="text-muted">Sin archivo</span>';

	const saveButton = document.getElementById('btnGuardarConstancia');
	const inputEl = document.getElementById('inputConstanciaModal');
	if (saveButton) {
		saveButton.setAttribute('data-id', empresa.id);
	}
	if (inputEl) {
		inputEl.setAttribute('data-id', empresa.id);
	}
	resetUploadControls({
		inputId: 'inputConstanciaModal',
		labelId: 'nombreConstanciaArchivo',
		buttonId: 'btnGuardarConstancia',
	});

	if (window.PostergarDocumentoManager) {
		window.PostergarDocumentoManager.load('constancia', empresa.id);
	} else {
		const handler = () => {
			if (window.PostergarDocumentoManager) {
				window.PostergarDocumentoManager.load('constancia', empresa.id);
			}
		};
		window.addEventListener('postergar:manager-ready', handler, { once: true });
	}
}

/**
 * Configura el modal 32D
 * @param {Object} empresa - Datos de la empresa
 */
function setup32DModal(empresa) {
	const cont = document.getElementById('iframe-32d-container');
	cont.innerHTML = empresa.pdf
		? `<iframe src='${empresa.pdf}' width='100%' height='500px'></iframe>`
		: '<span class="text-muted">Sin archivo</span>';

	const saveButton = document.getElementById('btnGuardar32D');
	const inputEl = document.getElementById('input32DModal');
	if (saveButton) {
		saveButton.setAttribute('data-id', empresa.id);
	}
	if (inputEl) {
		inputEl.setAttribute('data-id', empresa.id);
	}
	resetUploadControls({
		inputId: 'input32DModal',
		labelId: 'nombre32DArchivo',
		buttonId: 'btnGuardar32D',
	});

	if (window.PostergarDocumentoManager) {
		window.PostergarDocumentoManager.load('32d', empresa.id);
	} else {
		const handler = () => {
			if (window.PostergarDocumentoManager) {
				window.PostergarDocumentoManager.load('32d', empresa.id);
			}
		};
		window.addEventListener('postergar:manager-ready', handler, { once: true });
	}
}

/**
 * Configura el modal de fin de dominio
 * @param {Object} empresa - Datos de la empresa
 */
function setupFinDominioModal(empresa) {
	const fecha = empresa.finDominio || '';
	const fechaSpan = document.getElementById('finDominioFecha');
	const estadoDiv = document.getElementById('finDominioEstado');
	const estadoHint = document.getElementById('finDominioEstadoHint');

	if (!fecha || fecha === '0000-00-00') {
		fechaSpan.textContent = 'Fecha no definida';
		estadoDiv.innerHTML = `<span class="badge bg-secondary fs-6 px-3 py-2"><i class="bx bx-minus-circle me-1"></i> Sin información</span>`;
	} else {
		fechaSpan.textContent = fecha;

		let vigente = false;
		if (fecha) {
			const hoy = new Date();
			let fechaDom = null;

			if (/^\d{4}-\d{2}-\d{2}$/.test(fecha)) {
				fechaDom = new Date(fecha + 'T23:59:59');
			} else if (/^\d{2}\/\d{2}\/\d{4}$/.test(fecha)) {
				const [d, m, y] = fecha.split('/');
				fechaDom = new Date(`${y}-${m}-${d}T23:59:59`);
			}

			if (fechaDom) {
				const hoyYMD = hoy.toISOString().slice(0, 10);
				const domYMD = fechaDom.toISOString().slice(0, 10);
				vigente = domYMD >= hoyYMD;
			}
		}

		if (vigente) {
			// Calcular si está por vencer (<= 30 días)
			const estadoFin = getFinDominioEstado(fecha);
			if (estadoFin === 'urgente') {
				estadoDiv.innerHTML = `<span class="badge bg-warning fs-6 px-3 py-2"><i class="bx bx-time-five me-1"></i> Dominio por vencer</span>`;
				if (estadoHint) {
					const f = parseFechaFlexibleToDateEndOfDay(fecha);
					const dias = f ? Math.max(0, Math.ceil((f - new Date()) / (1000 * 60 * 60 * 24))) : null;
					estadoHint.textContent = dias !== null ? `Faltan ${dias} día(s) para el vencimiento.` : '';
				}
			} else {
				estadoDiv.innerHTML = `<span class="badge bg-success fs-6 px-3 py-2"><i class="bx bx-check-circle me-1"></i> Dominio vigente</span>`;
				if (estadoHint) estadoHint.textContent = '';
			}
		} else {
			estadoDiv.innerHTML = `<span class="badge bg-danger fs-6 px-3 py-2"><i class="bx bx-error-alt me-1"></i> Dominio vencido</span>`;
			if (estadoHint)
				estadoHint.textContent = 'El dominio ya está vencido. Actualiza la fecha si ya fue renovado.';
		}
	}

	if (typeof window.updateFinDominioFormData === 'function') {
		window.updateFinDominioFormData({ id: empresa.id, finDominio: fecha });
	}

	if (typeof window.refreshFinDominioPermissions === 'function') {
		window.refreshFinDominioPermissions();
	}
}

/**
 * Configura el modal de teléfono
 * @param {Object} empresa - Datos de la empresa
 */
function setupTelefonoModal(empresa) {
	const form = document.getElementById('telefonoForm');
	const input = document.getElementById('telefonoInput');
	if (form) {
		form.setAttribute('data-id', empresa.id);
	}
	const initialTelefono = empresa.telefono || '';
	if (typeof window.setTelefonoInitialValue === 'function') {
		window.setTelefonoInitialValue(initialTelefono);
	} else if (input) {
		input.value = initialTelefono;
		const saveButton = form ? form.querySelector('button[type="submit"]') : null;
		if (saveButton) {
			saveButton.style.display = 'none';
		}
	}

	if (window.PostergarDocumentoManager) {
		window.PostergarDocumentoManager.load('telefono', empresa.id);
	} else {
		const handler = () => {
			if (window.PostergarDocumentoManager) {
				window.PostergarDocumentoManager.load('telefono', empresa.id);
			}
		};
		window.addEventListener('postergar:manager-ready', handler, { once: true });
	}
}

/**
 * Configura el modal de correo contacto
 * @param {Object} empresa - Datos de la empresa
 */
function setupCorreoContactoModal(empresa) {
	const form = document.getElementById('correoContactoForm');
	const input = document.getElementById('correoContactoInput');
	if (form) {
		form.setAttribute('data-id', empresa.id);
	}
	const initialCorreo = empresa.correo || '';
	if (typeof window.setCorreoContactoInitialValue === 'function') {
		window.setCorreoContactoInitialValue(initialCorreo);
	} else if (input) {
		input.value = initialCorreo;
		const saveButton = form ? form.querySelector('button[type="submit"]') : null;
		if (saveButton) {
			saveButton.style.display = 'none';
		}
	}

	if (window.PostergarDocumentoManager) {
		window.PostergarDocumentoManager.load('correoContacto', empresa.id);
	} else {
		const handler = () => {
			if (window.PostergarDocumentoManager) {
				window.PostergarDocumentoManager.load('correoContacto', empresa.id);
			}
		};
		window.addEventListener('postergar:manager-ready', handler, { once: true });
	}
}

/**
 * Configura el modal de sitio web
 * @param {Object} empresa - Datos de la empresa
 */
function setupSitioWebModal(empresa) {
	const form = document.getElementById('sitioWebForm');
	const input = document.getElementById('sitioWebInput');
	if (form) {
		form.setAttribute('data-id', empresa.id);
	}
	const initialSitioWeb = empresa.sitioWeb || '';
	if (typeof window.setSitioWebInitialValue === 'function') {
		window.setSitioWebInitialValue(initialSitioWeb);
	} else if (input) {
		input.value = initialSitioWeb;
		const saveButton = form ? form.querySelector('button[type="submit"]') : null;
		if (saveButton) {
			saveButton.style.display = 'none';
		}
	}

	if (window.PostergarDocumentoManager) {
		window.PostergarDocumentoManager.load('sitioWeb', empresa.id);
	} else {
		const handler = () => {
			if (window.PostergarDocumentoManager) {
				window.PostergarDocumentoManager.load('sitioWeb', empresa.id);
			}
		};
		window.addEventListener('postergar:manager-ready', handler, { once: true });
	}
}

/**
 * Configura modales que requieren lazy loading
 * @param {string} modalTarget - Selector del modal
 * @param {Object} empresa - Datos de la empresa
 */
function setupLazyLoadModal(modalTarget, empresa, detallePromise) {
	const sectionConfig = DETAIL_MODAL_MAP[modalTarget];
	if (!sectionConfig || typeof sectionConfig.populate !== 'function') {
		return;
	}

	const populateFromDetalle = (detalle) => {
		try {
			sectionConfig.populate(detalle || {});
		} catch (error) {
			console.error('Error al poblar modal con detalle:', error);
		}
	};

	if (detallePromise && typeof detallePromise.then === 'function') {
		detallePromise.then(populateFromDetalle).catch((error) => {
			console.error('Error al obtener detalle para modal:', error);
			populateFromDetalle(null);
		});
	} else if (empresa && empresa.id) {
		cargarDetalleEmpresa(empresa.id, (detalle) => populateFromDetalle(detalle), { forceReload: true });
	}
}

/**
 * Funciones para poblar modales con lazy loading
 */
function populateCuentasModal(detalle) {
	// Selecciona el tbody del modal actual por id
	const cont = document.getElementById('tbody-cuentas-bancarias');
	cont.innerHTML = '';
	const cuentas = detalle && Array.isArray(detalle.cuentas) ? detalle.cuentas : [];
	const puedeEscribir = canUserWrite();
	if (cuentas.length) {
		cuentas.forEach((cuenta, index) => {
			const banco = cuenta.banco || '';
			const numero = cuenta.numero_cuenta || cuenta.numero || cuenta.cuenta || '';
			const clabe = cuenta.clabe_interbancaria || cuenta.clave || '';
			const moneda = cuenta.moneda || '';
			// Botón copiar fila
			const btnCopyFila = `<button type="button" class="btn btn-outline-secondary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-copy-fila" title="Copiar toda la fila" aria-label="Copiar fila"><i class="bx bx-copy align-middle"></i></button>`;
			// Botón eliminar
			let btnEliminar = '';
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				btnEliminar = `<button type="button" class="${deleteButtonClass} btn-eliminar-cuenta" data-id="${
					cuenta.id || index
				}" data-requires="write" title="Eliminar" aria-label="Eliminar"><i class="bx bx-trash align-middle"></i></button>`;
			}
			cont.innerHTML += `<tr>
				<td>${buildCopyableSpan(banco, 'Copiar banco')}</td>
				<td>${buildCopyableSpan(numero, 'Copiar número de cuenta')}</td>
				<td>${buildCopyableSpan(clabe, 'Copiar CLABE')}</td>
				<td>${buildCopyableSpan(moneda, 'Copiar moneda')}</td>
				<td class='text-center'>
					<div class="d-inline-flex align-items-center justify-content-center gap-2">
						${btnCopyFila}
						${btnEliminar}
					</div>
				</td>
			</tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="5" class="text-center">Sin cuentas</td></tr>';
	}
}

function populateContrasenaSATModal(detalle) {
	// Selecciona el tbody del modal actual
	const cont = document.querySelector('#modalContrasenaSAT tbody');
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.contrasSat) ? detalle.contrasSat : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const usuario = row.usuario || '';
			const contrasena = row.contrasena || '';
			const rfc = row.rfc || '';
			// Botón copiar fila
			const btnCopyFila = `<button type="button" class="btn btn-outline-secondary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-copy-fila" title="Copiar toda la fila" aria-label="Copiar fila"><i class="bx bx-copy align-middle"></i></button>`;
			// Botón eliminar
			let btnEliminar = '';
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				btnEliminar = `<button type="button" class="${deleteButtonClass} btn-eliminar-contrasena-sat" data-id="${
					row.id || index
				}" data-requires="write" title="Eliminar" aria-label="Eliminar"><i class="bx bx-trash align-middle"></i></button>`;
			}
			cont.innerHTML += `<tr>
				<td>${buildCopyableSpan(usuario, 'Copiar usuario')}</td>
				<td>${buildCopyableSpan(contrasena, 'Copiar contraseña')}</td>
				<td>${buildCopyableSpan(rfc, 'Copiar RFC')}</td>
				<td class='text-center'>
					<div class="d-inline-flex align-items-center justify-content-center gap-2">
						${btnCopyFila}
						${btnEliminar}
					</div>
				</td>
			</tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
	}
}

function populateFIELModal(detalle) {
	const cont = document.querySelector('#modalFIEL tbody');
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.fiel) ? detalle.fiel : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const actionButtons = [];
			if (row.archivoFaltante) actionButtons.push(getMissingDocumentBadgeHtml());
			const downloadHtml = getDownloadButtonHtml(row.documento, 'Descargar documento');
			if (downloadHtml) {
				actionButtons.push(downloadHtml);
			}
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				actionButtons.push(`
					<button type="button" class="${deleteButtonClass} btn-eliminar-fiel" data-id="${
					row.id || index
				}" data-requires="write" title="Eliminar" aria-label="Eliminar">
						<i class="bx bx-trash"></i>
					</button>
				`);
			}
			const accionesHtml = actionButtons.length
				? renderActionGroup(actionButtons)
				: `<span class="text-muted small">${puedeEscribir ? 'Sin acciones' : 'Sin permisos'}</span>`;
			cont.innerHTML += `<tr>
				<td>${row.tipo || ''}</td>
				<td>${row.fechaCreacion || ''}</td>
				<td class='text-center'>${accionesHtml}</td>
			</tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="3" class="text-center">Sin FIEL</td></tr>';
	}
}

function populateContrasenaIOFacturoModal(detalle) {
	const cont = document.querySelector('#modalContrasenaIOFacturo tbody');
	if (!cont) return;
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.contrasIofacturo) ? detalle.contrasIofacturo : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const usuario = row.usuario || '';
			const rfc = row.rfc || '';
			const contrasena = row.contrasena || '';
			// Botón copiar fila
			const btnCopyFila = `<button type=\"button\" class=\"btn btn-outline-secondary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-copy-fila\" title=\"Copiar toda la fila\" aria-label=\"Copiar fila\"><i class=\"bx bx-copy align-middle\"></i></button>`;
			// Botón eliminar
			let btnEliminar = '';
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				btnEliminar = `<button type=\"button\" class=\"${deleteButtonClass} btn-eliminar-contrasena-iofacturo\" data-id=\"${
					row.id || index
				}\" data-requires=\"write\" title=\"Eliminar\" aria-label=\"Eliminar\"><i class=\"bx bx-trash align-middle\"></i></button>`;
			}
			cont.innerHTML += `<tr>
				<td>${buildCopyableSpan(usuario, 'Copiar usuario')}</td>
				<td>${buildCopyableSpan(rfc, 'Copiar RFC')}</td>
				<td>${buildCopyableSpan(contrasena, 'Copiar contraseña')}</td>
				<td class='text-center'>
					<div class=\"d-inline-flex align-items-center justify-content-center gap-2\">
						${btnCopyFila}
						${btnEliminar}
					</div>
				</td>
			</tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="4" class="text-center">Sin contraseñas</td></tr>';
	}
}

function populateContrasenasBancosModal(detalle) {
	const tbody = document.querySelector('#modalContrasenasBancos tbody');
	tbody.innerHTML = '';
	if (detalle.contrasBanco && detalle.contrasBanco.length) {
		detalle.contrasBanco.forEach((row) => {
			const banco = row.banco && row.banco.trim() ? row.banco : 'Sin datos';
			const usuario = row.usuario && row.usuario.trim() ? row.usuario : 'Sin datos';
			const contrasena = row.contrasena && row.contrasena.trim() ? row.contrasena : 'Sin datos';
			const nip = row.nip && row.nip.trim() ? row.nip : 'Sin datos';
			const claveOp = row.claveOp && row.claveOp.trim() ? row.claveOp : 'Sin datos';
			tbody.innerHTML += `<tr>
				<td>${buildCopyableSpan(banco, 'Copiar banco')}</td>
				<td>${buildCopyableSpan(usuario, 'Copiar usuario')}</td>
				<td>${buildCopyableSpan(contrasena, 'Copiar contraseña')}</td>
				<td>${buildCopyableSpan(nip, 'Copiar NIP')}</td>
				<td>${buildCopyableSpan(claveOp, 'Copiar clave operativa')}</td>
				<td class="text-center">
					<div class="d-inline-flex align-items-center justify-content-center gap-2">
						<button type="button" class="btn btn-outline-primary btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-copy-row" 
							title="Copiar toda la fila" 
							data-rowcopy="Banco: ${escapeAttribute(banco)}\nUsuario: ${escapeAttribute(usuario)}\nContraseña: ${escapeAttribute(
				contrasena
			)}\nNIP: ${escapeAttribute(nip)}\nClave Operativa: ${escapeAttribute(claveOp)}">
							<i class="bx bx-copy"></i>
						</button>
						<button type="button" class="btn btn-outline-danger btn-sm action-icon-btn d-inline-flex align-items-center justify-content-center btn-eliminar-contrasena-banco" data-id="${
							row.id
						}" data-requires="write" title="Eliminar" aria-label="Eliminar">
							<i class="bx bx-trash"></i>
						</button>
					</div>
				</td>
			</tr>`;
		});
	} else {
		tbody.innerHTML = '<tr><td colspan="6" class="text-center">Sin contraseñas</td></tr>';
	}
}

let estadoTooltipHtmlCache = null;
let accionesTooltipHtmlCache = null; // ya no se usa para cachear por permisos; se recalcula dinámicamente

function getEstadoTooltipHtml() {
	if (estadoTooltipHtmlCache) {
		return estadoTooltipHtmlCache;
	}
	const template = document.getElementById('estados-info-template');
	if (template && template.innerHTML.trim()) {
		estadoTooltipHtmlCache = template.innerHTML.trim();
		return estadoTooltipHtmlCache;
	}
	estadoTooltipHtmlCache = `
	<div class="d-flex flex-column gap-2" style="min-width:220px;">
		<div class="d-flex align-items-center gap-2">
			<i class="bx bx-check-circle text-success fs-5"></i>
			<span class="small">Completado: toda la información está actualizada y verificada.</span>
		</div>
		<div class="d-flex align-items-center gap-2">
			<i class="bx bx-search-alt text-info fs-5"></i>
			<span class="small">Revisión: el documento recién cargado se encuentra en espera de validación.</span>
		</div>
		<div class="d-flex align-items-center gap-2">
			<i class="bx bx-time-five text-warning fs-5"></i>
			<span class="small">Archivo faltante: el documento reportado no se encuentra disponible o requiere complementarlo.</span>
		</div>
		<div class="d-flex align-items-center gap-2">
			<i class="bx bx-error text-danger fs-5"></i>
			<span class="small">Rechazado: el documento en revisión fue rechazado y requiere reemplazo.</span>
		</div>
	</div>
	`.trim();
	return estadoTooltipHtmlCache;
}

function getAccionesTooltipHtml() {
	// Construir dinámicamente según el tipo de usuario
	const canWrite =
		typeof canUserWrite === 'function'
			? canUserWrite()
			: !!(window.appPermissions && window.appPermissions.canWrite);
	const isAdmin = !!(window.appPermissions && window.appPermissions.isAdmin === true);

	const items = [];
	// Ver
	items.push(`
		<div class=\"d-flex align-items-center gap-2\">
			<i class=\"bx bx-show text-secondary fs-5\"></i>
			<span class=\"small\">Ver: abre la vista previa del documento.</span>
		</div>
	`);
	// Descargar
	items.push(`
		<div class=\"d-flex align-items-center gap-2\">
			<i class=\"bx bx-download text-primary fs-5\"></i>
			<span class=\"small\">Descargar: guarda el archivo localmente.</span>
		</div>
	`);
	// Acciones de escritura (Capturista/Admin)
	if (canWrite) {
		items.push(`
			<div class=\"d-flex align-items-center gap-2\">
				<i class=\"bx bx-transfer text-info fs-5\"></i>
				<span class=\"small\">Reemplazar: disponible cuando está <strong>rechazado</strong>.</span>
			</div>
		`);
		items.push(`
			<div class=\"d-flex align-items-center gap-2\">
				<i class=\"bx bx-upload text-primary fs-5\"></i>
				<span class=\"small\">Subir archivo: disponible cuando hay <strong>archivo faltante</strong>.</span>
			</div>
		`);
		items.push(`
			<div class=\"d-flex align-items-center gap-2\">
				<i class=\"bx bx-trash text-danger fs-5\"></i>
				<span class=\"small\">Eliminar: borra el registro del documento permanente.</span>
			</div>
		`);
	}
	// Acciones de revisión (solo se muestran a Admin)
	if (isAdmin) {
		items.push(`
			<div class=\"d-flex align-items-center gap-2\">
				<i class=\"bx bx-check text-success fs-5\"></i>
				<span class=\"small\">Aprobar: completa la revisión del documento.</span>
			</div>
		`);
		items.push(`
			<div class=\"d-flex align-items-center gap-2\">
				<i class=\"bx bx-x text-danger fs-5\"></i>
				<span class=\"small\">Rechazar: envía el documento a reemplazo.</span>
			</div>
		`);
	}

	return `
		<div class=\"d-flex flex-column gap-2\" style=\"min-width:240px;\">
			${items.join('')}
		</div>
	`.trim();
}

function assignEstadoPopoverContent() {
	const popoverHtml = getEstadoTooltipHtml();
	if (!popoverHtml) return;
	const buttons = document.querySelectorAll('.estado-info-trigger');
	buttons.forEach((btn) => {
		btn.setAttribute('data-bs-html', 'true');
		btn.setAttribute('data-bs-content', popoverHtml);
		btn.setAttribute('title', '');
	});
}

function assignAccionesPopoverContent() {
	const popoverHtml = getAccionesTooltipHtml();
	if (!popoverHtml) return;
	const buttons = document.querySelectorAll('.acciones-info-trigger');
	buttons.forEach((btn) => {
		btn.setAttribute('data-bs-html', 'true');
		btn.setAttribute('data-bs-content', popoverHtml);
		btn.setAttribute('title', '');
	});
}

function initializeEstadoPopovers() {
	if (!window.bootstrap || typeof window.bootstrap.Popover !== 'function') {
		return;
	}
	const popoverElements = document.querySelectorAll('.estado-info-trigger[data-bs-toggle="popover"]');
	popoverElements.forEach((el) => {
		if (typeof window.bootstrap.Popover.getInstance === 'function') {
			const instance = window.bootstrap.Popover.getInstance(el);
			if (instance) {
				instance.dispose();
			}
		}
		new window.bootstrap.Popover(el, {
			html: true,
			trigger: el.getAttribute('data-bs-trigger') || 'hover focus',
			placement: el.getAttribute('data-bs-placement') || 'top',
		});
	});
}

function initializeAccionesPopovers() {
	if (!window.bootstrap || typeof window.bootstrap.Popover !== 'function') {
		return;
	}
	const popoverElements = document.querySelectorAll('.acciones-info-trigger[data-bs-toggle="popover"]');
	popoverElements.forEach((el) => {
		if (typeof window.bootstrap.Popover.getInstance === 'function') {
			const instance = window.bootstrap.Popover.getInstance(el);
			if (instance) {
				instance.dispose();
			}
		}
		new window.bootstrap.Popover(el, {
			html: true,
			trigger: el.getAttribute('data-bs-trigger') || 'hover focus',
			placement: el.getAttribute('data-bs-placement') || 'bottom',
		});
	});
}

function initializeGestionPopovers() {
	if (!window.bootstrap || typeof window.bootstrap.Popover !== 'function') {
		return;
	}
	const popoverElements = document.querySelectorAll('.gestion-info-trigger[data-bs-toggle="popover"]');
	popoverElements.forEach((el) => {
		if (typeof window.bootstrap.Popover.getInstance === 'function') {
			const instance = window.bootstrap.Popover.getInstance(el);
			if (instance) {
				instance.dispose();
			}
		}
		new window.bootstrap.Popover(el, {
			html: String(el.getAttribute('data-bs-html') || '').toLowerCase() === 'true',
			trigger: el.getAttribute('data-bs-trigger') || 'hover focus',
			placement: el.getAttribute('data-bs-placement') || 'top',
		});
	});
}

function populateDocumentosPermanentesModal(detalle) {
	const accordion = document.getElementById('documentos-permanentes-accordion');
	const emptyStateEl = document.getElementById('documentos-permanentes-empty');
	if (!accordion || !emptyStateEl) return;
	accordion.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.documentosPermanentes) ? detalle.documentosPermanentes : [];
	// Filtrar: no mostrar constancias de situación fiscal (representante legal o socio) en Documentos Permanentes
	const registrosVisibles = registros.filter((row) => {
		const txt = ((row.descripcion || row.contenido || '') + '').toLowerCase();
		if (!txt) return true;
		const esRep =
			(txt.includes('constancia') && txt.includes('representante') && txt.includes('legal')) ||
			txt.includes('const sif. rep. legal');
		const esSocio = (txt.includes('constancia') && txt.includes('socio')) || txt.includes('const sif. socio');
		return !(esRep || esSocio);
	});
	window.documentosPermanentesEntries = {};
	const puedeEscribir = canUserWrite();
	const isAdmin = !!(appPermissions && appPermissions.isAdmin === true);
	const gestionPopoverAdminHtml = `
		<div class="d-flex flex-column gap-2" style="min-width:220px;">
			<div class="d-flex align-items-center gap-2">
				<i class="bx bx-upload text-primary fs-5"></i>
				<span class="small">Subió el documento.</span>
			</div>
			<div class="d-flex align-items-center gap-2">
				<i class="bx bx-check-double text-success fs-5"></i>
				<span class="small">Verificó el documento.</span>
			</div>
		</div>
	`.trim();
	const gestionPopoverUserHtml = `
		<div class="d-flex flex-column gap-2" style="min-width:220px;">
			<div class="d-flex align-items-center gap-2">
				<i class="bx bx-upload text-primary fs-5"></i>
				<span class="small">Subió el documento.</span>
			</div>
		</div>
	`.trim();
	const gestionHeader = document.getElementById('documentos-permanentes-gestion-header');
	if (gestionHeader) {
		const gestionButton = gestionHeader.querySelector('.gestion-info-trigger');
		// Mostrar siempre el encabezado de Gestión; cambiar contenido del popover según permisos
		gestionHeader.classList.remove('d-none');
		if (gestionButton) {
			const html = isAdmin ? gestionPopoverAdminHtml : gestionPopoverUserHtml;
			gestionButton.setAttribute('data-bs-html', 'true');
			gestionButton.setAttribute('data-bs-content', html);
			gestionButton.setAttribute('title', '');
			if (window.bootstrap && typeof window.bootstrap.Popover === 'function') {
				const existing = window.bootstrap.Popover.getInstance(gestionButton);
				if (existing) existing.dispose();
				new window.bootstrap.Popover(gestionButton, {
					html: true,
					trigger: 'hover focus',
					placement: 'bottom',
				});
			}
		}
	}

	if (!registrosVisibles.length) {
		accordion.classList.add('d-none');
		emptyStateEl.classList.remove('d-none');
		assignEstadoPopoverContent();
		initializeEstadoPopovers();
		assignAccionesPopoverContent();
		initializeAccionesPopovers();
		initializeGestionPopovers();
		if (typeof window.actualizarSelectArchivoFaltante === 'function') {
			window.actualizarSelectArchivoFaltante(registrosVisibles);
		}
		if (typeof window.syncPreviewHighlight === 'function') {
			window.syncPreviewHighlight();
		}
		return;
	}

	emptyStateEl.classList.add('d-none');
	accordion.classList.remove('d-none');

	const NORMALIZED_ORDER = [
		'Acta Constitutiva',
		'RPPC Acta',
		'RPPC Asamblea',
		'Asamblea',
		'Poder',
		'INE rep. legal',
		'INE Socios',
		'Const Sif. Socio.',
		'Const Sif. Rep. Legal',
	];
	const ALIASES = {
		'acta constitutiva': 'Acta Constitutiva',
		'rppc acta': 'RPPC Acta',
		'rppc asamblea': 'RPPC Asamblea',
		'registro público de la propiedad y de comercio (acta)': 'RPPC Acta',
		'registro público de la propiedad y de comercio (asamblea)': 'RPPC Asamblea',
		asamblea: 'Asamblea',
		poder: 'Poder',
		'ine rep. legal': 'INE rep. legal',
		'ine del representante legal': 'INE rep. legal',
		'ine representante legal': 'INE rep. legal',
		'ine socios': 'INE Socios',
		'ine del socio': 'INE Socios',
		'const sif. socio.': 'Const Sif. Socio.',
		'constancia de situación fiscal socio': 'Const Sif. Socio.',
		'const sif. rep. legal': 'Const Sif. Rep. Legal',
		'constancia de situación fiscal representante legal': 'Const Sif. Rep. Legal',
	};
	const splitTipos = (txt) =>
		(txt || '')
			.split(/[;,:]/)
			.map((t) => t.trim())
			.filter(Boolean);
	const canon = (t) => {
		const key = (t || '').toString().trim().toLowerCase();
		return ALIASES[key] || t.trim();
	};
	const sortByOrder = (arr) =>
		arr.slice().sort((a, b) => {
			const ia = NORMALIZED_ORDER.indexOf(a);
			const ib = NORMALIZED_ORDER.indexOf(b);
			if (ia !== -1 && ib !== -1) return ia - ib;
			if (ia !== -1) return -1;
			if (ib !== -1) return 1;
			return a.localeCompare(b, 'es', { sensitivity: 'base' });
		});
	const combinationKey = (txt) => {
		const tipos = splitTipos(txt).map(canon);
		const unique = Array.from(new Set(tipos));
		const sorted = sortByOrder(unique);
		return sorted.join(' : ') || 'Sin tipo';
	};

	const displayNameForGroup = (key) => {
		if (key === 'RPPC Acta') return 'Registro Público de la Propiedad y de Comercio (Acta)';
		if (key === 'RPPC Asamblea') return 'Registro Público de la Propiedad y de Comercio (Asamblea)';
		if (key === 'INE rep. legal') return 'INE del representante legal';
		return key;
	};

	const escapeSelector = (value) => {
		const str = value !== null && value !== undefined ? String(value) : '';
		if (typeof CSS !== 'undefined' && typeof CSS.escape === 'function') {
			return CSS.escape(str);
		}
		return str.replace(/["'\\]/g, '\\$&');
	};

	const groups = new Map();
	registrosVisibles.forEach((row) => {
		const key = combinationKey(row.descripcion || row.contenido || '');
		if (!groups.has(key)) groups.set(key, []);
		groups.get(key).push(row);
	});

	const groupKeys = Array.from(groups.keys());
	groupKeys.sort((a, b) => {
		const aTypes = a.split(' : ');
		const bTypes = b.split(' : ');
		const ia = NORMALIZED_ORDER.indexOf(aTypes[0]);
		const ib = NORMALIZED_ORDER.indexOf(bTypes[0]);
		if (ia !== -1 && ib !== -1 && ia !== ib) return ia - ib;
		if (ia !== -1 && ib === -1) return -1;
		if (ib !== -1 && ia === -1) return 1;
		if (aTypes.length !== bTypes.length) return aTypes.length - bTypes.length;
		return a.localeCompare(b, 'es', { sensitivity: 'base' });
	});

	let html = '';
	let groupIndex = 0;
	groupKeys.forEach((groupKey) => {
		const rows = groups.get(groupKey) || [];
		const getRowEstadoKey = (r) =>
			normalizarEstadoDocumentoPermanente(r.estadoDocumento || r.estado_documento || r.estado || 'completado');
		let hasRechazado = false,
			hasFaltante = false,
			hasRevision = false,
			hasCompletado = false;
		rows.forEach((r) => {
			const k = getRowEstadoKey(r);
			if (k === 'rechazado') hasRechazado = true;
			else if (k === 'archivo_faltante') hasFaltante = true;
			else if (k === 'revision') hasRevision = true;
			else if (k === 'completado') hasCompletado = true;
		});
		const groupEstadoKey = hasRechazado
			? 'rechazado'
			: hasFaltante
			? 'archivo_faltante'
			: hasRevision
			? 'revision'
			: hasCompletado
			? 'completado'
			: 'sin_datos';
		const headerEstadoCircleClass =
			'd-inline-flex justify-content-center align-items-center rounded-circle estado-indicator';
		const estadoConfigMapHeader = {
			completado: {
				label: 'Completado',
				className: `${headerEstadoCircleClass} bg-success text-white`,
				icon: 'bx bx-check-circle',
			},
			revision: {
				label: 'Revisión',
				className: `${headerEstadoCircleClass} bg-info text-white`,
				icon: 'bx bx-search-alt',
			},
			rechazado: {
				label: 'Rechazado',
				className: `${headerEstadoCircleClass} bg-danger text-white`,
				icon: 'bx bx-error',
			},
			archivo_faltante: {
				label: 'Archivo faltante',
				className: `${headerEstadoCircleClass} bg-warning text-white`,
				icon: 'bx bx-time-five',
			},
			sin_datos: {
				label: 'Sin datos',
				className: `${headerEstadoCircleClass} bg-secondary text-white`,
				icon: 'bx bx-data',
			},
		};
		const groupEstadoCfg = estadoConfigMapHeader[groupEstadoKey] || estadoConfigMapHeader.sin_datos;
		const safeGroupKeyAttr = escapeAttribute(groupKey);
		const displayGroupKey = escapeHtml(displayNameForGroup(groupKey));
		const collapseId = `documentos-permanentes-collapse-${groupIndex}`;
		const headerId = `${collapseId}-header`;
		groupIndex += 1;
		const childRows = [];

		rows.forEach((row, index) => {
			const idDocumento = row.id || row.idDocumento || index;
			const idDocumentoStr = String(idDocumento);
			const estadoKey = normalizarEstadoDocumentoPermanente(
				row.estadoDocumento || row.estado_documento || row.estado || 'completado'
			);
			const puedeRevisarEste = typeof row.puedeRevisar === 'boolean' ? row.puedeRevisar : canUserMarkReviewed();
			const puedeReemplazarEste = typeof row.puedeReemplazar === 'boolean' ? row.puedeReemplazar : canUserWrite();
			const baseEstadoCircleClass =
				'd-inline-flex justify-content-center align-items-center rounded-circle estado-indicator';
			const estadoConfigMap = {
				completado: {
					label: 'Completado',
					className: `${baseEstadoCircleClass} bg-success text-white`,
					icon: 'bx bx-check-circle',
				},
				revision: {
					label: 'Revisión',
					className: `${baseEstadoCircleClass} bg-info text-white`,
					icon: 'bx bx-search-alt',
				},
				rechazado: {
					label: 'Rechazado',
					className: `${baseEstadoCircleClass} bg-danger text-white`,
					icon: 'bx bx-error',
				},
				archivo_faltante: {
					label: 'Archivo faltante',
					className: `${baseEstadoCircleClass} bg-warning text-white`,
					icon: 'bx bx-time-five',
				},
				sin_datos: {
					label: 'Sin datos',
					className: `${baseEstadoCircleClass} bg-secondary text-white`,
					icon: 'bx bx-data',
				},
			};
			const estadoConfig = estadoConfigMap[estadoKey] || estadoConfigMap.sin_datos;

			const fechaSubidaRaw =
				row.fechaSubio ||
				row.fecha_subio ||
				row.fechaCreacion ||
				row.fecha_creacion ||
				row.fec_creacion ||
				null;
			const fechaVerificacionRaw =
				row.fechaVerificacion ||
				row.fecha_verificacion ||
				row.fechaRevision ||
				row.fecha_revision ||
				row.fechaRevisado ||
				row.fecha_revisado ||
				row.fec_revision ||
				null;
			const fechaSubidaDisplay = formatFechaHoraCorta(fechaSubidaRaw);
			const fechaVerificacionDisplay = formatFechaHoraCorta(fechaVerificacionRaw, {
				emptyText: '',
			});
			const fechaSubidaTitle = fechaSubidaRaw ? `Subió: ${fechaSubidaRaw}` : 'Subió: Sin registro';
			const fechaVerificacionTitle = fechaVerificacionRaw
				? `Verificó: ${fechaVerificacionRaw}`
				: 'Verificación pendiente';

			const actionButtons = [];
			const uploaderIdValue =
				typeof row.idUsuarioSubio !== 'undefined' && row.idUsuarioSubio !== null ? row.idUsuarioSubio : null;
			const documentUrl =
				typeof window.resolveAppPath === 'function'
					? window.resolveAppPath(row.documento || '')
					: row.documento || '';
			const downloadHtml = getDownloadButtonHtml(documentUrl, 'Descargar documento');
			if (downloadHtml) {
				actionButtons.push(downloadHtml);
			}
			if (documentUrl) {
				const viewBtnClass = getActionButtonClass('outline-secondary');
				const safeUrl = escapeAttribute(documentUrl);
				const safeName = escapeAttribute(row.descripcion || row.contenido || 'Documento');
				const uploaderIdAttr =
					uploaderIdValue !== null && uploaderIdValue !== undefined
						? ` data-uploader-id="${escapeAttribute(uploaderIdValue)}"`
						: '';
				actionButtons.push(`
				<button type="button" class="${viewBtnClass} btn-ver-documento-permanente" data-doc-id="${escapeAttribute(
					idDocumentoStr
				)}" data-url="${safeUrl}" data-name="${safeName}" data-estado="${escapeAttribute(
					estadoKey
				)}" data-can-review="${
					estadoKey === 'revision' && puedeRevisarEste ? '1' : '0'
				}"${uploaderIdAttr} title="Ver" aria-label="Ver">
					<i class="bx bx-show"></i>
				</button>
			`);
			}
			if (puedeEscribir) {
				const reemplazarButtonClass = getActionButtonClass('outline-primary');
				const deleteButtonClass = getActionButtonClass('outline-danger');
				if (estadoKey === 'rechazado' && puedeReemplazarEste) {
					actionButtons.push(`
					<label class="btn ${reemplazarButtonClass} m-0" title="Reemplazar documento" aria-label="Reemplazar documento">
						<input type="file" class="d-none input-reemplazar-documento" data-id="${idDocumento}" accept=".pdf,.jpg,.jpeg,.png" />
						<i class="bx bx-transfer"></i>
					</label>
				`);
				}
				if (estadoKey === 'archivo_faltante') {
					actionButtons.push(`
					<label class="btn ${reemplazarButtonClass} m-0" title="Subir archivo" aria-label="Subir archivo">
						<input type="file" class="d-none input-reemplazar-documento" data-id="${idDocumento}" accept=".pdf,.jpg,.jpeg,.png" />
						<i class="bx bx-upload"></i>
					</label>
				`);
				}
				actionButtons.push(`
				<button type="button" class="${deleteButtonClass} btn-eliminar-documento-permanente" data-id="${idDocumento}" data-requires="write" title="Eliminar" aria-label="Eliminar">
					<i class="bx bx-trash"></i>
				</button>
			`);
			}

			const accionesHtml = actionButtons.length
				? renderActionGroup(actionButtons)
				: `<span class="text-muted small">${puedeEscribir ? 'Sin acciones' : 'Sin permisos'}</span>`;

			const subioFull = row.nombreUsuarioSubio || '';
			const revisoFull = row.nombreUsuarioReviso || '';
			const subioName = extractFirstName(subioFull);
			const revisoName = extractFirstName(revisoFull);
			const descripcionDoc = row.descripcion || row.contenido || '';
			if (!window.documentosPermanentesEntries) {
				window.documentosPermanentesEntries = {};
			}
			window.documentosPermanentesEntries[idDocumentoStr] = {
				id: idDocumento,
				idString: idDocumentoStr,
				descripcion: descripcionDoc,
				documento: documentUrl,
				estado: estadoKey,
				puedeRevisar: estadoKey === 'revision' && !!puedeRevisarEste,
				puedeRevisarRaw: !!puedeRevisarEste,
				puedeReemplazar: !!puedeReemplazarEste,
				nombreUsuarioSubio: subioFull,
				nombreUsuarioReviso: revisoFull,
				idUsuarioSubio: uploaderIdValue,
				fechaCreacion: row.fechaCreacion || null,
				fechaSubida: fechaSubidaRaw,
				fechaVerificacion: fechaVerificacionRaw,
				grupo: groupKey,
			};

			// Si el documento está rechazado y tiene observaciones, mostrar como popover en el estado y un punto de notificación
			let estadoBadge = `<span class="${estadoConfig.className} position-relative`;
			let notificationDot = '';
			if (estadoKey === 'rechazado' && row.observaciones && row.observaciones.trim()) {
				const popoverContent = `<div class=\"d-flex align-items-start gap-2\"><i class=\"bx bx-message-rounded-dots text-info fs-5\"></i><span class=\"small\">${escapeHtml(
					row.observaciones.trim()
				)}</span></div>`;
				estadoBadge += ` observaciones-info-trigger" aria-label="${escapeAttribute(
					estadoConfig.label
				)}" style="width:2rem;height:2rem;" data-bs-toggle="popover" data-bs-placement="top" data-bs-html="true" data-bs-content='${popoverContent}'>`;
				// Punto de notificación (círculo pequeño azul en la esquina superior derecha)
				notificationDot = `<span class=\"position-absolute top-0 start-100 translate-middle p-1 bg-info border border-light rounded-circle\" style=\"z-index:2;width:0.75em;height:0.75em;\" title=\"Observación disponible\"></span>`;
			} else {
				estadoBadge += `" aria-label="${escapeAttribute(estadoConfig.label)}" style="width:2rem;height:2rem;">`;
			}
			estadoBadge += `<i class="${estadoConfig.icon} fs-5"></i><span class="visually-hidden">${escapeHtml(
				estadoConfig.label
			)}</span>${notificationDot}</span>`;

			const subioDisplayLabel = subioName || (subioFull ? subioFull.trim().split(/\s+/)[0] : '');
			const subioNameContent = subioDisplayLabel
				? `<span class="fw-semibold">${escapeHtml(subioDisplayLabel)}</span>`
				: '<span class="text-muted">Sin responsable</span>';
			const subioFechaContent = fechaSubidaDisplay
				? `<span class="text-muted fs-12">${escapeHtml(fechaSubidaDisplay)}</span>`
				: '<span class="text-muted fs-12">Sin fecha</span>';
			const revisoDisplayLabel = revisoName || (revisoFull ? revisoFull.trim().split(/\s+/)[0] : '');
			const revisoNameContent = revisoDisplayLabel
				? `<span class="fw-semibold">${escapeHtml(revisoDisplayLabel)}</span>`
				: '<span class="text-muted">Sin verificador</span>';
			const revisoFechaContent = fechaVerificacionDisplay
				? `<span class="text-muted fs-12">${escapeHtml(fechaVerificacionDisplay)}</span>`
				: '<span class="text-muted fs-12">Pendiente</span>';

			const subioGestionItem = `
				<div class="d-flex align-items-center gap-2" title="${escapeAttribute(fechaSubidaTitle)}">
					<span class="badge bg-light text-muted border shrink-0">
						<i class="bx bx-upload"></i>
					</span>
					<div class="d-flex flex-column">
						<span class="fw-semibold text-muted fs-11">Subió documento</span>
						${subioNameContent}
						${subioFechaContent}
					</div>
				</div>
			`;
			const revisoGestionItem = `
				<div class="d-flex align-items-center gap-2" title="${escapeAttribute(fechaVerificacionTitle)}">
					<span class="badge bg-light text-muted border shrink-0">
						<i class="bx bx-check-double"></i>
					</span>
					<div class="d-flex flex-column">
						<span class="fw-semibold text-muted fs-11">Verificación</span>
						${revisoNameContent}
						${revisoFechaContent}
					</div>
				</div>
			`;
			const gestionColumnHtml = `
				<div class="d-flex flex-wrap align-items-center gap-3">
					${subioGestionItem}
					${revisoGestionItem}
				</div>
			`;

			const gestionColumnHtmlVisible = `
						<div class="d-flex flex-wrap align-items-center gap-3">
							${subioGestionItem}
							${isAdmin ? revisoGestionItem : ''}
						</div>
					`;

			const infoSectionHtml = `
						<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3 grow w-100">
							<div class="shrink-0">
								${estadoBadge}
							</div>
							<div class="grow small text-muted">
								${gestionColumnHtmlVisible}
							</div>
						</div>
					`;

			childRows.push(`
				<div class="list-group-item group-item py-3" data-group-key="${safeGroupKeyAttr}" data-doc-id="${escapeAttribute(
				idDocumentoStr
			)}">
					<div class="d-flex flex-row w-100 align-items-center justify-content-between" style="min-height:64px;">
						<div class="d-flex align-items-center pe-0"">${accionesHtml}</div>
						<div class="grow d-flex align-items-center ps-3" style="min-width:27.5em;">${infoSectionHtml}</div>
					</div>
				</div>
			`);
		});

		const childRowsHtml = childRows.length
			? childRows.join('')
			: '<div class="list-group-item text-center text-muted">Sin documentos</div>';

		html += `
			<div class="accordion-item" data-group-key="${safeGroupKeyAttr}">
				<h2 class="accordion-header" id="${headerId}">
					<button class="accordion-button collapsed group-toggle gap-3" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}" data-group-key="${safeGroupKeyAttr}">
						<div class="d-flex w-100 align-items-center gap-3">
							   <span class="${groupEstadoCfg.className} me-2" title="${escapeAttribute(
			groupEstadoCfg.label
		)}" style="width:2rem;height:2rem;">
								   <i class="${groupEstadoCfg.icon} fs-5"></i>
								   <span class="visually-hidden">${escapeHtml(groupEstadoCfg.label)}</span>
							   </span>
							   <div class="d-flex flex-wrap align-items-center gap-2 grow text-start">
								   <span class="fw-semibold">${displayGroupKey}</span>
								   <span class="badge bg-light text-muted border fw-normal">${rows.length}</span>
							   </div>
						</div>
					</button>
				</h2>
				<div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headerId}" data-bs-parent="#documentos-permanentes-accordion">
					<div class="accordion-body p-0">
						<div class="list-group list-group-flush">
							${childRowsHtml}
						</div>
					</div>
				</div>
			</div>
		`;
	});

	accordion.innerHTML = html;
	assignEstadoPopoverContent();
	initializeEstadoPopovers();
	// Inicializar popovers de Bootstrap para observaciones
	if (window.bootstrap && typeof window.bootstrap.Popover === 'function') {
		accordion.querySelectorAll('.observaciones-info-trigger[data-bs-toggle="popover"]').forEach((el) => {
			if (typeof window.bootstrap.Popover.getInstance === 'function') {
				const instance = window.bootstrap.Popover.getInstance(el);
				if (instance) {
					instance.dispose();
				}
			}
			new window.bootstrap.Popover(el, {
				html: true,
				trigger: el.getAttribute('data-bs-trigger') || 'hover focus',
				placement: el.getAttribute('data-bs-placement') || 'top',
			});
		});
	}
	assignAccionesPopoverContent();
	initializeAccionesPopovers();
	initializeGestionPopovers();

	if (window.bootstrap && typeof window.bootstrap.Collapse === 'function') {
		accordion.querySelectorAll('.accordion-collapse').forEach((collapseEl) => {
			collapseEl.addEventListener('hidden.bs.collapse', () => {
				const currentPreviewId =
					typeof window.getCurrentDocumentPreviewId === 'function'
						? window.getCurrentDocumentPreviewId()
						: null;
				if (!currentPreviewId && currentPreviewId !== 0) {
					return;
				}
				const selector = `.group-item[data-doc-id="${escapeSelector(currentPreviewId)}"]`;
				const hasPreview = collapseEl.querySelector(selector);
				if (hasPreview && typeof window.cerrarVistaPreviaDocumentos === 'function') {
					window.cerrarVistaPreviaDocumentos();
				}
			});
			collapseEl.addEventListener('shown.bs.collapse', () => {
				if (typeof window.syncPreviewHighlight === 'function') {
					window.syncPreviewHighlight();
				}
			});
		});
	}

	if (typeof window.actualizarSelectArchivoFaltante === 'function') {
		window.actualizarSelectArchivoFaltante(registrosVisibles);
	}
	if (typeof window.syncPreviewHighlight === 'function') {
		window.syncPreviewHighlight();
	}
}

async function recargarDocumentosPermanentes(empresaId) {
	if (!empresaId) return;
	await cargarDetalleEmpresa(
		empresaId,
		(detalle) => {
			const payload = detalle || { documentosPermanentes: [] };
			populateDocumentosPermanentesModal(payload);
			if (typeof window.actualizarSelectArchivoFaltante === 'function') {
				window.actualizarSelectArchivoFaltante(payload.documentosPermanentes || []);
			}
			// Actualiza el estado de la sección en la tabla principal (incluye 'rechazado')
			actualizarEstadoSecciones(empresaId, detalle);
		},
		{ forceReload: true }
	);
}

window.recargarDocumentosPermanentes = recargarDocumentosPermanentes;

function setupEstadosPopover() {
	const btn = document.getElementById('estados-info-btn');
	const template = document.getElementById('estados-info-template');
	if (!btn || !template || typeof bootstrap === 'undefined') {
		return;
	}
	btn.setAttribute('data-bs-content', template.innerHTML);
	btn.setAttribute('data-bs-html', 'true');
	bootstrap.Popover.getOrCreateInstance(btn, {
		html: true,
		trigger: 'hover focus',
		placement: 'bottom',
		container: 'body',
		content: template.innerHTML,
	});
}

function populateEstadosCuentaModal(detalle) {
	const cont = document.querySelector('#modalEstadosCuenta tbody');
	if (!cont) return;
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.estadosCuenta) ? detalle.estadosCuenta : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const tipo = row.tipo || '';
			const fecha = row.fechaCreacion || row.fec_creacion || row.fecha || '';
			const rawDocumento = row.documento || row.documento1 || '';
			const documento =
				typeof window.resolveAppPath === 'function' ? window.resolveAppPath(rawDocumento) : rawDocumento;
			const idFila = row.id || index;
			const actionButtons = [];
			if (row.archivoFaltante) actionButtons.push(getMissingDocumentBadgeHtml());
			// Botón ver (igual que documentos permanentes)
			if (documento) {
				const viewBtnClass = getActionButtonClass('outline-secondary');
				actionButtons.push(`
					   <button type="button" class="${viewBtnClass} btn-ver-estado-cuenta" data-url="${encodeURIComponent(
					documento
				)}" title="Ver" aria-label="Ver">
						   <i class="bx bx-show"></i>
					   </button>
				   `);
			}
			const downloadHtml = getDownloadButtonHtml(documento, 'Descargar estado de cuenta');
			if (downloadHtml) {
				actionButtons.push(downloadHtml);
			}
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				actionButtons.push(`
					   <button type="button" class="${deleteButtonClass} btn-eliminar-estados-cuenta" data-id="${idFila}" data-requires="write" title="Eliminar" aria-label="Eliminar">
						   <i class="bx bx-trash"></i>
					   </button>
				   `);
			}
			const accionesHtml = actionButtons.length
				? renderActionGroup(actionButtons)
				: `<span class="text-muted small">${puedeEscribir ? 'Sin acciones' : 'Sin permisos'}</span>`;
			cont.innerHTML += `<tr>
				   <td>${fecha}</td>
				   <td class='text-center'>${accionesHtml}</td>
			   </tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="2" class="text-center">Sin estados</td></tr>';
	}
}

function populateCaratulasBancariasModal(detalle) {
	const cont = document.querySelector('#modalCaratulasBancarias tbody');
	if (!cont) return;
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.caratulas) ? detalle.caratulas : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const tipo = row.tipo || '';
			const fecha = row.fechaCreacion || row.fec_creacion || row.fecha || '';
			const rawDocumento = row.documento || row.documento1 || '';
			const documento =
				typeof window.resolveAppPath === 'function' ? window.resolveAppPath(rawDocumento) : rawDocumento;
			const idFila = row.id || index;
			const actionButtons = [];
			if (row.archivoFaltante) actionButtons.push(getMissingDocumentBadgeHtml());
			// Botón ver (igual que documentos permanentes)
			if (documento) {
				const viewBtnClass = getActionButtonClass('outline-secondary');
				actionButtons.push(`
					   <button type="button" class="${viewBtnClass} btn-ver-caratula-bancaria" data-url="${encodeURIComponent(
					documento
				)}" title="Ver" aria-label="Ver">
						   <i class="bx bx-show"></i>
					   </button>
				   `);
			}
			const downloadHtml = getDownloadButtonHtml(documento, 'Descargar carátula');
			if (downloadHtml) {
				actionButtons.push(downloadHtml);
			}
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				actionButtons.push(`
					   <button type="button" class="${deleteButtonClass} btn-eliminar-caratula-bancaria" data-id="${idFila}" data-requires="write" title="Eliminar" aria-label="Eliminar">
						   <i class="bx bx-trash"></i>
					   </button>
				   `);
			}
			const accionesHtml = actionButtons.length
				? renderActionGroup(actionButtons)
				: `<span class="text-muted small">${puedeEscribir ? 'Sin acciones' : 'Sin permisos'}</span>`;
			cont.innerHTML += `<tr>
				   <td>${fecha}</td>
				   <td class='text-center'>${accionesHtml}</td>
			   </tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="2" class="text-center">Sin carátulas</td></tr>';
	}
}

function populateIMSSModal(detalle) {
	const cont = document.querySelector('#modalIMSS tbody');
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.imss) ? detalle.imss : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const actionButtons = [];
			if (row.archivoFaltante) actionButtons.push(getMissingDocumentBadgeHtml());
			const downloadHtml = getDownloadButtonHtml(row.documento || '', 'Descargar documento');
			if (downloadHtml) {
				actionButtons.push(downloadHtml);
			}
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				actionButtons.push(`
					<button type="button" class="${deleteButtonClass} btn-eliminar-imss" data-id="${
					row.id || index
				}" data-requires="write" title="Eliminar" aria-label="Eliminar">
						<i class="bx bx-trash"></i>
					</button>
				`);
			}
			const accionesHtml = actionButtons.length
				? renderActionGroup(actionButtons)
				: `<span class="text-muted small">${puedeEscribir ? 'Sin acciones' : 'Sin permisos'}</span>`;
			cont.innerHTML += `<tr>
				<td>${row.tipo || ''}</td>
				<td>${row.fechaCreacion || ''}</td>
				<td class='text-center'>${accionesHtml}</td>
			</tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="3" class="text-center">Sin IMSS</td></tr>';
	}
}

function populateSellosSATModal(detalle) {
	// Selecciona el tbody del modal actual
	const cont = document.querySelector('#modalSellosSAT tbody');
	cont.innerHTML = '';
	const registros = detalle && Array.isArray(detalle.sellosSat) ? detalle.sellosSat : [];
	const puedeEscribir = canUserWrite();
	if (registros.length) {
		registros.forEach((row, index) => {
			const actionButtons = [];
			if (row.archivoFaltante) actionButtons.push(getMissingDocumentBadgeHtml());
			const downloadHtml = getDownloadButtonHtml(row.documento, 'Descargar sello SAT');
			if (downloadHtml) {
				actionButtons.push(downloadHtml);
			}
			if (puedeEscribir) {
				const deleteButtonClass = getActionButtonClass('outline-danger');
				actionButtons.push(`
					<button type="button" class="${deleteButtonClass} btn-eliminar-sello-sat" data-id="${
					row.id || index
				}" title="Eliminar sello SAT" aria-label="Eliminar sello SAT" data-requires="write">
						<i class="bx bx-trash"></i>
					</button>
				`);
			}
			const accionesHtml = actionButtons.length
				? renderActionGroup(actionButtons)
				: `<span class="text-muted small">${puedeEscribir ? 'Sin acciones' : 'Sin permisos'}</span>`;
			cont.innerHTML += `<tr>
				<td>${row.tipo || ''}</td>
				<td>${row.fechaCreacion || ''}</td>
				<td class="text-center">${accionesHtml}</td>
            </tr>`;
		});
	} else {
		cont.innerHTML = '<tr><td colspan="3" class="text-center">Sin sellos</td></tr>';
	}
}

// Modal de confirmación para eliminar elementos
const modalConfirmDelete = document.createElement('div');
modalConfirmDelete.innerHTML = `
<div class="modal fade" id="modalConfirmDelete" tabindex="-1" aria-labelledby="modalConfirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalConfirmDeleteLabel">Confirmar eliminación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que deseas eliminar este elemento? Esta acción no se puede deshacer.</p>
      </div>
      <div class="modal-footer">
				<button type="button" class="btn btn-secondary rounded-pill shadow-sm px-4" data-bs-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-danger rounded-pill shadow-sm px-4 d-inline-flex align-items-center gap-2" id="btnConfirmDelete">
					<i class="bx bx-trash"></i>
					<span class="fw-semibold">Eliminar</span>
				</button>
      </div>
    </div>
  </div>
</div>
`;
document.body.appendChild(modalConfirmDelete);

let deleteParams = null;

function mostrarModalConfirmacion(seccion, elementoId, empresaId) {
	if (!ensureCanWrite('eliminar elementos')) {
		return;
	}
	deleteParams = { seccion, elementoId, empresaId };
	const modalEl = document.getElementById('modalConfirmDelete');
	// Oscurecer cualquier otro modal abierto
	let lastModalBlurred = null;
	document.querySelectorAll('.modal.show').forEach(function (m) {
		if (m !== modalEl) {
			m.classList.add('modal-blur');
			lastModalBlurred = m;
		}
	});
	const modal = new bootstrap.Modal(modalEl);
	modal.show();
	modalEl.addEventListener('hidden.bs.modal', function handler() {
		if (lastModalBlurred) {
			lastModalBlurred.classList.remove('modal-blur');
			lastModalBlurred = null;
		}
		modalEl.removeEventListener('hidden.bs.modal', handler);
	});
	// Estilo global para el efecto blur
	if (!document.getElementById('modal-blur-style')) {
		const style = document.createElement('style');
		style.id = 'modal-blur-style';
		style.textContent = `.modal-blur { filter: blur(2px) brightness(0.7); pointer-events: none; transition: filter 0.2s; }`;
		document.head.appendChild(style);
	}
}

document.getElementById('btnConfirmDelete').addEventListener('click', async function () {
	if (!deleteParams) return;
	await eliminarElementoModal(deleteParams.seccion, deleteParams.elementoId, deleteParams.empresaId);
	const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmDelete'));
	modal.hide();
	deleteParams = null;
});

/**
 * Elimina un elemento específico de una sección de empresa
 * @param {string} seccion - La sección de donde eliminar (cuentas_bancarias, contraseñas_sat, etc.)
 * @param {number} elementoId - ID del elemento a eliminar
 * @param {number} empresaId - ID de la empresa
 */
async function eliminarElementoModal(seccion, elementoId, empresaId) {
	if (!seccion || !elementoId) {
		alert('Error: Parámetros inválidos para eliminar elemento.');
		return;
	}
	if (!ensureCanWrite('eliminar elementos')) {
		return;
	}
	// Quitar confirm, ya se hace con el modal
	try {
		const formData = new FormData();
		if (seccion === 'cuentas_bancarias') {
			formData.append('accion', 'eliminarCuenta');
			formData.append('id', elementoId);
		} else {
			formData.append('accion', 'eliminarElementoEmpresa');
			formData.append('seccion', seccion);
			formData.append('elementoId', elementoId);
			formData.append('idEmpresa', empresaId);
		}

		const response = await fetch('../../api/routes/apiEmpresa.php', {
			method: 'POST',
			body: formData,
		});

		if (!response.ok) {
			throw new Error('Error en la respuesta del servidor');
		}

		const data = await response.json();

		if (data.success || data.estatus === 'Exito') {
			const sectionConfig = DETAIL_SECTION_MAP[seccion];
			if (sectionConfig) {
				await cargarDetalleEmpresa(
					empresaId,
					(detalleActualizado) => {
						if (typeof sectionConfig.populate === 'function') {
							sectionConfig.populate(detalleActualizado || {});
						}
					},
					{ forceReload: true }
				);
			}
			// Aquí podrías mostrar un toast o mensaje en el modal si lo deseas
		} else {
			throw new Error(data.message || data.mensaje || 'Error al eliminar el elemento');
		}
	} catch (error) {
		console.error('Error:', error);
		alert('Error al eliminar el elemento: ' + error.message);
	}
}

/**
 * Elimina una empresa después de confirmar con el usuario
 * @param {number} empresaId - ID de la empresa a eliminar
 */
async function eliminarEmpresa(empresaId) {
	const empresa = empresasCache[empresaId];
	const nombreEmpresa = empresa ? empresa.razon || 'Empresa sin nombre' : 'Empresa';
	if (!ensureCanWrite('eliminar empresas')) {
		return;
	}

	if (
		!(await window.AppDialog.confirm(
			`¿Estás seguro de que deseas eliminar la empresa "${nombreEmpresa}"?\n\nEsta acción no se puede deshacer.`,
			{ title: 'Eliminar empresa', confirmText: 'Sí, eliminar', variant: 'danger' }
		))
	) {
		return;
	}

	try {
		const formData = new FormData();
		formData.append('accion', 'eliminarEmpresa');
		formData.append('idEmpresa', empresaId);

		const response = await fetch('../../api/routes/apiEmpresa.php', {
			method: 'POST',
			body: formData,
		});

		if (!response.ok) {
			throw new Error('Error en la respuesta del servidor');
		}

		const data = await response.json();

		if (data.success) {
			// Recargar la tabla
			reloadEmpresasTable();

			// Mostrar mensaje de éxito
			alert('Empresa eliminada correctamente');
		} else {
			throw new Error(data.message || 'Error al eliminar la empresa');
		}
	} catch (error) {
		console.error('Error:', error);
		alert('Error al eliminar la empresa: ' + error.message);
	}
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', initializeApp);
