<div class="card card-danger card-outline h-100">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-star me-2"></i>
            Calificación promedio
        </h3>
    </div>

    <div class="card-body text-center">

        @php
        $prom = $value ?? 0;
        @endphp

        <h1 class="mb-1" style="font-size: 3.5rem; font-weight: 300;">
            {{ number_format($prom, 1) }}
        </h1>

        <div class="mb-2">
            @for($i = 1; $i <= 5; $i++)
            <i class="fas fa-star {{ $i <= round($prom) ? 'text-warning' : 'text-muted' }}"></i>
            @endfor
        </div>

        <small class="text-muted">sobre 5.0</small>

    </div>
</div>
