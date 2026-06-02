{{-- Estado y duración --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <span class="badge bg-{{ $acceso->estado === 'en_curso' ? 'success' : 'secondary' }} fs-6">
        <i class="fas fa-{{ $acceso->estado === 'en_curso' ? 'play-circle' : 'check-circle' }} me-1"></i>
        {{ $acceso->estado === 'en_curso' ? 'En curso' : 'Completado' }}
    </span>
    @if($acceso->duracion)
    <span class="text-muted">
            <i class="fas fa-clock me-1"></i>{{ $acceso->duracion }} min
        </span>
    @elseif($acceso->estado === 'en_curso')
    <span class="text-success">
            <i class="fas fa-clock me-1"></i>
            {{ $acceso->hora_ingreso->diffForHumans(null, true) }} dentro
        </span>
    @endif
</div>

<div class="row g-3">

    {{-- Persona --}}
    <div class="col-12">
        <div class="card card-outline card-primary mb-0">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">
                    <i class="fas fa-user me-2"></i>Persona
                </h6>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Nombre</small>
                        <span>{{ $acceso->persona->nombre_completo }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Documento</small>
                        <span>{{ $acceso->persona->doc_identidad }}</span>
                    </div>
                    @if($acceso->persona->email)
                    <div class="col-md-6 mt-2">
                        <small class="text-muted d-block">Email</small>
                        <span>{{ $acceso->persona->email }}</span>
                    </div>
                    @endif
                    @if($acceso->persona->celular)
                    <div class="col-md-6 mt-2">
                        <small class="text-muted d-block">Celular</small>
                        <span>{{ $acceso->persona->celular }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Actividad y locación --}}
    <div class="col-md-6">
        <div class="card card-outline card-success mb-0 h-100">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>Actividad
                </h6>
            </div>
            <div class="card-body py-2">
                <span class="d-block">{{ $acceso->actividad->nombre }}</span>
                <small class="text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    {{ $acceso->locacion->nombre ?? '—' }}
                </small>
            </div>
        </div>
    </div>

    {{-- Casillero --}}
    <div class="col-md-6">
        <div class="card card-outline card-warning mb-0 h-100">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>Casillero
                </h6>
            </div>
            <div class="card-body py-2">
                @if($acceso->casillero)
                <span class="fs-5 fw-bold">{{ $acceso->casillero->codigo }}</span>
                @if($acceso->casillero->es_virtual ?? false)
                <span class="badge bg-secondary ms-1">Externo</span>
                @endif
                @else
                <span class="text-muted">Sin casillero</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Tiempos --}}
    <div class="col-12">
        <div class="card card-outline card-info mb-0">
            <div class="card-header py-2">
                <h6 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>Tiempos
                </h6>
            </div>
            <div class="card-body py-2">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Ingreso</small>
                        <span>{{ $acceso->hora_ingreso?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Salida</small>
                        <span>{{ $acceso->hora_salida?->format('d/m/Y H:i') ?? 'Aún dentro' }}</span>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Método</small>
                        <span class="badge bg-secondary">
                            {{ ucfirst($acceso->metodo_acceso) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
