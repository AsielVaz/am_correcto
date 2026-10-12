(() => {
    'use strict';
    const schemas = {
        empresa: [['razon', 'Razón social', 'text', 150], ['rfc', 'RFC', 'text', 50], ['telefono', 'Teléfono', 'tel', 20], ['correo', 'Correo', 'email', 100], ['sitio_web', 'Sitio web', 'url', 100], ['calle', 'Calle / dirección', 'text', 150], ['numero', 'Número', 'text', 50], ['colonia', 'Colonia', 'text', 70], ['cp', 'Código postal', 'text', 70], ['estado', 'Estado', 'text', 70], ['regimen', 'Régimen fiscal', 'text', 200]],
        cuenta: [['banco', 'Banco', 'text', 100], ['cuenta', 'Número de cuenta', 'text', 50], ['clave', 'CLABE', 'text', 100], ['moneda', 'Moneda', 'text', 50]],
        banco: [['banco', 'Banco', 'text', 128], ['usuario', 'Usuario', 'text', 256], ['contrasena', 'Nueva contraseña', 'password', 159], ['nip', 'Nuevo NIP', 'password', 159], ['clave_op', 'Nueva clave de operación', 'password', 159]],
        sat: [['usuario', 'Usuario', 'text', 256], ['contrasena', 'Nueva contraseña', 'password', 159], ['rfc', 'Nuevo RFC', 'password', 159]],
        iofacturo: [['usuario', 'Usuario', 'text', 100], ['rfc', 'Nuevo RFC', 'password', 159], ['contrasena', 'Nueva contraseña', 'password', 159]],
    };
    const tables = [
        ['#modalCuentas', 'cuenta', '.btn-eliminar-cuenta', populateCuentasModal],
        ['#modalContrasenasBancos', 'banco', '.btn-eliminar-contrasena-banco', populateContrasenasBancosModal],
        ['#modalContrasenaSAT', 'sat', '.btn-eliminar-contrasena-sat', populateContrasenaSATModal],
        ['#modalContrasenaIOFacturo', 'iofacturo', '.btn-eliminar-contrasena-iofacturo', populateContrasenaIOFacturoModal],
    ];
    const admin = () => window.appPermissions?.isAdmin === true;
    const el = (tag, classes = '', text) => {
        const node = document.createElement(tag); node.className = classes;
        if (text !== undefined) node.textContent = text;
        return node;
    };
    const iconButton = (classes, icon, title) => {
        const button = el('button', `btn btn-outline-secondary btn-sm action-icon-btn ${classes}`);
        button.type = 'button'; button.title = title; button.setAttribute('aria-label', title);
        button.append(el('i', `bx ${icon}`)); return button;
    };
    const pencil = (type, record, field = '') => {
        const button = iconButton('btn-inline-edit', 'bx-pencil', field ? 'Editar campo' : 'Editar registro');
        button.dataset.editType = type; button.dataset.record = record; button.dataset.field = field;
        button.setAttribute('data-requires', 'admin'); return button;
    };
    let active = null, busy = false, scheduled = false;
    const decorate = () => {
        if (!admin()) { document.querySelectorAll('.btn-inline-edit').forEach((button) => button.remove()); return; }
        document.querySelectorAll('#empresas-tbody tr[data-empresa-id]').forEach((row) => {
            const name = row.querySelector('.razon-social-ellipsis');
            if (!name || row.querySelector('.btn-inline-edit')) return;
            const actions = el('span', 'd-inline-flex gap-1 ms-1');
            const copy = iconButton('btn-copy-field', 'bx-copy', 'Copiar razón social');
            copy.dataset.value = name.textContent;
            const edit = pencil('empresa', row.dataset.empresaId); edit.dataset.empresa = row.dataset.empresaId;
            edit.title = 'Editar datos de la empresa'; edit.setAttribute('aria-label', edit.title);
            actions.append(copy, edit); name.after(actions);
        });
        tables.forEach(([selector, type, deleteSelector]) => {
            document.querySelectorAll(`${selector} tbody tr`).forEach((row) => {
                const record = row.querySelector(deleteSelector)?.dataset.id;
                if (!record) return;
                const copyRow = row.querySelector('.btn-copy-fila, .btn-copy-row');
                if (copyRow && !copyRow.parentElement.querySelector('.btn-inline-edit')) copyRow.after(pencil(type, record));
            });
        });
    };
    const schedule = () => {
        if (scheduled) return; scheduled = true;
        queueMicrotask(() => { scheduled = false; decorate(); });
    };
    const init = () => {
        const base = document.querySelector('meta[name="app-base"]').content;
        const api = `${base}/api/routes/apiEdicion.php`;
        const request = async (url, options) => {
            const response = await fetch(url, options); const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.error || 'No fue posible completar la operación.');
            return data;
        };
        const modified = () => active?.form && [...active.form.querySelectorAll('input')].some((input) => input.value !== input.defaultValue);
        const closeEditor = () => {
            active?.panel.remove(); active = null;
        };
        const showStatus = (host, text, kind = 'success') => {
            let status = host.querySelector('.inline-edit-status');
            if (!status) { status = el('div', ''); status.setAttribute('role', 'status'); host.prepend(status); }
            status.className = `inline-edit-status alert alert-${kind}`; status.textContent = text;
        };
        const refresh = async (type, empresaId, parent) => {
            // Esperar lecturas anteriores para que no sobrescriban la caché actualizada.
            if (empresaDetalleInflight[empresaId]) await empresaDetalleInflight[empresaId].catch(() => {});
            delete empresaDetalleCache[empresaId]; delete empresaStatusCache[empresaId];
            if (type === 'empresa') {
                const data = await request(`${api}?empresa=${encodeURIComponent(empresaId)}`);
                const cached = window.empresasCache[empresaId];
                if (cached) {
                    Object.assign(cached, data.empresa, { sitioWeb: data.empresa.sitio_web });
                    const rows = [...document.querySelectorAll('#empresas-tbody tr[data-empresa-id]')].map((row) => window.empresasCache[row.dataset.empresaId]).filter(Boolean);
                    renderEmpresasTable(rows);
                }
            } else {
                const detail = await cargarDetalleEmpresa(empresaId, null, { forceReload: true });
                if (!detail) throw new Error('No se pudo refrescar la tabla.');
                const config = tables.find(([, entryType]) => entryType === type);
                if (config && parent.dataset.empresaId === String(empresaId)) config[3](detail);
            }
        };
        const openEditor = async (button) => {
            if (!admin() || busy) return;
            if (modified() && !window.confirm('Hay cambios sin guardar. ¿Deseas descartarlos?')) return;
            closeEditor();
            const type = button.dataset.editType, field = button.dataset.field;
            const parent = button.closest('.modal') || document.getElementById('modalEdicionEmpresa');
            const empresaId = button.dataset.empresa || parent.dataset.empresaId;
            const recordId = button.dataset.record;
            if (!empresaId || !schemas[type]) return;
            parent.dataset.empresaId = empresaId;
            const host = parent.querySelector('.modal-body');
            host.querySelector('.inline-edit-status')?.remove();
            const panel = el('div', 'inline-edit-panel border rounded p-3 mb-3');
            panel.setAttribute('aria-live', 'polite'); panel.append(el('p', 'mb-0', 'Cargando datos para editar…'));
            host.prepend(panel); active = { panel, parent, form: null }; busy = true;
            if (type === 'empresa') bootstrap.Modal.getOrCreateInstance(parent).show();
            try {
                const data = await request(`${api}?empresa=${encodeURIComponent(empresaId)}`);
                const record = type === 'empresa' ? data.empresa : data.accesos[type]?.find((row) => String(row.id) === recordId);
                if (!record) throw new Error('El registro ya no existe o no pertenece a esta empresa.');
                const selectedFields = field ? schemas[type].filter(([name]) => name === field) : schemas[type];
                const form = el('form', ''); form.autocomplete = 'off';
                form.append(el('h6', 'mb-3', `Editar · ${data.empresa.razon}`));
                const row = el('div', 'row g-3');
                selectedFields.forEach(([name, label, inputType, max]) => {
                    const col = el('div', field ? 'col-12' : 'col-md-6');
                    const caption = el('label', 'form-label', label);
                    const input = el('input', 'form-control');
                    input.id = `inline-edit-${type}-${recordId}-${name}`; input.name = name; input.type = inputType; input.maxLength = max; caption.htmlFor = input.id;
                    input.defaultValue = inputType === 'password' ? '' : String(record[name] ?? '');
                    input.required = name === 'razon' || (inputType === 'password' && !!field);
                    if (name === 'telefono') { input.pattern = '\\d{10}'; input.inputMode = 'numeric'; }
                    if (inputType === 'password') {
                        input.autocomplete = 'new-password'; input.placeholder = 'Vacío: conservar valor actual';
                        col.append(caption, input, el('small', 'text-muted', 'Escribe un nuevo valor para reemplazar el actual.'));
                    } else col.append(caption, input);
                    row.append(col);
                });
                const error = el('div', 'alert alert-danger d-none mt-3'); error.setAttribute('role', 'alert');
                const actions = el('div', 'd-flex gap-2 mt-3');
                const save = el('button', 'btn btn-primary', 'Guardar'); save.type = 'submit';
                const cancel = el('button', 'btn btn-outline-secondary', 'Cancelar'); cancel.type = 'button';
                cancel.addEventListener('click', () => { if (!busy) { closeEditor(); if (type === 'empresa') bootstrap.Modal.getInstance(parent)?.hide(); } });
                actions.append(save, cancel); form.append(row, error, actions); panel.replaceChildren(form);
                active.form = form;
                form.addEventListener('submit', async (event) => {
                    event.preventDefault(); if (busy || !admin() || !form.reportValidity()) return;
                    const body = new FormData(form);
                    body.set('empresa', empresaId); body.set('registro', recordId); body.set('tipo', type);
                    busy = true; error.classList.add('d-none'); save.textContent = 'Guardando…';
                    form.querySelectorAll('input, button').forEach((control) => { control.disabled = true; });
                    try {
                        await request(api, { method: 'POST', body });
                        // El guardado ya terminó: un fallo al refrescar no debe provocar otro envío.
                        try {
                            await refresh(type, empresaId, parent);
                            showStatus(host, 'Cambios guardados correctamente.');
                        } catch { showStatus(host, 'Los cambios se guardaron. Cierra y vuelve a abrir para actualizar los datos.', 'warning'); }
                        closeEditor();
                        if (type === 'empresa') { busy = false; bootstrap.Modal.getInstance(parent)?.hide(); showCopyNotification('Cambios guardados'); }
                    } catch (err) {
                        error.textContent = err.message || 'Error de conexión. Intenta nuevamente.'; error.classList.remove('d-none');
                        form.querySelectorAll('input, button').forEach((control) => { control.disabled = false; });
                        save.textContent = 'Guardar';
                    } finally { busy = false; }
                });
                form.querySelector('input')?.focus(); panel.scrollIntoView({ block: 'nearest' });
            } catch (error) {
                closeEditor(); showStatus(host, error.message || 'No se pudo cargar el registro.', 'danger');
            } finally { busy = false; }
        };
        document.addEventListener('click', (event) => {
            const button = event.target.closest('.btn-inline-edit');
            if (button) { event.preventDefault(); event.stopPropagation(); openEditor(button); }
        });
        document.querySelectorAll('.modal').forEach((modal) => {
            modal.addEventListener('show.bs.modal', (event) => {
                if (event.relatedTarget?.dataset.id) modal.dataset.empresaId = event.relatedTarget.dataset.id;
            });
            modal.addEventListener('hide.bs.modal', (event) => {
                if (active?.parent !== modal) return;
                if (busy || (modified() && !window.confirm('Hay cambios sin guardar. ¿Deseas descartarlos?'))) event.preventDefault();
            });
            modal.addEventListener('hidden.bs.modal', () => {
                if (active?.parent === modal) closeEditor();
                modal.querySelector('.inline-edit-status')?.remove();
            });
        });
        window.addEventListener('beforeunload', (event) => {
            if (busy || modified()) { event.preventDefault(); event.returnValue = ''; }
        });
        window.addEventListener('permissions:updated', () => { if (!admin() && !busy) closeEditor(); schedule(); });
        ['#empresas-tbody', ...tables.map(([selector]) => `${selector} tbody`)].forEach((selector) => {
            const root = document.querySelector(selector);
            if (root) new MutationObserver(schedule).observe(root, { childList: true, subtree: true });
        });
        decorate();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
