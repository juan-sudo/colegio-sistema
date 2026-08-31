@extends('layouts.admin')
@section('title', 'Pagos')
@section('content')
<h1 class="text-xl font-bold mb-4">Gestión de pagos</h1>
<div class="bg-white rounded shadow p-4 mb-4">
    <form method="GET" action="{{ route('admin.payments.index') }}" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="type" class="border rounded p-2">
                <option value="">Todos</option>
                <option value="matricula" {{ request('type') == 'matricula' ? 'selected' : '' }}>Matrícula</option>
                <option value="pension" {{ request('type') == 'pension' ? 'selected' : '' }}>Pensión</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Estado</label>
            <select name="status" class="border rounded p-2">
                <option value="">Todos</option>
                <option value="pendiente" {{ request('status') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="pagado" {{ request('status') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                <option value="vencido" {{ request('status') == 'vencido' ? 'selected' : '' }}>Vencido</option>
            </select>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filtrar</button>
        <a href="{{ route('admin.payments.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">+ Nuevo pago</a>
        <a href="{{ route('admin.reports.payments.export', request()->query()) }}" class="bg-emerald-600 text-white px-4 py-2 rounded">Exportar Excel</a>
    </form>
</div>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Estudiante</th>
            <th class="p-2 text-left">Tipo</th>
            <th class="p-2 text-left">Monto</th>
            <th class="p-2 text-left">Pagado</th>
            <th class="p-2 text-left">Saldo</th>
            <th class="p-2 text-left">Vence</th>
            <th class="p-2 text-left">Estado</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $payment)
        <tr class="border-t {{ $payment->is_overdue ? 'bg-red-50' : '' }}">
            <td class="p-2">{{ $payment->student->fullName() ?? '-' }}</td>
            <td class="p-2">{{ ucfirst($payment->type) }}</td>
            <td class="p-2">S/ {{ number_format($payment->amount, 2) }}</td>
            <td class="p-2">S/ {{ number_format($payment->paid, 2) }}</td>
            <td class="p-2">S/ {{ number_format($payment->balance, 2) }}</td>
            <td class="p-2">{{ $payment->due_date }}</td>
            <td class="p-2">
                <span class="px-2 py-1 rounded text-xs
                    {{ $payment->status == 'pagado' ? 'bg-green-100 text-green-800' : ($payment->is_overdue ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </td>
            <td class="p-2">
                <a href="{{ route('admin.payments.edit', $payment) }}" class="text-indigo-600">Editar</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $payments->links() }}
@endsection
