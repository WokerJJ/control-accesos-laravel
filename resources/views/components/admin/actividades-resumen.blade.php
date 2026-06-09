<div class="card card-outline card-info shadow-sm mb-3">

    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-bar mr-2"></i>
            Resumen
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
            :color="$stats['en_curso']['color']"
            :icon="$stats['en_curso']['icono']"
            label="En curso"
            :value="$stats['en_curso']['valor']"></x-shared.info-box>

        <x-shared.info-box
            :color="$stats['pendientes']['color']"
            :icon="$stats['pendientes']['icono']"
            label="Pendientes"
            :value="$stats['pendientes']['valor']"></x-shared.info-box>

        <x-shared.info-box
            :color="$stats['finalizadas']['color']"
            :icon="$stats['finalizadas']['icono']"
            label="Finalizadas"
            :value="$stats['finalizadas']['valor']"></x-shared.info-box>

        <x-shared.info-box
            :color="$stats['total']['color']"
            :icon="$stats['total']['icono']"
            label="Total"
            :value="$stats['total']['valor']"></x-shared.info-box>

    </div>

    {{-- Barra de progreso --}}
    @php
    $total   = $stats['total']['valor']    ?? 0;
    $enCurso = $stats['en_curso']['valor'] ?? 0;
    $pct     = $total > 0 ? round(($enCurso / $total) * 100) : 0;
    @endphp

    <div class="card-footer p-2">
        <small class="text-muted d-flex justify-content-between mb-1">
            <span>En curso</span>
            <span>{{ $enCurso }} / {{ $total }}</span>
        </small>
        <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-{{ $stats['en_curso']['color'] }}"
                 style="width: {{ $pct }}%">
            </div>
        </div>
        <small class="text-muted">{{ $pct }}% activas ahora</small>
    </div>

</div>
