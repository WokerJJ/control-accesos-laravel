{{-- resources/views/components/alerta.blade.php --}}

{{-- Errores de validación --}}
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show mb-3 mt-4"
     id="alerta-global"
     style="max-width: 600px; width: 100%;"
     role="alert">

    <i class="fas fa-times-circle mr-2"></i>

    @if($errors->count() === 1)
    {{ $errors->first() }}
    @else
    <strong>Corrige los siguientes errores:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif

    <div class="progress mt-2" style="height: 3px;">
        <div class="progress-bar bg-danger"
             id="barra-progreso"
             style="width: 100%; transition: width 4s linear;">
        </div>
    </div>
</div>
@endif

{{-- Mensajes de sesión --}}
@if(session('mensaje'))
<div class="alert alert-{{ session('mensaje.tipo') }} alert-dismissible fade show mb-3"
     id="alerta-session"
     style="max-width: 600px; width: 100%;"
     role="alert">

    <i class="fas mr-2
        {{ session('mensaje.tipo') === 'success' ? 'fa-check-circle'         : '' }}
        {{ session('mensaje.tipo') === 'danger'  ? 'fa-times-circle'         : '' }}
        {{ session('mensaje.tipo') === 'warning' ? 'fa-exclamation-triangle' : '' }}
        {{ session('mensaje.tipo') === 'info'    ? 'fa-info-circle'          : '' }}
    "></i>

    {{ session('mensaje.texto') }}

    <div class="progress mt-2" style="height: 3px;">
        <div class="progress-bar bg-{{ session('mensaje.tipo') }}"
             id="barra-session"
             style="width: 100%; transition: width 4s linear;">
        </div>
    </div>
</div>
@endif

{{-- Script solo si hay algo que mostrar --}}
@if($errors->any() || session('mensaje'))
@php
$hayErrores = $errors->any();
$hayMensaje = session('mensaje') ? true : false;
@endphp

@push('scripts')
<script>
    $(function () {
        if ({{ $hayErrores ? 'true' : 'false' }}) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    $('#barra-progreso').css('width', '0%');
                });
            });
            setTimeout(function () { $('#alerta-global').alert('close'); }, 4200);
        }

        if ({{ $hayMensaje ? 'true' : 'false' }}) {
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    $('#barra-session').css('width', '0%');
                });
            });
            setTimeout(function () { $('#alerta-session').alert('close'); }, 4200);
        }
    });
</script>
@endpush
@endif
