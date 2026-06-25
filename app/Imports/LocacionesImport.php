<?php

namespace App\Imports;

use App\Models\Locacion;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocacionesImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $creados  = 0;
    public int $omitidos = 0;

    public function rules(): array
    {
        return [
            'nombre'     => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'activa'     => 'nullable|string',
        ];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nombre = trim($row['nombre']);

            if (Locacion::where('nombre', $nombre)->exists()) {
                $this->omitidos++;
                continue;
            }

            $activaRaw = strtolower(trim($row['activa'] ?? 'true'));
            $activa    = in_array($activaRaw, ['1', 'true', 'si', 'activo']);

            Locacion::create([
                'nombre'     => $nombre,
                'descripcion' => trim($row['descripcion'] ?? ''),
                'activa'     => $activa,
            ]);

            $this->creados++;
        }
    }
}
