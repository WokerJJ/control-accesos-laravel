<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Actividad;
use App\Models\TipoActividad;
use Illuminate\Validation\ValidationException;

class IngresoService
{
    public function __construct(
        private AccesoService $accesoService,
    ) {}

    public function registrar($request, int $personaId)
    {
        $persona = Persona::query()
            ->select('id', 'primer_nombre', 'primer_apellido', 'doc_identidad', 'estado')
            ->findOrFail($personaId);

        $actividad = Actividad::query()
            ->select('id', 'nombre', 'locacion_id')
            ->findOrFail($request->actividad_id);

        return $this->accesoService->iniciar($persona, $actividad);
    }
}
