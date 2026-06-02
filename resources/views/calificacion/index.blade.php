@extends('layouts.public')

@section('titulo', 'Calificación')

@section('content')

<div class="mx-auto" style="max-width: 560px; width: 100%;">

    <div class="card shadow-sm" style="border-radius: 16px;">

        {{-- Header con icono de éxito --}}
        <div class="card-header text-center border-0 pt-4 pb-0" style="background: transparent;">
            <div class="mb-3">
                <i class="fas fa-star fa-3x text-warning"></i>
            </div>
            <h4 class="mb-1">¡Gracias por visitarnos!</h4>
            <p class="text-muted mb-0">{{ $acceso['nombre_completo'] }}</p>
        </div>

        <div class="card-body px-4">

            {{-- Info de duración --}}
            @if($acceso['duracion'])
            <div class="callout callout-info mb-3 text-center py-2">
                <span class="text-muted small">Duración de tu visita</span>
                <div class="fw-bold" style="font-size: 1.3rem;">
                    <i class="fas fa-clock text-info me-1"></i>
                    {{ floor($acceso['duracion'] / 60) > 0 ? floor($acceso['duracion'] / 60) . 'h ' : '' }}
                    {{ $acceso['duracion'] % 60 }}m
                </div>
            </div>
            @endif

            <p class="text-center text-muted mb-3" style="font-size: 0.9rem;">
                Tu opinión nos ayuda a mejorar el servicio
            </p>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('calificacion.guardar') }}">
                @csrf

                @php
                $campos = [
                'servicio' => ['Servicio', 'fa-concierge-bell'],
                'atencion' => ['Atención', 'fa-user-tie'],
                'lugar'    => ['Instalaciones', 'fa-building'],
                'calidad'  => ['Calidad', 'fa-gem'],
                ];
                @endphp

                <div class="row g-2 mb-3">
                    @foreach ($campos as $name => $info)
                    <div class="col-6 text-center">
                        <div class="card border" style="border-radius: 12px;">
                            <div class="card-body p-2">
                                <i class="fas {{ $info[1] }} text-muted mb-1" style="font-size: 1.2rem;"></i>
                                <div class="fw-bold small mb-2" style="font-size: 0.8rem;">{{ $info[0] }}</div>

                                <div class="rating d-flex justify-content-center gap-1" data-name="{{ $name }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                    <button type="button"
                                            class="btn btn-link p-0 text-decoration-none"
                                            style="color: #dee2e6; font-size: 1.3rem; line-height: 1;"
                                            data-value="{{ $i }}">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    @endfor
                                </div>

                                <input type="hidden" name="{{ $name }}" value="{{ old($name) }}">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Comentario --}}
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">
                        Comentario <span class="fw-normal">(opcional)</span>
                    </label>
                    <textarea name="comentario" maxlength="500"
                              rows="3"
                              class="form-control"
                              placeholder="¿Cómo fue tu experiencia?"
                              style="border-radius: 10px; resize: none;">{{ old('comentario') }}</textarea>
                </div>

                {{-- Botones --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg" style="border-radius: 12px;">
                        <i class="fas fa-paper-plane me-2"></i>Enviar calificación
                    </button>
                    <a href="{{ route('index') }}" class="btn btn-light" style="border-radius: 12px;">
                        <i class="fas fa-arrow-left me-1"></i>Omitir por ahora
                    </a>
                </div>

            </form>

        </div>

    </div>

</div>

<script>
    document.querySelectorAll('.rating').forEach(group => {
        const stars = group.querySelectorAll('button');
        const input = group.nextElementSibling;
        let selectedValue = 0;

        stars.forEach((star, index) => {
            const value = parseInt(star.dataset.value);

            star.addEventListener('click', () => {
                selectedValue = value;
                input.value = value;
                updateStars();
            });

            star.addEventListener('mouseenter', () => {
                highlightStars(value);
            });
        });

        group.addEventListener('mouseleave', () => {
            updateStars();
        });

        function highlightStars(maxValue) {
            stars.forEach((s, i) => {
                if (i < maxValue) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#dee2e6';
                }
            });
        }

        function updateStars() {
            stars.forEach((s, i) => {
                if (i < selectedValue) {
                    s.style.color = '#ffc107';
                } else {
                    s.style.color = '#dee2e6';
                }
            });
        }
    });
</script>

@endsection
