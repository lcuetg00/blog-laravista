<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Usuario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsuariosExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * Recibe la ordenación ya validada (clave URL → dirección) para aplicarla a la consulta de exportación.
     */
    public function __construct(private array $ordenacion = []) {}

    /**
     * Devuelve la colección de usuarios a exportar aplicando la ordenación recibida y, finalmente, id descendente.
     */
    public function collection(): Collection
    {
        return Usuario::query()
            ->byOrdenacion($this->ordenacion)
            ->orderByDesc('id')
            ->get(['nombre', 'primer_apellido', 'segundo_apellido', 'email']);
    }

    /**
     * Cabeceras traducidas que aparecerán en la primera fila del Excel.
     */
    public function headings(): array
    {
        return [
            trans('fields.input.nombre'),
            trans('fields.input.primer_apellido'),
            trans('fields.input.segundo_apellido'),
            trans('fields.input.email'),
        ];
    }

    /**
     * Mapea cada usuario a la fila de campos que se escribe en el Excel.
     *
     * @param  Usuario  $usuario
     */
    public function map($usuario): array
    {
        return [
            $usuario->nombre,
            $usuario->primer_apellido,
            $usuario->segundo_apellido,
            $usuario->email,
        ];
    }

    /**
     * Aplica estilo a la primera fila (secondary-strong con texto blanco en negrita) y al resto de filas (fondo blanco).
     */
    public function styles(Worksheet $sheet): array
    {
        // Calculamos el rango de filas de datos para pintarlas con fondo blanco
        $ultimaFila = $sheet->getHighestRow();
        $ultimaColumna = $sheet->getHighestColumn();

        if ($ultimaFila > 1) {
            $sheet->getStyle("A2:{$ultimaColumna}{$ultimaFila}")->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFFFFF'],
                ],
            ]);
        }

        return [
            1 => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6D28D9'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
    }

    /**
     * Nombre traducido de la hoja del Excel exportado.
     */
    public function title(): string
    {
        return trans('fields.usuarios.titulo');
    }
}
