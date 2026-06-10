@extends('layouts.admin')

@section('titulo', 'Calendario de actividades')
@section('header', 'Calendario')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.dashboard') }}">
        Dashboard
    </a>
</li>

<li class="breadcrumb-item active">
    Calendario
</li>
@endsection

@section('content')

<div class="row">

    {{-- Calendario --}}
    <div class="col-lg-9">

        <x-admin.actividades-calendario :locaciones="$locaciones" :tipos-actividad="$tiposActividad" :eventos="$eventos" />

    </div>

    {{-- Sidebar --}}
    <div class="col-lg-3">

        <x-admin.actividades-resumen :stats="$stats" />

        <x-admin.actividades-leyenda />

    </div>

</div>

@endsection


@push('styles')
<style>
    .fc-event { cursor: pointer !important; }
</style>
@endpush
