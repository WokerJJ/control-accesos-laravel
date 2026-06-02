<?php

// app/Services/EstadisticasService.php
namespace App\Services;

use App\Repositories\AccesoRepository;
use App\Models\Usuario;
use App\Models\Calificacion;

class EstadisticasService
{
    public function __construct(
        private AccesoRepository $accesos,
        private CasilleroService $casilleros
    ) {}

    public function resumen(): array
    {
        return cache()->remember('estadisticas.resumen', now()->addSecond(5), function () {
            return [
                'total_usuarios' => Usuario::count(),
                'accesos_hoy' => $this->accesos->totalHoy(),
                'activos' => $this->accesos->totalActivos(),
                'personas_dentro' => $this->accesos->obtenerActivosDetalle(),
                'accesos_mes' => $this->accesos->totalMes(),
                'accesos_por_dia'  => $this->accesos->accesosPorDia(7),
                'casilleros'       => $this->casilleros->resumen(),
                'promedio_calificacion' => $this->calcularPromedio()
            ];
        });
    }

    private function calcularPromedio(): float
    {
        $data = Calificacion::selectRaw('
        AVG(servicio) as servicio,
        AVG(atencion) as atencion,
        AVG(lugar) as lugar,
        AVG(calidad) as calidad
    ')->first();

        return round((
                ($data->servicio ?? 0) +
                ($data->atencion ?? 0) +
                ($data->lugar ?? 0) +
                ($data->calidad ?? 0)
            ) / 4, 1);
    }
}
