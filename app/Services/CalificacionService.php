<?php

namespace App\Services;

use App\Models\Acceso;
use App\Models\Calificacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CalificacionService
{
    public function guardar($request, int $accesoId): Calificacion
    {
        return DB::transaction(function () use ($request, $accesoId) {

            if (Calificacion::existeParaAcceso($accesoId)) {
                throw ValidationException::withMessages([
                    'calificacion' => 'Este acceso ya fue calificado.'
                ]);
            }

            return Calificacion::create([
                'acceso_id' => $accesoId,
                'servicio'  => $request->servicio,
                'atencion'  => $request->atencion,
                'lugar'     => $request->lugar,
                'calidad'   => $request->calidad,
                'comentario'=> $request->comentario,
            ]);
        });
    }

    public function obtenerResumen(int $accesoId): array
    {
        $acceso = Acceso::query()
            ->select(['duracion', 'persona_id'])
            ->with([
                'persona:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
            ])
            ->findOrFail($accesoId);

        $persona = $acceso->persona;
        return [
            'nombre_completo' => trim(
                $persona->primer_nombre . ' ' .
                ($persona->segundo_nombre ? $persona->segundo_nombre . ' ' : '') .
                $persona->primer_apellido . ' ' .
                $persona->segundo_apellido
            ),
            'duracion' => $acceso->duracion,
        ];
    }
}
