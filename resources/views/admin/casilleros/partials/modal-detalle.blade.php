<div class="modal fade" id="modalDetalle" tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Detalle del casillero
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <h1 id="detalleCodigo" class="fw-bold mb-3"></h1>

                <hr>

                {{-- Sección para casilleros normales: persona, actividad, hora --}}
                <div id="detalleInfoNormal">
                    <p class="mb-2">
                        <strong>Estado:</strong>
                        <span id="detalleEstado"></span>
                    </p>

                    <p class="mb-2">
                        <strong>Persona:</strong>
                        <span id="detallePersona"></span>
                    </p>

                    <p class="mb-2">
                        <strong>Actividad:</strong>
                        <span id="detalleActividad"></span>
                    </p>

                    <p class="mb-0">
                        <strong>Ingreso:</strong>
                        <span id="detalleHora"></span>
                    </p>
                </div>

                {{-- Sección para casillero externo: solo usos --}}
                <div id="detalleInfoExterno" class="d-none text-center">
                    <p class="mb-2">
                        <strong>Estado:</strong>
                        <span id="detalleEstadoExterno"></span>
                    </p>

                    <p class="mb-0">
                        <strong>Usos totales:</strong>
                        <span id="detalleUsos" class="badge bg-secondary fs-6"></span>
                    </p>
                </div>

            </div>

        </div>

    </div>

</div>
