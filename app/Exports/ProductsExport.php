<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom',
            'Référence',
            'Catégorie',
            'Description',
            'Prix (FCFA)',
            'Quantité',
            'Quantité Minimum',
            'Statut',
            'Valeur Stock',
            'Date Création'
        ];
    }

    public function map($product): array
    {
        $status = match(true) {
            $product->quantity == 0 => 'Rupture',
            $product->quantity <= $product->min_quantity => 'Stock Faible',
            default => 'En Stock'
        };

        return [
            $product->id,
            $product->name,
            $product->reference,
            $product->category ? $product->category->name : 'Aucune',
            $product->description ?: '-',
            number_format($product->price, 0, ',', ' '),
            $product->quantity,
            $product->min_quantity,
            $status,
            number_format($product->price * $product->quantity, 0, ',', ' '),
            $product->created_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2EFDA']
                ]
            ]
        ];
    }
} 