<?php

namespace App\Imports;

use App\Models\Casillero;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CasillerosImport implements ToCollection, WithHeadingRow, WithValidation
{
    public int $creados  = 0;
    public int $omitidos = 0;

    private const ESTADOS_VALIDOS = ['libre', 'ocupado', 'mantenimiento'];

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:10',
            'estado' => 'nullable|string',
        ];
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $codigo = trim($row['codigo']);

            if (Casillero::where('codigo', $codigo)->exists()) {
                $this->omitidos++;
                continue;
            }

            $estado = strtolower(trim($row['estado'] ?? 'libre'));
            if (!in_array($estado, self::ESTADOS_VALIDOS)) {
                $estado = 'libre';
            }

            Casillero::create([
                'codigo' => $codigo,
                'estado' => $estado,
            ]);

            $this->creados++;
        }
    }
}
