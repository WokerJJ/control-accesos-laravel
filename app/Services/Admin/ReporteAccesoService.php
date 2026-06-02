<?php

namespace App\Services\Admin;

use App\Models\Acceso;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Servicio de reportes para accesos del día.
 * Todas las consultas están acotadas a hoy (America/Bogota).
 */
class ReporteAccesoService
{
    private string $hoy;

    public function __construct()
    {
        $this->hoy = now('America/Bogota')->toDateString();
    }

    // ═══════════════════════════════════════════════
    // KPIs principales
    // ═══════════════════════════════════════════════

    /**
     * Totales del día: ingresos, en curso, completados, duración promedio
     */
    public function kpis(): array
    {
        $base = Acceso::whereDate('hora_ingreso', $this->hoy);

        $total      = (clone $base)->count();
        $enCurso    = (clone $base)->where('estado', 'en_curso')->count();
        $completados = (clone $base)->where('estado', 'completado')->count();

        // Duración promedio solo de completados (en minutos)
        $duracionPromedio = (clone $base)
            ->where('estado', 'completado')
            ->whereNotNull('duracion')
            ->avg('duracion');

        return [
            'total'            => $total,
            'en_curso'         => $enCurso,
            'completados'      => $completados,
            'duracion_promedio' => $duracionPromedio
                ? round($duracionPromedio) . ' min'
                : '—',
        ];
    }

    // ═══════════════════════════════════════════════
    // Gráfica — flujo por hora
    // ═══════════════════════════════════════════════

    /**
     * Ingresos agrupados por hora del día (0-23).
     * Retorna array listo para Chart.js: labels + data.
     */
    public function flujoPorHora(): array
    {
        $registros = Acceso::query()
            ->selectRaw('HOUR(hora_ingreso) as hora, COUNT(*) as total')
            ->whereDate('hora_ingreso', $this->hoy)
            ->groupBy('hora')
            ->orderBy('hora')
            ->pluck('total', 'hora');

        // Rellenar las 24 horas aunque no haya datos
        $labels = [];
        $data   = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $data[]   = $registros[$h] ?? 0;
        }

