{{-- Uso: <x-reporte.chart-card title="Flujo por hora" height="300"> <canvas id="miChart"></canvas> </x-reporte.chart-card> --}}
@props(['title', 'icon' => 'fas fa-chart-bar', 'height' => 280])

<div class="card card-outline card-primary h-100">
    <div class="card-header">
        <h3 class="card-title">
            <i class="{{ $icon }} mr-2"></i>{{ $title }}
        </h3>
    </div>
    <div class="card-body">
        <div style="position:relative; height:{{ $height }}px;">
            {{ $slot }}
        </div>
    </div>
</div>
