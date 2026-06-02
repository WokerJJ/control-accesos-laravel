@extends('layouts.admin')

@section('titulo', 'Resumen del día')
@section('header', 'Reportes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item">Reportes</li>
<li class="breadcrumb-item active">Resumen del día</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Fecha ────────────────────────────────────── --}}
    <div class="d-flex align-items-center mb-3 gap-2">
        <i class="fas fa-calendar-day fa-lg text-primary"></i>
        <span class="text-muted text-capitalize">{{ $fecha }}</span>
        <span class="badge bg-primary ms-1">Tiempo real</span>
    </div>

    {{-- ── KPIs ─────────────────────────────────────── --}}
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <x-dashboard.stat-box
            label="Ingresos hoy"
            :value="$kpis['total']"
            icon="fas fa-door-open"
            color="text-bg-primary"/>
        <x-dashboard.stat-box
            label="En curso"
            :value="$kpis['en_curso']"
            icon="fas fa-play-circle"
            color="text-bg-success"/>
        <x-dashboard.stat-box
            label="Completados"
            :value="$kpis['completados']"
            icon="fas fa-check-circle"
            color="text-bg-secondary"/>
        <x-dashboard.stat-box
            label="Permanencia promedio"
            :value="$kpis['duracion_promedio']"
            icon="fas fa-clock"
            color="text-bg-info"/>
    </div>

    {{-- ── Gráficas fila 1 ─────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Flujo por hora --}}
        <div class="col-md-8">
            <x-reporte.chart-card title="Flujo de ingresos por hora" icon="fas fa-stream" :height="280">
                <canvas id="chartFlujo"></canvas>
            </x-reporte.chart-card>
        </div>

        {{-- Por locación --}}
        <div class="col-md-4">
            <x-reporte.chart-card title="Por locación" icon="fas fa-map-marker-alt" :height="280">
                <canvas id="chartLocacion"></canvas>
            </x-reporte.chart-card>
        </div>

    </div>

    {{-- ── Gráficas fila 2 ─────────────────────────── --}}
    <div class="row g-3 mb-4">

        {{-- Por actividad --}}
        <div class="col-md-6">
            <x-reporte.chart-card title="Actividades más registradas" icon="fas fa-tasks" :height="260">
                <canvas id="chartActividad"></canvas>
            </x-reporte.chart-card>
        </div>

        {{-- Completados vs En curso — dona --}}
        <div class="col-md-6">
            <x-reporte.chart-card title="Estado de accesos" icon="fas fa-chart-pie" :height="260">
                <canvas id="chartEstado"></canvas>
            </x-reporte.chart-card>
        </div>

    </div>

    {{-- ── Tabla últimos accesos ────────────────────── --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-list mr-2"></i>Últimos accesos del día
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Persona</th>
                        <th>Documento</th>
                        <th>Actividad</th>
                        <th>Locación</th>
                        <th>Ingreso</th>
                        <th>Salida</th>
                        <th>Duración</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($ultimosAccesos as $acceso)
                    <tr>
                        <td>
                            {{ $acceso->persona->primer_nombre }}
                            {{ $acceso->persona->primer_apellido }}
                        </td>
                        <td>
                            <small class="text-muted">
                                {{ $acceso->persona->doc_identidad }}
                            </small>
                        </td>
                        <td>{{ $acceso->actividad->nombre }}</td>
                        <td>{{ $acceso->locacion->nombre }}</td>
                        <td>{{ $acceso->hora_ingreso?->format('H:i') }}</td>
                        <td>{{ $acceso->hora_salida?->format('H:i') ?? '—' }}</td>
                        <td>
                            {{ $acceso->duracion ? $acceso->duracion . ' min' : '—' }}
                        </td>
                        <td>
                                <span class="badge bg-{{ $acceso->estado === 'en_curso' ? 'success' : 'secondary' }}">
                                    {{ $acceso->estado === 'en_curso' ? 'En curso' : 'Completado' }}
                                </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                            Sin accesos registrados hoy
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        var flujo     = {!! json_encode($flujoPorHora, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};
        var locacion  = {!! json_encode($porLocacion,  JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};
        var actividad = {!! json_encode($porActividad, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};
        var kpis      = {!! json_encode($kpis,         JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};

        const COLORES = [
            '#007bff','#28a745','#17a2b8','#ffc107',
            '#dc3545','#6f42c1','#fd7e14','#20c997',
        ]

        new Chart(document.getElementById('chartFlujo'), {
            type: 'bar',
            data: {
                labels: flujo.labels,
                datasets: [{
                    label: 'Ingresos',
                    data: flujo.data,
                    backgroundColor: 'rgba(0,123,255,0.7)',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45 } }
                }
            }
        })

        new Chart(document.getElementById('chartLocacion'), {
            type: 'doughnut',
            data: {
                labels: locacion.labels,
                datasets: [{
                    data: locacion.data,
                    backgroundColor: COLORES,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                }
            }
        })

        new Chart(document.getElementById('chartActividad'), {
            type: 'bar',
            data: {
                labels: actividad.labels,
                datasets: [{
                    label: 'Accesos',
                    data: actividad.data,
                    backgroundColor: 'rgba(40,167,69,0.75)',
                    borderColor: '#28a745',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        })

        new Chart(document.getElementById('chartEstado'), {
            type: 'doughnut',
            data: {
                labels: ['En curso', 'Completados'],
                datasets: [{
                    data: [kpis.en_curso, kpis.completados],
                    backgroundColor: ['#28a745', '#6c757d'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12 } }
                }
            }
        })

    })
</script>
@endpush
