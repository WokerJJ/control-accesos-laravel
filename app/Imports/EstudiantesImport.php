<?php

namespace App\Imports;

use App\Models\Persona;
use App\Models\ProgramaAcademico;
use App\Models\TipoIdentificacion;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EstudiantesImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $programasCreados = 0;
    public int $personasCreadas  = 0;
    public int $usuariosCreados  = 0;
    public int $omitidos         = 0;

    public function rules(): array
    {
        return [
            'cod_programa'     => 'required|string|max:10',
            'nombre_programa'  => 'required|string|max:150',
            'apellidos'        => 'required|string',
            'nombres'          => 'required|string',
            'documento'        => 'required|string|max:20',
            'email'            => 'nullable|email|max:100',
            'celular'          => 'nullable|string|max:15',
            'telefono'         => 'nullable|string|max:15',
            'direccion'        => 'nullable|string|max:200',
            'codigo'           => 'nullable|string|max:20',
        ];
    }

    public function collection(Collection $rows): void
    {
        $rolId = \DB::table('roles')->where('nombre_rol', 'Usuario')->value('id');
        if (!$rolId) {
            $rolId = \DB::table('roles')->first()->id ?? null;
        }

        foreach ($rows as $row) {
            // ── 1. Programa académico — insertar si no existe ──────
            $programa = ProgramaAcademico::firstOrCreate(
                ['codigo' => trim($row['cod_programa'])],
                [
                    'nombre' => trim($row['nombre_programa']),
                    'tipo'   => 'carrera',
                    'estado' => 'activo',
                ]
            );

            if ($programa->wasRecentlyCreated) {
                $this->programasCreados++;
            }

            // ── 2. Limpiar documento ──────────────────────────────
            $documento  = trim($row['documento']);
            $docLimpio  = trim(preg_replace('/^[A-Z\\.]+\\s*/u', '', $documento));

            // Determinar abreviatura del tipo de identificación
            preg_match('/^([A-Z\\.]+)\\s/u', $documento, $match);
            $abreviatura = strtoupper(str_replace('.', '', $match[1] ?? 'CC'));
            $tipoId      = TipoIdentificacion::where('abreviatura', $abreviatura)
                ->value('id') ?? 1;

            // ── 3. Evitar duplicados por documento ────────────────
            if (Persona::where('doc_identidad', $docLimpio)->exists()) {
                $this->omitidos++;
                continue;
            }

            // ── 4. Separar apellidos y nombres ────────────────────
            $partesApellidos = explode(' ', trim($row['apellidos']), 2);
            $partesNombres   = explode(' ', trim($row['nombres']), 2);

            // ── 5. Crear persona ──────────────────────────────────
            $persona = Persona::create([
                'tipo_identificacion_id' => $tipoId,
                'doc_identidad'          => $docLimpio,
                'primer_apellido'        => $partesApellidos[0]  ?? '',
                'segundo_apellido'       => $partesApellidos[1]  ?? '',
                'primer_nombre'          => $partesNombres[0]    ?? '',
                'segundo_nombre'         => $partesNombres[1]    ?? '',
                'email'                  => strtolower(trim($row['email'] ?? '')),
                'celular'                => trim($row['celular'] ?? '') ?: trim($row['telefono'] ?? ''),
                'direccion'              => trim($row['direccion'] ?? ''),
                'programa_academico_id'  => $programa->id,
                'codigo_institucional'   => trim($row['codigo'] ?? '') ?: 'EXTERNO',
                'estado'                 => 'activo',
                'fecha_registro'         => now(),
            ]);

            $this->personasCreadas++;

            // ── 6. Crear usuario vinculado ────────────────────────
            if ($rolId) {
                Usuario::create([
                    'persona_id'    => $persona->id,
                    'rol_id'        => $rolId,
                    'password_hash' => Hash::make($docLimpio),
                    'ultimo_acceso' => now(),
                    'estado'        => 'activo',
                ]);

                $this->usuariosCreados++;
            }
        }
    }
}
