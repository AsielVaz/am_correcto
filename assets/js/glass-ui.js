(() => {
	'use strict';

	const typeAliases = { danger: 'error', primary: 'info', secondary: 'info' };
	const settings = {
		info: { title: 'Información', icon: 'i' },
		success: { title: 'Operación exitosa', icon: '✓' },
		warning: { title: 'Atención', icon: '!' },
		error: { title: 'Ocurrió un problema', icon: '×' },
	};

	let region;

	function getRegion() {
		if (region?.isConnected) return region;
		region = document.createElement('div');
		region.className = 'glass-toast-region';
		region.setAttribute('role', 'region');
		region.setAttribute('aria-label', 'Notificaciones');
		region.setAttribute('aria-live', 'polite');
		document.body.appendChild(region);
		return region;
	}

	function normalizeType(type) {
		const normalized = String(type || 'info').toLowerCase();
		return typeAliases[normalized] || (settings[normalized] ? normalized : 'info');
	}

	function inferType(message) {
		const value = String(message || '').toLowerCase();
		if (/correctamente|exitos[oa]|guardad[oa]|completad[oa]/.test(value)) return 'success';
		if (/error|inválid|no tienes permisos|no fue posible|falló|incorrect/.test(value)) return 'error';
		if (/atención|advertencia|seguro|requerid/.test(value)) return 'warning';
		return 'info';
	}

	function dismiss(toast) {
		if (!toast || toast.classList.contains('is-leaving')) return;
		toast.classList.add('is-leaving');
		setTimeout(() => toast.remove(), 300);
	}

	function show(message, options = {}) {
		if (!document.body) {
			document.addEventListener('DOMContentLoaded', () => show(message, options), { once: true });
			return null;
		}

		const type = normalizeType(typeof options === 'string' ? options : options.type);
		const config = typeof options === 'string' ? {} : options;
		const duration = Math.max(1800, Number(config.duration) || 4500);
		const toast = document.createElement('article');
		toast.className = 'glass-toast';
		toast.dataset.type = type;
		toast.style.setProperty('--toast-duration', `${duration}ms`);
		toast.setAttribute('role', type === 'error' ? 'alert' : 'status');

		const icon = document.createElement('span');
		icon.className = 'glass-toast__icon';
		icon.setAttribute('aria-hidden', 'true');
		icon.textContent = settings[type].icon;

		const content = document.createElement('div');
		content.className = 'glass-toast__content';
		const title = document.createElement('p');
		title.className = 'glass-toast__title';
		title.textContent = config.title || settings[type].title;
		const body = document.createElement('p');
		body.className = 'glass-toast__message';
		body.textContent = String(message ?? '');
		content.append(title, body);

		const close = document.createElement('button');
		close.type = 'button';
		close.className = 'glass-toast__close';
		close.setAttribute('aria-label', 'Cerrar notificación');
		close.textContent = '×';
		close.addEventListener('click', () => dismiss(toast));

		const progress = document.createElement('span');
		progress.className = 'glass-toast__progress';
		progress.setAttribute('aria-hidden', 'true');
		toast.append(icon, content, close, progress);

		const toastRegion = getRegion();
		toastRegion.prepend(toast);
		while (toastRegion.children.length > 5) toastRegion.lastElementChild.remove();

		let remaining = duration;
		let startedAt = Date.now();
		let timer = setTimeout(() => dismiss(toast), remaining);
		toast.addEventListener('mouseenter', () => {
			clearTimeout(timer);
			remaining -= Date.now() - startedAt;
		});
		toast.addEventListener('mouseleave', () => {
			startedAt = Date.now();
			timer = setTimeout(() => dismiss(toast), Math.max(250, remaining));
		});
		return toast;
	}

	window.AppToast = {
		show,
		success: (message, options = {}) => show(message, { ...options, type: 'success' }),
		error: (message, options = {}) => show(message, { ...options, type: 'error' }),
		warning: (message, options = {}) => show(message, { ...options, type: 'warning' }),
		info: (message, options = {}) => show(message, { ...options, type: 'info' }),
	};
	window.appNotify = (message, type = 'info', options = {}) => show(message, { ...options, type });
	window.alert = (message, type) => show(message, { type: type || inferType(message) });
	window.AppDialog = {
		confirm(message, options = {}) {
			return new Promise((resolve) => {
				const backdrop = document.createElement('div');
				backdrop.className = 'glass-dialog-backdrop';
				const dialog = document.createElement('section');
				dialog.className = 'glass-dialog';
				dialog.setAttribute('role', 'alertdialog');
				dialog.setAttribute('aria-modal', 'true');

				const icon = document.createElement('div');
				icon.className = 'glass-dialog__icon';
				icon.textContent = '!';
				const title = document.createElement('h2');
				title.className = 'glass-dialog__title';
				title.textContent = options.title || 'Confirma esta acción';
				const body = document.createElement('p');
				body.className = 'glass-dialog__message';
				body.textContent = String(message || '');
				const actions = document.createElement('div');
				actions.className = 'glass-dialog__actions';
				const cancel = document.createElement('button');
				cancel.type = 'button';
				cancel.className = 'btn btn-light rounded-pill px-3';
				cancel.textContent = options.cancelText || 'Cancelar';
				const accept = document.createElement('button');
				accept.type = 'button';
				accept.className = `btn btn-${options.variant || 'danger'} rounded-pill px-3`;
				accept.textContent = options.confirmText || 'Confirmar';
				actions.append(cancel, accept);
				dialog.append(icon, title, body, actions);
				backdrop.appendChild(dialog);
				document.body.appendChild(backdrop);

				const finish = (result) => {
					document.removeEventListener('keydown', onKeydown);
					backdrop.remove();
					resolve(result);
				};
				const onKeydown = (event) => {
					if (event.key === 'Escape') finish(false);
				};
				document.addEventListener('keydown', onKeydown);
				cancel.addEventListener('click', () => finish(false));
				accept.addEventListener('click', () => finish(true));
				backdrop.addEventListener('click', (event) => {
					if (event.target === backdrop) finish(false);
				});
				requestAnimationFrame(() => cancel.focus());
			});
		},
	};

	function enhance(root = document) {
		root.querySelectorAll?.('.alert:not(.glass-alert-enter)').forEach((item) => item.classList.add('glass-alert-enter'));
		root.querySelectorAll?.('.card:not(.glass-reveal), .companies-table-wrapper:not(.glass-reveal)').forEach((item, index) => {
			item.classList.add('glass-reveal');
			item.style.setProperty('--reveal-delay', `${Math.min(index, 10) * 55}ms`);
		});
	}

	document.addEventListener('DOMContentLoaded', () => {
		document.querySelectorAll('.modal').forEach((modal) => {
			if (modal.parentElement !== document.body) {
				document.body.appendChild(modal);
			}
		});

		enhance();
		new MutationObserver((mutations) => {
			mutations.forEach((mutation) => mutation.addedNodes.forEach((node) => {
				if (node instanceof Element) enhance(node.matches('.alert, .card, .companies-table-wrapper') ? node.parentElement : node);
			}));
		}).observe(document.body, { childList: true, subtree: true });
	});

	document.addEventListener('pointerdown', (event) => {
		const button = event.target.closest('.btn, .topbar-button');
		if (!button || button.disabled) return;
		const rect = button.getBoundingClientRect();
		const size = Math.max(rect.width, rect.height);
		const ripple = document.createElement('span');
		ripple.className = 'glass-ripple';
		ripple.style.width = ripple.style.height = `${size}px`;
		ripple.style.left = `${event.clientX - rect.left - size / 2}px`;
		ripple.style.top = `${event.clientY - rect.top - size / 2}px`;
		button.appendChild(ripple);
		setTimeout(() => ripple.remove(), 700);
	});
})();

