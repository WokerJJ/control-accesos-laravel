@extends('layouts.public')

@section('titulo', 'Iniciar sesión')

@section('content')

<div class="card" style="max-width: 420px; width: 100%; border-radius: 16px;">

    {{-- Header --}}
    <div class="card-header text-center border-0 pt-4 pb-0" style="background: transparent;">
        <div class="mb-3">
            <i class="fas fa-lock fa-3x text-primary"></i>
        </div>
        <h4 class="mb-1">Panel Administrativo</h4>
        <p class="text-muted mb-0">Inicia sesión para continuar</p>
    </div>

    <div class="card-body px-4 py-4">

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf

            {{-- Documento --}}
            <div class="input-group input-group-lg mb-3">
                <input
                    type="text"
                    name="doc_identidad"
                    class="form-control @error('doc_identidad') is-invalid @enderror"
                    placeholder="Número de documento"
                    value="{{ old('doc_identidad') }}"
                    autocomplete="off"
                    autofocus
                >
                <div class="input-group-append">
                    <span class="input-group-text h-100">
                        <i class="fas fa-id-card"></i>
                    </span>
                </div>
            </div>

            {{-- Password --}}
            <div class="input-group input-group-lg mb-4">
                <input
                    type="password"
                    name="password"
                    id="input-password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Contraseña"
                    autocomplete="current-password"
                >
                <div class="input-group-append">
                    <span class="input-group-text h-100" id="toggle-password"
                          style="cursor: pointer;"
                          title="Mostrar/ocultar contraseña">
                        <i class="fas fa-eye" id="icono-password"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg w-100">
                <i class="fas fa-sign-in-alt mr-2"></i>Iniciar sesión
            </button>

        </form>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="fas fa-shield-alt mr-1"></i>
                Acceso restringido al personal autorizado
            </small>
        </div>
    </div>
    <div class="card-footer text-center border-0">
        <a href="{{ route('index') }}" class="text-muted">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        const $input  = $('#input-password');
        const $icono  = $('#icono-password');
        const $toggle = $('#toggle-password');

        $toggle.on('click', function () {
            const visible = $input.attr('type') === 'text';

            // Cambiar tipo
            $input.attr('type', visible ? 'password' : 'text');

            // Cambiar icono
            $icono
                .toggleClass('fa-eye',        visible)
                .toggleClass('fa-eye-slash', !visible);

            // Mantener foco en el input
            $input.focus();
        });

        // Ocultar password al salir del campo por seguridad
        $input.on('blur', function () {
            $input.attr('type', 'password');
            $icono.removeClass('fa-eye-slash').addClass('fa-eye');
        });
    });
</script>
@endpush
