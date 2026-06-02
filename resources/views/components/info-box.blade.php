@props([
'color' => 'primary',
'icon'  => 'fas fa-info',
'label' => '',
'value' => 0,
'footer' => null,
])

<div class="info-box mb-3">
    <span class="info-box-icon bg-{{ $color }} elevation-1">
        <i class="{{ $icon }}"></i>
    </span>
    <div class="info-box-content">
        <span class="info-box-text">{{ $label }}</span>
        <span class="info-box-number">
            {{ $value ?: '—' }}
        </span>
        @if($footer)
        <div class="progress">
            <div class="progress-bar" style="width: 0"></div>
        </div>
        <span class="progress-description">{{ $footer }}</span>
        @endif
    </div>
</div>
