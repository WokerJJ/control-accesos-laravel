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
    <x-dashboard.filtro-card action="{{ route('admin.usuarios.index') }}" col-boton="2">
        <x-slot:campos>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Buscar</label>
                    <input type="text" name="buscar" class="form-control"
                           placeholder="Nombre o documento"
                           value="{{ request('buscar') }}"
                           maxlength="100">
                </div>
            </div>
            <div class="col-md-2">
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
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos</option>
                        <option value="activo"   @selected(request('estado') === 'activo')>Activo</option>
                        <option value="inactivo" @selected(request('estado') === 'inactivo')>Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Registro</label>
                    <select name="registro" class="form-control">
                        <option value="">Todos</option>
                        <option value="hoy"  @selected(request('registro') === 'hoy')>Hoy</option>
                        <option value="mes"  @selected(request('registro') === 'mes')>Este mes</option>
                        <option value="anio" @selected(request('registro') === 'anio')>Este año</option>
                    </select>
                </div>
            </div>
        </x-slot:campos>
    </x-dashboard.filtro-card>

    {{-- Tabla --}}
    <x-admin.data-table
        icon="fas fa-users"
        title="Usuarios registrados"
        :count="$usuarios->total()"
        count-label="resultados"
        variant="secondary"
        striped
        align
        shadow
    >
    <thead class="table-dark">
                    <tr>
                        <th>Persona</th>
                        <th>Documento</th>
                        <th>Rol</th>
                        <th>Contacto</th>
                        <th>Último acceso</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center" style="width:120px;">Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($usuarios as $usuario)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                     style="width:36px;height:36px;font-size:14px;font-weight:600;">
                                    {{ strtoupper(substr($usuario->nombre_completo, 0, 1)) }}
                                </div>
                                <span class="fw-medium">{{ $usuario->nombre_completo }}</span>
                            </div>
                        </td>
                        <td><span class="font-monospace text-muted">{{ $usuario->doc_identidad }}</span></td>
                        <td><span class="badge bg-light text-dark border">{{ $usuario->rol }}</span></td>
                        <td>
                            <small class="d-block"><i class="fas fa-phone text-muted me-1" style="font-size:10px;"></i>{{ $usuario->celular ?? '—' }}</small>
                            <small class="text-muted"><i class="fas fa-envelope text-muted me-1" style="font-size:10px;"></i>{{ $usuario->email ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $usuario->ultimo_acceso ?? 'Nunca' }}</span>
                            @if($usuario->total_accesos > 0)
                            <small class="text-success d-block fw-medium">{{ $usuario->total_accesos }} ingresos</small>
                            @endif
                        </td>
                        <td class="text-center">
                                <span class="badge bg-{{ $usuario->activo ? 'success' : 'secondary' }} rounded-pill">
                                    <i class="fas fa-circle me-1" style="font-size:6px;"></i>{{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#usuarioDetalleModal"
                                        data-id="{{ $usuario->usuario_id }}"
                                        title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal"
                                        data-id="{{ $usuario->usuario_id }}"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fas fa-users-slash fa-3x mb-3 d-block text-secondary opacity-50"></i>
                            <p class="mb-0">No hay usuarios registrados</p>
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
    <x-slot:footer>
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Mostrando {{ $usuarios->firstItem() ?? 0 }} - {{ $usuarios->lastItem() ?? 0 }} de {{ $usuarios->total() }}</small>
            {{ $usuarios->withQueryString()->links() }}
        </div>
    </x-slot:footer>

</x-admin.data-table>

</div>

<x-admin.usuario-detalle-modal :roles="$roles" :municipios="$municipios"/>

@endsection
