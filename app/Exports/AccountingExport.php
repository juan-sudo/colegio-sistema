<?php

namespace App\Exports;

use App\Models\AccountingEntry;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountingExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private ?string $type = null, private ?string $dateFrom = null, private ?string $dateTo = null) {}

    public function query()
    {
        $query = AccountingEntry::query();

        if ($this->type) {
            $query->where('type', $this->type);
        }
        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }

        return $query->orderByDesc('date');
    }

    public function headings(): array
    {
        return ['Fecha', 'Tipo', 'Categoría', 'Descripción', 'Monto', 'Referencia'];
    }

    public function map($entry): array
    {
        return [
            $entry->date,
            ucfirst(str_replace('_', ' ', $entry->type)),
            $entry->category,
            $entry->description,
            $entry->amount,
            $entry->reference ?? '-',
        ];
    }
}
