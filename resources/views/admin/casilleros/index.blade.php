@extends('layouts.admin')

@section('titulo', 'Casilleros')
@section('header', 'Mapa de casilleros')
@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('admin.dashboard') }}">
        Dashboard
    </a>
</li>

<li class="breadcrumb-item active">
    Casilleros
</li>
@endsection
@section('content')

<div class="row">
    <div class="col-lg-9" id="contenedor-mapa">
        <x-admin-casilleros-mapa :mapa="$mapa" />
    </div>
    <div class="col-lg-3">
        <x-admin-casilleros-resumen :stats="$stats" />
    </div>
</div>

<x-admin-casilleros-modal-detalle />

@endsection

@include('admin.casilleros.js.realtime')
