<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\ProgramaAcademico;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;

class RegistroService
{
    public function registrar(array $data): Persona
    {
        return DB::transaction(function () use ($data) {

            // Auto-resolver programa_academico_id según el área si no se envió
            $programaId = $data['programa_academico_id'] ?? null;
            if (!$programaId && !empty($data['area'])) {
                $tipoMap = [
                    'estudiante'     => 'carrera',
                    'profesor'       => 'profesor',
                    'administrativo' => 'administrativo',
                    'externo'        => 'externo',
                ];
                $tipo = $tipoMap[$data['area']] ?? null;
                if ($tipo) {
                    $prog = ProgramaAcademico::where('tipo', $tipo)
                        ->where('estado', 'activo')
                        ->first();
                    $programaId = $prog?->id;
                }
            }

            // Fallback: si aún no hay programa, buscar cualquiera activo por tipo
            if (!$programaId) {
                $prog = ProgramaAcademico::where('estado', 'activo')->first();
                $programaId = $prog?->id;
            }

            $persona = Persona::create([
                'tipo_identificacion_id' => $data['tipo_identificacion_id'],
                'doc_identidad' => $data['doc_identidad'],
                'primer_nombre' => $data['primer_nombre'],
                'primer_apellido' => $data['primer_apellido'],
                'segundo_nombre' => $data['segundo_nombre'] ?? null,
                'segundo_apellido' => $data['segundo_apellido'] ?? null,
                'email' => $data['email'] ?? null,
                'celular' => $data['celular'] ?? null,
                'programa_academico_id' => $programaId,
                'codigo_institucional'  => $data['codigo_institucional'] ?? 'EXTERNO',
                'municipio_id' => $data['municipio_id'] ?? null,
                'estado' => 'activo',
                'fecha_registro' => now(),
            ]);

            Usuario::create([
                'persona_id' => $persona->id,
                'rol_id' => 2,
                'password_hash' => bcrypt($data['doc_identidad']),
                'ultimo_acceso' => now(),
                'estado' => 'activo',
            ]);

            return $persona;
        });
    }
}
