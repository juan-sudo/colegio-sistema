<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentsExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(private ?string $type = null, private ?string $status = null) {}

    public function query()
    {
        $query = Payment::with('student');

        if ($this->type) {
            $query->where('type', $this->type);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderByDesc('due_date');
    }

    public function headings(): array
    {
        return ['Estudiante', 'Tipo', 'Monto', 'Descuento', 'Pagado', 'Saldo', 'Estado', 'Vencimiento', 'Fecha Pago', 'Método'];
    }

    public function map($payment): array
    {
        return [
            $payment->student->fullName() ?? '-',
            ucfirst($payment->type),
            $payment->amount,
            $payment->discount,
            $payment->paid,
            $payment->balance,
            ucfirst($payment->status),
            $payment->due_date,
            $payment->paid_date ?? '-',
            $payment->payment_method ?? '-',
        ];
    }
}
