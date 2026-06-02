@extends('layouts.admin')

@section('titulo', 'Ajustes de cuenta')
@section('header', 'Ajustes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Ajustes</li>
@endsection

@section('content')
<div class="container-fluid">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-3">

        {{-- ── Sidebar perfil ───────────────────────── --}}
        <div class="col-md-3">
            <div class="card card-outline card-primary">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-secondary"></i>
                    </div>
                    <h5 class="mb-1">{{ $persona->nombre_completo }}</h5>
                    <span class="badge bg-primary mb-2">{{ $usuario->rol->nombre_rol }}</span>
                    <div class="text-muted small">
                        <i class="fas fa-id-card me-1"></i>{{ $persona->doc_identidad }}
                    </div>
                    @if($persona->municipio)
                    <div class="text-muted small mt-1">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $persona->municipio->nombre }}, {{ $persona->municipio->departamento->nombre }}
                    </div>
                    @endif
                </div>
                <div class="card-footer text-center text-muted small">
                    <i class="fas fa-clock me-1"></i>
                    Último acceso: {{ $usuario->ultimo_acceso?->format('d/m/Y H:i') ?? 'Sin registros' }}
                </div>
            </div>
        </div>

        {{-- ── Formularios ──────────────────────────── --}}
        <div class="col-md-9">

            {{-- Datos personales --}}
            <div class="card card-outline card-primary mb-3">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-edit me-2"></i>Datos de contacto
                    </h3>
                </div>
                <form method="POST" action="{{ route('admin.ajustes.actualizar') }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row g-3">

                            {{-- Solo lectura --}}
                            <div class="col-md-6">
                                <label class="form-label text-muted">Nombre completo</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $persona->nombre_completo }}"
                                       disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted">Documento</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $persona->doc_identidad }}"
                                       disabled>
                            </div>

                            {{-- Editables --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Email
                                </label>
                                <input type="email"
                                       name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $persona->email) }}"
                                       placeholder="correo@ejemplo.com"
                                       maxlength="100">
                                @error('email')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Celular</label>
                                <input type="text"
                                       name="celular"
                                       class="form-control @error('celular') is-invalid @enderror"
                                       value="{{ old('celular', $persona->celular) }}"
                                       placeholder="3001234567"
                                       maxlength="15">
                                @error('celular')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Municipio</label>
                                <select name="municipio_id"
                                        class="form-control @error('municipio_id') is-invalid @enderror">
                                    <option value="">— Sin municipio —</option>
                                    @foreach($municipios as $municipio)
                                    <option value="{{ $municipio->id }}"
                                            @selected(old('municipio_id', $persona->municipio_id) == $municipio->id)>
                                    {{ $municipio->nombre }} — {{ $municipio->departamento->nombre }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('municipio_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Dirección</label>
                                <input type="text"
                                       name="direccion"
                                       class="form-control @error('direccion') is-invalid @enderror"
                                       value="{{ old('direccion', $persona->direccion) }}"
                                       placeholder="Calle 123 # 45-67"
                                       maxlength="255">
                                @error('direccion')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar cambios
                        </button>
                    </div>
                </form>
            </div>

            {{-- Cambiar contraseña --}}
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lock me-2"></i>Cambiar contraseña
                    </h3>
                </div>
                <form method="POST" action="{{ route('admin.ajustes.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label class="form-label">Contraseña actual</label>
                                <input type="password"
                                       name="password_actual"
                                       class="form-control @error('password_actual') is-invalid @enderror"
                                       placeholder="Tu contraseña actual"
                                       maxlength="100">
                                @error('password_actual')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Mínimo 8 caracteres"
                                       maxlength="100">
                                @error('password')
                                <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar contraseña</label>
                                <input type="password"
                                       name="password_confirmation"
                                       class="form-control"
                                       placeholder="Repite la nueva contraseña"
                                       maxlength="100">
                            </div>

                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-1"></i>Cambiar contraseña
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
