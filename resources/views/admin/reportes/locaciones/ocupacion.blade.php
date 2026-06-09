@extends('layouts.admin')

@section('titulo', 'Ocupación de locaciones')
@section('header', 'Reportes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item">Reportes</li>
<li class="breadcrumb-item active">Ocupación de locaciones</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Filtros ──────────────────────────────────── --}}
    <x-dashboard.filtro-card action="{{ route('admin.reportes.locaciones.ocupacion') }}" col-boton="2">
        <x-slot:campos>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control"
                           value="{{ $desde }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control"
                           value="{{ $hasta }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>
        </x-slot:campos>
    </x-dashboard.filtro-card>

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <x-dashboard.stat-box
            label="Total accesos"
            :value="$kpis['total_accesos']"
            icon="fas fa-door-open"
            color="text-bg-primary"/>
        <x-dashboard.stat-box
            label="Locación más usada"
            :value="$kpis['locacion_top']"
            icon="fas fa-map-marker-alt"
            color="text-bg-success"/>
        <x-dashboard.stat-box
            label="Usos locación top"
            :value="$kpis['locacion_top_usos']"
            icon="fas fa-fire"
            color="text-bg-warning"/>
        <x-dashboard.stat-box
            label="Hora pico"
            :value="$kpis['hora_pico_global']"
            icon="fas fa-clock"
            color="text-bg-info"/>
    </div>

    @php
    $chartFlujoLocacion = [
        'type' => 'bar',
        'data' => [
            'labels' => $grafica['labels'],
            'datasets' => $grafica['datasets'],
        ],
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => ['mode' => 'index', 'intersect' => false],
            'plugins' => ['legend' => ['position' => 'top']],
            'scales' => [
                'x' => ['stacked' => true, 'ticks' => ['maxRotation' => 45]],
                'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
            ],
        ],
    ];

    $chartDona = [
        'type' => 'doughnut',
        'data' => [
            'labels' => $ocupacion->pluck('nombre')->toArray(),
            'datasets' => [[
                'data' => $ocupacion->pluck('total_accesos')->toArray(),
                'backgroundColor' => ['#007bff','#28a745','#17a2b8','#ffc107','#dc3545','#6f42c1','#fd7e14','#20c997'],
                'borderWidth' => 2,
            ]],
        ],
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['position' => 'bottom', 'labels' => ['boxWidth' => 12]]],
        ],
    ];
    @endphp

    {{-- ── Gráfica apilada ──────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <x-reporte.chart-card
                title="Flujo por hora desglosado por locación"
                icon="fas fa-chart-bar"
                :height="300">
                <canvas id="chartFlujoLocacion" data-chart-config='{!! json_encode($chartFlujoLocacion, JSON_HEX_TAG | JSON_HEX_APOS) !!}'></canvas>
            </x-reporte.chart-card>
        </div>
    </div>

    {{-- ── Tabla ranking ────────────────────────────── --}}
    <div class="row g-3">

        {{-- Dona distribución --}}
        <div class="col-md-4">
            <x-reporte.chart-card
                title="Distribución"
                icon="fas fa-chart-pie"
                :height="320">
                <canvas id="chartDona" data-chart-config='{!! json_encode($chartDona, JSON_HEX_TAG | JSON_HEX_APOS) !!}'></canvas>
            </x-reporte.chart-card>
        </div>

        {{-- Ranking tabla --}}
        <div class="col-md-8">
            <x-admin.data-table
                icon="fas fa-map-marker-alt"
                title="Ranking de locaciones"
                full-height
            >
                <x-slot:tools>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.reportes.export.locaciones.csv', request()->query()) }}"
                           class="btn btn-sm btn-success export-btn" data-turbo="false">
                            <i class="fas fa-file-excel mr-1"></i><span class="btn-text">Excel</span>
                        </a>
                        <a href="{{ route('admin.reportes.export.locaciones.pdf', request()->query()) }}"
                           class="btn btn-sm btn-danger export-btn" data-turbo="false">
                            <i class="fas fa-file-pdf mr-1"></i><span class="btn-text">PDF</span>
                        </a>
                    </div>
                </x-slot:tools>

                <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Locación</th>
                    <th class="text-end">Accesos</th>
                    <th class="text-end">En curso</th>
                    <th>Participación</th>
                    <th>Días activa</th>
                    <th>Prom.</th>
                    <th>Últ. acceso</th>
                </tr>
                </thead>
                <tbody>
                @forelse($ocupacion as $i => $loc)
                <tr>
                    <td>
                        @if($i === 0)
                        <i class="fas fa-medal text-warning"></i>
                        @elseif($i === 1)
                        <i class="fas fa-medal text-secondary"></i>
                        @elseif($i === 2)
                        <i class="fas fa-medal" style="color:#cd7f32"></i>
                        @else
                        <span class="text-muted">{{ $i + 1 }}</span>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $loc->nombre }}</td>
                    <td class="text-end">
                        <span class="badge bg-primary">{{ $loc->total_accesos }}</span>
                    </td>
                    <td class="text-end">
                        @if($loc->en_curso > 0)
                        <span class="badge bg-success">{{ $loc->en_curso }}</span>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="min-width:130px;">
                        <div class="d-flex align-items-center gap-1">
                            <div class="progress flex-grow-1" style="height:6px;">
                                <div class="progress-bar bg-primary"
                                     style="width:{{ $loc->porcentaje }}%"></div>
                            </div>
                            <small class="text-muted">{{ $loc->porcentaje }}%</small>
                        </div>
                    </td>
                    <td>
                        <small class="text-muted">{{ $loc->dias_activa }} días</small>
                    </td>
                    <td>
                        <small class="text-muted">{{ $loc->duracion_promedio }}</small>
                    </td>
                    <td>
                        <small class="text-muted">{{ $loc->ultimo_acceso }}</small>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                        Sin datos en el período
                    </td>
                </tr>
                @endforelse
                </tbody>
            </x-admin.data-table>
        </div>

    </div>

</div>
@endsection
