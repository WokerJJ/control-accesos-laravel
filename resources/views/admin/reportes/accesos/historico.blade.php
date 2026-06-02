@extends('layouts.admin')

@section('titulo', 'Histórico de accesos')
@section('header', 'Reportes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item">Reportes</li>
<li class="breadcrumb-item active">Histórico</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Filtros ──────────────────────────────────── --}}
    <x-dashboard.filtro-card action="{{ route('admin.reportes.accesos.historico') }}" col-boton="2">
        <x-slot:campos>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Desde</label>
                    <input type="date" name="desde" class="form-control"
                           value="{{ $desde }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Hasta</label>
                    <input type="date" name="hasta" class="form-control"
                           value="{{ $hasta }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Locación</label>
                    <select name="locacion_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($locaciones as $loc)
                        <option value="{{ $loc->id }}" @selected($locacionId == $loc->id)>
                            {{ $loc->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="en_curso"   @selected($estado === 'en_curso')>En curso</option>
                        <option value="completado" @selected($estado === 'completado')>Completado</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label>Buscar persona</label>
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Nombre o documento"
                           value="{{ $buscar }}"
                           maxlength="100">
                </div>
            </div>

        </x-slot:campos>
    </x-dashboard.filtro-card>

    {{-- ── KPIs del período ────────────────────────── --}}
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <x-dashboard.stat-box
            label="Total accesos"
            :value="$kpis['total']"
            icon="fas fa-door-open"
            color="text-bg-primary"/>
        <x-dashboard.stat-box
            label="Completados"
            :value="$kpis['completados']"
            icon="fas fa-check-circle"
            color="text-bg-success"/>
        <x-dashboard.stat-box
            label="En curso"
            :value="$kpis['en_curso']"
            icon="fas fa-play-circle"
            color="text-bg-warning"/>
        <x-dashboard.stat-box
            label="Duración promedio"
            :value="$kpis['duracion_promedio']"
            icon="fas fa-clock"
            color="text-bg-info"/>
    </div>

    @php
    $chartTendencia = [
        'type' => 'line',
        'data' => [
            'labels' => $grafica['labels'],
            'datasets' => [[
                'label' => 'Ingresos',
                'data' => $grafica['data'],
                'borderColor' => '#007bff',
                'backgroundColor' => 'rgba(0,123,255,0.1)',
                'borderWidth' => 2,
                'pointBackgroundColor' => '#007bff',
                'pointRadius' => 4,
                'fill' => true,
                'tension' => 0.3,
            ]],
        ],
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => ['legend' => ['display' => false]],
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['stepSize' => 1]],
                'x' => ['ticks' => ['maxRotation' => 45, 'autoSkip' => true, 'maxTicksLimit' => 20]],
            ],
        ],
    ];
    @endphp

    {{-- ── Gráfica tendencia ───────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <x-reporte.chart-card
                title="Ingresos por día"
                icon="fas fa-chart-line"
                :height="260">
                <canvas id="chartTendencia" data-chart-config='{!! json_encode($chartTendencia, JSON_HEX_TAG | JSON_HEX_APOS) !!}'></canvas>
            </x-reporte.chart-card>
        </div>
    </div>

    {{-- ── Tabla histórico ─────────────────────────── --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>Accesos del período
                <span class="badge bg-primary ms-1">{{ $accesos->total() }}</span>
            </h3>
            <div class="d-flex justify-content-end gap-1">
                <a href="{{ route('admin.reportes.export.historico.csv', request()->query()) }}"
                   class="btn btn-sm btn-success export-btn" data-turbo="false">
                    <i class="fas fa-file-excel mr-1"></i><span class="btn-text">Excel</span>
                </a>
                <a href="{{ route('admin.reportes.export.historico.pdf', request()->query()) }}"
                   class="btn btn-sm btn-danger export-btn" data-turbo="false">
                    <i class="fas fa-file-pdf mr-1"></i><span class="btn-text">PDF</span>
                </a>
            </div>
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
                        <th>Método</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($accesos as $acceso)
                    <tr>
                        <td>
                            {{ $acceso->persona->primer_nombre }}
                            {{ $acceso->persona->primer_apellido }}
                        </td>
                        <td>
                            <small class="text-muted">{{ $acceso->persona->doc_identidad }}</small>
                        </td>
                        <td>{{ $acceso->actividad->nombre }}</td>
                        <td>{{ $acceso->locacion->nombre }}</td>
                        <td>{{ $acceso->hora_ingreso?->format('d/m/Y H:i') }}</td>
                        <td>{{ $acceso->hora_salida?->format('H:i') ?? '—' }}</td>
                        <td>{{ $acceso->duracion ? $acceso->duracion . ' min' : '—' }}</td>
                        <td>
                                <span class="badge bg-secondary">
                                    {{ ucfirst($acceso->metodo_acceso) }}
                                </span>
                        </td>
                        <td>
                                <span class="badge bg-{{ $acceso->estado === 'en_curso' ? 'success' : 'secondary' }}">
                                    {{ $acceso->estado === 'en_curso' ? 'En curso' : 'Completado' }}
                                </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x d-block mb-2"></i>
                            Sin accesos en el período seleccionado
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $accesos->links() }}
        </div>
    </div>

</div>
@endsection
