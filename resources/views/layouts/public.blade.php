{{-- resources/views/layouts/public.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Control de Accesos')</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700&display=fallback">
    <link rel="stylesheet" href="/adminlte/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/adminlte/dist/css/adminlte.min.css">
    
    @stack('styles')
</head>
<body class="hold-transition" style="background: #1a1a2e;">

<div class="container-fluid d-flex flex-column align-items-center justify-content-center"
     style="min-height: 100vh; padding: 2rem;">

    {{-- Logo / Header --}}
    <div class="text-center mb-4">
        <i class="fas fa-building fa-3x text-primary mb-2"></i>
        <h2 class="text-white font-weight-bold">Control de Accesos</h2>
        <p class=" text-light">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</p>
    </div>

    {{-- Contenido de la vista --}}
    <turbo-frame id="main" style="display: contents;">
        @yield('content')
    </turbo-frame>

    {{-- Mensaje de alerta si existe --}}
    <x-alerta />

</div>

<script src="/adminlte/plugins/jquery/jquery.min.js"></script>
<script src="/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/adminlte/dist/js/adminlte.min.js"></script>

@stack('scripts')
</body>
</html>