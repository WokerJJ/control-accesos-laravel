<div class="row g-3">

    {{-- Estado badge --}}
    <div class="col-12 d-flex justify-content-between align-items-center">
        <span class="badge bg-{{ $usuario->estado === 'activo' ? 'success' : 'secondary' }} fs-6">
            <i class="fas fa-circle me-1" style="font-size:8px;"></i>
            {{ ucfirst($usuario->estado) }}
        </span>
        <small class="text-muted">
            <i class="fas fa-calendar me-1"></i>
            Registrado {{ \Carbon\Carbon::parse($usuario->created_at)->isoFormat('D MMM YYYY') }}
        </small>
    </div>

    {{-- Datos personales --}}
    <div class="col-12">
        <div class="card card-outline card-primary mb-0">
            <div class="card-header py-2">
                <h6 class="card-title mb-0"><i class="fas fa-user me-2"></i>Datos personales</h6>
            </div>
            <div class="card-body py-2">
                <div class="row g-2">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Nombre completo</small>
                        <span class="fw-bold">{{ $usuario->nombre_completo }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Documento</small>
                        <span>{{ $usuario->doc_identidad }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Email</small>
                        <span>{{ $usuario->email ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Celular</small>
                        <span>{{ $usuario->celular ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Municipio</small>
                        <span>{{ $usuario->municipio ?? '—' }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Departamento</small>
                        <span>{{ $usuario->departamento ?? '—' }}</span>
                    </div>
                    @if($usuario->direccion)
                    <div class="col-12">
                        <small class="text-muted d-block">Dirección</small>
                        <span>{{ $usuario->direccion }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Rol y accesos --}}
    <div class="col-md-6">
        <div class="card card-outline card-success mb-0 h-100">
            <div class="card-header py-2">
                <h6 class="card-title mb-0"><i class="fas fa-id-badge me-2"></i>Rol</h6>
            </div>
            <div class="card-body py-2">
                <span class="badge bg-primary fs-6">{{ $usuario->rol }}</span>
            </div>
        </div>
    </div>

    {{-- Estadísticas --}}
    <div class="col-md-6">
        <div class="card card-outline card-info mb-0 h-100">
            <div class="card-header py-2">
                <h6 class="card-title mb-0"><i class="fas fa-chart-bar me-2"></i>Actividad</h6>
            </div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-muted d-block">Total ingresos</small>
                        <span class="fw-bold fs-5">{{ $usuario->total_accesos }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Último acceso</small>
                        <span>{{ $usuario->ultimo_acceso }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Botón editar al pie del detalle --}}
<div class="mt-3 text-end">
    <button class="btn btn-primary btn-sm btn-editar"
            data-id="{{ $usuario->usuario_id }}"
            data-email="{{ $usuario->email }}"
            data-celular="{{ $usuario->celular }}"
            data-direccion="{{ $usuario->direccion }}"
            data-municipio-id="{{ $usuario->municipio_id }}"
            data-rol-id="{{ $usuario->rol_id }}"
            data-estado="{{ $usuario->estado }}">
        <i class="fas fa-edit me-1"></i>Editar
    </button>
</div>
