@props([
    'icon'          => null,
    'title'         => '',
    'variant'       => 'primary',
    'count'         => null,
    'countLabel'    => null,
    'striped'       => false,
    'hover'         => true,
    'align'         => false,
    'small'         => false,
    'theadVariant'  => 'light',
    'shadow'        => false,
    'fullHeight'    => false,
])

@php
    $cardClasses = $shadow ? 'card shadow-sm' : 'card card-outline card-' . $variant;
    if ($fullHeight) $cardClasses .= ' h-100';

    $tableClasses = 'table mb-0';
    if ($hover)   $tableClasses .= ' table-hover';
    if ($striped) $tableClasses .= ' table-striped';
    if ($align)   $tableClasses .= ' align-middle';
    if ($small)   $tableClasses .= ' table-sm';
@endphp

<div class="{{ $cardClasses }}">

    <div class="card-header">
        <h3 class="card-title">
            @if($icon)
                <i class="{{ $icon }} mr-2"></i>
            @endif
            {{ $title }}
            @if($count !== null)
                <span class="badge bg-{{ $variant }} ms-1">{{ $count }}</span>
            @endif
            @if($countLabel)
                <small class="text-muted ms-1">{{ $countLabel }}</small>
            @endif
        </h3>

        @if(isset($tools) && $tools)
            <div class="card-tools">
                {{ $tools }}
            </div>
        @endif
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="{{ $tableClasses }}">
                {{ $slot }}
            </table>
        </div>
    </div>

    @if(isset($footer) && $footer)
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif

</div>
