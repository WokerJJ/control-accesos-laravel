<?php

namespace App\Services\Admin;

use App\Models\Acceso;
use Illuminate\Support\Collection;

/**
 * Reportes de período arbitrario: histórico, tendencia, flujo detallado.
 */
class AccesoHistoricoService
{
    /**
     * KPIs para un período arbitrario.
     */
    public function kpisPeriodo(string $desde, string $hasta, ?int $locacionId = null): array
    {
        $base = $this->periodoQuery($desde, $hasta, $locacionId);

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
     * Ingresos agrupados por día — gráfica de tendencia.
     */
    public function ingresoPorDia(string $desde, string $hasta, ?int $locacionId = null): array
    {
        $base = $this->periodoQuery($desde, $hasta, $locacionId);

        $registros = $base
            ->selectRaw('DATE(hora_ingreso) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');

        $labels  = [];
        $data    = [];
        $current = \Carbon\Carbon::parse($desde);
        $fin     = \Carbon\Carbon::parse($hasta);

        while ($current->lte($fin)) {
            $key      = $current->toDateString();
            $labels[] = $current->isoFormat('D MMM');
            $data[]   = $registros[$key] ?? 0;
            $current->addDay();
        }

        return compact('labels', 'data');
    }

    /**
     * Tabla paginada de histórico con filtros completos.
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
            ->where('accesos.hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('accesos.hora_ingreso', '<=', $hasta . ' 23:59:59')
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
     * Flujo por hora de un día específico con ingresos + salidas y franjas.
     */
    public function flujoPorHoraDetallado(string $fecha, ?int $locacionId = null): array
    {
        $base = Acceso::query()
            ->where('hora_ingreso', '>=', $fecha . ' 00:00:00')
            ->where('hora_ingreso', '<=', $fecha . ' 23:59:59');

        if ($locacionId) {
            $base->where('locacion_id', $locacionId);
        }

        $ingresos = (clone $base)
            ->selectRaw('HOUR(hora_ingreso) as hora, COUNT(*) as total')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        $salidas = (clone $base)
            ->selectRaw('HOUR(hora_salida) as hora, COUNT(*) as total')
            ->whereNotNull('hora_salida')
            ->groupBy('hora')
            ->pluck('total', 'hora');

        $labels  = [];
        $dataIn  = [];
        $dataOut = [];

        for ($h = 0; $h < 24; $h++) {
            $labels[]  = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $dataIn[]  = $ingresos[$h] ?? 0;
            $dataOut[] = $salidas[$h]  ?? 0;
        }

        $horaMax = $ingresos->sortDesc()->keys()->first();

        return [
            'labels'    => $labels,
            'ingresos'  => $dataIn,
            'salidas'   => $dataOut,
            'hora_pico' => $horaMax !== null
                ? str_pad($horaMax, 2, '0', STR_PAD_LEFT) . ':00'
                : '—',
            'total'   => $ingresos->sum(),
            'franjas' => $this->calcularFranjas($ingresos),
        ];
    }

    /**
     * Query base para un período con filtros comunes.
     */
    private function periodoQuery(string $desde, string $hasta, ?int $locacionId = null): \Illuminate\Database\Eloquent\Builder
    {
        $base = Acceso::query()
            ->where('hora_ingreso', '>=', $desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $hasta . ' 23:59:59');

        if ($locacionId) {
            $base->where('locacion_id', $locacionId);
        }

        return $base;
    }

    /**
     * Agrupa horas en franjas legibles.
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
}
