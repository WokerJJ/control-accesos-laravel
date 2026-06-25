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
<x-dashboard.filtro-card action="{{ route('admin.accesos.index') }}">
    <x-slot:campos>
        <div class="form-group">
            <label>Estado</label>
            <select name="estado" class="form-control">
                <option value="">Todos</option>
                <option value="en_curso"   @selected(request('estado') === 'en_curso')>En curso</option>
                <option value="completado" @selected(request('estado') === 'completado')>Completado</option>
            </select>
        </div>
        <div class="form-group">
            <label>Período</label>
            <select name="fecha" class="form-control">
                <option value="hoy"    @selected(request('fecha', 'hoy') === 'hoy')>Hoy</option>
                <option value="semana" @selected(request('fecha') === 'semana')>Esta semana</option>
                <option value="mes"    @selected(request('fecha') === 'mes')>Este mes</option>
            </select>
        </div>
        <div class="form-group">
            <label>Buscar persona</label>
            <input type="text" name="buscar" class="form-control"
                   placeholder="Documento o nombre"
                   value="{{ request('buscar') }}"
                   maxlength="100">
        </div>
        <div class="form-group">
            <label>Área</label>
            <select name="area" class="form-control">
                <option value="">Todas</option>
                <option value="estudiante"     @selected(request('area') === 'estudiante')>Estudiante</option>
                <option value="profesor"       @selected(request('area') === 'profesor')>Profesor</option>
                <option value="administrativo"  @selected(request('area') === 'administrativo')>Administrativo</option>
                <option value="externo"        @selected(request('area') === 'externo')>Externo</option>
            </select>
        </div>
    </x-slot:campos>
</x-dashboard.filtro-card>{{-- ── Tabla ── --}}
<x-admin.data-table
    title="Accesos registrados"
    variant="secondary"
    :count="$accesos->total()"
    count-label="registros"
    striped
    align
    responsive
>
    @if($accesos->hasPages())
    <x-slot:footer>
        {{ $accesos->links('vendor.pagination.custom') }}
    </x-slot:footer>
    @endif

    <thead class="table-light">
    <tr>
        <th>Persona</th>
        <th class="d-none d-md-table-cell">Documento</th>
        <th class="d-none d-md-table-cell">Área</th>
        <th class="d-none d-lg-table-cell">Actividad</th>
        <th class="d-none d-md-table-cell">Ingreso</th>
        <th class="d-none d-lg-table-cell">Salida</th>
        <th class="d-none d-lg-table-cell">Duración</th>
        <th>Estado</th>
        <th width="60"></th>
    </tr>
    </thead>
    <tbody>
    @forelse($accesos as $acceso)
    <tr class="{{ $acceso->estado === 'en_curso' ? 'row-en-curso' : '' }}">
        <td data-label="Persona">
            <div class="d-flex align-items-center">
                <i class="fas fa-user-circle fa-lg text-secondary mr-2"></i>
                <span>{{ $acceso->persona->nombre_completo }}</span>
            </div>
        </td>
        <td class="d-none d-md-table-cell" data-label="Documento"><small class="text-muted">{{ $acceso->persona->doc_identidad }}</small></td>
        <td class="d-none d-md-table-cell" data-label="Área">
            @php
            $tipo = $acceso->persona->programaAcademico?->tipo;
            @endphp
            <span class="badge bg-{{ \App\Models\ProgramaAcademico::areaColor($tipo) }}">
                {{ \App\Models\ProgramaAcademico::areaLabel($tipo) }}
            </span>
        </td>
        <td class="d-none d-md-table-cell" data-label="Actividad"><small>{{ $acceso->actividad->nombre }}</small></td>
        <td class="d-none d-md-table-cell" data-label="Ingreso">
            <span title="{{ $acceso->hora_ingreso }}">
                {{ $acceso->hora_ingreso?->format('d/m H:i') ?? '—' }}
            </span>
        </td>
        <td class="d-none d-lg-table-cell" data-label="Salida">{{ $acceso->hora_salida?->format('d/m H:i') ?? '—' }}</td>
        <td class="d-none d-lg-table-cell" data-label="Duración">
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
        <td data-label="Estado">
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
            <div class="btn-group btn-group-sm">
                <button class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#accesoDetalleModal"
                        data-id="{{ $acceso->id }}"
                        title="Ver detalle">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="9">
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                No hay accesos registrados con estos filtros
            </div>
        </td>
    </tr>
    @endforelse
    </tbody>
</x-admin.data-table>

{{-- ── Modal detalle ── --}}
<x-admin.acceso-detalle-modal />

@endsection
