(() => {
	'use strict';

	const base = document.querySelector('meta[name="app-base"]')?.content?.replace(/\/$/, '') || '';
	const managedRoots = ['/Documentos/', '/Imagenes/'];

	window.resolveAppPath = (value) => {
		if (typeof value !== 'string' || value === '') return value;
		return managedRoots.some((root) => value.startsWith(root)) ? base + value : value;
	};

	const resolveNode = (node) => {
		if (!(node instanceof Element)) return;
		[node, ...node.querySelectorAll('[href], [src]')].forEach((element) => {
			['href', 'src'].forEach((attribute) => {
				const value = element.getAttribute(attribute);
				const resolved = window.resolveAppPath(value);
				if (resolved !== value) element.setAttribute(attribute, resolved);
			});
		});
	};

	document.addEventListener('DOMContentLoaded', () => {
		resolveNode(document.body);
		new MutationObserver((mutations) => {
			mutations.forEach((mutation) => mutation.addedNodes.forEach(resolveNode));
		}).observe(document.body, { childList: true, subtree: true });
	});
})();
