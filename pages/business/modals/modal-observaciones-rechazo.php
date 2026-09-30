<!-- Modal para motivo de rechazo de documento permanente -->
<div class="modal fade" id="modalObservacionesRechazo" tabindex="-1" aria-labelledby="modalObservacionesRechazoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalObservacionesRechazoLabel">Motivo de rechazo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="inputModalObservacionesRechazo" class="form-label">Observación (opcional)</label>
                    <textarea class="form-control" id="inputModalObservacionesRechazo" rows="3" placeholder="Escribe el motivo del rechazo..." maxlength="500" style="overflow:hidden;resize:none;"></textarea>
                    <div class="form-text text-end"><span id="observacionRechazoContador">0</span>/500 caracteres</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarRechazoDocumento">Rechazar documento</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Contador de caracteres y auto-resize para textarea de observación
    document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('inputModalObservacionesRechazo');
        var contador = document.getElementById('observacionRechazoContador');
        if (textarea && contador) {
            var updateCountAndResize = function() {
                contador.textContent = textarea.value.length;
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            };
            textarea.addEventListener('input', updateCountAndResize);
            // Ajustar altura al mostrar el modal
            var modal = document.getElementById('modalObservacionesRechazo');
            if (modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    updateCountAndResize();
                });
            }
            updateCountAndResize();
        }
    });
</script>