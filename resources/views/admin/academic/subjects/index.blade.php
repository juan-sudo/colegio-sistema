@extends('layouts.admin')
@section('title', 'Materias')
@section('content')
<h1 class="text-xl font-bold mb-4">Materias</h1>
<a href="{{ route('admin.academic.subjects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nueva materia</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Código</th>
            <th class="p-2 text-left">Nombre</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($subjects as $subject)
        <tr class="border-t">
            <td class="p-2">{{ $subject->code ?? '-' }}</td>
            <td class="p-2">{{ $subject->name }}</td>
            <td class="p-2">
                <a href="{{ route('admin.academic.subjects.edit', $subject) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.academic.subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $subjects->links() }}
@endsection
