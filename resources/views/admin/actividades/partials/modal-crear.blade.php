<div class="modal fade" id="modalActividad" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2 text-primary"></i>
                    Nueva actividad programada
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('admin.actividades.programar') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="row">

                        {{-- Tipo de actividad --}}
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

                        {{-- Locación --}}
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

                        {{-- Nombre --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">
                                Nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="nombre"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   placeholder="Ej: Conferencia de Inteligencia Artificial"
                                   value="{{ old('nombre') }}"
                                   required>
                            @error('nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="col-12 mb-3">
                            <label class="form-label">
                                Descripción
                                <small class="text-muted">(opcional)</small>
                            </label>
                            <textarea name="descripcion"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="2"
                                      placeholder="Detalles adicionales...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    <hr>

                    <div class="row">

                        {{-- Fecha inicio --}}
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

                        {{-- Fecha fin --}}
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

                        {{-- Hora inicio --}}
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

                        {{-- Hora fin --}}
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

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-1"></i>
                        Crear actividad
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
