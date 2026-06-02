<?php

namespace App\Services\Admin;

use App\Models\Acceso;
use Illuminate\Support\Collection;

/**
 * KPIs y gráficas del dashboard diario.
 * Todas las consultas están acotadas a hoy (America/Bogota).
 */
class AccesoReporteService
{
    private string $hoy;

    public function __construct()
    {
        $this->hoy = now('America/Bogota')->toDateString();
    }

    /**
     * KPIs del día en una sola query: total, en curso, completados, duración promedio.
     */
    public function kpis(): array
    {
        $row = Acceso::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN estado = "en_curso" THEN 1 ELSE 0 END) as en_curso,
                SUM(CASE WHEN estado = "completado" THEN 1 ELSE 0 END) as completados,
                AVG(CASE WHEN estado = "completado" AND duracion IS NOT NULL THEN duracion END) as duracion_promedio
            ')
            ->where('hora_ingreso', '>=', $this->hoy . ' 00:00:00')
            ->where('hora_ingreso', '<=', $this->hoy . ' 23:59:59')
            ->first();

        return [
            'total'            => $row->total,
            'en_curso'         => (int) $row->en_curso,
            'completados'      => (int) $row->completados,
            'duracion_promedio' => $row->duracion_promedio
                ? round($row->duracion_promedio) . ' min'
                : '—',
        ];
    }

    /**
     * Ingresos agrupados por hora del día (0-23), listo para Chart.js.
     */
    public function flujoPorHora(): array
    {
        $registros = Acceso::query()
            ->selectRaw('HOUR(hora_ingreso) as hora, COUNT(*) as total')
            ->where('hora_ingreso', '>=', $this->hoy . ' 00:00:00')
            ->where('hora_ingreso', '<=', $this->hoy . ' 23:59:59')
            ->groupBy('hora')
            ->orderBy('hora')
            ->pluck('total', 'hora');

        $labels = [];
        $data   = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $data[]   = $registros[$h] ?? 0;
        }

        return compact('labels', 'data');
    }

    /**
     * Accesos por locación hoy — gráfica de dona o barras.
     */
    public function porLocacion(): array
    {
        $rows = Acceso::query()
            ->selectRaw('locacion.nombre as locacion, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->where('accesos.hora_ingreso', '>=', $this->hoy . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $this->hoy . ' 23:59:59')
            ->groupBy('locacion.nombre')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('locacion')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    /**
     * Actividades más registradas hoy.
     */
    public function porActividad(): array
    {
        $rows = Acceso::query()
            ->selectRaw('actividades.nombre as actividad, COUNT(*) as total')
            ->join('actividades', 'actividades.id', '=', 'accesos.actividad_id')
            ->where('accesos.hora_ingreso', '>=', $this->hoy . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $this->hoy . ' 23:59:59')
            ->groupBy('actividades.nombre')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->pluck('actividad')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    /**
     * Últimos N accesos del día con relaciones mínimas.
     */
    public function ultimosAccesos(int $limite = 10): Collection
    {
        return Acceso::query()
            ->select([
                'accesos.id',
                'accesos.persona_id',
                'accesos.locacion_id',
                'accesos.actividad_id',
                'accesos.hora_ingreso',
                'accesos.hora_salida',
                'accesos.duracion',
                'accesos.estado',
            ])
            ->with([
                'persona:id,primer_nombre,primer_apellido,doc_identidad',
                'locacion:id,nombre',
                'actividad:id,nombre',
            ])
            ->where('hora_ingreso', '>=', $this->hoy . ' 00:00:00')
            ->where('hora_ingreso', '<=', $this->hoy . ' 23:59:59')
            ->latest('hora_ingreso')
            ->limit($limite)
            ->get();
    }
}
