<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Acceso;
use App\Services\Admin\AccesoAdminService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccesoController extends Controller
{
    public function __construct(
        private AccesoAdminService $service
    ) {}

    public function index(Request $request): View
    {
        return view('admin.accesos.index', [
            'stats'   => $this->service->obtenerStats(),
            'accesos' => $this->service->obtenerListado(
                $request->only([
                    'estado',
                    'fecha',
                    'buscar'
                ])
            ),
        ]);
    }

    public function show(Acceso $acceso): \Illuminate\Http\Response
    {
        $acceso->load([
            'persona:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,doc_identidad,email,celular',
            'actividad:id,nombre',
            'locacion:id,nombre',
            'casillero:id,codigo',
        ]);

        return response()->view('admin.accesos._detalle', compact('acceso'));
    }
}
