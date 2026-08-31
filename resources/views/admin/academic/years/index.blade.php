@extends('layouts.admin')
@section('title', 'Años escolares')
@section('content')
<h1 class="text-xl font-bold mb-4">Años escolares</h1>
<a href="{{ route('admin.academic.years.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nuevo año</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Inicio</th>
            <th class="p-2 text-left">Fin</th>
            <th class="p-2 text-left">Actual</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($years as $year)
        <tr class="border-t">
            <td class="p-2">{{ $year->name }}</td>
            <td class="p-2">{{ $year->start_date->format('d/m/Y') }}</td>
            <td class="p-2">{{ $year->end_date->format('d/m/Y') }}</td>
            <td class="p-2">{{ $year->is_current ? 'Sí' : 'No' }}</td>
            <td class="p-2">
                <a href="{{ route('admin.academic.years.edit', $year) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.academic.years.destroy', $year) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $years->links() }}
@endsection
