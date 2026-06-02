<?php

namespace App\Http\Controllers;

use App\Models\TipoIdentificacion;
use App\Services\RegistroService;
use Illuminate\Http\Request;

class RegistroController extends Controller
{
    public function create()
    {
        $tipo_identificacion = TipoIdentificacion::opciones();
        return view('registro.create', [
            'doc_identidad' => session('ingreso.doc_identidad'),
            'tipo_identificacion' => $tipo_identificacion,
            'departamentos'       => \App\Models\Departamento::orderBy('nombre')
                ->with(['municipios' => fn($q) => $q->orderBy('nombre')])
                ->get(['id', 'nombre']),
        ]);
    }

    public function store(Request $request, RegistroService $registroService)
    {
        $persona = $registroService->registrar($request->all());

        session([
            'ingreso.persona_id' => $persona->id,
            'ingreso.nombre' => $persona->nombre_completo,
            'ingreso.doc_identidad' => $persona->doc_identidad,
            'ingreso.tipo' => 'ingreso',
        ]);

        return redirect()->route('actividad.index');
    }
}
