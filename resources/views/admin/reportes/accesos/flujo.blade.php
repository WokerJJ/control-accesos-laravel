@extends('layouts.admin')

@section('titulo', 'Flujo por horas')
@section('header', 'Reportes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item">Reportes</li>
<li class="breadcrumb-item active">Flujo por horas</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- ── Filtros ──────────────────────────────────── --}}
    <x-dashboard.filtro-card action="{{ route('admin.reportes.accesos.flujo') }}" col-boton="2">
        <x-slot:campos>

            <div class="col-md-4">
                <div class="form-group">
                    <label>Fecha</label>
                    <input type="date"
                           name="fecha"
                           class="form-control"
                           value="{{ $fecha }}"
                           max="{{ now('America/Bogota')->toDateString() }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Locación</label>
                    <select name="locacion_id" class="form-control">
                        <option value="">Todas las locaciones</option>
                        @foreach($locaciones as $loc)
                        <option value="{{ $loc->id }}"
                                @selected($locacionId == $loc->id)>
                            {{ $loc->nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

        </x-slot:campos>
    </x-dashboard.filtro-card>

    {{-- ── KPIs rápidos ─────────────────────────────── --}}
    <div class="row row-cols-2 row-cols-md-4 g-3 mb-4">
        <x-dashboard.stat-box
            label="Total ingresos"
            :value="$flujo['total']"
            icon="fas fa-door-open"
            color="text-bg-primary"/>
        <x-dashboard.stat-box
            label="Hora pico"
            :value="$flujo['hora_pico']"
            icon="fas fa-fire"
            color="text-bg-warning"/>
        <x-dashboard.stat-box
            label="Fecha consultada"
            :value="\Carbon\Carbon::parse($fecha)->isoFormat('D MMM')"
            icon="fas fa-calendar-day"
            color="text-bg-info"/>
        <x-dashboard.stat-box
            label="Franja activa"
            :value="collect($flujo['franjas'])->sortByDesc('total')->first()['nombre'] ?? '—'"
            icon="fas fa-clock"
            color="text-bg-success"/>
    </div>

    {{-- ── Gráfica principal ────────────────────────── --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <x-reporte.chart-card
                title="Ingresos vs Salidas por hora"
                icon="fas fa-stream"
                :height="320">
                <canvas id="chartFlujo"></canvas>
            </x-reporte.chart-card>
        </div>
    </div>

    {{-- ── Franjas + tabla ──────────────────────────── --}}
    <div class="row g-3">

        {{-- Franjas horarias --}}
        <div class="col-md-4">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-th-large mr-2"></i>Franjas horarias
                    </h3>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                        <tr>
                            <th>Franja</th>
                            <th class="text-end">Ingresos</th>
                            <th class="text-end">%</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($flujo['franjas'] as $franja)
                        @php
                        $pct = $flujo['total'] > 0
                        ? round($franja['total'] * 100 / $flujo['total'])
                        : 0;
                        @endphp
                        <tr>
                            <td>{{ $franja['nombre'] }}</td>
                            <td class="text-end fw-bold">{{ $franja['total'] }}</td>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    <div class="progress flex-grow-1" style="height:6px; min-width:50px;">
                                        <div class="progress-bar bg-primary"
                                             style="width:{{ $pct }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $pct }}%</small>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Detalle por hora --}}
        <div class="col-md-8">
            <div class="card card-outline card-primary h-100">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-table mr-2"></i>Detalle por hora
                    </h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:380px; overflow-y:auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light sticky-top">
                            <tr>
                                <th>Hora</th>
                                <th class="text-center">Ingresos</th>
                                <th class="text-center">Salidas</th>
                                <th>Volumen</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($flujo['labels'] as $i => $hora)
                            @php
                            $ing = $flujo['ingresos'][$i];
                            $sal = $flujo['salidas'][$i];
                            $max = max(array_merge($flujo['ingresos'], [1]));
                            $pct = round($ing * 100 / $max);
                            @endphp
                            <tr class="{{ $ing > 0 ? '' : 'text-muted' }}">
                                <td>{{ $hora }}</td>
                                <td class="text-end">
                                    @if($ing > 0)
                                    <span class="badge bg-primary">{{ $ing }}</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($sal > 0)
                                    <span class="badge bg-secondary">{{ $sal }}</span>
                                    @else
                                    <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td style="min-width:100px;">
                                    @if($ing > 0)
                                    <div class="progress" style="height:6px;">
                                        <div class="progress-bar bg-primary"
                                             style="width:{{ $pct }}%"></div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        var flujo = {!! json_encode($flujo, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!};

        new Chart(document.getElementById('chartFlujo'), {
            type: 'bar',
            data: {
                labels: flujo.labels,
                datasets: [
                    {
                        label: 'Ingresos',
                        data: flujo.ingresos,
                        backgroundColor: 'rgba(0,123,255,0.75)',
                        borderColor: '#007bff',
                        borderWidth: 1,
                        borderRadius: 4,
                        order: 1,
                    },
                    {
                        label: 'Salidas',
                        data: flujo.salidas,
                        backgroundColor: 'rgba(108,117,125,0.6)',
                        borderColor: '#6c757d',
                        borderWidth: 1,
                        borderRadius: 4,
                        order: 2,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            footer: function (items) {
                                var ing = items.find(function(i) { return i.dataset.label === 'Ingresos'; });
                                var sal = items.find(function(i) { return i.dataset.label === 'Salidas'; });
                                if (ing && sal) {
                                    var diff = ing.parsed.y - sal.parsed.y;
                                    return 'En local: ' + (diff > 0 ? '+' : '') + diff;
                                }
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { ticks: { maxRotation: 45 } }
                }
            }
        });

    });
</script>
@endpush
