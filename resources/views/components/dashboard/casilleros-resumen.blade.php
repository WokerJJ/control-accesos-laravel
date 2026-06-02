<div class="card card-warning card-outline">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-boxes me-2"></i>
            Casilleros
        </h3>
    </div>

    <div class="card-body text-center">

        <div class="row mb-3">
            <div class="col-6">
                <h2 class="text-success">{{ $data['libres'] }}</h2>
                <small>Libres</small>
            </div>
            <div class="col-6">
                <h2 class="text-danger">{{ $data['ocupados'] }}</h2>
                <small>Ocupados</small>
            </div>
        </div>

        @php
        $pct = $data['porcentaje'] ?? 0;
        @endphp

        <div class="progress" style="height: 8px;">
            <div class="progress-bar bg-danger"
                 style="width: {{ $pct }}%">
            </div>
        </div>

        <small class="text-muted d-block mt-2">
            {{ $data['total'] }} total
        </small>

    </div>
</div>
