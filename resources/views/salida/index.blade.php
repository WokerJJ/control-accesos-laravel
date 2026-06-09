@extends('layouts.public')

@section('titulo', 'Confirmar salida')

@section('content')

<div class="card" style="max-width: 560px; width: 100%; border-radius: 16px;">

    {{-- Header --}}
    <div class="card-header text-center border-0 pt-4 pb-0" style="background: transparent;">
        <div>
            <i class="fas fa-sign-out-alt fa-3x text-warning"></i>
        </div>
        <h4 class="mb-1">Confirmar salida</h4>
        <p class="text-muted mb-0">Estás a punto de finalizar tu visita</p>
    </div>

    <div class="card-body">

        {{-- Info del usuario --}}
        <div class="callout callout-warning mb-2 text-center">
            <h5 class="mb-1">
                <i class="fas fa-user mr-2 text-muted"></i>
                {{ $acceso->persona->nombre_completo }}
            </h5>
<!--            <small class="text-muted">{{ $acceso->persona->doc_identidad }}</small>-->
        </div>

        {{-- Detalle de la visita --}}
        <div class="row mb-2">

            <div class="col-6 mb-3">
                <x-shared.info-box
                    icon="fas fa-running"
                    color="primary"
                    label="Actividad"
                    :value="$acceso->actividad->nombre"></x-shared.info-box>
            </div>

            @if($acceso->locacion)
            <div class="col-6 mb-3">
                <x-shared.info-box
                    icon="fas fa-map-marker-alt"
                    color="info"
                    label="Locación"
                    :value="$acceso->locacion->nombre"></x-shared.info-box>
            </div>
            @endif

            <div class="col-6 mb-3">
                <x-shared.info-box
                    icon="fas fa-clock"
                    color="success"
                    label="Ingreso"
                    :value="$acceso->hora_ingreso?->format('h:i A') ?? '—'"></x-shared.info-box>
            </div>

            <div class="col-6 mb-3">
                <x-shared.info-box
                    icon="fas fa-hourglass-half"
                    color="warning"
                    label="Tiempo"
                    :value="$acceso->hora_ingreso?->diffForHumans(null, true) ?? '—'"></x-shared.info-box>
            </div>

            <div class="col-6 mb-3">
                <x-shared.info-box
                    icon="fas fa-hourglass-half"
                    color="primary"
                    label="Casillero"
                    :value="optional($acceso->casillero)->codigo ?? '—'"></x-shared.info-box>
            </div>

        </div>

        {{-- Aviso --}}
        <div class="alert alert-default-warning text-center mb-0" style="border-radius: 10px;">
            <i class="fas fa-info-circle mr-2"></i>
            Se registrará tu salida y la duración total de tu visita.
        </div>

        {{-- Botones --}}
        <form action="{{ route('salida.registrar') }}" method="POST" class="mb-2 text-center">
            @csrf
            <button type="submit" class="btn btn-warning btn-block btn-lg">
                <i class="fas fa-check mr-2"></i>Confirmar salida
            </button>
        </form>

        <div class="text-center mt-2 mb-2">
            <a href="{{ route('index') }}" class="text-muted">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </div>

    </div>
</div>

@endsection
