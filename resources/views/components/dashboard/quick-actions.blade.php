<x-dashboard.card-section
    title="Acciones rápidas"
    icon="fas fa-bolt">

    <div class="d-grid gap-2">

        @foreach($actions as $action)

        <a href="{{ $action['url'] }}"
           class="btn btn-outline-primary">

            <i class="{{ $action['icon'] }} me-1"></i>
            {{ $action['label'] }}

        </a>

        @endforeach

    </div>

</x-dashboard.card-section>
