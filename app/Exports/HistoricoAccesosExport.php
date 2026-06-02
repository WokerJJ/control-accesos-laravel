<?php

namespace App\Exports;

use App\Models\Acceso;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HistoricoAccesosExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnWidths,
    WithColumnFormatting
{
    public function __construct(
        private string  $desde,
        private string  $hasta,
        private ?int    $locacionId = null,
        private ?string $estado     = null,
        private ?string $buscar     = null,
    ) {}

    /** Query base — mismos filtros que la vista */
    public function query()
    {
        return Acceso::query()
            ->with([
                'persona:id,primer_nombre,primer_apellido,doc_identidad',
                'locacion:id,nombre',
                'actividad:id,nombre',
            ])
            ->where('hora_ingreso', '>=', $this->desde . ' 00:00:00')
            ->where('hora_ingreso', '<=', $this->hasta . ' 23:59:59')
            ->when($this->locacionId, fn($q) => $q->where('locacion_id', $this->locacionId))
            ->when($this->estado,     fn($q) => $q->where('estado', $this->estado))
            ->when($this->buscar, function ($q) {
                $q->whereHas('persona', function ($q) {
                    $q->where('doc_identidad',    'like', "%{$this->buscar}%")
                        ->orWhere('primer_nombre',  'like', "%{$this->buscar}%")
                        ->orWhere('primer_apellido','like', "%{$this->buscar}%");
                });
            })
            ->latest('hora_ingreso');
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,  // ← columna Documento
        ];
    }

    /** Encabezados de columna */
    public function headings(): array
    {
        return [
            '#',
            'Persona',
            'Documento',
            'Actividad',
            'Locación',
            'Ingreso',
            'Salida',
            'Duración (min)',
            'Método',
            'Estado',
        ];
    }

    /** Mapeo de cada fila */
    public function map($acceso): array
    {
        static $i = 0;
        $i++;

        return [
            $i,
            $acceso->persona->primer_nombre . ' ' . $acceso->persona->primer_apellido,
            (string) $acceso->persona->doc_identidad,  // ← cast explícito
            $acceso->actividad->nombre,
            $acceso->locacion->nombre,
            $acceso->hora_ingreso?->format('d/m/Y H:i'),
            $acceso->hora_salida?->format('H:i') ?? '—',
            $acceso->duracion ?? '—',
            ucfirst($acceso->metodo_acceso),
            $acceso->estado === 'en_curso' ? 'En curso' : 'Completado',
        ];
    }

    /** Estilo encabezado */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '007BFF']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    /** Anchos fijos — reemplaza ShouldAutoSize (mucho más rápido) */
    public function columnWidths(): array
    {
        return [
            'A' => 6,   // #
            'B' => 28,  // Persona
            'C' => 16,  // Documento
            'D' => 25,  // Actividad
            'E' => 20,  // Locación
            'F' => 18,  // Ingreso
            'G' => 10,  // Salida
            'H' => 14,  // Duración
            'I' => 12,  // Método
            'J' => 14,  // Estado
        ];
    }

    public function title(): string
    {
        return 'Histórico ' . $this->desde . ' a ' . $this->hasta;
    }
}
