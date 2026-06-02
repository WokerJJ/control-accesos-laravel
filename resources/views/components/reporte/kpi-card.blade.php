{{-- Uso: <x-reporte.kpi-card label="Total" :value="$kpis['total']" icon="fas fa-door-open" color="primary" /> --}}
@props(['label', 'value', 'icon', 'color' => 'primary', 'sub' => null])

<div class="col">
    <div class="small-box bg-{{ $color }}">
        <div class="inner">
            <h3>{{ $value }}</h3>
            <p>{{ $label }}</p>
            @if($sub)
            <small class="opacity-75">{{ $sub }}</small>
            @endif
        </div>
        <div class="icon">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
</div>
