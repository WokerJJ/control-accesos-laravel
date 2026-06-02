<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Mostrar vista de login
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Procesar login
     */
    public function login(LoginRequest $request, AuthService $authService)
    {
        try {
            $authService->login(
                $request->doc_identidad,
                $request->password
            );

            return redirect()->route('admin.dashboard');

        } catch (ValidationException $e) {
            return back()
                ->withInput()
                ->withErrors($e->errors());
        }
    }

    /**
     * Cerrar sesión
     */
    public function logout(AuthService $authService)
    {
        $authService->logout();

        return redirect()->route('admin.login');
    }
}
