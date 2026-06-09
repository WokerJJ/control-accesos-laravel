<div class="card card-secondary card-outline shadow-sm">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-tags me-2"></i>
            Estados
        </h3>

    </div>

    <div class="card-body">

        @php
        $estados = [
        ['En curso', 'success'],
        ['Pendiente', 'warning'],
        ['Finalizada', 'secondary'],
        ['Cancelada', 'danger'],
        ];
        @endphp

        @foreach($estados as [$texto, $color])

        <div class="d-flex align-items-center mb-3">

                <span class="badge bg-{{ $color }} me-2">
                    &nbsp;
                </span>

            <span>{{ $texto }}</span>

        </div>

        @endforeach

    </div>

</div>
