@extends('layouts.admin')
@section('title', 'Turnos')
@section('content')
<h1 class="text-xl font-bold mb-4">Turnos</h1>
<a href="{{ route('admin.academic.shifts.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nuevo turno</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Inicio</th>
            <th class="p-2 text-left">Fin</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shifts as $shift)
        <tr class="border-t">
            <td class="p-2">{{ $shift->name }}</td>
            <td class="p-2">{{ $shift->start_time }}</td>
            <td class="p-2">{{ $shift->end_time }}</td>
            <td class="p-2">
                <a href="{{ route('admin.academic.shifts.edit', $shift) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.academic.shifts.destroy', $shift) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $shifts->links() }}
@endsection
