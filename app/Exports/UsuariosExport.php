<?php

declare(strict_types=1);

namespace App\Exports;

use App\Helpers\ExcelHelper;
use App\Models\Usuario;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsuariosExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping, WithStyles, WithTitle
{
    /**
     * Recibe la ordenación y los filtros ya validados para aplicarlos a la consulta de exportación.
     */
    public function __construct(
        private array $ordenacion = [],
        private ?string $nombreCompleto = null,
        private ?string $email = null,
    ) {}

    /**
     * Devuelve la colección de usuarios a exportar aplicando filtros, ordenación recibida y, finalmente, id descendente.
     */
    public function collection(): Collection
    {
        return Usuario::query()
            ->byNombreCompleto($this->nombreCompleto)
            ->byEmail($this->email)
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
     * Mapea cada usuario a la fila de campos que se escribe en el Excel, neutralizando posibles fórmulas en cada celda.
     *
     * @param  Usuario  $usuario
     */
    public function map($usuario): array
    {
        return [
            ExcelHelper::sanearFormula($usuario->nombre),
            ExcelHelper::sanearFormula($usuario->primer_apellido),
            ExcelHelper::sanearFormula($usuario->segundo_apellido),
            ExcelHelper::sanearFormula($usuario->email),
        ];
    }

    /**
     * Fuerza por seguridad que cualquier celda con valor string se escriba como TYPE_STRING para que Excel nunca la interprete como fórmula, manteniendo el tipo nativo para int, float, bool, null y fechas.
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        // Solo los strings pueden contener una fórmula; los marcamos explícitamente como texto para anular cualquier intento de formula injection
        if (is_string($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // Para el resto de tipos delegamos en el binder por defecto que asigna TYPE_NUMERIC, TYPE_BOOL, TYPE_NULL, etc.
        return parent::bindValue($cell, $value);
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
