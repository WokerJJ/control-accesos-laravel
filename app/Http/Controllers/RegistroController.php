<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistroRequest;
use App\Mail\RegistroConfirmacionMail;
use App\Models\ProgramaAcademico;
use App\Models\TipoIdentificacion;
use App\Services\RegistroService;
use Illuminate\Support\Facades\Mail;

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
            'programas' => ProgramaAcademico::activos()->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre', 'tipo']),
        ]);
    }

    public function store(RegistroRequest $request, RegistroService $registroService)
    {
        $persona = $registroService->registrar($request->validated());

        session([
            'ingreso.persona_id' => $persona->id,
            'ingreso.nombre' => $persona->nombre_completo,
            'ingreso.doc_identidad' => $persona->doc_identidad,
            'ingreso.tipo' => 'ingreso',
        ]);

        // Enviar correo de confirmación (sin bloquear el registro)
        $correoEnviado = false;
        if ($persona->email) {
            try {
                Mail::to($persona->email)->send(new RegistroConfirmacionMail($persona));
                $correoEnviado = true;
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('No se pudo enviar correo de confirmación: ' . $e->getMessage());
            }
        }

        $mensaje = $correoEnviado
            ? 'Registro exitoso. Se envió un correo de confirmación a tu email.'
            : 'Registro exitoso.';

        return redirect()->route('actividad.index')
            ->with('mensaje', [
                'tipo'  => 'success',
                'texto' => $mensaje,
            ]);
    }
}
