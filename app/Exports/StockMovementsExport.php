<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return StockMovement::with(['product', 'user'])->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Produit',
            'Type',
            'Quantité',
            'Avant',
            'Après',
            'Raison',
            'Référence',
            'Utilisateur',
            'Date'
        ];
    }

    public function map($movement): array
    {
        return [
            $movement->id,
            $movement->product->name,
            $movement->type_label,
            $movement->quantity,
            $movement->quantity_before,
            $movement->quantity_after,
            $movement->reason ?: '-',
            $movement->reference ?: '-',
            $movement->user->name,
            $movement->created_at->format('d/m/Y H:i')
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