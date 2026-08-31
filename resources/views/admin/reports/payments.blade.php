@extends('layouts.admin')
@section('title', 'Reporte de pagos')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">💰 Reporte de pagos</h1>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
            ← Volver a reportes
        </a>
    </div>

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-blue-600">S/ {{ number_format($stats['total_amount'], 2) }}</p>
            <p class="text-sm text-blue-700">Total cobrado</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-green-600">S/ {{ number_format($stats['total_paid'], 2) }}</p>
            <p class="text-sm text-green-700">Total pagado</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">S/ {{ number_format($stats['total_balance'], 2) }}</p>
            <p class="text-sm text-yellow-700">Saldo pendiente</p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-gray-600">{{ $stats['total_count'] }}</p>
            <p class="text-sm text-gray-700">Total registros</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Estudiante</th>
                    <th class="p-2 text-left">Tipo</th>
                    <th class="p-2 text-right">Monto</th>
                    <th class="p-2 text-right">Descuento</th>
                    <th class="p-2 text-right">Pagado</th>
                    <th class="p-2 text-right">Saldo</th>
                    <th class="p-2 text-center">Estado</th>
                    <th class="p-2 text-left">Vencimiento</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr class="border-t">
                    <td class="p-2">{{ $payment->student->fullName() ?? '-' }}</td>
                    <td class="p-2">{{ ucfirst($payment->type) }}</td>
                    <td class="p-2 text-right">S/ {{ number_format($payment->amount, 2) }}</td>
                    <td class="p-2 text-right">S/ {{ number_format($payment->discount, 2) }}</td>
                    <td class="p-2 text-right">S/ {{ number_format($payment->paid, 2) }}</td>
                    <td class="p-2 text-right">S/ {{ number_format($payment->balance, 2) }}</td>
                    <td class="p-2 text-center">
                        <span class="px-2 py-1 rounded text-xs
                            {{ $payment->status == 'pagado' ? 'bg-green-100 text-green-800' : ($payment->status == 'pendiente' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="p-2">{{ $payment->due_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection