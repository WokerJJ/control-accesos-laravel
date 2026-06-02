@props([
'model',
'route',
'type' => 'tipo',
'modalId',
])

@php
$isProgramada = $type === 'programada';
$isPersonalizada = $type === 'personalizada';
$isFija = $type === 'fija';
$isModal = $type === 'modal';

$hasHorario = is_object($model) && isset($model->hora_inicio) && isset($model->hora_fin) && $model->hora_inicio && $model->hora_fin;

$icon = $isProgramada ? 'clock' : ($isPersonalizada ? 'star' : ($isFija ? 'infinity' : 'circle'));
$colorClass = $isProgramada ? 'bg-info' : ($isPersonalizada ? 'bg-warning' : ($isFija ? 'bg-success' : 'bg-secondary'));

$title = $isModal ? $model['nombre'] : $model->nombre;
$description = $isModal ? ($model['descripcion'] ?? null) : ($model->descripcion ?? null);
$hasLocacion = is_object($model) && isset($model->locacion) && $model->locacion;
@endphp

@if(!$isModal)
<form action="{{ route($route) }}" method="POST" class="h-100 m-0">
    @csrf
    <input type="hidden" name="actividad_id" value="{{ $model->id }}">
    @endif

    <button
        type="{{ $isModal ? 'button' : 'submit' }}"
        class="btn btn-light border w-100 h-100 p-2 p-md-3 text-start"
        style="border-radius: 10px;"
        @if($isModal)
        data-bs-toggle="modal"
        data-bs-target="#{{ $modalId }}"
        @endif
    >
        <div class="d-flex align-items-center gap-2 gap-md-3">

            <div class="flex-shrink-0">
                <div class="{{ $colorClass }} d-flex align-items-center justify-content-center"
                     style="width:40px;height:40px;border-radius:8px;">
                    <i class="fas fa-{{ $icon }} text-white" style="font-size:1rem;"></i>
                </div>
            </div>

            <div class="flex-grow-1 min-w-0" style="min-width:0;">

                <div class="d-flex flex-wrap gap-1 mb-1">
                    @if($hasLocacion)
                    <span class="badge bg-light text-muted border" style="font-size:0.65rem;">
                        <i class="fas fa-map-marker-alt me-1" style="font-size:0.5rem;"></i>
                        {{ Str::limit($model->locacion->nombre, 20) }}
                    </span>
                    @endif

                    @if($hasHorario)
                    <span class="badge bg-light text-muted border" style="font-size:0.65rem;">
                        <i class="fas fa-clock me-1" style="font-size:0.5rem;"></i>
                        {{ \Carbon\Carbon::parse($model->hora_inicio)->format('h:i A') }} -
                        {{ \Carbon\Carbon::parse($model->hora_fin)->format('h:i A') }}
                    </span>
                    @endif
                </div>

                <div class="fw-bold text-truncate" style="font-size:0.85rem;color:#2c3e50;">
                    {{ $title }}
                </div>

                @if($description)
                <small class="text-muted d-block text-truncate" style="font-size:0.75rem;">
                    {{ Str::limit($description, 40) }}
                </small>
                @endif

            </div>

            <div class="flex-shrink-0 text-muted d-none d-sm-block">
                <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
            </div>

        </div>
    </button>

    @if(!$isModal)
</form>
@endif
