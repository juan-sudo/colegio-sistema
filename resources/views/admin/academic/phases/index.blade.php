@extends('layouts.admin')
@section('title', 'Fases escolares')
@section('content')
<h1 class="text-xl font-bold mb-4">Fases escolares</h1>
<a href="{{ route('admin.academic.phases.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nueva fase</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Orden</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($phases as $phase)
        <tr class="border-t">
            <td class="p-2">{{ $phase->name }}</td>
            <td class="p-2">{{ $phase->order }}</td>
            <td class="p-2">
                <a href="{{ route('admin.academic.phases.edit', $phase) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.academic.phases.destroy', $phase) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $phases->links() }}
@endsection
