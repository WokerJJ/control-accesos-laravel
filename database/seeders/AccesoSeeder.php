<?php

namespace Database\Seeders;

use App\Models\Acceso;
use App\Models\Actividad;
use App\Models\Casillero;
use App\Models\Persona;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccesoSeeder extends Seeder
{
    /** ID del casillero externo — nunca se marca como ocupado. */
    private int $casilleroExternoId;

    public function run(): void
    {
        DB::transaction(function () {
            /*
            |------------------------------------------------------------------
            | Datos base
            |------------------------------------------------------------------
            */
            $personas    = Persona::all();
            $actividades = Actividad::where('estado', 'en_curso')
                ->where(function ($q) {
                    $q->where('tipo', 'fija')
                        ->orWhere(fn($q) => $q->where('tipo', 'programada')
                            ->whereDate('fecha_inicio', '<=', now())
                            ->whereDate('fecha_fin',    '>=', now()))
                        ->orWhere(fn($q) => $q->where('tipo', 'personalizada')
                            ->where('fecha_inicio', '<=', now())
                            ->where('fecha_fin',    '>=', now()));
                })
                ->get();

            if ($personas->isEmpty() || $actividades->isEmpty()) {
                throw new \Exception('Faltan datos base. Ejecuta primero ActividadSeeder y PersonaSeeder.');
            }

            $this->casilleroExternoId = config('acceso.casillero_externo_id');

            /*
            |------------------------------------------------------------------
            | Reset
            |------------------------------------------------------------------
            */
            Acceso::query()->delete();
            Casillero::where('id', '!=', $this->casilleroExternoId)
                ->update(['estado' => 'libre']);

            /*
            |------------------------------------------------------------------
            | Históricos (30 días) — casillero aleatorio o externo
            |------------------------------------------------------------------
            | En históricos todos los accesos están completados, así que
            | los casilleros ya fueron liberados. Simulamos la asignación
            | que hubo en ese momento: 80% tuvo casillero real, 20% externo.
            */
            $todosCasilleros = Casillero::where('id', '!=', $this->casilleroExternoId)
                ->pluck('id')
                ->all();

            $registros = [];

            foreach (range(1, 30) as $dia) {
                $fechaBase = now()->subDays($dia);
                $cantidad  = rand(80, 250);

                for ($i = 0; $i < $cantidad; $i++) {
                    $actividad   = $actividades->random();
                    $horaIngreso = $this->horaRealista($fechaBase);
                    $duracion    = rand(15, 240);

                    $registros[] = [
                        'persona_id'    => $personas->random()->id,
                        'actividad_id'  => $actividad->id,
                        'locacion_id'   => $actividad->locacion_id,
                        'casillero_id'  => $this->casilleroHistorico($todosCasilleros),
                        'hora_ingreso'  => $horaIngreso,
                        'hora_salida'   => $horaIngreso->copy()->addMinutes($duracion),
                        'duracion'      => $duracion,
                        'metodo_acceso' => 'manual',
                        'estado'        => 'completado',
                        'created_at'    => $horaIngreso,
                        'updated_at'    => $horaIngreso->copy()->addMinutes($duracion),
                    ];
                }
            }

            foreach (array_chunk($registros, 1000) as $chunk) {
                Acceso::insert($chunk);
            }

            /*
            |------------------------------------------------------------------
            | Activos — asignación real con estado ocupado
            |------------------------------------------------------------------
            | Se toman los casilleros normales disponibles (shuffled).
            | Cuando se agotan, el resto va al casillero externo.
            | El externo nunca se marca ocupado.
            */
            $casillerosLibres = Casillero::where('id', '!=', $this->casilleroExternoId)
                ->where('estado', 'libre')
                ->pluck('id')
                ->shuffle();

            $personasDentro = $personas->shuffle()->take(min(rand(15, 60), $personas->count()));
            $activos        = [];
            $casilleroIndex = 0;

            foreach ($personasDentro as $persona) {
                $actividad   = $actividades->random();
                $horaIngreso = now()->subMinutes(rand(5, 240));

                // Asignar casillero libre si hay, si no el externo
                if ($casilleroIndex < $casillerosLibres->count()) {
                    $casilleroId = $casillerosLibres[$casilleroIndex++];
                    // Marcar como ocupado inmediatamente
                    Casillero::where('id', $casilleroId)->update(['estado' => 'ocupado']);
                } else {
                    $casilleroId = $this->casilleroExternoId;
                    // El externo nunca se ocupa
                }

                $activos[] = [
                    'persona_id'    => $persona->id,
                    'actividad_id'  => $actividad->id,
                    'locacion_id'   => $actividad->locacion_id,
                    'casillero_id'  => $casilleroId,
                    'hora_ingreso'  => $horaIngreso,
                    'hora_salida'   => null,
                    'duracion'      => null,
                    'metodo_acceso' => 'manual',
                    'estado'        => 'en_curso',
                    'created_at'    => $horaIngreso,
                    'updated_at'    => $horaIngreso,
                ];
            }

            Acceso::insert($activos);
        });
    }

    /**
     * Para históricos: 80% casillero real aleatorio, 20% externo.
     * Simula que no siempre había casilleros disponibles.
     */
    private function casilleroHistorico(array $casilleros): int
    {
        if (empty($casilleros) || rand(1, 100) > 80) {
            return $this->casilleroExternoId;
        }
        return $casilleros[array_rand($casilleros)];
    }

    /**
     * Genera una hora realista con picos de mañana y tarde.
     */
    private function horaRealista(Carbon $fecha): Carbon
    {
        $p = rand(1, 100);
        return match(true) {
            $p <= 55 => $fecha->copy()->setTime(rand(7, 11),  rand(0, 59)), // pico mañana 55%
            $p <= 90 => $fecha->copy()->setTime(rand(15, 20), rand(0, 59)), // pico tarde  35%
            default  => $fecha->copy()->setTime(rand(12, 14), rand(0, 59)), // horas bajas 10%
        };
    }
}
