(function () {
	const DEFAULT_PERMISSIONS = {
		role: 'Usuario',
		canWrite: false,
		isAdmin: false,
	};

	function getPermissions() {
		return window.appPermissions || DEFAULT_PERMISSIONS;
	}

	function storeOriginalState(element) {
		if (!element.dataset.permissionsOriginalDisplay) {
			element.dataset.permissionsOriginalDisplay = element.style.display || '';
		}
		if (typeof element.disabled !== 'undefined' && !element.dataset.permissionsOriginalDisabled) {
			element.dataset.permissionsOriginalDisabled = element.disabled ? 'true' : 'false';
		}
		if (!element.dataset.permissionsOriginalOpacity) {
			element.dataset.permissionsOriginalOpacity = element.style.opacity || '';
		}
	}

	function restoreElement(element) {
		if (element.dataset.permissionsOriginalDisplay !== undefined) {
			element.style.display = element.dataset.permissionsOriginalDisplay;
		}
		if (typeof element.disabled !== 'undefined' && element.dataset.permissionsOriginalDisabled !== 'true') {
			element.disabled = false;
		}
		element.classList.remove('permission-readonly', 'permission-disabled', 'disabled');
		element.style.pointerEvents = '';
		element.style.opacity = element.dataset.permissionsOriginalOpacity || '';
	}

	function applyDisableMode(element, hasAccess) {
		storeOriginalState(element);
		if (element.matches('form')) {
			const fields = element.querySelectorAll('input, select, textarea, button');
			fields.forEach((field) => {
				if (!field.dataset.permissionsOriginalDisabled) {
					field.dataset.permissionsOriginalDisabled = field.disabled ? 'true' : 'false';
				}
				if (!hasAccess) {
					field.disabled = true;
				} else if (field.dataset.permissionsOriginalDisabled !== 'true') {
					field.disabled = false;
				}
			});
			element.classList.toggle('permission-readonly', !hasAccess);
		} else {
			if (typeof element.disabled !== 'undefined') {
				if (!hasAccess) {
					element.disabled = true;
				} else if (element.dataset.permissionsOriginalDisabled !== 'true') {
					element.disabled = false;
				}
			}
			element.classList.toggle('permission-disabled', !hasAccess);
			element.style.pointerEvents = !hasAccess ? 'none' : '';
			element.style.opacity = !hasAccess ? '0.5' : element.dataset.permissionsOriginalOpacity || '';
			if (element.matches('.btn')) {
				element.classList.toggle('disabled', !hasAccess);
			}
		}
	}

	function applyHideMode(element, hasAccess) {
		storeOriginalState(element);
		if (!hasAccess) {
			element.style.display = 'none';
		} else {
			element.style.display = element.dataset.permissionsOriginalDisplay || '';
		}
	}

	function applyToElement(element, permissions) {
		const requirement = element.getAttribute('data-requires');
		if (!requirement) return;

		const modeAttr = element.getAttribute('data-permission-mode');
		const defaultMode = element.matches('form') ? 'disable' : 'hide';
		const mode = modeAttr ? modeAttr.toLowerCase() : defaultMode;

		let hasAccess = true;
		switch (requirement) {
			case 'admin':
				hasAccess = permissions.isAdmin;
				break;
			case 'write':
				hasAccess = permissions.canWrite;
				break;
			default:
				hasAccess = true;
		}

		if (hasAccess) {
			restoreElement(element);
			return;
		}

		if (mode === 'disable') {
			applyDisableMode(element, hasAccess);
		} else {
			applyHideMode(element, hasAccess);
		}
	}

	function collectTargets(root) {
		const targets = [];
		if (root.nodeType === 1 && root.hasAttribute('data-requires')) {
			targets.push(root);
		}
		if (root.querySelectorAll) {
			targets.push(...root.querySelectorAll('[data-requires]'));
		}
		return targets;
	}

	function applyPermissions(root) {
		const permissions = getPermissions();
		const targets = collectTargets(root);
		targets.forEach((element) => applyToElement(element, permissions));
	}

	function initPermissionsObserver() {
		const observer = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				mutation.addedNodes.forEach((node) => {
					if (node.nodeType === 1) {
						applyPermissions(node);
					}
				});
			});
		});
		observer.observe(document.body, { childList: true, subtree: true });
	}

	function initialize() {
		applyPermissions(document.body);
		initPermissionsObserver();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initialize);
	} else {
		initialize();
	}

	window.addEventListener('permissions:updated', () => applyPermissions(document.body));
})();
