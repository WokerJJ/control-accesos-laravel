<?php

namespace App\Services;

use App\Models\Acceso;
use App\Models\Actividad;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Servicio central de control de acceso.
 *
 * Responsabilidades:
 *  - Identificar personas por documento
 *  - Validar el contexto de acceso (ingreso/salida) por separado
 *  - Iniciar y finalizar accesos con transacciones seguras
 */
class AccesoService
{
    public function __construct(private CasilleroService $casilleroService) {}

    public function identificarPersona(string $documento): array
    {
        $persona = Persona::select('id', 'doc_identidad')
            ->where('doc_identidad', $documento)
            ->first();

        // Sin registro → redirigir a creación
        if (!$persona) {
            return ['ingreso.tipo' => 'registro', 'ingreso.doc_identidad' => $documento];
        }

        $tipo = session('ingreso.tipo'); // 'ingreso' o 'salida'
        $tieneAcceso = Acceso::select('id')->where('persona_id', $persona->id)
            ->where('estado', 'en_curso')
            ->exists();

        if ($tipo === 'ingreso' && $tieneAcceso) {
            $this->validarSinAccesoActivo($persona);
        }

        if ($tipo === 'salida' && !$tieneAcceso) {
            $this->validarConAccesoActivo($persona);
        }

        return [
            'ingreso.persona_id'    => $persona->id,
            'ingreso.doc_identidad' => $persona->doc_identidad,
        ];
    }

    public function iniciar(Persona $persona, Actividad $actividad): Acceso
    {
        return DB::transaction(function () use ($persona, $actividad) {
            $casillero = $this->casilleroService->asignar();

            return Acceso::create([
                'persona_id'     => $persona->id,
                'locacion_id'    => $actividad->locacion_id,
                'actividad_id'   => $actividad->id,
                'casillero_id'   => $casillero->id,
                'hora_ingreso'   => now(),
                'estado'         => 'en_curso',
                'metodo_acceso'  => 'manual',
            ]);
        });
    }

    public function finalizar(int $personaId): int
    {
        return DB::transaction(function () use ($personaId) {
            $acceso = Acceso::select('id', 'persona_id', 'hora_ingreso', 'hora_salida', 'duracion', 'estado', 'casillero_id')
                ->where('persona_id', $personaId)
                ->where('estado', 'en_curso')
                ->lockForUpdate() // evita condiciones de carrera
                ->firstOrFail();  // si falló validarContexto() esto nunca llega, pero seguro igual

            $acceso->update([
                'hora_salida' => now(),
                'duracion'    => $acceso->hora_ingreso->diffInMinutes(now()),
                'estado'      => 'completado',
            ]);

            if ($acceso->casillero_id) {
                $this->casilleroService->liberar($acceso->casillero_id);
            }

            return $acceso->id;
        });
    }

    public function obtenerParaConfirmacion(int $accesoId): array
    {
        $acceso = Acceso::select('id', 'persona_id', 'actividad_id', 'locacion_id', 'casillero_id', 'hora_ingreso')
            ->with([
                'persona:id,primer_nombre,primer_apellido',
                'actividad:id,nombre',
                'locacion:id,nombre',
                'casillero:id,codigo',
            ])
            ->findOrFail($accesoId);

        return [
            'id'          => $acceso->id,
            'hora_ingreso' => $acceso->hora_ingreso,
            'persona'     => ['nombre' => "{$acceso->persona->primer_nombre} {$acceso->persona->primer_apellido}"],
            'actividad'   => ['nombre' => $acceso->actividad->nombre],
            'locacion'    => ['nombre' => $acceso->locacion->nombre],
            'casillero'   => ['codigo' => $acceso->casillero->codigo],
        ];
    }

    public function totalActivos(): int
    {
        return Acceso::where('estado', 'en_curso')->count();
    }

    // -------------------------------------------------------------------------
    // Validadores privados (invocados dinámicamente por validarContexto)
    // -------------------------------------------------------------------------

    /**
     * Flujo ingreso: la persona NO debe tener un acceso activo.
     *
     * @throws ValidationException
     */
    private function validarSinAccesoActivo(Persona $persona): void
    {
        if (Acceso::select('id')->where('persona_id', $persona->id)->where('estado', 'en_curso')->exists()) {
            throw ValidationException::withMessages([
                'persona' => 'Ya tienes un acceso activo. Debes salir antes de ingresar nuevamente.',
            ]);
        }
    }

    /**
     * Flujo salida: la persona SÍ debe tener un acceso activo.
     *
     * @throws ValidationException
     */
    private function validarConAccesoActivo(Persona $persona): void
    {
        if (!Acceso::select('id')->where('persona_id', $persona->id)->where('estado', 'en_curso')->exists()) {
            throw ValidationException::withMessages([
                'persona' => 'No tienes un acceso activo para registrar salida.',
            ]);
        }
    }
}
