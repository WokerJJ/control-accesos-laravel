@extends('layouts.public')

@section('titulo', 'Selección de Actividad')

@section('content')

<div class="card" style="max-width: 900px; width: 100%; border-radius: 16px;">

    <div class="card-header text-center border-0 pt-4 pb-0" style="background: transparent;">
        <h4 class="mb-1">
            <i class="fas fa-running mr-2 text-primary"></i>
            Hola, {{ session('ingreso.nombre') }}
        </h4>
        <p class="text-muted mb-0">¿Qué vas a realizar hoy?</p>
    </div>

    <div class="card-body p-4">

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ $errors->first() }}
        </div>
        @endif

        {{-- Actividades predefinidas --}}
        <form action="{{ route('ingreso.confirmarActividad') }}" method="POST" id="form-actividad">
            @csrf
            <input type="hidden" name="tipo" value="{{ session('ingreso.tipo', 'ingreso') }}">
            <input type="hidden" name="actividad_personalizada" id="actividad_personalizada" value="0">

            <div class="row">
                @foreach ($actividades as $actividad)
                <div class="col-md-6 col-lg-4 mb-3">
                    <label for="actividad_{{ $actividad->id }}" class="w-100 h-100" style="cursor: pointer;">
                        <input
                            class="actividad-radio d-none"
                            type="radio"
                            name="actividad_id"
                            value="{{ $actividad->id }}"
                            id="actividad_{{ $actividad->id }}"
                        >
                        <div class="card card-actividad h-100 text-center p-3"
                             style="border: 2px solid transparent; border-radius: 12px; transition: all .2s;">
                            <div class="mb-2 mt-1">
                                <i class="{{ $actividad->icono ?? 'fas fa-star' }} fa-3x text-primary"></i>
                            </div>
                            <h6 class="font-weight-bold mb-1">{{ $actividad->nombre }}</h6>
                            <small class="text-muted">{{ $actividad->descripcion }}</small>
                        </div>
                    </label>
                </div>
                @endforeach

                {{-- Tarjeta: otra actividad --}}
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card card-actividad h-100 text-center p-3"
                         id="card-otra"
                         style="border: 2px solid transparent; border-radius: 12px; cursor: pointer; transition: all .2s;">
                        <div class="mb-2 mt-1">
                            <i class="fas fa-plus-circle fa-3x text-secondary"></i>
                        </div>
                        <h6 class="font-weight-bold mb-1">Otra actividad</h6>
                        <small class="text-muted">Especifica qué vas a hacer</small>
                    </div>
                </div>
            </div>

            {{-- Formulario actividad personalizada --}}
            <div id="form-personalizada" class="card mt-3 p-3" style="display: none; border-radius: 12px; background: rgba(255,255,255,0.05);">
                <h6 class="mb-3">
                    <i class="fas fa-pen mr-2 text-primary"></i>Describe tu actividad
                </h6>
                <div class="form-group">
                    <label class="text-muted">Título <span class="text-danger">*</span></label>
                    <input
                        type="text"
                        name="actividad_nombre"
                        class="form-control"
                        placeholder="Ej: Lectura personal, Tutoría..."
                        value="{{ old('actividad_nombre') }}"
                    >
                </div>
                <div class="form-group">
                    <label class="text-muted">Descripción <span class="text-danger">*</span></label>
                    <textarea
                        name="actividad_descripcion"
                        class="form-control"
                        rows="2"
                        placeholder="Describe brevemente lo que harás"
                    >{{ old('actividad_descripcion') }}</textarea>
                </div>
            </div>

            {{-- Botones --}}
            <div class="row mt-4">
                <div class="col-6">
                    <a href="{{ route('ingreso.identificar') }}" class="btn btn-secondary btn-lg btn-block">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </a>
                </div>
                <div class="col-6">
                    <button type="submit" class="btn btn-success btn-lg btn-block" id="btn-confirmar" disabled>
                        <i class="fas fa-check mr-2"></i>Confirmar
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {

        // Seleccionar tarjeta de actividad predefinida
        $('.actividad-radio').on('change', function () {
            // Quitar selección visual de todas
            $('.card-actividad').css('border-color', 'transparent');
            $('#card-otra').css('border-color', 'transparent');
            $('#form-personalizada').hide();
            $('#actividad_personalizada').val('0');

            // Marcar la seleccionada
            $(this).closest('label').find('.card-actividad')
                .css('border-color', '#007bff');

            habilitarBoton();
        });

        // Seleccionar "Otra actividad"
        $('#card-otra').on('click', function () {
            // Desmarcar radios
            $('.actividad-radio').prop('checked', false);
            $('.card-actividad').css('border-color', 'transparent');

            // Resaltar esta tarjeta
            $(this).css('border-color', '#6c757d');

            // Mostrar formulario personalizado
            $('#form-personalizada').slideDown(200);
            $('#actividad_personalizada').val('1');

            habilitarBoton();
        });

        // Habilitar botón confirmar
        function habilitarBoton() {
            $('#btn-confirmar').prop('disabled', false);
        }

    });
</script>
@endpush
