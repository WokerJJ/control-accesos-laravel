@props([
    'title'  => 'Resumen',
    'icon'   => 'fas fa-chart-bar',
    'color'  => 'info',
    'items'  => [],       // [{color, icon, label, value}]
    'progress' => null,   // {label, current, total, percentage, segments: [{color, percent}]}
])

<div class="card card-outline card-{{ $color }} shadow-sm mb-3">

    <div class="card-header">
        <h3 class="card-title">
            <i class="{{ $icon }} mr-2"></i>
            {{ $title }}
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool"
                    data-lte-toggle="card-collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    <div class="card-body p-2">
        @foreach($items as $item)
        <x-shared.info-box
            :color="$item['color']"
            :icon="$item['icon']"
            :label="$item['label']"
            :value="$item['value']" />
        @endforeach
    </div>

    @if($progress)
    <div class="card-footer p-2">
        <small class="text-muted d-flex justify-content-between mb-1">
            <span>{{ $progress['label'] }}</span>
            <span>{{ $progress['current'] }} / {{ $progress['total'] }}</span>
        </small>
        <div class="progress" style="height: 6px;">
            @if(isset($progress['segments']))
                @foreach($progress['segments'] as $seg)
                <div class="progress-bar bg-{{ $seg['color'] }}"
                     style="width: {{ $seg['percent'] }}%"></div>
                @endforeach
            @else
                <div class="progress-bar bg-{{ $progress['barColor'] ?? 'primary' }}"
                     style="width: {{ $progress['percentage'] }}%"></div>
            @endif
        </div>
        <small class="text-muted">{{ $progress['percentage'] }}% {{ $progress['detail'] ?? '' }}</small>
    </div>
    @endif

</div>
