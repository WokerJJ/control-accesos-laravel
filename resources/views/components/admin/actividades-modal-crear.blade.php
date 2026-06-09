@props(['locaciones', 'tiposActividad'])

<div class="modal fade" id="modalActividad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2 text-primary" id="modalIcon"></i>
                    <span id="modalTitulo">Nueva actividad programada</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Form principal SIN form anidado --}}
            <form id="formActividad"
                  action="{{ route('admin.actividades.programar') }}"
                  method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body">
                    {{-- ... todos los campos igual ... --}}

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Tipo de actividad <span class="text-danger">*</span>
                            </label>
                            <select name="tipo_actividad_id"
                                    class="form-select @error('tipo_actividad_id') is-invalid @enderror"
                                    required>
                                <option value="">— Selecciona una categoría —</option>
                                @foreach($tiposActividad as $tipo)
                                <option value="{{ $tipo->id }}"
                                        {{ old('tipo_actividad_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('tipo_actividad_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Locación</label>
                            <select name="locacion_id"
                                    class="form-select @error('locacion_id') is-invalid @enderror">
                                <option value="">— Sin locación específica —</option>
                                @foreach($locaciones as $locacion)
                                <option value="{{ $locacion->id }}"
                                        {{ old('locacion_id') == $locacion->id ? 'selected' : '' }}>
                                {{ $locacion->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('locacion_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nombre" maxlength="150"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   placeholder="Ej: Conferencia de Inteligencia Artificial"
                                   value="{{ old('nombre') }}"
                                   required>
                            @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">
                                Descripción <small class="text-muted">(opcional)</small>
                            </label>
                            <textarea name="descripcion" maxlength="500"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Detalles adicionales...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3 d-none" id="wrapEstado">
                            <label class="form-label">Estado</label>
                            <select name="estado" class="form-select">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_curso">En curso</option>
                                <option value="cancelada">Cancelada</option>
                                <option value="finalizada">Finalizada</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Fecha inicio <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="fecha_inicio"
                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                   value="{{ old('fecha_inicio', now()->toDateString()) }}"
                                   min="{{ now()->toDateString() }}"
                                   required>
                            @error('fecha_inicio')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Fecha fin <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="fecha_fin"
                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                   value="{{ old('fecha_fin', now()->toDateString()) }}"
                                   min="{{ now()->toDateString() }}"
                                   required>
                            @error('fecha_fin')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Hora inicio <span class="text-danger">*</span>
                            </label>
                            <input type="time"
                                   name="hora_inicio"
                                   class="form-control @error('hora_inicio') is-invalid @enderror"
                                   value="{{ old('hora_inicio', '08:00') }}"
                                   required>
                            @error('hora_inicio')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Hora fin <span class="text-danger">*</span>
                            </label>
                            <input type="time"
                                   name="hora_fin"
                                   class="form-control @error('hora_fin') is-invalid @enderror"
                                   value="{{ old('hora_fin', '10:00') }}"
                                   required>
                            @error('hora_fin')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div id="wrapEliminar" class="d-none">
                        <button type="button" class="btn btn-outline-danger" id="btnEliminar">
                            <i class="fas fa-trash me-1"></i>Cancelar actividad
                        </button>
                    </div>

                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fas fa-calendar-plus me-1" id="btnIcon"></i>
                            <span id="btnTexto">Crear actividad</span>
                        </button>
                    </div>
                </div>
            </form>
            {{-- Cierre del formActividad — FUERA de aquí NO puede haber otro form --}}

        </div>
    </div>
</div>

<form id="formEliminar" method="POST" action="" class="d-none">
    @csrf
    @method('DELETE')
</form>
