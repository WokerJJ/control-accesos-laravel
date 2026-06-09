@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', () => {

        const modal = document.getElementById('modalDetalle');
        if (!modal) return;

        // Mover modal a <body> para escapar stacking context de .app-main
        // Así el z-index del modal puede superar al backdrop
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        // Evitar duplicar listeners en re-inicializaciones Turbo
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

    });

</script>

@endpush
