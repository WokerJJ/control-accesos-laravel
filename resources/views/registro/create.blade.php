@extends('layouts.public')

@section('titulo', 'Registro de usuario')

@section('content')

<div class="card card-outline card-primary shadow-lg" style="max-width: 640px; width: 100%;">

    {{-- Header --}}
    <div class="card-header text-center pt-4 pb-3 border-bottom-0">
        <div class="mb-3">
            <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                <i class="fas fa-user-plus fa-lg"></i>
            </span>
        </div>
        <h4 class="mb-1 font-weight-bold">Crear cuenta</h4>
        <p class="text-muted mb-0 small">Regístrate para acceder al sistema</p>
    </div>

    <div class="card-body px-4 pt-3 pb-4">

        <form action="{{ route('registro.store') }}" method="POST" id="formRegistro">
            @csrf

            {{-- ═══════════════════ Identificación ═══════════════════ --}}
            <div class="mb-4">
                <h6 class="text-uppercase text-muted font-weight-bold small mb-3">
                    <i class="fas fa-id-card mr-1"></i> Identificación
                </h6>
                <div class="row">
                    <div class="col-md-5">
                        <label for="tipo_identificacion_id" class="small font-weight-bold">
                            Tipo <span class="text-danger">*</span>
                        </label>
                        <select name="tipo_identificacion_id" id="tipo_identificacion_id"
                                class="form-control @error('tipo_identificacion_id') is-invalid @enderror">
                            @foreach($tipo_identificacion as $tipo)
                            <option value="{{ $tipo->id }}"
                                    {{ old('tipo_identificacion_id', 1) == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->abreviatura }}
                            </option>
                            @endforeach
                        </select>
                        @error('tipo_identificacion_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-7">
                        <label for="doc_identidad" class="small font-weight-bold">
                            Número de documento <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="doc_identidad" id="doc_identidad"
                               class="form-control @error('doc_identidad') is-invalid @enderror"
                               value="{{ old('doc_identidad', $doc_identidad ?? '') }}"
                               readonly>
                        @error('doc_identidad')
                        <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <hr class="my-3">

            {{-- ═══════════════════ Datos personales ═══════════════════ --}}
            <div class="mb-4">
                <h6 class="text-uppercase text-muted font-weight-bold small mb-3">
                    <i class="fas fa-user mr-1"></i> Datos personales
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="primer_nombre" class="small font-weight-bold">
                                Primer nombre <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="primer_nombre" id="primer_nombre" maxlength="20"
                                   class="form-control @error('primer_nombre') is-invalid @enderror"
                                   value="{{ old('primer_nombre') }}"
                                   placeholder="Ej: Juan" required>
                            @error('primer_nombre')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="segundo_nombre" class="small font-weight-bold">
                                Segundo nombre
                            </label>
                            <input type="text" name="segundo_nombre" id="segundo_nombre" maxlength="20"
                                   class="form-control"
                                   value="{{ old('segundo_nombre') }}"
                                   placeholder="Opcional">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="primer_apellido" class="small font-weight-bold">
                                Primer apellido <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="primer_apellido" id="primer_apellido" maxlength="20"
                                   class="form-control @error('primer_apellido') is-invalid @enderror"
                                   value="{{ old('primer_apellido') }}"
                                   placeholder="Ej: Pérez" required>
                            @error('primer_apellido')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="segundo_apellido" class="small font-weight-bold">
                                Segundo apellido
                            </label>
                            <input type="text" name="segundo_apellido" id="segundo_apellido" maxlength="20"
                                   class="form-control"
                                   value="{{ old('segundo_apellido') }}"
                                   placeholder="Opcional">
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            {{-- ═══════════════════ Contacto ═══════════════════ --}}
            <div class="mb-4">
                <h6 class="text-uppercase text-muted font-weight-bold small mb-3">
                    <i class="fas fa-envelope mr-1"></i> Contacto
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email" class="small font-weight-bold">
                                Correo electrónico <span class="text-danger">*</span>
                            </label>
                            <input type="email" name="email" id="email" maxlength="100"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="correo@ejemplo.com" required>
                            @error('email')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="celular" class="small font-weight-bold">
                                Celular
                            </label>
                            <input type="tel" name="celular" id="celular" maxlength="10"
                                   inputmode="numeric" pattern="[0-9]*"
                                   class="form-control @error('celular') is-invalid @enderror"
                                   value="{{ old('celular') }}"
                                   placeholder="Ej: 3001234567"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('celular')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            {{-- ═══════════════════ Área ═══════════════════ --}}
            <div class="mb-4">
                <h6 class="text-uppercase text-muted font-weight-bold small mb-3">
                    <i class="fas fa-layer-group mr-1"></i> Área
                </h6>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="selectArea" class="small font-weight-bold">
                                Selecciona tu área <span class="text-danger">*</span>
                            </label>
                            <select name="area" id="selectArea"
                                    class="form-control @error('area') is-invalid @enderror" required>
                                <option value="">— Selecciona una opción —</option>
                                <option value="estudiante" {{ old('area') == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                <option value="administrativo" {{ old('area') == 'administrativo' ? 'selected' : '' }}>Administrativo</option>
                                <option value="profesor" {{ old('area') == 'profesor' ? 'selected' : '' }}>Profesor</option>
                                <option value="externo" {{ old('area') == 'externo' ? 'selected' : '' }}>Externo</option>
                            </select>
                            @error('area')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════ Programa académico (solo estudiante) ═══════════════════ --}}
            <div id="filaPrograma" class="mb-4" style="display: none;">
                <hr class="my-3">
                <h6 class="text-uppercase text-muted font-weight-bold small mb-3">
                    <i class="fas fa-graduation-cap mr-1"></i> Programa académico
                </h6>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="selectPrograma" class="small font-weight-bold">
                                Carrera <span class="text-danger">*</span>
                            </label>
                            <select name="programa_academico_id" id="selectPrograma"
                                    class="form-control @error('programa_academico_id') is-invalid @enderror"
                                    data-programas-no-carrera="{{ urlencode(json_encode(
                                        $programas->where('tipo', '!=', 'carrera')->mapWithKeys(fn($p) => [
                                            $p->tipo => ['id' => $p->id, 'codigo' => $p->codigo]
                                        ]),
                                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
                                    )) }}">
                                <option value="">— Selecciona tu programa —</option>
                                @foreach($programas->where('tipo', 'carrera') as $prog)
                                <option value="{{ $prog->id }}"
                                        {{ old('programa_academico_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->nombre }}
                                </option>
                                @endforeach
                                {{-- Opciones ocultas para programas no-carrera (auto-asignados por JS) --}}
                                @foreach($programas->where('tipo', '!=', 'carrera') as $prog)
                                <option value="{{ $prog->id }}"
                                        data-tipo="{{ $prog->tipo }}"
                                        style="display:none;"
                                        {{ old('programa_academico_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->nombre }}
                                </option>
                                @endforeach
                            </select>
                            @error('programa_academico_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════ Carnet / Código institucional ═══════════════════ --}}
            <div id="filaCodigo" class="mb-4" style="display: none;">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="inputCodigoInstitucional" class="small font-weight-bold" id="labelCodigoInstitucional">
                                Carnet / Código institucional
                            </label>
                            <input type="text" name="codigo_institucional" id="inputCodigoInstitucional"
                                   maxlength="20"
                                   class="form-control @error('codigo_institucional') is-invalid @enderror"
                                   value="{{ old('codigo_institucional') }}"
                                   placeholder="Ej: 2023123456">
                            <small class="form-text text-muted" id="hintCodigoInstitucional"></small>
                            @error('codigo_institucional')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-3">

            {{-- ═══════════════════ Ubicación ═══════════════════ --}}
            <div class="mb-4">
                <h6 class="text-uppercase text-muted font-weight-bold small mb-3">
                    <i class="fas fa-map-marker-alt mr-1"></i> Ubicación
                    <small class="font-weight-normal text-lowercase">(opcional)</small>
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="selectDepartamento" class="small font-weight-bold">
                                Departamento
                            </label>
                            <select id="selectDepartamento" class="form-control"
                                    data-departamentos="{{ urlencode(json_encode(
                                        $departamentos->mapWithKeys(fn($d) => [
                                            $d->id => $d->municipios->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])
                                        ]),
                                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
                                    )) }}"
                                    data-municipio-actual="">
                                <option value="">— Selecciona —</option>
                                @foreach($departamentos as $depto)
                                <option value="{{ $depto->id }}"
                                        {{ old('departamento_id') == $depto->id ? 'selected' : '' }}>
                                    {{ $depto->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="selectMunicipio" class="small font-weight-bold">
                                Municipio
                            </label>
                            <select name="municipio_id" id="selectMunicipio"
                                    class="form-control @error('municipio_id') is-invalid @enderror" disabled>
                                <option value="">— Selecciona un departamento —</option>
                            </select>
                            @error('municipio_id')
                            <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════ Botones ═══════════════════ --}}
            <div class="d-flex flex-column mt-4">
                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    <i class="fas fa-user-check mr-2"></i>Registrarme
                </button>
            </div>

        </form>

    </div>

    {{-- Footer --}}
    <div class="card-footer text-center border-top bg-transparent pt-3 pb-3">
        <a href="{{ route('index') }}" class="text-decoration-none text-muted small">
            <i class="fas fa-arrow-left mr-1"></i> Volver al inicio
        </a>
    </div>

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectArea     = document.getElementById('selectArea');
    const selectPrograma = document.getElementById('selectPrograma');
    const inputCodigo    = document.getElementById('inputCodigoInstitucional');
    const hintCodigo     = document.getElementById('hintCodigoInstitucional');
    const labelCodigo    = document.getElementById('labelCodigoInstitucional');
    const filaPrograma   = document.getElementById('filaPrograma');
    const filaCodigo     = document.getElementById('filaCodigo');

    if (!selectArea) return;

    const programasNoCarrera = JSON.parse(decodeURIComponent(
        (selectPrograma ? selectPrograma.dataset.programasNoCarrera : '{}') || '{}'
    ));

    // Transición suave para mostrar/ocultar filas
    function mostrarFila(el) {
        el.style.display = '';
        el.style.opacity = '0';
        el.style.transform = 'translateY(-8px)';
        requestAnimationFrame(function () {
            el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }

    function ocultarFila(el) {
        el.style.transition = 'opacity 0.15s ease';
        el.style.opacity = '0';
        setTimeout(function () { el.style.display = 'none'; }, 150);
    }

    function aplicarArea() {
        var area = selectArea.value;

        if (!area) {
            ocultarFila(filaPrograma);
            ocultarFila(filaCodigo);
            limpiarCodigo();
            return;
        }

        if (area === 'estudiante') {
            mostrarFila(filaPrograma);
            mostrarFila(filaCodigo);
            selectPrograma.required = true;
            inputCodigo.required = true;
            inputCodigo.readOnly = false;
            inputCodigo.value = '{{ old("codigo_institucional") }}';
            inputCodigo.placeholder = 'Ej: 2023123456';
            inputCodigo.classList.remove('bg-light');
            hintCodigo.textContent = 'Ingresa tu código institucional de estudiante.';
            labelCodigo.innerHTML = '<i class="fas fa-id-badge text-muted mr-1"></i> Carnet / Código institucional <span class="text-danger">*</span>';
        } else if (area === 'externo') {
            ocultarFila(filaPrograma);
            ocultarFila(filaCodigo);
            selectPrograma.required = false;
            selectPrograma.value = '';
            inputCodigo.required = false;
            inputCodigo.readOnly = true;
            inputCodigo.value = 'EXTERNO';
            inputCodigo.classList.add('bg-light');
            // Asignar programa externo oculto
            var ext = programasNoCarrera['externo'];
            if (ext) selectPrograma.value = ext.id;
        } else {
            // Administrativo / Profesor
            ocultarFila(filaPrograma);
            mostrarFila(filaCodigo);
            selectPrograma.required = false;
            selectPrograma.value = '';
            var info = programasNoCarrera[area];
            if (info) selectPrograma.value = info.id;
            inputCodigo.required = true;
            inputCodigo.readOnly = false;
            inputCodigo.value = '';
            inputCodigo.placeholder = 'Ej: 2023123456';
            inputCodigo.classList.remove('bg-light');
            hintCodigo.textContent = '';
            labelCodigo.innerHTML = '<i class="fas fa-id-badge text-muted mr-1"></i> Carnet / Código institucional <span class="text-danger">*</span>';
        }
    }

    function limpiarCodigo() {
        inputCodigo.value = '';
        inputCodigo.readOnly = false;
        inputCodigo.placeholder = '';
        hintCodigo.textContent = '';
        inputCodigo.classList.remove('bg-light');
    }

    selectArea.addEventListener('change', aplicarArea);

    // Aplicar al cargar si ya hay valor seleccionado (old())
    if (selectArea.value) {
        aplicarArea();
    }
});
</script>
@endpush
