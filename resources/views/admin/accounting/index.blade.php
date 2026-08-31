@extends('layouts.admin')
@section('title', 'Contabilidad')
@section('content')
<h1 class="text-xl font-bold mb-4">Registro contable</h1>
<div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Ingresos</p>
        <p class="text-2xl font-bold text-green-600">S/ {{ number_format($totalIncome, 2) }}</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Egresos</p>
        <p class="text-2xl font-bold text-red-600">S/ {{ number_format($totalExpense, 2) }}</p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-sm text-gray-500">Gastos fijos</p>
        <p class="text-2xl font-bold text-orange-600">S/ {{ number_format($totalFixedCost, 2) }}</p>
    </div>
</div>
<div class="bg-white rounded shadow p-4 mb-4">
    <form method="GET" action="{{ route('admin.accounting.index') }}" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm font-medium mb-1">Tipo</label>
            <select name="type" class="border rounded p-2">
                <option value="">Todos</option>
                <option value="ingreso" {{ request('type') == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                <option value="egreso" {{ request('type') == 'egreso' ? 'selected' : '' }}>Egreso</option>
                <option value="gasto_fijo" {{ request('type') == 'gasto_fijo' ? 'selected' : '' }}>Gasto fijo</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Desde</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded p-2">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Hasta</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded p-2">
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Filtrar</button>
        <a href="{{ route('admin.reports.accounting.export', request()->query()) }}" class="bg-emerald-600 text-white px-4 py-2 rounded">Exportar Excel</a>
    </form>
</div>
<a href="{{ route('admin.accounting.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nuevo asiento</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Fecha</th>
            <th class="p-2 text-left">Tipo</th>
            <th class="p-2 text-left">Categoría</th>
            <th class="p-2 text-left">Descripción</th>
            <th class="p-2 text-left">Monto</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($entries as $entry)
        <tr class="border-t">
            <td class="p-2">{{ $entry->date }}</td>
            <td class="p-2">{{ ucfirst(str_replace('_', ' ', $entry->type)) }}</td>
            <td class="p-2">{{ $entry->category }}</td>
            <td class="p-2">{{ $entry->description }}</td>
            <td class="p-2">S/ {{ number_format($entry->amount, 2) }}</td>
            <td class="p-2">
                <a href="{{ route('admin.accounting.edit', $entry) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.accounting.destroy', $entry) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $entries->links() }}
@endsection
