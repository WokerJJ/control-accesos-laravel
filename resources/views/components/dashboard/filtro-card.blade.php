@props(['action'])

<div class="card card-outline card-primary mb-4 filtro-card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtros</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ $action }}" class="filtro-form">
            <div class="filtro-row">

                {{ $campos }}

                <div class="filtro-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Buscar
                    </button>
                    @if(request()->hasAny(array_keys(request()->except('page'))))
                    <a href="{{ $action }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Limpiar
                    </a>
                    @endif
                </div>

            </div>
        </form>
    </div>
</div>
