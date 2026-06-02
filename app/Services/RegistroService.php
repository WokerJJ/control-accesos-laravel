<?php


namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class RegistroService
{
    public function registrar(array $data): Persona
    {
        return DB::transaction(function () use ($data) {

            $persona = Persona::create([
                'tipo_identificacion_id' => $data['tipo_identificacion_id'],
                'doc_identidad' => $data['doc_identidad'],
                'primer_nombre' => $data['primer_nombre'],
                'primer_apellido' => $data['primer_apellido'],
                'segundo_nombre' => $data['segundo_nombre'] ?? null,
                'segundo_apellido' => $data['segundo_apellido'] ?? null,
                'email' => $data['email'] ?? null,
                'celular' => $data['celular'] ?? null,
                'municipio_id' => $data['municipio_id'] ?? null,
                'estado' => 'activo',
                'fecha_registro' => now(),
            ]);

            Usuario::create([
                'persona_id' => $persona->id,
                'rol_id' => 2,
                'password_hash' => bcrypt($data['doc_identidad']), // la contraseña es la identificacion
                'ultimo_acceso' => now(),
                'estado' => 'activo',
            ]);

            return $persona;
        });
    }
}
