<div class="col">

    <div class="small-box {{ $color }}">

        <div class="inner">
            <h3>{{ $value }}</h3>
            <p>{{ $label }}</p>
        </div>

        <div class="small-box-icon">
            <i class="{{ $icon }}"></i>
        </div>

        @isset($url)
        <a href="{{ $url }}" class="small-box-footer">
            Ver más
            <i class="fas fa-arrow-circle-right"></i>
        </a>
        @endisset

    </div>

</div>
