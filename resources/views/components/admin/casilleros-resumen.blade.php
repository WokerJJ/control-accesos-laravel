<x-admin.resumen-card
    title="Casilleros"
    icon="fas fa-boxes"
    color="warning"
    :items="[
        ['color' => 'success', 'icon' => 'fas fa-lock-open', 'label' => 'Libres',   'value' => $stats['libres']],
        ['color' => 'danger',  'icon' => 'fas fa-lock',      'label' => 'Ocupados', 'value' => $stats['ocupados']],
        ['color' => 'info',    'icon' => 'fas fa-boxes',     'label' => 'Total',    'value' => $stats['total']],
    ]"
    :progress="[
        'label'      => 'Ocupación',
        'current'    => $stats['ocupados'],
        'total'      => $stats['total'],
        'percentage' => $stats['porcentaje'],
        'segments'   => [
            ['color' => 'danger',  'percent' => $stats['porcentaje']],
            ['color' => 'success', 'percent' => 100 - $stats['porcentaje']],
        ],
        'detail'     => 'ocupados',
    ]"
/>
