<?php

namespace App\Http\Controllers\Admin\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Locacion;
use App\Services\Admin\AccesoReporteService;
use App\Services\Admin\AccesoHistoricoService;
use App\Services\Admin\AccesoEstadisticaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccesoReporteController extends Controller
{
    public function __construct(
        private AccesoReporteService $reporte,
        private AccesoHistoricoService $historico,
        private AccesoEstadisticaService $estadistica,
    ) {}

    public function resumen(): View
    {
        return view('admin.reportes.accesos.resumen', [
            'kpis'           => $this->reporte->kpis(),
            'flujoPorHora'   => $this->reporte->flujoPorHora(),
            'porLocacion'    => $this->reporte->porLocacion(),
            'porActividad'   => $this->reporte->porActividad(),
            'ultimosAccesos' => $this->reporte->ultimosAccesos(),
            'fecha'          => now('America/Bogota')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
        ]);
    }

    public function flujo(Request $request): View
    {
        $fecha      = $request->input('fecha', now('America/Bogota')->toDateString());
        $locacionId = $request->input('locacion_id');

        return view('admin.reportes.accesos.flujo', [
            'flujo'      => $this->historico->flujoPorHoraDetallado($fecha, $locacionId),
            'locaciones' => Locacion::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'fecha'      => $fecha,
            'locacionId' => $locacionId,
        ]);
    }

    public function historico(Request $request): View
    {
        $desde      = $request->input('desde', now('America/Bogota')->startOfMonth()->toDateString());
        $hasta      = $request->input('hasta', now('America/Bogota')->toDateString());
        $locacionId = $request->input('locacion_id');
        $estado     = $request->input('estado');
        $buscar     = $request->input('buscar');

        return view('admin.reportes.accesos.historico', [
            'kpis'       => $this->historico->kpisPeriodo($desde, $hasta, $locacionId),
            'grafica'    => $this->historico->ingresoPorDia($desde, $hasta, $locacionId),
            'accesos'    => $this->historico->historicoTabla($desde, $hasta, $locacionId, $estado, $buscar),
            'locaciones' => Locacion::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'desde'      => $desde,
            'hasta'      => $hasta,
            'locacionId' => $locacionId,
            'estado'     => $estado,
            'buscar'     => $buscar,
        ]);
    }

    public function actividadesUsadas(Request $request): View
    {
        $desde      = $request->input('desde', now('America/Bogota')->startOfMonth()->toDateString());
        $hasta      = $request->input('hasta', now('America/Bogota')->toDateString());
        $locacionId = $request->input('locacion_id');

        return view('admin.reportes.actividades.usadas', [
            'actividades' => $this->estadistica->actividadesMasUsadas($desde, $hasta, $locacionId),
            'grafica'     => $this->estadistica->actividadesGrafica($desde, $hasta, $locacionId),
            'locaciones'  => Locacion::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'desde'       => $desde,
            'hasta'       => $hasta,
            'locacionId'  => $locacionId,
        ]);
    }

    public function locacionesOcupacion(Request $request): View
    {
        $desde = $request->input('desde', now('America/Bogota')->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', now('America/Bogota')->toDateString());

        return view('admin.reportes.locaciones.ocupacion', [
            'kpis'      => $this->estadistica->kpisLocaciones($desde, $hasta),
            'ocupacion' => $this->estadistica->ocupacionPorLocacion($desde, $hasta),
            'grafica'   => $this->estadistica->flujoPorHoraYLocacion($desde, $hasta),
            'desde'     => $desde,
            'hasta'     => $hasta,
        ]);
    }
}
