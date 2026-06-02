@extends('layouts.admin')

@section('titulo', 'Accesos')
@section('header', 'Gestión de Accesos')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
</li>
<li class="breadcrumb-item active">Accesos</li>
@endsection

@section('content')

{{-- ── KPIs ── --}}
<div class="row row-cols-1 row-cols-md-4 g-3">
    <x-dashboard.stat-box color="text-bg-primary" :value="$stats['hoy'] ?? '--'" label="Accesos hoy" icon="fas fa-calendar-day"/>
    <x-dashboard.stat-box color="text-bg-success" :value="$stats['en_curso'] ?? '--'" label="Dentro actualmente" icon="fas fa-calendar-day"/>
    <x-dashboard.stat-box color="text-bg-warning" :value="$stats['salidas'] ?? '--'" label="Salidas hoy" icon="fas fa-sign-out-alt"/>
    <x-dashboard.stat-box color="text-bg-danger" :value="$stats['casilleros_ocupados'] ?? '--'" label="Casilleros ocupados" icon="fas fa-lock"/>
</div>

{{-- ── Filtros ── --}}
<x-dashboard.filtro-card action="{{ route('admin.accesos.index') }}" col-boton="2">
    <x-slot:campos>
        <div class="col-md-3">
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="en_curso"   @selected(request('estado') === 'en_curso')>En curso</option>
                    <option value="completado" @selected(request('estado') === 'completado')>Completado</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Período</label>
                <select name="fecha" class="form-control">
                    <option value="hoy"    @selected(request('fecha', 'hoy') === 'hoy')>Hoy</option>
                    <option value="semana" @selected(request('fecha') === 'semana')>Esta semana</option>
                    <option value="mes"    @selected(request('fecha') === 'mes')>Este mes</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>Buscar persona</label>
                <input type="text" name="buscar" class="form-control"
                       placeholder="Documento o nombre"
                       value="{{ request('buscar') }}">
            </div>
        </div>
    </x-slot:campos>
</x-dashboard.filtro-card>

{{-- ── Tabla ── --}}
<div class="card card-outline card-secondary">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-list mr-2"></i>Accesos registrados
        </h3>
        <div class="card-tools">
            <span class="badge bg-secondary">{{ $accesos->total() }} registros</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Persona</th>
                    <th>Documento</th>
                    <th>Actividad</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Duración</th>
                    <th>Estado</th>
                    <th width="60"></th>
                </tr>
                </thead>
                <tbody>
                @forelse($accesos as $acceso)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-circle fa-lg text-secondary mr-2"></i>
                            <span>{{ $acceso->persona->nombre_completo }}</span>
                        </div>
                    </td>
                    <td><small class="text-muted">{{ $acceso->persona->doc_identidad }}</small></td>
                    <td>{{ $acceso->actividad->nombre }}</td>
                    <td>
                            <span title="{{ $acceso->hora_ingreso }}">
                                {{ $acceso->hora_ingreso?->format('d/m H:i') ?? '—' }}
                            </span>
                    </td>
                    <td>{{ $acceso->hora_salida?->format('d/m H:i') ?? '—' }}</td>
                    <td>
                        @if($acceso->hora_salida)
                        <small class="text-muted">
                            {{ $acceso->hora_ingreso->diffForHumans($acceso->hora_salida, true) }}
                        </small>
                        @elseif($acceso->estado === 'en_curso')
                        <small class="text-success">
                            <i class="fas fa-circle mr-1" style="font-size:7px;"></i>
                            {{ $acceso->hora_ingreso->diffForHumans(null, true) }}
                        </small>
                        @else
                        <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($acceso->estado === 'en_curso')
                        <span class="badge bg-success">
                                    <i class="fas fa-circle mr-1" style="font-size:7px;"></i>En curso
                                </span>
                        @else
                        <span class="badge bg-secondary">
                                    <i class="fas fa-check mr-1"></i>Completado
                                </span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#detalleModal"
                                data-id="{{ $acceso->id }}"
                                title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No hay accesos registrados con estos filtros
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($accesos->hasPages())
    <div class="card-footer">{{ $accesos->links() }}</div>
    @endif
</div>

{{-- ── Modal detalle ── --}}
<x-admin.acceso-detalle-modal />

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        const modalEl   = document.getElementById('detalleModal')
        const modalBody = document.getElementById('detalleModalBody')
        const spinner   = `
        <div class="text-center text-muted py-4">
            <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
            Cargando...
        </div>`

        modalEl.addEventListener('show.bs.modal', function (e) {
            const id  = e.relatedTarget.dataset.id
            const url = `/admin/accesos/${id}`

            modalBody.innerHTML = spinner

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(r => r.text())
                .then(html => { modalBody.innerHTML = html })
                .catch(() => {
                    modalBody.innerHTML = `
                    <div class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-circle fa-2x mb-2 d-block"></i>
                        Error al cargar el detalle
                    </div>`
                })
        })

        modalEl.addEventListener('hidden.bs.modal', function () {
            modalBody.innerHTML = spinner
        })

    })
</script>
@endpush
