@extends('layouts.public')

@section('titulo', 'Seleccionar actividad')

@section('content')

@php $confirmarRoute = 'actividad.confirmar' @endphp

<div class="container py-2">
    <div class="mx-auto" style="max-width:900px;">

    {{-- Card principal --}}
        <div class="card shadow-sm" style="border-radius: 16px;">

            {{-- Header --}}
            @include('actividad.partials.bienvenida', ['persona' => $persona])

            <div class="card-body px-3">

                {{-- DOS COLUMNAS: En curso | Fijas --}}
                <div class="row g-2">

                    {{-- COLUMNA IZQUIERDA: En curso --}}
                    <div class="col-12 col-lg-6">
                        <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-clock me-1"></i>En curso ahora
                            @if($actividadesEnCurso->count())
                            <span class="badge bg-info ms-1">{{ $actividadesEnCurso->count() }}</span>
                            @endif
                        </h6>

                        <div class="overflow-auto" style="max-height: 300px;">
                            @forelse($actividadesEnCurso as $actividad)
                            <div class="list-group-item border-0 p-1">
                                @include('actividad.partials.actividad-card', [
                                'model' => $actividad,
                                'route' => $confirmarRoute,
                                'type' => $actividad->tipo
                                ])
                            </div>
                            @empty
                            <div class="text-center py-2 text-muted">
                                <i class="fas fa-clock mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="small mb-0">No hay actividades en curso</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- COLUMNA DERECHA: Fijas --}}
                    <div class="col-12 col-lg-6">
                        <h6 class="text-muted text-uppercase mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <i class="fas fa-infinity me-1"></i>Disponibles
                            @if($actividadesFijas->count())
                            <span class="badge bg-success ms-1">{{ $actividadesFijas->count() }}</span>
                            @endif
                        </h6>

                        <div class="overflow-auto" style="max-height: 300px;">
                            @forelse($actividadesFijas as $actividad)
                            <div class="list-group-item border-0 p-1">
                                @include('actividad.partials.actividad-card', [
                                'model' => $actividad,
                                'route' => $confirmarRoute,
                                'type' => $actividad->tipo
                                ])
                            </div>
                            @empty
                            <div class="text-center py-2 text-muted">
                                <i class="fas fa-infinity mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="small mb-0">No hay servicios disponibles</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer text-center" style="background: transparent;">
                <a href="{{ route('index') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Volver al inicio
                </a>
            </div>

        </div>
    </div>

</div>

@endsection
