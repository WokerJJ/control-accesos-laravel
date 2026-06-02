{{-- resources/views/ingreso/identificar.blade.php --}}
@extends('layouts.public')

@section('titulo', 'Identificación')

@section('content')

<div class="card" style="max-width: 500px; width: 100%; border-radius: 16px;">
    <div class="card-header text-center border-0 pt-4 pb-0" style="background: transparent;">
        <h4 class="mb-1">
            @if($tipo === 'salida')
            <i class="fas fa-sign-out-alt text-warning"></i>Registrar Salida
            @else
            <i class="fas fa-sign-in-alt text-success"></i>Registrar Ingreso
            @endif
        </h4>
        <p class="text-muted mb-0">Ingresa tu documento o escanea tu carnet</p>
    </div>

    <div class="card-body d-grid p-4">

        {{-- Formulario manual --}}
        <form action="{{ route('ingreso.buscar') }}" method="POST">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">

            <div class="form-group">
                <label class="text-muted">Número de documento</label>
                <input
                    type="text"
                    name="doc_identidad" maxlength="20"
                    id="doc_identidad"
                    class="form-control form-control-lg @error('doc_identidad') is-invalid @enderror"
                    placeholder="Ej: 1234567890"
                    value="{{ old('doc_identidad') }}"
                >
            </div>
            @if($tipo === 'salida')
            <button type="submit" class="btn btn-warning btn-lg mt-3 w-100">
                <i class="fas fa-search mr-2"></i>Salir
            </button>
            @else
            <button type="submit" class="btn btn-primary btn-lg mt-3 w-100">
                <i class="fas fa-search mr-2"></i>Ingresar
            </button>
            @endif
        </form>

        <hr>

        {{-- Botón escanear --}}
        <button type="button" class="btn btn-secondary btn-lg w-100" id="btn-escanear">
            <i class="fas fa-qrcode mr-2"></i>Escanear Carnet
        </button>

    </div>

    {{-- Volver --}}
    <div class="card-footer text-center border-0">
        <a href="{{ route('index') }}" class="text-muted">
            <i class="fas fa-arrow-left mr-1"></i>Volver
        </a>
    </div>
</div>

@endsection
