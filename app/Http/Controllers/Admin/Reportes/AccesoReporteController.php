<?php

namespace App\Http\Controllers\Admin\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Locacion;
use App\Services\Admin\ReporteAccesoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccesoReporteController extends Controller
{
    public function __construct(
        private ReporteAccesoService $service
    ) {}

    public function resumen(): View
    {
        return view('admin.reportes.accesos.resumen', [
            'kpis'          => $this->service->kpis(),
            'flujoPorHora'  => $this->service->flujoPorHora(),
            'porLocacion'   => $this->service->porLocacion(),
            'porActividad'  => $this->service->porActividad(),
            'ultimosAccesos' => $this->service->ultimosAccesos(),
            'fecha'         => now('America/Bogota')->isoFormat('dddd, D [de] MMMM [de] YYYY'),
        ]);
    }

    public function flujo(Request $request): View
    {
        $fecha      = $request->input('fecha', now('America/Bogota')->toDateString());
        $locacionId = $request->input('locacion_id');

        return view('admin.reportes.accesos.flujo', [
            'flujo'     => $this->service->flujoPorHoraDetallado($fecha, $locacionId),
            'locaciones'=> \App\Models\Locacion::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'fecha'     => $fecha,
            'locacionId'=> $locacionId,
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
            'kpis'      => $this->service->kpisPeriodo($desde, $hasta, $locacionId),
            'grafica'   => $this->service->ingresoPorDia($desde, $hasta, $locacionId),
            'accesos'   => $this->service->historicoTabla($desde, $hasta, $locacionId, $estado, $buscar),
            'locaciones'=> Locacion::activas()->orderBy('nombre')->get(['id', 'nombre']),
            'desde'     => $desde,
            'hasta'     => $hasta,
            'locacionId'=> $locacionId,
            'estado'    => $estado,
            'buscar'    => $buscar,
        ]);
    }

    public function actividadesUsadas(Request $request): View
    {
        $desde      = $request->input('desde', now('America/Bogota')->startOfMonth()->toDateString());
        $hasta      = $request->input('hasta', now('America/Bogota')->toDateString());
        $locacionId = $request->input('locacion_id');

        return view('admin.reportes.actividades.usadas', [
            'actividades' => $this->service->actividadesMasUsadas($desde, $hasta, $locacionId),
            'grafica'     => $this->service->actividadesGrafica($desde, $hasta, $locacionId),
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
            'kpis'      => $this->service->kpisLocaciones($desde, $hasta),
            'ocupacion' => $this->service->ocupacionPorLocacion($desde, $hasta),
            'grafica'   => $this->service->flujoPorHoraYLocacion($desde, $hasta),
            'desde'     => $desde,
            'hasta'     => $hasta,
        ]);
    }

}
