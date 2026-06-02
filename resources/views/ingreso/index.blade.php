{{-- resources/views/acceso/index.blade.php --}}
@extends('layouts.public')

@section('titulo', 'Bienvenido')

@section('content')
    {{-- Hora actual --}}
    <div class="text-center mb-4">
        <h1 class="text-white" id="reloj" style="font-size: 3rem; font-weight: 300;">
            {{ now()->format('H:i') }}
        </h1>
    </div>

    {{-- Botones principales --}}
    <div class="row justify-content-center" style="max-width: 700px; width: 100%;">
        {{-- Ingresar --}}
        <div class="col-md-6 mb-3">
            <a href="{{ route('ingreso.iniciar', ['tipo' => 'ingreso']) }}"
               class="btn btn-success btn-block d-flex flex-column align-items-center justify-content-center p-4"
               style="height: 180px; border-radius: 16px; font-size: 1.4rem;">
                <i class="fas fa-sign-in-alt mb-3" style="font-size: 3rem;"></i>
                <span class="font-weight-bold">Ingresar</span>
                <small class="mt-1 font-weight-normal" style="font-size: 0.85rem; opacity: 0.8;">
                    Registrar entrada
                </small>
            </a>
        </div>

        {{-- Salida --}}
        <div class="col-md-6 mb-3">
            <a href="{{ route('ingreso.iniciar', ['tipo' => 'salida']) }}"
               class="btn btn-warning btn-block d-flex flex-column align-items-center justify-content-center p-4"
               style="height: 180px; border-radius: 16px; font-size: 1.4rem;">
                <i class="fas fa-sign-out-alt mb-3" style="font-size: 3rem;"></i>
                <span class="font-weight-bold">Registrar Salida</span>
                <small class="mt-1 font-weight-normal" style="font-size: 0.85rem; opacity: 0.8;">
                    Finalizar visita
                </small>
            </a>
        </div>

    </div>

    {{-- Accesos activos en este momento --}}
    @if($accesos_activos > 0)
        <div class="text-center mt-3">
            <span class="badge badge-info p-2" style="font-size: 0.9rem;">
                <i class="fas fa-circle text-success mr-1" style="font-size: 8px;"></i>
                {{ $accesos_activos }} {{ Str::plural('persona', $accesos_activos) }}
                {{ $accesos_activos === 1? 'ingresada' : 'ingresadas' }}
            </span>
        </div>
    @endif

<x-footer-public />

@endsection

@push('scripts')
<script>
    // Reloj en tiempo real
    function actualizarReloj() {
        const ahora = new Date();
        const horas   = String(ahora.getHours()).padStart(2, '0');
        const minutos = String(ahora.getMinutes()).padStart(2, '0');
        document.getElementById('reloj').textContent = `${horas}:${minutos}`;
    }
    setInterval(actualizarReloj, 1000);
</script>
@endpush
