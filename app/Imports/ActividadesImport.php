<?php

namespace App\Imports;

use App\Models\Actividad;
use App\Models\TipoActividad;
use App\Models\Locacion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ActividadesImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $creados  = 0;
    public int $omitidos = 0;
    public array $errores = [];

    /** Tipos válidos de la columna "tipo" */
    private const TIPOS_VALIDOS = ['fija', 'programada', 'personalizada'];

    /** Estados válidos de la columna "estado" */
    private const ESTADOS_VALIDOS = ['en_curso', 'pendiente', 'finalizada', 'cancelada'];

    public function rules(): array
    {
        return [
            'tipo_actividad' => 'required|string',
            'locacion'       => 'required|string',
            'nombre'         => 'required|string|max:150',
            'descripcion'    => 'nullable|string',
            'tipo'           => 'nullable|in:fija,programada,personalizada',
            'estado'         => 'nullable|in:en_curso,pendiente,finalizada,cancelada',
            'fecha_inicio'   => 'nullable|date',
            'fecha_fin'      => 'nullable|date|after_or_equal:fecha_inicio',
            'hora_inicio'    => 'nullable|date_format:H:i',
            'hora_fin'       => 'nullable|date_format:H:i',
        ];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $tipoNombre = trim($row['tipo_actividad']);
            $locNombre  = trim($row['locacion']);
            $nombre     = trim($row['nombre']);

            // ── Verificar que el tipo de actividad exista ──────────
            $tipoActividad = TipoActividad::where('nombre', $tipoNombre)->first();
            if (!$tipoActividad) {
                $this->errores[] = "Fila " . ($i + 2) . ": Tipo de actividad '{$tipoNombre}' no encontrado.";
                continue;
            }

            // ── Verificar que la locación exista ───────────────────
            $locacion = Locacion::where('nombre', $locNombre)->first();
            if (!$locacion) {
                $this->errores[] = "Fila " . ($i + 2) . ": Locación '{$locNombre}' no encontrada.";
                continue;
            }

            // ── Evitar duplicados por nombre + locación ────────────
            if (Actividad::where('nombre', $nombre)
                ->where('locacion_id', $locacion->id)
                ->exists()
            ) {
                $this->omitidos++;
                continue;
            }

            $tipo   = in_array($row['tipo'] ?? '', self::TIPOS_VALIDOS)
                ? $row['tipo'] : 'programada';
            $estado = in_array($row['estado'] ?? '', self::ESTADOS_VALIDOS)
                ? $row['estado'] : 'pendiente';

            Actividad::create([
                'tipo_actividad_id' => $tipoActividad->id,
                'locacion_id'       => $locacion->id,
                'nombre'            => $nombre,
                'descripcion'       => trim($row['descripcion'] ?? ''),
                'tipo'              => $tipo,
                'estado'            => $estado,
                'fecha_inicio'      => $row['fecha_inicio'] ?? null,
                'fecha_fin'         => $row['fecha_fin'] ?? null,
                'hora_inicio'       => $row['hora_inicio'] ?? null,
                'hora_fin'          => $row['hora_fin'] ?? null,
            ]);

            $this->creados++;
        }
    }
}
