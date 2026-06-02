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
        return [
            'hoy' => Acceso::whereDate('hora_ingreso',today())->count(),
            'en_curso' => Acceso::where('estado','en_curso')->count(),
            'salidas' => Acceso::whereDate('hora_salida',today())->count(),
            'casilleros_ocupados' => Casillero::where('estado','ocupado')->count(),
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
                        'hoy'    => $q->whereDate('hora_ingreso', today()),
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
            ->whereDate('hora_ingreso', '<', today('America/Bogota'))
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

