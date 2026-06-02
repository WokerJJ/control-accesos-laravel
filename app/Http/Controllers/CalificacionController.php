<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarCalificacionRequest;
use App\Services\CalificacionService;

class CalificacionController extends Controller
{
    private $calificacionService;

    public function __construct(CalificacionService $calificacionService)
    {
        $this->calificacionService = $calificacionService;
    }

    public function index(CalificacionService $calificacionService)
    {
        $accesoId = session('calificacion.acceso_id');

        if (!$accesoId) {
            return redirect()->route('index');
        }

        return view('calificacion.index', [
            'acceso' => $calificacionService->obtenerResumen($accesoId)
        ]);
    }

    public function guardar(GuardarCalificacionRequest $request)
    {
        $accesoId = session('calificacion.acceso_id');

        $this->calificacionService->guardar($request, $accesoId);

        session()->forget('calificacion.acceso_id');

        return redirect()->route('index')->with('mensaje', [
            'tipo' => 'success',
            'texto' => '¡Gracias por tu opinión!'
        ]);
    }
}
