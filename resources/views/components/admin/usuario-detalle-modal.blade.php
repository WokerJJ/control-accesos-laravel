@props(['roles', 'municipios'])

{{-- Modal detalle --}}
<div class="modal fade" id="detalleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2 text-primary"></i>Detalle usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalleModalBody">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i>
                    Cargando...
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal editar --}}
<div class="modal fade" id="editarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2 text-warning"></i>Editar usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" id="edit_email" class="form-control" placeholder="correo@ejemplo.com">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Celular</label>
                        <input type="text" id="edit_celular" class="form-control" placeholder="3001234567">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Dirección</label>
                        <input type="text" id="edit_direccion" class="form-control" placeholder="Calle 123 # 45-67">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Municipio</label>
                        <select id="edit_municipio_id" class="form-control">
                            <option value="">— Sin municipio —</option>
                            @foreach($municipios as $municipio)
                            <option value="{{ $municipio->id }}">
                                {{ $municipio->nombre }} — {{ $municipio->departamento->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Rol</label>
                        <select id="edit_rol_id" class="form-control">
                            @foreach($roles as $rol)
                            <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Estado</label>
                        <select id="edit_estado" class="form-control">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">
                    <i class="fas fa-save me-1"></i>Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>
