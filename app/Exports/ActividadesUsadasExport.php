<?php

namespace App\Exports;

use App\Models\Acceso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActividadesUsadasExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    public function __construct(
        private \Illuminate\Support\Collection $actividades,
        private string $desde,
        private string $hasta,
    ) {}

    public function collection(): \Illuminate\Support\Collection
    {
        return $this->actividades;
    }

    public function headings(): array
    {
        return ['#', 'Actividad', 'Tipo', 'Locación', 'Total usos', 'Participación (%)', 'Último uso', 'Duración prom.'];
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $row->nombre,
            ucfirst($row->tipo),
            $row->locacion,
            $row->total_usos,
            $row->porcentaje . '%',
            $row->ultimo_uso,
            $row->duracion_promedio,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '28A745']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'Actividades ' . $this->desde . ' a ' . $this->hasta;
    }
}
