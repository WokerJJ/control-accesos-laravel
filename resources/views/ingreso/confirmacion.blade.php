@extends('layouts.public')

@section('titulo', 'Ingreso Registrado')

@section('content')

<div class="container py-2">
    <div class="card shadow-sm mx-auto"
         style="max-width: 440px; border-radius: 14px;">

        {{-- Header --}}
        <div class="card-header bg-success border-0 py-2"
             style="border-radius:14px 14px 0 0;">
        </div>

        <div class="card-body text-center p-3">

            {{-- Icono --}}
            <div class="mb-2">
                <i class="fas fa-check-circle fa-4x text-success"></i>
            </div>

            {{-- Título --}}
            <h4 class="mb-1">¡Ingreso registrado!</h4>

            <p class="text-muted small mb-3">
                Bienvenido,
                <strong>{{ $acceso['persona']['nombre'] }}</strong>
            </p>

            {{-- Actividad --}}
            <div class="info-box mb-2 text-start">
                <span class="info-box-icon bg-primary">
                    <i class="fas fa-running"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Actividad</span>
                    <span class="info-box-number text-truncate">
                        {{ $acceso['actividad']['nombre'] }}
                    </span>
                </div>
            </div>

            {{-- Locación --}}
            <div class="info-box mb-3 text-start">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-map-marker-alt"></i>
                </span>

                <div class="info-box-content">
                    <span class="info-box-text">Locación</span>
                    <span class="info-box-number text-truncate">
                        {{ $acceso['locacion']['nombre'] ?? '—' }}
                    </span>
                </div>
            </div>

            {{-- Casillero --}}
            @if($acceso['casillero'])

            <p class="text-muted small mb-2">
                Casillero asignado
            </p>

            <div class="callout callout-primary py-2 mb-3">
                <h1 class="mb-0"
                    style="font-size:3rem;font-weight:700;letter-spacing:3px;">
                    {{ $acceso['casillero']['codigo'] }}
                </h1>

                <small class="text-muted">
                    <i class="fas fa-lock me-1"></i>
                    Recuerda tu número
                </small>
            </div>

            @else

            <div class="callout callout-warning text-start py-2 mb-3">
                <h6 class="mb-1">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Sin casilleros
                </h6>

                <p class="small mb-0">
                    Tu ingreso fue registrado correctamente,
                    pero no hay casilleros disponibles.
                </p>
            </div>

            @endif

            {{-- Hora --}}
            <div class="description-block mb-2">
                <span class="description-text small">
                    HORA DE INGRESO
                </span>

                <h5 class="description-header mb-0">
                    {{ $acceso['hora_ingreso']->format('h:i A') }}
                </h5>
            </div>

        </div>

        {{-- Footer --}}
        <div class="card-footer text-center border-0 py-2 bg-transparent">

            <a href="{{ route('index') }}"
               class="btn btn-success w-100 mb-2">
                <i class="fas fa-check me-1"></i>
                Finalizar
            </a>

            <small class="text-muted d-block">
                <i class="fas fa-info-circle me-1"></i>
                Recuerda cerrar bien la puerta
            </small>

        </div>

    </div>
</div>

@endsection
