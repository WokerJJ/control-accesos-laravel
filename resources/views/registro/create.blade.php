@extends('layouts.public')

@section('titulo', 'Registro de usuario')

@section('content')

<div class="card" style="max-width: 600px; width: 100%; border-radius: 16px;">

    {{-- Header --}}
    <div class="card-header text-center border-0 pt-4 pb-0" style="background: transparent;">
        <i class="fas fa-user-plus fa-3x text-primary mb-2"></i>
        <h4 class="mb-1">Registro rápido</h4>
        <p class="text-muted mb-0">Completa tus datos para continuar</p>
    </div>

    <div class="card-body px-4 pb-4">

        <form action="{{ route('registro.store') }}" method="POST">
            @csrf

            {{-- ───────── Nombres ───────── --}}
            <div class="row">
                <div class="col-md-6">
                    <label class="text-muted" class="font-weight-bold">
                        <i class="fas fa-id-card mr-1 text-muted"></i>
                        Tipo de identificación
                    </label>

                    <select name="tipo_identificacion_id"
                            class="form-control @error('tipo_identificacion_id') is-invalid @enderror">

                        @foreach($tipo_identificacion as $tipo)
                        <option value="{{ $tipo->id }}"
                                {{ old('tipo_identificacion_id', 1) == $tipo->id ? 'selected' : '' }}>
                        {{ $tipo->abreviatura }}
                        </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6">
                    <label class="text-muted" class="font-weight-bold">
                        <i class="fas fa-fingerprint mr-1 text-muted"></i>
                        Número de documento
                    </label>

                    <input type="text"
                           name="doc_identidad"
                           class="form-control"
                           value="{{ old('doc_identidad', $doc_identidad ?? '') }}"
                           readonly>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">Primer nombre *</label>
                        <input type="text"
                               name="primer_nombre" maxlength="50"
                               class="form-control @error('primer_nombre') is-invalid @enderror"
                               value="{{ old('primer_nombre') }}"
                               required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">Segundo nombre</label>
                        <input type="text"
                               name="segundo_nombre" maxlength="50"
                               class="form-control"
                               value="{{ old('segundo_nombre') }}">
                    </div>
                </div>
            </div>

            {{-- ───────── Apellidos ───────── --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">Primer apellido *</label>
                        <input type="text"
                               name="primer_apellido" maxlength="50"
                               class="form-control @error('primer_apellido') is-invalid @enderror"
                               value="{{ old('primer_apellido') }}"
                               required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">Segundo apellido</label>
                        <input type="text"
                               name="segundo_apellido" maxlength="50"
                               class="form-control"
                               value="{{ old('segundo_apellido') }}">
                    </div>
                </div>
            </div>

            {{-- ───────── Contacto ───────── --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">Email</label>
                        <input type="email"
                               name="email" maxlength="100"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">Celular</label>
                        <input type="text"
                               name="celular" maxlength="15"
                               class="form-control @error('celular') is-invalid @enderror"
                               value="{{ old('celular') }}">
                    </div>
                </div>
            </div>

            {{-- ───────── Ubicación ───────── --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="text-muted">
                            Departamento
                        </label>
                        <select id="selectDepartamento" class="form-control"
                                data-departamentos="{!! urlencode(json_encode(
                                    $departamentos->mapWithKeys(fn($d) => [
                                        $d->id => $d->municipios->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])
                                    ]),
                                    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
                                )) !!}"
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
                        <label class="text-muted">
                            Municipio
                        </label>
                        <select name="municipio_id"
                                id="selectMunicipio"
                                class="form-control @error('municipio_id') is-invalid @enderror"
                                disabled>
                            <option value="">— Primero selecciona un departamento —</option>
                        </select>
                        @error('municipio_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ───────── Botones ───────── --}}
                <div class="mt-4">
                    <button type="submit"
                            class="btn btn-primary btn-lg w-100">
                        Registrarme
                    </button>
                </div>
            </div>
        </form>

    {{-- Volver --}}
    <div class="card-footer text-center border-0">
        <a href="{{ route('index') }}" class="text-muted">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>

    </div>
</div>

@endsection

