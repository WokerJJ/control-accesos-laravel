@php
$chartAccesos = [
    'type' => 'line',
    'data' => [
        'labels' => array_keys($data ?? []),
        'datasets' => [[
            'label' => 'Accesos',
            'data' => array_values($data ?? []),
            'fill' => true,
            'tension' => 0.4,
            'borderColor' => 'rgba(60, 141, 188, 1)',
            'backgroundColor' => 'rgba(60, 141, 188, 0.15)',
            'pointRadius' => 4,
        ]],
    ],
    'options' => [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => ['legend' => ['display' => false]],
        'scales' => ['y' => ['beginAtZero' => true]],
    ],
];
@endphp

<div class="card card-primary card-outline h-100">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-chart-area me-2"></i>
            Accesos últimos 7 días
        </h3>
    </div>

    <div class="card-body">
        <canvas id="accesosChart" data-chart-config='{!! json_encode($chartAccesos, JSON_HEX_TAG | JSON_HEX_APOS) !!}'></canvas>
    </div>
</div>