        return compact('labels', 'data');
    }

    // ═══════════════════════════════════════════════
    // Distribuciones
    // ═══════════════════════════════════════════════

    /**
     * Accesos por locación hoy — para gráfica de dona o barras
     */
    public function porLocacion(): array
    {
        $rows = Acceso::query()
            ->selectRaw('locacion.nombre as locacion, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->whereDate('accesos.hora_ingreso', $this->hoy)
            ->groupBy('locacion.nombre')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $rows->pluck('locacion')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    /**
     * Actividades más registradas hoy
     */
    public function porActividad(): array
    {
        $rows = Acceso::query()
            ->selectRaw('actividades.nombre as actividad, COUNT(*) as total')
            ->join('actividades', 'actividades.id', '=', 'accesos.actividad_id')
            ->whereDate('accesos.hora_ingreso', $this->hoy)
            ->groupBy('actividades.nombre')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->pluck('actividad')->toArray(),
            'data'   => $rows->pluck('total')->toArray(),
        ];
    }

    // ═══════════════════════════════════════════════
    // Tabla — últimos accesos del día
    // ═══════════════════════════════════════════════

    /**
     * Últimos N accesos del día con relaciones mínimas
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
            ->whereDate('hora_ingreso', $this->hoy)
            ->latest('hora_ingreso')
            ->limit($limite)
            ->get();
    }

    /**
     * Flujo por hora de un día específico, opcionalmente filtrado por locación.
     * Retorna ingresos Y salidas separados para comparativa.
     */
    public function flujoPorHoraDetallado(string $fecha, ?int $locacionId = null): array
    {
        $base = Acceso::query()->whereDate('hora_ingreso', $fecha);

        if ($locacionId) {
            $base->where('locacion_id', $locacionId);
        }

        // Ingresos por hora
        $ingresos = (clone $base)
            ->selectRaw('HOUR(hora_ingreso) as hora, COUNT(*) as total')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        // Salidas por hora (solo completados)
        $salidas = (clone $base)
            ->selectRaw('HOUR(hora_salida) as hora, COUNT(*) as total')
            ->whereNotNull('hora_salida')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        $labels   = [];
        $dataIn   = [];
        $dataOut  = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[]  = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $dataIn[]  = $ingresos[$h] ?? 0;
            $dataOut[] = $salidas[$h]  ?? 0;
        }

        // Franja pico — hora con más ingresos
        $horaMax = $ingresos->sortDesc()->keys()->first();
        $franjas   = $this->calcularFranjas($ingresos);

        return [
            'labels'   => $labels,
            'ingresos' => $dataIn,
            'salidas'  => $dataOut,
            'hora_pico' => $horaMax !== null
                ? str_pad($horaMax, 2, '0', STR_PAD_LEFT) . ':00'
                : '—',
            'total'    => $ingresos->sum(),
            'franjas'  => $franjas,
        ];
    }

    /**
     * Agrupa las horas en franjas legibles con su volumen.
     */
    private function calcularFranjas(Collection $ingresos): array
    {
        $franjas = [
            'Madrugada (00-06)' => [0, 1, 2, 3, 4, 5],
            'Mañana (06-12)'    => [6, 7, 8, 9, 10, 11],
            'Tarde (12-18)'     => [12, 13, 14, 15, 16, 17],
            'Noche (18-24)'     => [18, 19, 20, 21, 22, 23],
        ];

        return collect($franjas)->map(function ($horas, $nombre) use ($ingresos) {
            $total = collect($horas)->sum(fn($h) => $ingresos[$h] ?? 0);
            return ['nombre' => $nombre, 'total' => $total];
        })->values()->toArray();
    }

    /**
     * KPIs para un período arbitrario.
     */
    public function kpisPeriodo(string $desde, string $hasta, ?int $locacionId = null): array
    {
        $base = Acceso::query()
            ->whereDate('hora_ingreso', '>=', $desde)
            ->whereDate('hora_ingreso', '<=', $hasta);

        if ($locacionId) {
            $base->where('locacion_id', $locacionId);
        }

        $total       = (clone $base)->count();
        $completados = (clone $base)->where('estado', 'completado')->count();
        $enCurso     = (clone $base)->where('estado', 'en_curso')->count();
        $duracion    = (clone $base)
            ->whereNotNull('duracion')
            ->where('estado', 'completado')
            ->avg('duracion');

        return [
            'total'             => $total,
            'completados'       => $completados,
            'en_curso'          => $enCurso,
            'duracion_promedio' => $duracion ? round($duracion) . ' min' : '—',
        ];
    }

    /**
     * Ingresos agrupados por día — para gráfica de tendencia.
     */
    public function ingresoPorDia(string $desde, string $hasta, ?int $locacionId = null): array
    {
        $base = Acceso::query()
            ->whereDate('hora_ingreso', '>=', $desde)
            ->whereDate('hora_ingreso', '<=', $hasta);

        if ($locacionId) {
            $base->where('locacion_id', $locacionId);
        }

        $registros = $base
            ->selectRaw('DATE(hora_ingreso) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');

        // Rellenar todos los días del rango aunque no tengan datos
        $labels = [];
        $data   = [];
        $current = \Carbon\Carbon::parse($desde);
        $fin     = \Carbon\Carbon::parse($hasta);

        while ($current->lte($fin)) {
            $key     = $current->toDateString();
            $labels[] = $current->isoFormat('D MMM');
            $data[]   = $registros[$key] ?? 0;
            $current->addDay();
        }

        return compact('labels', 'data');
    }

    /**
     * Tabla paginada con filtros completos.
     */
    public function historicoTabla(
        string $desde,
        string $hasta,
        ?int $locacionId = null,
        ?string $estado = null,
        ?string $buscar = null,
        int $porPagina = 15
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {

        return Acceso::query()
            ->select([
                'accesos.id',
                'accesos.persona_id',
                'accesos.locacion_id',
                'accesos.actividad_id',
                'accesos.hora_ingreso',
                'accesos.hora_salida',
                'accesos.duracion',
                'accesos.metodo_acceso',
                'accesos.estado',
            ])
            ->with([
                'persona:id,primer_nombre,primer_apellido,doc_identidad',
                'locacion:id,nombre',
                'actividad:id,nombre',
            ])
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
            ->when($locacionId, fn($q) => $q->where('accesos.locacion_id', $locacionId))
            ->when($estado,     fn($q) => $q->where('accesos.estado', $estado))
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('persona', function ($q) use ($buscar) {
                    $q->where('doc_identidad',    'like', "%{$buscar}%")
                        ->orWhere('primer_nombre',  'like', "%{$buscar}%")
                        ->orWhere('primer_apellido','like', "%{$buscar}%");
                });
            })
            ->latest('accesos.hora_ingreso')
            ->paginate($porPagina)
            ->withQueryString();
    }

    /**
     * Actividades más registradas en accesos, con filtros opcionales.
     * Incluye conteo, porcentaje y última vez usada.
     */
    public function actividadesMasUsadas(
        string $desde,
        string $hasta,
        ?int $locacionId = null,
        int $limite = 10
    ): Collection {
        $total = Acceso::query()
            ->whereDate('hora_ingreso', '>=', $desde)
            ->whereDate('hora_ingreso', '<=', $hasta)
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
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
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
                    ? \Carbon\Carbon::parse($row->ultimo_uso)
                        ->isoFormat('D MMM · H:mm')
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
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
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
            ->whereDate('hora_ingreso', '>=', $desde)
            ->whereDate('hora_ingreso', '<=', $hasta)
            ->count();

        $locacionMasUsada = Acceso::query()
            ->selectRaw('locacion.nombre, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
            ->groupBy('locacion.nombre')
            ->orderByDesc('total')
            ->first();

        $horasPico = Acceso::query()
            ->selectRaw('locacion.nombre, HOUR(accesos.hora_ingreso) as hora, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
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
    public function ocupacionPorLocacion(string $desde, string $hasta): \Illuminate\Support\Collection
    {
        $total = Acceso::query()
            ->whereDate('hora_ingreso', '>=', $desde)
            ->whereDate('hora_ingreso', '<=', $hasta)
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
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
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
     * Flujo por hora desglosado por locación — para gráfica apilada.
     */
    public function flujoPorHoraYLocacion(string $desde, string $hasta): array
    {
        $rows = Acceso::query()
            ->selectRaw('locacion.nombre as locacion, HOUR(accesos.hora_ingreso) as hora, COUNT(*) as total')
            ->join('locacion', 'locacion.id', '=', 'accesos.locacion_id')
            ->whereDate('accesos.hora_ingreso', '>=', $desde)
            ->whereDate('accesos.hora_ingreso', '<=', $hasta)
            ->groupBy('locacion.nombre', 'hora')
            ->orderBy('hora')
            ->get();

        // Agrupar por locación
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
