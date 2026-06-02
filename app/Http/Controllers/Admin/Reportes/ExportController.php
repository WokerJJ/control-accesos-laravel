<?php

namespace App\Http\Controllers\Admin\Reportes;

use App\Exports\HistoricoAccesosExport;
use App\Exports\ActividadesUsadasExport;
use App\Models\Acceso;
use App\Models\Locacion;
use App\Services\Admin\ReporteAccesoService;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LocacionesOcupacionExport;

class ExportController extends Controller
{
    public function __construct(
        private ReporteAccesoService $service
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
        $accesos = Acceso::query()
            ->select([
                'id',
                'persona_id',
                'locacion_id',
                'actividad_id',
                'estado',
                'hora_ingreso',
                'hora_salida',
                'duracion'
            ])
            ->with([
                'persona:id,primer_nombre,primer_apellido,doc_identidad',
                'locacion:id,nombre',
                'actividad:id,nombre',
            ])
            ->where('hora_ingreso', '>=', $params['desde'] . ' 00:00:00')
            ->where('hora_ingreso', '<=', $params['hasta'] . ' 23:59:59')
            ->when(
                $params['locacionId'],
                fn($q) => $q->where('locacion_id', $params['locacionId'])
            )
            ->when(
                $params['estado'],
                fn($q) => $q->where('estado', $params['estado'])
            )
            ->orderByDesc('hora_ingreso')
            ->get();

        if ($accesos->count() > 1000) {
            return back()->with(
                'error',
                'El rango es demasiado grande para PDF.'
            );
        }

        $locacion = $params['locacionId']
            ? Locacion::find($params['locacionId'])?->nombre
            : null;

        $pdf = Pdf::loadView('exports.historico-pdf', [
            'accesos' => $accesos,
            'kpis'    => $this->service->kpisPeriodo(
                $params['desde'],
                $params['hasta'],
                $params['locacionId']
            ),
            'desde'    => $params['desde'],
            'hasta'    => $params['hasta'],
            'locacion' => $locacion,
        ])->setPaper('a4', 'landscape');
        return $pdf->download('historico_' . $params['desde'] . '_' . $params['hasta'] . '.pdf');
    }

    // ── Actividades más usadas ─────────────────────

    public function actividadesCsv(Request $request)
    {
        $params      = $this->params($request);
        $actividades = $this->service->actividadesMasUsadas(
            $params['desde'],
            $params['hasta'],
            $params['locacionId']
        );
        $nombre = 'actividades_' . $params['desde'] . '_' . $params['hasta'] . '.xlsx';

        return Excel::download(
            new ActividadesUsadasExport($actividades, $params['desde'], $params['hasta']),
            $nombre
        );
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

    public function locacionesCsv(Request $request)
    {
        $params   = $this->params($request);
        $ocupacion = $this->service->ocupacionPorLocacion($params['desde'], $params['hasta']);
        $nombre   = 'locaciones_' . $params['desde'] . '_' . $params['hasta'] . '.xlsx';

        return Excel::download(
            new LocacionesOcupacionExport($ocupacion, $params['desde'], $params['hasta']),
            $nombre
        );
    }

    public function locacionesPdf(Request $request)
    {
        $params   = $this->params($request);
        $ocupacion = $this->service->ocupacionPorLocacion($params['desde'], $params['hasta']);

        $pdf = Pdf::loadView('exports.locaciones-pdf', [
            'ocupacion' => $ocupacion,
            'kpis'      => $this->service->kpisLocaciones($params['desde'], $params['hasta']),
            'desde'     => $params['desde'],
            'hasta'     => $params['hasta'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download('locaciones_' . $params['desde'] . '_' . $params['hasta'] . '.pdf');
    }
}
