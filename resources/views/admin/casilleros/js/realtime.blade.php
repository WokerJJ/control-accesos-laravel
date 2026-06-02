@push('scripts')

<script>

    document.addEventListener('DOMContentLoaded', () => {

        const modal = document.getElementById('modalDetalle');

        modal.addEventListener('show.bs.modal', event => {

            const box = event.relatedTarget;

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
