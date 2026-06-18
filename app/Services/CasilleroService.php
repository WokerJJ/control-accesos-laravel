<?php
namespace App\Services;

use App\Models\Casillero;
use Illuminate\Validation\ValidationException;

class CasilleroService
{
    /**
     * Asigna un casillero libre.
     *
     * Prioriza casilleros normales (no externos).
     * Solo asigna el casillero externo (EXT-00) cuando
     * todos los demás están ocupados.
     */
    public function asignar(): ?Casillero
    {
        $externoId = config('acceso.casillero_externo_id', 1);

        // 1) Intentar un casillero normal libre
        $casillero = Casillero::query()
            ->select('id', 'codigo')
            ->where('estado', 'libre')
            ->where('id', '!=', $externoId)
            ->lockForUpdate()
            ->first();

        // 2) Si no hay normales, usar el externo
        if (!$casillero) {
            $casillero = Casillero::query()
                ->select('id', 'codigo')
                ->where('id', $externoId)
                ->lockForUpdate()
                ->first();
        }

        // Marcar como ocupado solo si no es el externo
        if ($casillero && $casillero->id !== $externoId) {
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
        $externoId = config('acceso.casillero_externo_id', 1);

        $casilleros = Casillero::orderBy('id')->get();

        // Conteo de usos históricos por casillero (solo accesos completados)
        $usosPorCasillero = \App\Models\Acceso::select('casillero_id', \DB::raw('COUNT(*) as total_usos'))
            ->whereNotNull('casillero_id')
            ->where('estado', 'completado')
            ->groupBy('casillero_id')
            ->pluck('total_usos', 'casillero_id');

        return $casilleros
            ->groupBy(function ($casillero) {
                preg_match('/^[A-Za-z]+/', $casillero->codigo, $matches);
                return $matches[0] ?? 'General';
            })
            ->map(function ($grupo) use ($usosPorCasillero, $externoId) {
                return $grupo->map(function ($casillero) use ($usosPorCasillero, $externoId) {
                    $acceso = $casillero->acceso;
                    $esExterno = $casillero->id === $externoId;

                    return [
                        'id'          => $casillero->id,
                        'codigo'      => $casillero->codigo,
                        'estado'      => $casillero->estado,
                        'es_externo'  => $esExterno,
                        'total_usos'  => $usosPorCasillero->get($casillero->id, 0),
                        'persona'     => $esExterno ? null : ($acceso?->persona?->nombre_completo),
                        'actividad'   => $esExterno ? null : ($acceso?->actividad?->nombre),
                        'hora_ingreso' => $esExterno ? null : ($acceso?->hora_ingreso?->format('h:i A')),
                    ];
                });
            });
    }
}
