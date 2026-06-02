<?php

namespace App\Services;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Login completo
     */
    public function login(string $doc, string $password): Usuario
    {
        $usuario = $this->validarCredenciales($doc, $password);

        Auth::login($usuario);

        $this->registrarAcceso($usuario);

        return $usuario;
    }

    /**
     * Validación de credenciales
     */
    private function validarCredenciales(string $doc, string $password): Usuario
    {
        $usuario = Usuario::with('persona', 'rol')
            ->whereHas('persona', function ($q) use ($doc) {
                $q->where('doc_identidad', $doc);
            })
            ->first();

        if (!$usuario) {
            throw ValidationException::withMessages([
                'doc_identidad' => 'Credenciales incorrectas.'
            ]);
        }

        if (!$usuario->verificarPassword($password)) {
            throw ValidationException::withMessages([
                'password' => 'Credenciales incorrectas.'
            ]);
        }

        if (!$usuario->esActivo()) {
            throw ValidationException::withMessages([
                'doc_identidad' => 'Usuario inactivo.'
            ]);
        }

        return $usuario;
    }

    /**
     * Post-login
     */
    private function registrarAcceso(Usuario $usuario): void
    {
        $usuario->update([
            'ultimo_acceso' => now()
        ]);
    }

    /**
     * Logout completo
     */
    public function logout(): void
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
