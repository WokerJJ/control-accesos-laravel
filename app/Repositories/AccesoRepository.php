<?php

// app/Repositories/AccesoRepository.php
namespace App\Repositories;

use App\Models\Acceso;
use Illuminate\Support\Facades\DB;

class AccesoRepository
{
    public function totalHoy(): int
    {
        return Acceso::whereDate('created_at', today())->count();
    }

    public function totalMes(): int
    {
        return Acceso::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function porDia(int $dias = 7): array
    {
        return Acceso::selectRaw('DATE(created_at) as dia, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays($dias))
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia')
            ->toArray();
    }

    /**
     * Cuenta accesos activos (en_curso)
     */
    public function totalActivos(): int
    {
        return Acceso::activos()->count();
    }

    /**
     * Obtiene detalle de accesos activos con solo los datos necesarios
     */
    public function obtenerActivosDetalle(): array
    {
        return Acceso::query()
            ->select('id', 'persona_id', 'actividad_id', 'casillero_id', 'hora_ingreso')
            ->with([
                'persona:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido',
                'actividad:id,nombre',
                'casillero:id,codigo',
            ])
            ->activos()
            ->orderBy('hora_ingreso', 'desc')
            ->get()
            ->map(function ($acceso) {
                return [
                    'nombre' => $acceso->persona->nombre_completo,
                    'actividad' => $acceso->actividad->nombre ?? null,
                    'hora_entrada' => $acceso->hora_ingreso?->format('h:i A'),
                    'casillero' => $acceso->casillero->codigo ?? null,
                ];
            })
            ->toArray();
    }

    public function accesosPorDia(int $dias = 7): array
    {
        $datos = $this->porDia($dias);

        $resultado = [];

        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $resultado[$fecha] = $datos[$fecha] ?? 0;
        }

        return $resultado;
    }
}
