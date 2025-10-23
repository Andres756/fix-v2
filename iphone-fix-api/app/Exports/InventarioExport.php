<?php

namespace App\Exports;

use App\Models\Inventario\Inventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class InventarioExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Inventario::with('categoria');

        if (!empty($this->filters['tipo_inventario_id'])) {
            $query->where('tipo_inventario_id', $this->filters['tipo_inventario_id']);
        }

        if (!empty($this->filters['activo'])) {
            $query->where('activo', $this->filters['activo']);
        }

        if (!empty($this->filters['fecha_desde']) && !empty($this->filters['fecha_hasta'])) {
            $query->whereBetween('created_at', [$this->filters['fecha_desde'], $this->filters['fecha_hasta']]);
        }

        if (!empty($this->filters['filtro_stock'])) {
            switch ($this->filters['filtro_stock']) {
                case 'sin_stock':
                    $query->where('stock', '<=', 0);
                    break;
                case 'con_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'bajo_minimo':
                    $query->whereColumn('stock', '<', 'stock_minimo');
                    break;
            }
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            ['REPORTE DE INVENTARIO'], // ← título en la primera fila
            [
                'ID',
                'Nombre',
                'Código',
                'Categoría',
                'Stock',
                'Stock Mínimo',
                'Costo Unitario',
                'Precio Venta',
                'Tipo Impuesto',
                'Activo',
                'Fecha Creación',
            ]
        ];
    }

    public function map($inv): array
    {
        return [
            $inv->id,
            $inv->nombre,
            $inv->codigo,
            $inv->categoria->nombre ?? '',
            $inv->stock,
            $inv->stock_minimo,
            number_format($inv->costo, 2, ',', '.'),
            number_format($inv->precio, 2, ',', '.'),
            $inv->tipo_impuesto ?? 'N/A',
            $inv->activo ? 'Sí' : 'No',
            optional($inv->created_at)->format('Y-m-d'),
        ];
    }

    public function title(): string
    {
        return 'Inventario';
    }

    public function styles(Worksheet $sheet)
    {
        // 🔹 Título principal
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()
            ->setSize(14)->setBold(true)->getColor()->setARGB('C0392B');
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔹 Encabezados (segunda fila)
        $sheet->getStyle('A2:K2')->getFont()
            ->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A2:K2')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('C0392B');
        $sheet->getStyle('A2:K2')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // 🔹 Bordes y centrado general
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:K{$lastRow}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB('E0E0E0');

        $sheet->getStyle("A3:K{$lastRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
