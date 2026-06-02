<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CrearActividadProgramadaRequest;
use App\Models\Actividad;
use App\Services\ActividadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function __construct(
        private ActividadService $actividadService
    ) {}

    public function index(): View
    {
        return view('admin.actividades.index', [
            'eventos'         => $this->actividadService->obtenerEventosCalendario(),
            'stats'           => $this->actividadService->resumen(),
            'tiposActividad'  => $this->actividadService->obtenerTiposActividad(),
            'locaciones'      => $this->actividadService->obtenerLocaciones(),
        ]);
    }

    public function programar(CrearActividadProgramadaRequest $request): RedirectResponse
    {
        $this->actividadService->crearProgramada($request->validated());

        return redirect()->route('admin.actividades.index')
            ->with('mensaje', [
                'tipo'  => 'success',
                'texto' => 'Actividad creada correctamente.',
            ]);
    }
    public function actualizar(CrearActividadProgramadaRequest $request, Actividad $actividad): RedirectResponse
    {
        $this->actividadService->actualizarProgramada($actividad, $request->validated());

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad actualizada correctamente.');
    }

    public function eliminar(Actividad $actividad): RedirectResponse
    {
        // Solo cancelar — nunca borrar registros con accesos asociados
        $this->actividadService->cancelarProgramada($actividad);

        return redirect()->route('admin.actividades.index')
            ->with('success', 'Actividad cancelada correctamente.');
    }


}
