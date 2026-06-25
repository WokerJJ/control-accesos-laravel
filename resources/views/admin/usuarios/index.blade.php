@extends('layouts.admin')

@section('titulo', 'Gestión de Usuarios')
@section('header', 'Usuarios')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Stats --}}
    <div class="row row-cols-1 row-cols-md-2 g-3 mb-3">
        <x-dashboard.stat-box color="text-bg-primary" :value="$stats['total'] ?? '--'" label="Total registrados" icon="fas fa-users"/>
        <x-dashboard.stat-box color="text-bg-success" :value="$stats['nuevos_mes'] ?? '--'" label="Nuevos este mes" icon="fas fa-user-plus"/>
    </div>

    {{-- Filtros --}}
    <x-dashboard.filtro-card action="{{ route('admin.usuarios.index') }}">
        <x-slot:campos>
            <div class="form-group">
                <label>Buscar</label>
                <input type="text" name="buscar" class="form-control"
                       placeholder="Nombre o documento"
                       value="{{ request('buscar') }}"
                       maxlength="100">
            </div>
            <div class="form-group">
                <label>Rol</label>
                <select name="rol" class="form-control">
                    <option value="">Todos</option>
                    @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" @selected(request('rol') == $rol->id)>
                    {{ $rol->nombre_rol }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado" class="form-control">
                    <option value="">Todos</option>
                    <option value="activo"   @selected(request('estado') === 'activo')>Activo</option>
                    <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                </select>
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
            <div class="form-group">
                <label>Registro</label>
                <select name="registro" class="form-control">
                    <option value="">Todos</option>
                    <option value="hoy"  @selected(request('registro') === 'hoy')>Hoy</option>
                    <option value="mes"  @selected(request('registro') === 'mes')>Este mes</option>
                    <option value="anio" @selected(request('registro') === 'anio')>Este año</option>
                </select>
            </div>
        </x-slot:campos>
    </x-dashboard.filtro-card>

    {{-- Tabla --}}
    <x-admin.data-table
        title="Usuarios registrados"
        :count="$usuarios->total()"
        count-label="resultados"
        variant="secondary"
        striped
        align
        shadow
        responsive
    >
    <thead class="table-dark">
        <tr>
            <th>Persona</th>
            <th class="d-none d-md-table-cell">Documento</th>
            <th class="d-none d-md-table-cell">Área</th>
            <th class="d-none d-md-table-cell">Rol</th>
            <th class="d-none d-lg-table-cell">Contacto</th>
            <th class="d-none d-lg-table-cell">Último acceso</th>
            <th class="text-center">Estado</th>
            <th class="text-center" style="width:120px;">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($usuarios as $usuario)
        <tr>
            <td data-label="Nombre">
                <span class="fw-medium">{{ $usuario->nombre_completo }}</span>
            </td>
            <td class="d-none d-md-table-cell" data-label="Documento"><span class="font-monospace text-muted">{{ $usuario->doc_identidad }}</span></td>
            <td class="d-none d-md-table-cell" data-label="Área">
                <span class="badge bg-{{ $usuario->area_color }}">
                    {{ $usuario->area }}
                </span>
                @if($usuario->programa)
                <small class="d-block text-muted mt-1">{{ $usuario->programa }}</small>
                @endif
            </td>
            <td class="d-none d-md-table-cell" data-label="Rol"><span class="badge bg-light text-dark border">{{ $usuario->rol }}</span></td>
            <td class="d-none d-lg-table-cell" data-label="Contacto">
                <div class="text-end flex-fill min-w-0">
                    <small class="d-block text-truncate"><i class="fas fa-phone text-muted me-1" style="font-size:10px;"></i>{{ $usuario->celular ?? '—' }}</small>
                    <small class="text-muted d-block text-truncate"><i class="fas fa-envelope text-muted me-1" style="font-size:10px;"></i>{{ $usuario->email ?? '—' }}</small>
                </div>
            </td>
            <td class="d-none d-lg-table-cell" data-label="Último acceso">
                <span class="text-muted small">{{ $usuario->ultimo_acceso ?? 'Nunca' }}</span>
                @if($usuario->total_accesos > 0)
                <small class="text-success d-block fw-medium">{{ $usuario->total_accesos }} ingresos</small>
                @endif
            </td>
            <td class="text-center" data-label="Estado">
                <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }} rounded-pill">
                    <i class="fas fa-circle me-1" style="font-size:6px;"></i>{{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#usuarioDetalleModal"
                            data-id="{{ $usuario->usuario_id }}"
                            title="Ver detalle">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-warning btn-editar-tabla"
                            data-id="{{ $usuario->usuario_id }}"
                            data-bs-target="#editarModal"
                            title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-muted py-5">
                <i class="fas fa-users-slash fa-3x mb-3 d-block text-secondary opacity-50"></i>
                <p class="mb-0">No hay usuarios registrados</p>
            </td>
        </tr>
        @endforelse
    </tbody>
    <x-slot:footer>
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
            <small class="text-muted">Mostrando {{ $usuarios->firstItem() ?? 0 }} – {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }}</small>
            {{ $usuarios->withQueryString()->links('vendor.pagination.custom') }}
        </div>
    </x-slot:footer>
</x-admin.data-table>

</div>

<x-admin.usuario-detalle-modal :roles="$roles" :departamentos="$departamentos"/>

@endsection
