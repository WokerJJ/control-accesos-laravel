<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AjustesController extends Controller
{
    public function index(): View
    {
        $usuario = Auth::user()->load([
            'persona:id,primer_nombre,segundo_nombre,primer_apellido,segundo_apellido,doc_identidad,email,celular,direccion,municipio_id',
            'persona.municipio:id,nombre,departamento_id',
            'persona.municipio.departamento:id,nombre',
            'rol:id,nombre_rol',
        ]);

        return view('admin.ajustes.index', [
            'usuario'    => $usuario,
            'persona'    => $usuario->persona,
            'municipios' => \App\Models\Municipio::with('departamento:id,nombre')
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'departamento_id']),
        ]);
    }

    public function actualizar(Request $request): RedirectResponse
    {
        $request->validate([
            'email'        => 'nullable|email|max:150',
            'celular'      => 'nullable|string|max:20',
            'direccion'    => 'nullable|string|max:200',
            'municipio_id' => 'nullable|exists:municipios,id',
        ]);

        Auth::user()->persona->update([
            'email'        => $request->email,
            'celular'      => $request->celular,
            'direccion'    => $request->direccion,
            'municipio_id' => $request->municipio_id ?: null,
        ]);

        return redirect()->route('admin.ajustes')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    public function cambiarPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_actual' => 'required',
            'password'        => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
        ]);

        $usuario = Auth::user();

        if (!Hash::check($request->password_actual, $usuario->password_hash)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.'])
                ->withInput();
        }

        $usuario->update(['password_hash' => Hash::make($request->password)]);

        return redirect()->route('admin.ajustes.index')
            ->with('success', 'Contraseña actualizada correctamente.');
    }
}
