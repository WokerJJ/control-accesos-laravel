<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmarActividadRequest;

use App\Services\AccesoService;
use App\Services\ActividadService;
use App\Services\IngresoService;

class ActividadController extends Controller
{

    public function __construct(
        private AccesoService $accesoService,
        private ActividadService $actividadService,
        private IngresoService $ingresoService,
    )
    {}

    public function index(ActividadService $actividadService)
    {
        $personaId = session('ingreso.persona_id');
        if (!$personaId) {
            return redirect()->route('ingreso.identificar')
                ->withErrors(['doc_identidad' => 'Debes identificarte primero.']);
        }

        return view('actividad.index', [
            'persona' => $actividadService->obtenerPersonaIngreso($personaId),
            'tiposActividad' => $actividadService->obtenerTiposActividad(),
            'locaciones' => $actividadService->obtenerLocaciones(),
            'actividadesEnCurso' => $actividadService->obtenerEnCurso(6),
            'actividadesFijas' => $actividadService->obtenerFijas(),
        ]);
    }

    public function confirmarActividad(ConfirmarActividadRequest $request)
    {
        $personaId = session('ingreso.persona_id');

        $acceso = $this->ingresoService->registrar($request, $personaId);
        session()->forget([
            'ingreso.persona_id',
            'ingreso.tipo'
        ]);

        session([
            'ingreso.acceso_id' => $acceso->id
        ]);
        return redirect()->route('ingreso.confirmacion', )->with('mensaje', [
            'tipo'  => 'success',
            'texto' => '¡Ingreso registrado correctamente!'
        ]);
    }
}
