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

    <div class="card-body">

        <div id="calendar"></div>

    </div>

    <x-admin-actividades-modal-crear :locaciones="$locaciones" :tipos-actividad="$tiposActividad" />

</div>
