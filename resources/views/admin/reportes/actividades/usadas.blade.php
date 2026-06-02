@extends('layouts.admin')

@section('titulo', 'Actividades más usadas')
@section('header', 'Reportes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item">Reportes</li>
<li class="breadcrumb-item active">Actividades más usadas</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Filtros ──────────────────────────────────── --}}
    <x-dashboard.filtro-card action="{{ route('admin.reportes.actividades.usadas') }}" col-boton="2">
        <x-slot:campos>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control"
                           value="{{ $desde }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control"
                           value="{{ $hasta }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Locación</label>
                    <select name="locacion_id" class="form-control">
                        <option value="">Todas las locaciones</option>
                        @foreach($locaciones as $loc)
                        <option value="{{ $loc->id }}" @selected($locacionId == $loc->id)>
                            {{ $loc->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </x-slot:campos>
    </x-dashboard.filtro-card>

    {{-- ── Gráfica + tabla ─────────────────────────── --}}
    <div class="row g-3">

        {{-- Gráfica horizontal --}}
        <div class="col-md-5">
            <x-reporte.chart-card
                title="Top actividades"
                icon="fas fa-fire"
                :height="380">
                <canvas id="chartActividades"></canvas>
            </x-reporte.chart-card>
        </div>

        {{-- Tabla ranking --}}
        <div class="col-md-7">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-trophy mr-2"></i>Ranking del período
                    </h3>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.reportes.export.actividades.csv', request()->query()) }}"
                           class="btn btn-sm btn-success">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Actividad</th>
                            <th>Locación</th>
                            <th class="text-end">Usos</th>
                            <th>Participación</th>
                            <th>Últ. uso</th>
                            <th>Prom.</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($actividades as $i => $actividad)
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
                            <td>
                                <span>{{ $actividad->nombre }}</span>
                                <br>
                                <span class="badge bg-{{ $actividad->tipo === 'fija' ? 'info' : 'primary' }} mt-1">
                                        {{ ucfirst($actividad->tipo) }}
                                    </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $actividad->locacion }}</small>
                            </td>
                            <td class="text-end fw-bold">{{ $actividad->total_usos }}</td>
                            <td style="min-width:120px;">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="progress flex-grow-1" style="height:6px;">
                                        <div class="progress-bar bg-primary"
                                             style="width:{{ $actividad->porcentaje }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $actividad->porcentaje }}%</small>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $actividad->ultimo_uso }}</small>
                            </td>
                            <td>
                                <small class="text-muted">{{ $actividad->duracion_promedio }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                                Sin datos en el período
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        var grafica = {!! json_encode($grafica, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};

        var COLORES = [
            '#007bff','#28a745','#17a2b8','#ffc107',
            '#dc3545','#6f42c1','#fd7e14','#20c997',
        ];

        new Chart(document.getElementById('chartActividades'), {
            type: 'bar',
            data: {
                labels: grafica.labels,
                datasets: [{
                    label: 'Usos',
                    data: grafica.data,
                    backgroundColor: COLORES,
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1 } },
                    y: { ticks: { font: { size: 11 } } }
                }
            }
        });

    });
</script>
@endpush
