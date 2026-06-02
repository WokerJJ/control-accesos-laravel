<div class="card card-outline card-secondary h-100">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-bolt me-2"></i>
            Acceso rápido
        </h3>
    </div>

    <div class="card-body d-grid gap-2">

        <a href="{{ route('ingreso.iniciar', 'ingreso') }}"
           class="btn btn-success">
            <i class="fas fa-sign-in-alt me-2"></i>
            Registrar ingreso
        </a>

        <a href="{{ route('ingreso.iniciar', 'salida') }}"
           class="btn btn-warning">
            <i class="fas fa-sign-out-alt me-2"></i>
            Registrar salida
        </a>

        <a href="#"
           class="btn btn-secondary">
            <i class="fas fa-file-export me-2"></i>
            Exportar reporte
        </a>

    </div>
</div>
