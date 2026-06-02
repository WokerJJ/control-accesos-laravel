<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdentificarRequest;

use App\Services\AccesoService;
use App\Services\IngresoService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccesoController extends Controller
{
    public function __construct(
        private AccesoService $accesoService,
        private IngresoService $ingresoService,
    ) {}

    //Vista principal

    public function index(): View
    {
        $this->limpiarSesionIngreso();

        return view('ingreso.index', [
            'accesos_activos' => $this->accesoService->totalActivos(),
        ]);
    }

    public function iniciarFlujo(string $tipo): RedirectResponse {

        session([
            'ingreso.tipo' => $tipo
        ]);

        return redirect()->route('ingreso.identificar');
    }

    //vista identificacion
    public function identificar():View|RedirectResponse
    {
        if (!session('ingreso.tipo')) {
            return redirect()->route('index');
        }

        return view('ingreso.identificar', [
            'tipo' => session('ingreso.tipo')
        ]);
    }

    //buscar usuario
    public function buscarUsuario(IdentificarRequest $request): RedirectResponse {
        try {
            $persona = $this->accesoService->identificarPersona($request->doc_identidad);
            session($persona);
            return $this->redirigirPorTipo();
        }
        catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    //confirmar acceso
    public function confirmacion():View|RedirectResponse
    {
        $accesoId = session('ingreso.acceso_id');

        if (!$accesoId) {
            return redirect()->route('index');
        }

        $acceso = $this->accesoService->obtenerParaConfirmacion($accesoId);

        return view(
            'ingreso.confirmacion',
            compact('acceso')
        );
    }

    //Helpers

    private function redirigirPorTipo():RedirectResponse
    {
        return match (session('ingreso.tipo')) {
            'salida' => redirect()->route('salida.index'),
            'registro' => redirect()->route('registro.create'),
            default => redirect()->route('actividad.index'),
        };
    }

    private function limpiarSesionIngreso():
    void
    {
        session()->forget([
            'ingreso.acceso_id'
        ]);
    }
}
