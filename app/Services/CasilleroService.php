<?php
namespace App\Services;

use App\Models\Casillero;
use Illuminate\Validation\ValidationException;

class CasilleroService
{
    /**
     * Asigna un casillero libre
     */
    public function asignar(): ?Casillero
    {
        $casillero = Casillero::query()
            ->select('id', 'codigo')
            ->where('estado', 'libre')
            ->lockForUpdate()
            ->first();

        if ($casillero->id !== 1) {
            $casillero->update(['estado' => 'ocupado']);
        }

        return $casillero;
    }

    /**
     * Libera un casillero
     */
    public function liberar(int $casilleroId): void
    {
        $casillero = Casillero::query()
            ->select('id', 'codigo')
            ->where('id', $casilleroId)
            ->lockForUpdate()
            ->first();

        $casillero->update([
            'estado' => 'libre'
        ]);
    }

    public function resumen(): array
    {
        $libres = Casillero::where('estado', 'libre')->count();
        $ocupados = Casillero::where('estado', 'ocupado')->count();
        $total = $libres + $ocupados;
        $porcentaje = $total > 0
            ? round(($ocupados / $total) * 100)
            : 0;

        return [
            'libres' => $libres,
            'ocupados' => $ocupados,
            'total' => $total,
            'porcentaje' => $porcentaje
        ];
    }

    public function listar()
    {
        return Casillero::orderBy('codigo')->get();
    }

    public function mapa()
    {
        return Casillero::with([
            'acceso.persona',
            'acceso.actividad'
        ])
            ->orderBy('id')
            ->get()
            ->groupBy(function ($casillero) {

                preg_match('/^[A-Za-z]+/', $casillero->codigo, $matches);

                return $matches[0] ?? 'General';
            })
            ->map(function ($grupo) {

                return $grupo->map(function ($casillero) {

                    $acceso = $casillero->acceso;

                    return [
                        'id' => $casillero->id,
                        'codigo' => $casillero->codigo,
                        'estado' => $casillero->estado,

                        'persona' => $acceso?->persona?->nombre_completo,
                        'actividad' => $acceso?->actividad?->nombre,

                        'hora_ingreso' => $acceso?->hora_ingreso
                            ? $acceso->hora_ingreso->format('h:i A')
                            : null,
                    ];
                });
            });
    }
}
