@foreach($mapa as $fila => $casilleros)

<div class="card card-outline card-secondary mb-4">

    <div class="card-header">
        <h3 class="card-title">
            Fila {{ $fila }}
        </h3>
    </div>

    <div class="card-body">

        <div class="d-flex flex-wrap gap-3">

            @foreach($casilleros as $casillero)

            <div
                class="casillero-box"

                data-bs-toggle="modal"
                data-bs-target="#modalDetalle"

                data-codigo="{{ $casillero['codigo'] }}"
                data-estado="{{ $casillero['estado'] }}"
                data-persona="{{ $casillero['persona'] }}"
                data-actividad="{{ $casillero['actividad'] }}"
                data-hora="{{ $casillero['hora_ingreso'] }}"

                style="
                    width: 120px;
                    height: 120px;
                    border-radius: 18px;
                    cursor: pointer;
                    transition: .2s;

                    background:
                        {{ $casillero['estado'] === 'ocupado'
                            ? 'linear-gradient(135deg,#dc3545,#b02a37)'
                            : 'linear-gradient(135deg,#198754,#157347)' }};

                    color: white;
                "
            >

                <div class="h-100 d-flex flex-column justify-content-center align-items-center">

                    <i class="fas fa-box fa-2x mb-2"></i>

                    <h4 class="fw-bold mb-1">
                        {{ $casillero['codigo'] }}
                    </h4>

                    <small>
                        {{ ucfirst($casillero['estado']) }}
                    </small>

                </div>

            </div>

            @endforeach

        </div>

    </div>

</div>

@endforeach
