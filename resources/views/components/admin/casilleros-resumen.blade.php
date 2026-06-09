<div class="card card-outline card-warning shadow-sm mb-3">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-boxes mr-2"></i>
            Casilleros
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool"
                    data-lte-toggle="card-collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="card-body p-2">

        <x-shared.info-box
            color="success"
            icon="fas fa-lock-open"
            label="Libres"
            :value="$stats['libres']"
        />

        <x-shared.info-box
            color="danger"
            icon="fas fa-lock"
            label="Ocupados"
            :value="$stats['ocupados']"
        />

        <x-shared.info-box
            color="info"
            icon="fas fa-boxes"
            label="Total"
            :value="$stats['total']"
        />

    </div>

    <div class="card-footer p-2">
        <small class="text-muted d-flex justify-content-between mb-1">
            <span>Ocupación</span>
            <span>{{ $stats['ocupados'] }} / {{ $stats['total'] }}</span>
        </small>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-danger"
                 style="width: {{ $stats['porcentaje'] }}%">
            </div>
            <div class="progress-bar bg-success"
                 style="width: {{ 100 - $stats['porcentaje'] }}%">
            </div>
        </div>
        <small class="text-muted">{{ $stats['porcentaje'] }}% ocupados</small>
    </div>

</div>
