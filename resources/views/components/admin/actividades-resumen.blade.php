<x-admin.resumen-card
    title="Resumen"
    icon="fas fa-chart-bar"
    color="info"
    :items="[
        ['color' => $stats['en_curso']['color'],     'icon' => $stats['en_curso']['icono'],     'label' => 'En curso',     'value' => $stats['en_curso']['valor']],
        ['color' => $stats['pendientes']['color'],    'icon' => $stats['pendientes']['icono'],   'label' => 'Pendientes',   'value' => $stats['pendientes']['valor']],
        ['color' => $stats['finalizadas']['color'],   'icon' => $stats['finalizadas']['icono'],  'label' => 'Finalizadas',  'value' => $stats['finalizadas']['valor']],
        ['color' => $stats['total']['color'],         'icon' => $stats['total']['icono'],        'label' => 'Total',        'value' => $stats['total']['valor']],
    ]"
    :progress="[
        'label'      => 'En curso',
        'current'    => $stats['en_curso']['valor'] ?? 0,
        'total'      => $stats['total']['valor'] ?? 0,
        'percentage' => ($stats['total']['valor'] ?? 0) > 0 ? round(($stats['en_curso']['valor'] ?? 0) / $stats['total']['valor'] * 100) : 0,
        'barColor'   => $stats['en_curso']['color'],
        'detail'     => 'activas ahora',
    ]"
/>
