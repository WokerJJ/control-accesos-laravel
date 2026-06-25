<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ImportarEstudiantes extends Command
{
    protected $signature   = 'importar:estudiantes {archivo : Ruta al archivo .csv}';
    protected $description = 'Importa personas y usuarios desde un archivo CSV del registro academico';

    public function handle(): int
    {
        $archivo = $this->argument('archivo');

        if (!file_exists($archivo)) {
            $this->error("Archivo no encontrado: {$archivo}");
            return self::FAILURE;
        }

        $this->info("Importando: {$archivo}");
        $this->newLine();

        return $this->importarCsv($archivo);
    }

    private function importarCsv(string $archivo): int
    {
        $handle = fopen($archivo, 'r');
        if (!$handle) {
            $this->error("No se pudo abrir el archivo.");
            return self::FAILURE;
        }

        $lineas = [];
        while (($linea = fgets($handle)) !== false) {
            $lineas[] = $linea;
        }
        fclose($handle);

        if (empty($lineas)) {
            $this->error("Archivo vacio.");
            return self::FAILURE;
        }

        $delimitador = str_contains($lineas[0], "\t") ? "\t" : ",";

        // Buscar encabezados reales
        $idxEncabezado = 0;
        foreach ($lineas as $i => $linea) {
            if (str_contains($linea, 'Documento Identidad') || str_contains($linea, 'Nombres')) {
                $idxEncabezado = $i;
                break;
            }
        }

        // Parsear encabezados y filtrar vacios
        $encabezadoRaw = str_getcsv($lineas[$idxEncabezado], $delimitador);
        $encabezado = [];
        foreach ($encabezadoRaw as $pos => $col) {
            $limpio = trim(mb_strtolower($col));
            if ($limpio !== '') {
                $encabezado[$pos] = $limpio;
            }
        }

        $this->info("Columnas: " . implode(', ', array_values($encabezado)));
        $this->newLine();

        $requeridas = ['nombres', 'apellidos', 'documento identidad'];
        $faltantes  = array_diff($requeridas, array_values($encabezado));
        if (!empty($faltantes)) {
            $this->error("Faltan columnas: " . implode(', ', $faltantes));
            return self::FAILURE;
        }

        $now = now()->toDateTimeString();

        // ── FASE 1: Recopilar datos en memoria ────────────────────
        $programasMap = []; // codigo => id
        $tipoIdMap    = []; // abreviatura => id
        $docsExistentes = DB::table('personas')->pluck('doc_identidad')->flip()->toArray();
        $rolesMap     = DB::table('roles')->pluck('id', 'nombre_rol')->toArray();

        $rolId     = $rolesMap['Usuario'] ?? $rolesMap[array_key_first($rolesMap)] ?? null;
        $personas  = [];
        $usuarios  = [];
        $omitidos  = 0;
        $errores   = 0;
        $fila      = 0;

        // Programa externo por defecto
        $externo = DB::table('programas_academicos')->where('codigo', '0003')->first();
        if (!$externo) {
            $id = DB::table('programas_academicos')->insertGetId([
                'codigo' => '0003', 'nombre' => 'Externo', 'tipo' => 'externo',
                'estado' => 'activo', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $externo = (object) ['id' => $id];
            $programasMap['0003'] = $id;
        } else {
            $programasMap['0003'] = $externo->id;
        }

        for ($i = $idxEncabezado + 1; $i < count($lineas); $i++) {
            $fila++;
            $datos = str_getcsv($lineas[$i], $delimitador);
            if (!$datos || empty(array_filter($datos))) continue;

            $row = [];
            foreach ($encabezado as $pos => $nombre) {
                $row[$nombre] = trim($datos[$pos] ?? '');
            }

            $apellidos = $row['apellidos'] ?? '';
            $nombres   = $row['nombres'] ?? $row['nombre'] ?? '';
            $documento = $row['documento'] ?? $row['documento identidad'] ?? $row['doc_identidad'] ?? '';

            if (empty($nombres) || empty($apellidos) || empty($documento)) {
                $errores++;
                continue;
            }

            // ── Programa academico ─────────────────────────────
            $codPrograma = $row['programa academico'] ?? $row['cod_programa'] ?? '';
            if (!empty($codPrograma) && !isset($programasMap[$codPrograma])) {
                $existente = DB::table('programas_academicos')->where('codigo', $codPrograma)->value('id');
                if ($existente) {
                    $programasMap[$codPrograma] = $existente;
                } else {
                    $nombreProg = $row['nombre programa'] ?? $row['nombre_programa'] ?? $codPrograma;
                    $programasMap[$codPrograma] = DB::table('programas_academicos')->insertGetId([
                        'codigo' => $codPrograma, 'nombre' => $nombreProg,
                        'tipo' => 'carrera', 'estado' => 'activo',
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }
            $progId = $programasMap[$codPrograma] ?? $externo->id;

            // ── Limpiar documento ──────────────────────────────
            $docLimpio = trim(preg_replace('/^[A-Z\.]+\s*/u', '', trim($documento)));
            if (isset($docsExistentes[$docLimpio])) {
                $omitidos++;
                continue;
            }
            $docsExistentes[$docLimpio] = true; // Marcar para evitar duplos internos

            // ── Tipo identificacion ────────────────────────────
            preg_match('/^([A-Z\.]+)\s*/u', trim($documento), $match);
            $abrev = strtoupper(str_replace('.', '', $match[1] ?? 'CC'));
            if (!isset($tipoIdMap[$abrev])) {
                $existente = DB::table('tipo_identificacion')->where('abreviatura', $abrev)->value('id');
                if ($existente) {
                    $tipoIdMap[$abrev] = $existente;
                } else {
                    $tipoIdMap[$abrev] = DB::table('tipo_identificacion')->insertGetId([
                        'abreviatura' => $abrev, 'descripcion' => $abrev,
                        'created_at' => $now, 'updated_at' => $now,
                    ]);
                }
            }

            // ── Preparar persona ───────────────────────────────
            $partesA = explode(' ', trim($apellidos), 2);
            $partesN = explode(' ', trim($nombres), 2);
            $email   = trim($row['email'] ?? '');
            $celular = preg_replace('/\D/', '', $row['celular'] ?? '');
            $telefono = preg_replace('/\D/', '', $row['telefono'] ?? '');
            $direccion = $row['direccion'] ?? $row['dirección'] ?? '';

            $personas[] = [
                'tipo_identificacion_id' => $tipoIdMap[$abrev],
                'doc_identidad'          => $docLimpio,
                'primer_apellido'        => strtoupper($partesA[0] ?? ''),
                'segundo_apellido'       => strtoupper($partesA[1] ?? ''),
                'primer_nombre'          => strtoupper($partesN[0] ?? ''),
                'segundo_nombre'         => strtoupper($partesN[1] ?? ''),
                'email'                  => $email ? strtolower($email) : null,
                'celular'                => substr($celular ?: $telefono, 0, 10) ?: null,
                'direccion'              => $direccion ?: null,
                'programa_academico_id'  => $progId,
                'codigo_institucional'   => trim($row['codigo'] ?? '') ?: 'EXTERNO',
                'estado'                 => 'activo',
                'fecha_registro'         => now()->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($rolId) {
                $usuarios[] = [
                    'doc'           => $docLimpio,
                    'rol_id'        => $rolId,
                    'ultimo_acceso' => $now,
                    'estado'        => 'activo',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // ── FASE 2: Insertar por lotes ────────────────────────────
        $personasCreadas = 0;
        $usuariosCreados = 0;

        DB::beginTransaction();
        try {
            // Insertar personas en lotes de 500
            $chunks = array_chunk($personas, 500);
            foreach ($chunks as $chunk) {
                DB::table('personas')->insert($chunk);
                $personasCreadas += count($chunk);
            }

            // Obtener IDs de personas recien creadas y asociar usuarios
            if (!empty($usuarios)) {
                $todosDocs = array_column($personas, 'doc_identidad');
                $personaIds = DB::table('personas')
                    ->whereIn('doc_identidad', $todosDocs)
                    ->pluck('id', 'doc_identidad')
                    ->toArray();

                $usuariosConId = [];
                foreach ($usuarios as $u) {
                    $pid = $personaIds[$u['doc']] ?? null;
                    if ($pid) {
                        $u['password_hash'] = Hash::make($u['doc']);
                        unset($u['doc']);
                        $u['persona_id'] = $pid;
                        $usuariosConId[] = $u;
                    }
                }

                $uChunks = array_chunk($usuariosConId, 500);
                foreach ($uChunks as $uChunk) {
                    DB::table('usuarios')->insert($uChunk);
                    $usuariosCreados += count($uChunk);
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            return self::FAILURE;
        }

        // ── Resumen ───────────────────────────────────────────────
        $programasCreados = count($programasMap);
        $this->newLine();
        $this->info('=========================================');
        $this->info('  RESUMEN DE IMPORTACION');
        $this->info('=========================================');
        $this->info("  Programas:    {$programasCreados}");
        $this->info("  Personas:     {$personasCreadas}");
        $this->info("  Usuarios:     {$usuariosCreados}");
        if ($omitidos > 0) {
            $this->warn("  Omitidos:     {$omitidos}");
        }
        if ($errores > 0) {
            $this->warn("  Errores:      {$errores}");
        }
        $this->info('=========================================');
        $this->info("Importacion completada.");

        return self::SUCCESS;
    }
}
