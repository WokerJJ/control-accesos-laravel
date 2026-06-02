<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LocacionesOcupacionExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private \Illuminate\Support\Collection $ocupacion,
        private string $desde,
        private string $hasta,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->ocupacion;
    }

    public function headings(): array
    {
        return [
            '#', 'Locación', 'Total accesos', 'En curso',
            'Participación (%)', 'Días activa', 'Duración prom.', 'Último acceso',
        ];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->nombre,
            $row->total_accesos,
            $row->en_curso,
            $row->porcentaje . '%',
            $row->dias_activa,
            $row->duracion_promedio,
            $row->ultimo_acceso,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '17A2B8']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Locaciones ' . $this->desde . ' a ' . $this->hasta;
    }
}
