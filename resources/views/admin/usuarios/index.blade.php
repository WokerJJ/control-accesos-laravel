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
                                        data-bs-target="#detalleModal"
                                        data-id="{{ $usuario->id }}"
                                        title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editarModal"
                                        data-id="{{ $usuario->id }}"
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

@push('scripts')
<script>
    function initUsuariosModals() {
        const detalleModalEl = document.getElementById('detalleModal')
        if (!detalleModalEl) return

        // Evitar duplicar listeners en re-inicializaciones Turbo
        if (detalleModalEl._usuariosInit) return
        detalleModalEl._usuariosInit = true

        const editarModalEl  = document.getElementById('editarModal')
        const detalleBody    = document.getElementById('detalleModalBody')
        const spinner        = `
        <div class="text-center text-muted py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mb-0">Cargando información...</p>
        </div>`

        let editarId = null

        // ═══════════════════════════════════════════════════
        // MODAL DETALLE (carga vía AJAX)
        // ═══════════════════════════════════════════════════
        detalleModalEl?.addEventListener('show.bs.modal', function (e) {
            const id = e.relatedTarget?.dataset?.id
            if (!id) return

            detalleBody.innerHTML = spinner

            fetch(`/admin/usuarios/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(r => {
                    if (!r.ok) throw new Error('Error ' + r.status)
                    return r.text()
                })
                .then(html => {
                    detalleBody.innerHTML = html

                    // Inicializar tooltips del contenido cargado
                    const tooltips = detalleBody.querySelectorAll('[data-bs-toggle="tooltip"]')
                    tooltips.forEach(el => new bootstrap.Tooltip(el))
                })
                .catch(() => {
                    detalleBody.innerHTML = `
                <div class="text-center text-danger py-5">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <p class="mb-0">Error al cargar el detalle</p>
                    <button class="btn btn-sm btn-outline-danger mt-2" onclick="location.reload()">
                        <i class="fas fa-redo me-1"></i>Reintentar
                    </button>
                </div>`
                })
        })

        detalleModalEl?.addEventListener('hidden.bs.modal', function () {
            detalleBody.innerHTML = spinner
        })

        // ═══════════════════════════════════════════════════
        // DELEGACIÓN: botón editar dentro del detalle
        // ═══════════════════════════════════════════════════
        detalleBody?.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-editar')
            if (!btn) return

            const datos = {
                id:           btn.dataset.id,
                email:        btn.dataset.email        ?? '',
                celular:      btn.dataset.celular      ?? '',
                direccion:    btn.dataset.direccion    ?? '',
                municipio_id: btn.dataset.municipioId  ?? '',
                rol_id:       btn.dataset.rolId        ?? '',
                estado:       btn.dataset.estado       ?? 'activo',
            }

            abrirEditar(datos)
        })

        // ═══════════════════════════════════════════════════
        // MODAL EDITAR (desde tabla o desde detalle)
        // ═══════════════════════════════════════════════════
        editarModalEl?.addEventListener('show.bs.modal', function (e) {
            const btn = e.relatedTarget
            if (!btn?.dataset?.id) return

            // Si viene de la tabla (no del detalle), cargar datos vía AJAX
            if (!btn.classList.contains('btn-editar')) {
                const id = btn.dataset.id
                fetch(`/admin/usuarios/${id}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'  // ← Esto activa el JSON en el controller
                    }
                })
                    .then(r => {
                        if (!r.ok) throw new Error('Error ' + r.status)
                        return r.json()
                    })
                    .then(data => abrirEditar({
                        id:           data.id,
                        email:        data.email,
                        celular:      data.celular,
                        direccion:    data.direccion,
                        municipio_id: data.municipio_id,
                        rol_id:       data.rol_id,
                        estado:       data.estado,
                    }))
                    .catch(() => alert('Error al cargar datos para editar'))
            }
        })

        window.abrirEditar = function (datos) {
            editarId = datos.id

            document.getElementById('edit_email').value        = datos.email
            document.getElementById('edit_celular').value      = datos.celular
            document.getElementById('edit_direccion').value    = datos.direccion
            document.getElementById('edit_municipio_id').value = datos.municipio_id
            document.getElementById('edit_rol_id').value       = datos.rol_id
            document.getElementById('edit_estado').value       = datos.estado

            // Cerrar detalle si está abierto
            const detalleModal = bootstrap.Modal.getInstance(detalleModalEl)
            if (detalleModal) detalleModal.hide()

            bootstrap.Modal.getOrCreateInstance(editarModalEl).show()
        }

        // ═══════════════════════════════════════════════════
        // GUARDAR CAMBIOS
        // ═══════════════════════════════════════════════════
        document.getElementById('btnGuardarUsuario')?.addEventListener('click', function () {
            if (!editarId) return

            const btn = this
            const textoOriginal = btn.innerHTML
            btn.disabled = true
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Guardando...'

            fetch(`/admin/usuarios/${editarId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type':     'application/json',
                    'X-CSRF-TOKEN':     document.querySelector('meta[name=csrf-token]')?.content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept':           'application/json',
                },
                body: JSON.stringify({
                    email:        document.getElementById('edit_email').value,
                    celular:      document.getElementById('edit_celular').value,
                    direccion:    document.getElementById('edit_direccion').value,
                    municipio_id: document.getElementById('edit_municipio_id').value || null,
                    rol_id:       document.getElementById('edit_rol_id').value,
                    estado:       document.getElementById('edit_estado').value,
                })
            })
                .then(r => {
                    if (!r.ok) throw new Error('Error del servidor')
                    return r.json()
                })
                .then(data => {
                    if (data.ok || data.success) {
                        bootstrap.Modal.getInstance(editarModalEl)?.hide()
                        // Toast de éxito opcional
                        mostrarToast('Cambios guardados correctamente', 'success')
                        setTimeout(() => window.location.reload(), 800)
                    } else {
                        throw new Error(data.message || 'Error al guardar')
                    }
                })
                .catch(err => {
                    alert(err.message || 'Error al guardar. Intenta de nuevo.')
                })
                .finally(() => {
                    btn.disabled = false
                    btn.innerHTML = textoOriginal
                })
        })

        // Helper toast
        function mostrarToast(mensaje, tipo = 'success') {
            const toast = document.createElement('div')
            toast.className = `alert alert-${tipo} alert-dismissible fade show position-fixed`
            toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:300px;'
            toast.innerHTML = `
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `
            document.body.appendChild(toast)
            setTimeout(() => toast.remove(), 3000)
        }
    }

    document.addEventListener('DOMContentLoaded', initUsuariosModals)
    document.addEventListener('turbo:load', initUsuariosModals)
    document.addEventListener('turbo:frame-load', initUsuariosModals)
</script>
@endpush
