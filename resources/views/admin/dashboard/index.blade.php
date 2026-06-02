@extends('layouts.admin')

@section('titulo', 'Dashboard')
@section('header', 'Resumen del sistema')

@section('breadcrumb')
<li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

{{-- Estadísticas --}}
<div class="row row-cols-1 row-cols-md-3 g-3">
    <x-dashboard.stat-box color="text-bg-primary" :value="$stats['total_usuarios']" label="Usuarios registrados" icon="fas fa-users" url="hola"/>
    <x-dashboard.stat-box color="text-bg-success" :value="$stats['accesos_hoy']" label="Accesos hoy" icon="fas fa-calendar-day" url="hola"/>
    <x-dashboard.stat-box color="text-bg-info" :value="$stats['accesos_mes']" label="Accesos mes" icon="fas fa-chart-line" url="hola"/>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <x-dashboard.accesos-chart :data="$stats['accesos_por_dia']"/>
    </div>

    <div class="col-md-4">
        <x-dashboard.personas-dentro :personas="$stats['personas_dentro']" :adentro="$stats['activos']"/>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <x-dashboard.casilleros-resumen :data="$stats['casilleros']"/>
    </div>

    <div class="col-md-4">
        <x-dashboard.rating-promedio :value="$stats['promedio_calificacion']"/>
    </div>

    <div class="col-md-4">
        <x-dashboard.acceso-rapido/>
    </div>
</div>

@endsection
