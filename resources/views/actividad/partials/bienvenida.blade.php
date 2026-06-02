@props(['persona'])

<div class="card-header text-center border-0 pb-0" style="background: transparent;">
    <h2 class="text-black mb-1">
        <i class="fas fa-sign-out-alt text-warning"></i>Hola, {{ $persona->primer_nombre }}
    </h2>
    <p class="text-muted mb-0">
        ¿Qué actividad vas a realizar hoy?
    </p>
</div>
