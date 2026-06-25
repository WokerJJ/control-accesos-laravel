<?php

namespace App\Http\Controllers\Admin\Reportes;

use App\Exports\HistoricoAccesosExport;
use App\Exports\ActividadesUsadasExport;
use App\Models\Acceso;
use App\Models\Locacion;
use App\Services\Admin\AccesoHistoricoService;
use App\Services\Admin\AccesoEstadisticaService;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LocacionesOcupacionExport;

class ExportController extends Controller
{
    public function __construct(
        private AccesoHistoricoService $historico,
        private AccesoEstadisticaService $estadistica,
    ) {}

    // ── Histórico ─────────────────────────────────

    public function historicoCsv(Request $request)
    {
        $params = $this->params($request);
        $nombre = 'historico_' . $params['desde'] . '_' . $params['hasta'] . '.xlsx';

        return Excel::download(
            new HistoricoAccesosExport(...$params),
            $nombre
        );
    }

    public function historicoPdf(Request $request)
    {
        $params  = $this->params($request);
        $baseQuery = Acceso::query()
            ->where('hora_ingreso', '>=', $params['desde'] . ' 00:00:00')
            ->where('hora_ingreso', '<=', $params['hasta'] . ' 23:59:59')
            ->when($params['locacionId'], fn($q) => $q->where('locacion_id', $params['locacionId']))
            ->when($params['estado'],     fn($q) => $q->where('estado', $params['estado']))
            ->when($params['buscar'], function ($q) use ($params) {
                $q->whereHas('persona', function ($q) use ($params) {
                    $q->where('doc_identidad',   'like', "%{$params['buscar']}%")
                        ->orWhere('primer_nombre', 'like', "%{$params['buscar']}%")
                        ->orWhere('primer_apellido', 'like', "%{$params['buscar']}%");
                });
            });

        $totalRegistros = $baseQuery->clone()->count();

        if ($totalRegistros > 1000) {
            return back()->with('mensaje', [
                'tipo'  => 'danger',
                'texto' => 'El rango seleccionado contiene ' . number_format($totalRegistros) . ' registros. '
                         . 'El límite para exportar a PDF es de 1,000 registros. '
                         . 'Intente reducir el rango de fechas o use los filtros para afinar la búsqueda.',
            ]);
        }

        try {
            $accesos = $baseQuery
                ->select([
                    'id', 'persona_id', 'locacion_id', 'actividad_id',
                    'estado', 'hora_ingreso', 'hora_salida', 'duracion'
                ])
                ->with([
                    'persona:id,primer_nombre,primer_apellido,doc_identidad',
                    'locacion:id,nombre',
                    'actividad:id,nombre',
                ])
                ->orderByDesc('hora_ingreso')
                ->get();

            $locacion = $params['locacionId']
                ? Locacion::find($params['locacionId'])?->nombre
                : null;

            $pdf = Pdf::loadView('exports.historico-pdf', [
                'accesos'  => $accesos,
                'kpis'     => $this->historico->kpisPeriodo($params['desde'], $params['hasta'], $params['locacionId']),
                'desde'    => $params['desde'],
                'hasta'    => $params['hasta'],
                'locacion' => $locacion,
            ])->setPaper('a4', 'landscape');

            return $pdf->download('historico_' . $params['desde'] . '_' . $params['hasta'] . '.pdf');
        } catch (\Throwable $e) {
            return back()->with('mensaje', [
                'tipo'  => 'danger',
                'texto' => 'Error al generar el PDF con ' . number_format($totalRegistros) . ' registros. '
                         . 'Intente reducir el rango de fechas o exportar a Excel en su lugar.',
            ]);
        }
    }

    // ── Actividades más usadas ─────────────────────

    public function actividadesCsv(Request $request)
    {
        $params      = $this->params($request);
        $actividades = $this->estadistica->actividadesMasUsadas(
            $params['desde'], $params['hasta'], $params['locacionId']
        );
        $nombre = 'actividades_' . $params['desde'] . '_' . $params['hasta'] . '.xlsx';

        return Excel::download(
            new ActividadesUsadasExport($actividades, $params['desde'], $params['hasta']),
            $nombre
        );
    }

    // ── Locaciones ──────────────────────────────────

    public function locacionesCsv(Request $request)
    {
        $params   = $this->params($request);
        $ocupacion = $this->estadistica->ocupacionPorLocacion($params['desde'], $params['hasta']);
        $nombre   = 'locaciones_' . $params['desde'] . '_' . $params['hasta'] . '.xlsx';

        return Excel::download(
            new LocacionesOcupacionExport($ocupacion, $params['desde'], $params['hasta']),
            $nombre
        );
    }

    public function locacionesPdf(Request $request)
    {
        $params   = $this->params($request);
        $ocupacion = $this->estadistica->ocupacionPorLocacion($params['desde'], $params['hasta']);

        $pdf = Pdf::loadView('exports.locaciones-pdf', [
            'ocupacion' => $ocupacion,
            'kpis'      => $this->estadistica->kpisLocaciones($params['desde'], $params['hasta']),
            'desde'     => $params['desde'],
            'hasta'     => $params['hasta'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('locaciones_' . $params['desde'] . '_' . $params['hasta'] . '.pdf');
    }

    // ── Helper compartido ─────────────────────────

    private function params(Request $request): array
    {
        return [
            'desde'      => $request->input('desde', now('America/Bogota')->startOfMonth()->toDateString()),
            'hasta'      => $request->input('hasta', now('America/Bogota')->toDateString()),
            'locacionId' => $request->input('locacion_id'),
            'estado'     => $request->input('estado'),
            'buscar'     => $request->input('buscar'),
        ];
    }
}
