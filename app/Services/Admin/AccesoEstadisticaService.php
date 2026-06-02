<?php

namespace App\Services\Admin;

use App\Models\Acceso;
use Illuminate\Support\Collection;

/**
 * Estadísticas para reportes de actividades y locaciones.
 */
class AccesoEstadisticaService
{
    /**
     * Actividades más registradas con métricas completas.
     */
    public function actividadesMasUsadas(
        string $desde,
        string $hasta,
        ?int $locacionId = null,
        int $limite = 10
    ): Collection {
        $total = Acceso::query()
            ->where('hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->when($locacionId, fn($q) => $q->where('locacion_id', $locacionId))
            ->count();

        return Acceso::query()
            ->selectRaw('
                actividades.id,
                actividades.nombre,
                actividades.tipo,
                locacion.nombre as locacion,
                COUNT(accesos.id)        as total_usos,
                MAX(accesos.hora_ingreso) as ultimo_uso,
                AVG(accesos.duracion)    as duracion_promedio
            ')
            ->join('actividades', 'actividades.id', '=', 'accesos.actividad_id')
            ->leftJoin('locacion', 'locacion.id', '=', 'actividades.locacion_id')
            ->where('accesos.hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->when($locacionId, fn($q) => $q->where('accesos.locacion_id', $locacionId))
            ->groupBy('actividades.id', 'actividades.nombre', 'actividades.tipo', 'locacion.nombre')
            ->orderByDesc('total_usos')
            ->limit($limite)
            ->get()
            ->map(fn($row) => (object) [
                'id'                => $row->id,
                'nombre'            => $row->nombre,
                'tipo'              => $row->tipo,
                'locacion'          => $row->locacion ?? '—',
                'total_usos'        => $row->total_usos,
                'porcentaje'        => $total > 0 ? round($row->total_usos * 100 / $total) : 0,
                'ultimo_uso'        => $row->ultimo_uso
                    ? \Carbon\Carbon::parse($row->ultimo_uso)->isoFormat('D MMM · H:mm')
                    : '—',
                'duracion_promedio' => $row->duracion_promedio
                    ? round($row->duracion_promedio) . ' min'
                    : '—',
            ]);
    }

    /**
     * Top actividades para gráfica — simplificado.
     */
    public function actividadesGrafica(string $desde, string $hasta, ?int $locacionId = null): array
    {
        $rows = Acceso::query()
            ->selectRaw('actividades.nombre as nombre, COUNT(*) as total')
            ->join('actividades', 'actividades.id', '=', 'accesos.actividad_id')
            ->where('accesos.hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->when($locacionId, fn($q) => $q->where('accesos.locacion_id', $locacionId))
            ->groupBy('actividades.nombre')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->pluck('nombre')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    /**
     * KPIs generales de locaciones en el período.
     */
    public function kpisLocaciones(string $desde, string $hasta): array
    {
        $totalAccesos = Acceso::query()
            ->where('hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->count();

        $locacionMasUsada = Acceso::query()
            ->selectRaw('locacion.nombre, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->where('hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->groupBy('locacion.nombre')
            ->orderByDesc('total')
            ->first();

        $horasPico = Acceso::query()
            ->selectRaw('locacion.nombre, HOUR(accesos.hora_ingreso) as hora, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->where('hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->groupBy('locacion.nombre', 'hora')
            ->orderByDesc('total')
            ->first();

        return [
            'total_accesos'     => $totalAccesos,
            'locacion_top'      => $locacionMasUsada?->nombre ?? '—',
            'locacion_top_usos' => $locacionMasUsada?->total  ?? 0,
            'hora_pico_global'  => $horasPico
                ? str_pad($horasPico->hora, 2, '0', STR_PAD_LEFT) . ':00 · ' . $horasPico->nombre
                : '—',
        ];
    }

    /**
     * Ocupación por locación — ranking con métricas completas.
     */
    public function ocupacionPorLocacion(string $desde, string $hasta): Collection
    {
        $total = Acceso::query()
            ->where('hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->count();

        return Acceso::query()
            ->selectRaw('
                locacion.id,
                locacion.nombre,
                COUNT(accesos.id)                           as total_accesos,
                SUM(CASE WHEN accesos.estado = "en_curso" THEN 1 ELSE 0 END) as en_curso,
                AVG(accesos.duracion)                       as duracion_promedio,
                MAX(accesos.hora_ingreso)                   as ultimo_acceso,
                COUNT(DISTINCT DATE(accesos.hora_ingreso))  as dias_activa
            ')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->where('accesos.hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->groupBy('locacion.id', 'locacion.nombre')
            ->orderByDesc('total_accesos')
            ->get()
            ->map(fn($row) => (object) [
                'id'                => $row->id,
                'nombre'            => $row->nombre,
                'total_accesos'     => $row->total_accesos,
                'en_curso'          => $row->en_curso,
                'porcentaje'        => $total > 0 ? round($row->total_accesos * 100 / $total) : 0,
                'duracion_promedio' => $row->duracion_promedio ? round($row->duracion_promedio) . ' min' : '—',
                'ultimo_acceso'     => $row->ultimo_acceso
                    ? \Carbon\Carbon::parse($row->ultimo_acceso)->isoFormat('D MMM · H:mm')
                    : '—',
                'dias_activa'       => $row->dias_activa,
            ]);
    }

    /**
     * Flujo por hora desglosado por locación — gráfica apilada.
     */
    public function flujoPorHoraYLocacion(string $desde, string $hasta): array
    {
        $rows = Acceso::query()
            ->selectRaw('locacion.nombre as locacion, HOUR(accesos.hora_ingreso) as hora, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->where('accesos.hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $hasta . ' 23:59:59')
            ->groupBy('locacion.nombre', 'hora')
            ->orderBy('hora')
            ->get();

        $locaciones = $rows->pluck('locacion')->unique()->values();
        $labels     = array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00', range(0, 23));

        $datasets = $locaciones->map(function ($locacion, $i) use ($rows) {
            $colores = ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997'];
            $porHora = $rows->where('locacion', $locacion)->pluck('total', 'hora');
            return [
                'label'           => $locacion,
                'data'            => array_map(fn($h) => $porHora[$h] ?? 0, range(0, 23)),
                'backgroundColor' => $colores[$i % count($colores)],
                'borderWidth'     => 1,
                'borderRadius'    => 3,
            ];
        })->values()->toArray();

        return compact('labels', 'datasets');
    }
}
