<?php

namespace App\Imports;

use App\Models\Rol;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RolesImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $creados  = 0;
    public int $omitidos = 0;

    public function rules(): array
    {
        return [
            'nombre_rol'   => 'required|string|max:50',
            'descripcion'  => 'required|string',
            'estado'       => 'nullable|in:activo,inactivo',
        ];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $nombre = trim($row['nombre_rol']);

            if (Rol::where('nombre_rol', $nombre)->exists()) {
                $this->omitidos++;
                continue;
            }

            Rol::create([
                'nombre_rol'  => $nombre,
                'descripcion' => trim($row['descripcion']),
                'estado'      => trim($row['estado'] ?? 'activo'),
            ]);

            $this->creados++;
        }
    }
}
