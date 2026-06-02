<div class="card card-success card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-users me-2"></i>
            Dentro ahora
        </h3>
        <div class="card-tools">
            <span class="badge bg-success">
                {{ $adentro ?? '—' }}
            </span>
        </div>
    </div>

    <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
        @forelse($personas as $persona)
        <div class="d-flex align-items-center px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
            <div class="me-3">
                <i class="fas fa-user-circle fa-2x text-secondary"></i>
            </div>

            <div class="flex-grow-1">
                <span class="d-block fw-bold">
                    {{ $persona['nombre'] }}
                </span>
                <small class="text-muted">
                    {{ $persona['actividad'] ?? '—' }}
                </small>
            </div>

            <span class="badge bg-primary">
                {{ $persona['hora_entrada'] ?? '—' }}
            </span>
        </div>
        @empty
        <div class="text-center text-muted py-4">
            <i class="fas fa-door-closed fa-2x mb-2 d-block"></i>
            Sin personas actualmente
        </div>
        @endforelse
    </div>
</div>
