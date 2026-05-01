{{-- resources/views/ingreso/identificar.blade.php --}}
@extends('layouts.public')

@section('titulo', 'Identificación')

@section('content')

<div class="card" style="max-width: 500px; width: 100%; border-radius: 16px;">
    <div class="card-header text-center border-0 pt-4" style="background: transparent;">

        {{-- Ícono según tipo --}}
        @if($tipo === 'ingreso')
        <i class="fas fa-sign-in-alt fa-3x text-success mb-2"></i>
        <h4 class="text-white font-weight-bold mb-0">Registrar Ingreso</h4>
        @else
        <i class="fas fa-sign-out-alt fa-3x text-warning mb-2"></i>
        <h4 class="text-white font-weight-bold mb-0">Registrar Salida</h4>
        @endif

        <p class="text-muted mt-1 mb-0">Identifícate para continuar</p>
    </div>

    <div class="card-body px-4 pb-4">

        {{-- Error --}}
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <i class="icon fas fa-ban"></i>
            {{ session('error') }}
        </div>
        @endif

        {{-- Formulario manual --}}
        <form action="{{ route('ingreso.buscar') }}" method="POST" id="form-identificar">
            @csrf
            <input type="hidden" name="tipo" value="{{ $tipo }}">

            <div class="form-group">
                <label class="text-white">Número de documento</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-id-card"></i>
                            </span>
                    </div>
                    <input
                        type="text"
                        name="doc_identidad"
                        id="doc_identidad"
                        class="form-control form-control-lg @error('doc_identidad') is-invalid @enderror"
                        placeholder="Ej: 1234567890"
                        value="{{ old('doc_identidad') }}"
                        autocomplete="off"
                        autofocus
                        maxlength="20"
                    >
                    @error('doc_identidad')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <small class="text-muted">
                    <i class="fas fa-barcode mr-1"></i>
                    También puedes escanear tu carnet con el lector
                </small>
            </div>

            <button type="submit" class="btn btn-block btn-lg mt-3
                    {{ $tipo === 'ingreso' ? 'btn-success' : 'btn-warning' }}">
                <i class="fas fa-arrow-right mr-2"></i>
                Continuar
            </button>
        </form>

        <hr class="mt-4 mb-3">

        {{-- Volver --}}
        <a href="{{ route('ingreso.index') }}"
           class="btn btn-block btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver
        </a>

    </div>
</div>

@endsection

@push('scripts')
<script>
    $(function () {
        const $input = $('#doc_identidad');
        let bufferEscaner = '';
        let timerEscaner  = null;

        // ── Escáner de código de barras ──────────────────────────────
        // Los lectores envían los caracteres muy rápido + Enter al final.
        // Capturamos esa ráfaga y la separamos del tipeo manual.
        $(document).on('keydown', function (e) {

            // Ignorar si el foco está en el input (tipeo manual normal)
            if (document.activeElement === $input[0]) return;

            // Acumular caracteres del escáner
            if (e.key !== 'Enter') {
                bufferEscaner += e.key;

                // Reiniciar timer — si pasan 100ms sin más teclas, no es escáner
                clearTimeout(timerEscaner);
                timerEscaner = setTimeout(() => { bufferEscaner = ''; }, 100);
                return;
            }

            // Enter recibido — verificar si viene del escáner (>3 caracteres rápidos)
            if (bufferEscaner.length >= 3) {
                $input.val(bufferEscaner);
                bufferEscaner = '';
                clearTimeout(timerEscaner);

                // Feedback visual y envío automático
                $input.addClass('is-valid');
                setTimeout(() => $('#form-identificar').submit(), 300);
            }
        });

        // ── Foco automático al cargar ────────────────────────────────
        $input.focus();

        // ── Solo números ─────────────────────────────────────────────
        $input.on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endpush
