<div class="card shadow-sm h-100">

    @isset($title)
    <div class="card-header">
        <h3 class="card-title">
            @isset($icon)
            <i class="{{ $icon }} me-2"></i>
            @endisset
            {{ $title }}
        </h3>
    </div>
    @endisset

    <div class="card-body">
        {{ $slot }}
    </div>

</div>
