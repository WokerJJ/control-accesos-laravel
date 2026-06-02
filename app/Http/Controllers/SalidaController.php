<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Services\SalidaService;
use Illuminate\Http\Request;

class SalidaController extends Controller
{
    private $salidaService;

    public function __construct(SalidaService $salidaService)
    {
        $this->salidaService = $salidaService;
    }

    public function index()
    {
        $personaId = session('ingreso.persona_id');

        if (!$personaId) {
            return redirect()->route('ingreso.identificar')
                ->withErrors(['doc_identidad' => 'Debes identificarte primero.']);
        }

        $persona = Persona::find($personaId);

        $acceso = $persona?->accesoActivo();

        if (!$acceso) {
            return redirect()->route('ingreso.identificar')
                ->withErrors(['doc_identidad' => 'No hay un acceso activo.']);
        }
        return view('salida.index', compact('acceso'));
    }


    public function registrar()
    {
        $personaId = session('ingreso.persona_id');
        $accesoId = $this->salidaService->registrar($personaId);

        session()->forget([
            'ingreso.persona_id',
            'ingreso.doc_identidad',
            'ingreso.tipo'
        ]);

        session([
            'calificacion.acceso_id' => $accesoId,
        ]);

        return redirect()
            ->route('calificacion.index');
    }
}
