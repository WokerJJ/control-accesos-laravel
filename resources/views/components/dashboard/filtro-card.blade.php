@props(['action', 'colBoton' => '2'])

<div class="card card-outline card-primary mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ $action }}">
            <div class="row">

                {{ $campos }}

                <div class="col-md-{{ $colBoton }} d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>Buscar
                        </button>
                        @if(request()->hasAny(array_keys(request()->except('page'))))
                        <a href="{{ $action }}" class="btn btn-outline-secondary btn-block mt-1">
                            <i class="fas fa-times mr-1"></i>Limpiar
                        </a>
                        @endif
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
