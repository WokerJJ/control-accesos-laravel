<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Services\Admin\UsuarioAdminService;
use Illuminate\View\View;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private UsuarioAdminService $usuarioService
    ) {}

    public function index(Request $request): View
    {
        return view('admin.usuarios.index', [
            'stats'    => $this->usuarioService->obtenerStats(),
            'usuarios' => $this->usuarioService->obtenerListado($request),
            'roles'    => \App\Models\Rol::orderBy('nombre_rol')->get(['id', 'nombre_rol']),
            'municipios' => \App\Models\Municipio::with('departamento:id,nombre')
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'departamento_id']),
        ]);
    }

    public function show(int $id): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
    {
        $user = Usuario::findOrFail($id);
        $usuario = $this->usuarioService->obtenerDetalle($user->persona_id);

        // Si piden JSON, devolver JSON
        if (request()->wantsJson() || request()->header('Accept') === 'application/json') {
            return response()->json([
                'id'           => $usuario->id,
                'usuario_id'   => $usuario->usuario_id,
                'email'        => $usuario->email,
                'celular'      => $usuario->celular,
                'direccion'    => $usuario->direccion,
                'municipio_id' => $usuario->municipio_id,
                'rol_id'       => $usuario->rol_id,
                'estado'       => $usuario->estado,
            ]);
        }

        return response()->view('admin.usuarios._detalle', compact('usuario'));
    }

    public function update(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        $user = Usuario::findOrFail($id);
        $usuario = $this->usuarioService->obtenerDetalle($user->persona_id);
        $request->validate([
            'email'        => 'nullable|email|max:150',
            'celular'      => 'nullable|string|max:20',
            'direccion'    => 'nullable|string|max:200',
            'municipio_id' => 'nullable|exists:municipio,id',
            'rol_id'       => 'required|exists:roles,id',
            'estado'       => 'required|in:activo,inactivo',
        ]);

        $this->usuarioService->actualizar($usuario->id, $request->all());

        return response()->json(['ok' => true, 'mensaje' => 'Usuario actualizado correctamente.']);
    }
}
