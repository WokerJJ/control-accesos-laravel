<?php

namespace App\Services\Admin;

use App\Models\Acceso;
use App\Models\AccesoOlvidado;
use Illuminate\Support\Facades\DB;
use App\Models\Casillero;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AccesoAdminService
{
    public function obtenerStats(): array
    {
        $hoy = now('America/Bogota')->toDateString();

        $row = Acceso::query()
            ->selectRaw('
                SUM(CASE WHEN hora_ingreso >= ? AND hora_ingreso <= ? THEN 1 ELSE 0 END) as hoy,
                SUM(CASE WHEN estado = "en_curso" THEN 1 ELSE 0 END) as en_curso,
                SUM(CASE WHEN hora_salida >= ? AND hora_salida <= ? THEN 1 ELSE 0 END) as salidas
            ', [$hoy . ' 00:00:00', $hoy . ' 23:59:59', $hoy . ' 00:00:00', $hoy . ' 23:59:59'])
            ->first();

        return [
            'hoy'               => (int) $row->hoy,
            'en_curso'          => (int) $row->en_curso,
            'salidas'           => (int) $row->salidas,
            'casilleros_ocupados' => Casillero::where('estado', 'ocupado')->count(),
        ];
    }

    public function obtenerListado(array $filtros): LengthAwarePaginator
    {
        // Default: mostrar hoy si no se especifica fecha
        $filtros['fecha'] = $filtros['fecha'] ?? 'hoy';

        return Acceso::query()
            ->with([
                'persona:id,doc_identidad,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
                'actividad:id,nombre',
                'casillero:id,codigo',   // ← para el modal
                'locacion:id,nombre',    // ← para el modal
            ])
            ->when($filtros['estado'] ?? null,
                fn($q, $estado) => $q->where('estado', $estado)
            )
            ->when($filtros['fecha'] ?? null,
                function ($q, $fecha) {
                    match($fecha) {
                        'hoy'    => $q->where('hora_ingreso', '>=', now('America/Bogota')->startOfDay()->toDateTimeString())
                                      ->where('hora_ingreso', '<=', now('America/Bogota')->endOfDay()->toDateTimeString()),
                        'semana' => $q->where('hora_ingreso', '>=', now()->startOfWeek()),
                        'mes'    => $q->whereMonth('hora_ingreso', now()->month),
                        default  => null,
                    };
                }
            )
            ->when($filtros['buscar'] ?? null,
                function ($q, $buscar) {
                    $q->whereHas('persona', function ($persona) use ($buscar) {
                        $persona->where('doc_identidad', 'like', "%{$buscar}%")
                            ->orWhereRaw(
                                "CONCAT(primer_nombre,' ',primer_apellido) LIKE ?",
                                ["%{$buscar}%"]
                            );
                    });
                }
            )
            ->latest('hora_ingreso')
            ->paginate(15)
            ->withQueryString();
    }
    public function cierreDiario(): int
    {
        return DB::transaction(function () {

            $ahora = now('America/Bogota');

            $accesos = Acceso::query()
                ->with('casillero')
                ->where('estado', 'en_curso')
                ->get();

            foreach ($accesos as $acceso) {

                AccesoOlvidado::create([
                    'acceso_id' => $acceso->id,
                    'hora_cierre_forzado' => $ahora,
                    'motivo' => 'cierre_diario',
                ]);

                if ($acceso->casillero) {

                    $acceso->casillero->update([
                        'estado' => 'libre',
                    ]);
                }
                $acceso->update([
                    'estado' => 'completado',
                    'hora_salida' => $ahora,
                ]);
            }

            return $accesos->count();
        });
    }

    public function verificarCierrePendiente(): void
    {
        if (
            Cache::has('cierre_diario_verificado_' . today()->toDateString())
        ) {
            return;
        }

        $hayPendientes = Acceso::query()
            ->where('estado', 'en_curso')
            ->where('hora_ingreso', '<', now('America/Bogota')->startOfDay()->toDateTimeString())
            ->exists();

        if ($hayPendientes) {
            $this->cierreDiario();
        }

        Cache::put(
            'cierre_diario_verificado_' . today()->toDateString(),
            true,
            now()->endOfDay()
        );
    }
}

