@push('scripts')

<script>

    function initCasillerosModal() {
        const modal = document.getElementById('modalDetalle');
        if (!modal) return;

        // Mover modal a <body> para escapar stacking context de .app-main
        if (modal.parentElement !== document.body) {
            // Eliminar duplicados previos (solo hijos directos de <body>)
            const previo = document.body.querySelector(':scope > #modalDetalle');
            if (previo && previo !== modal) previo.remove();
            document.body.appendChild(modal);
        }

        // Evitar duplicar listeners
        if (modal._casillerosInit) return;
        modal._casillerosInit = true;

        modal.addEventListener('show.bs.modal', event => {
            const box = event.relatedTarget;
            if (!box) return;

            document.getElementById('detalleCodigo').textContent =
                box.dataset.codigo;
            document.getElementById('detalleEstado').textContent =
                box.dataset.estado;
            document.getElementById('detallePersona').textContent =
                box.dataset.persona || 'Libre';
            document.getElementById('detalleActividad').textContent =
                box.dataset.actividad || '—';
            document.getElementById('detalleHora').textContent =
                box.dataset.hora || '—';
        });
    }

    document.addEventListener('DOMContentLoaded', initCasillerosModal);
    document.addEventListener('turbo:load', initCasillerosModal);
    document.addEventListener('turbo:frame-load', initCasillerosModal);

</script>

@endpush
