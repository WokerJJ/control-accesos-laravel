<div class="card card-primary card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-calendar-alt me-2"></i>
            Actividades programadas
        </h3>

        <div class="card-tools">

            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#modalActividad">
                <i class="fas fa-plus me-1"></i>
                Nueva actividad

            </button>

        </div>

    </div>

    <div class="card-body" style="position:relative;">

        <div id="calendar-loading" class="text-center py-5" style="min-height:400px;">
            <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="text-muted mb-0">Cargando calendario...</p>
        </div>

        <div id="calendar" style="visibility:hidden;"></div>

    </div>

    <x-admin.actividades-modal-crear :locaciones="$locaciones" :tipos-actividad="$tiposActividad" />

</div>
