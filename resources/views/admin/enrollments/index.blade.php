@extends('layouts.admin')
@section('title', 'Matrículas')
@section('content')
<h1 class="text-xl font-bold mb-4">Matrículas</h1>
<a href="{{ route('admin.enrollments.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nueva matrícula</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Estudiante</th>
            <th class="p-2 text-left">Grado</th>
            <th class="p-2 text-left">Año</th>
            <th class="p-2 text-left">Estado</th>
            <th class="p-2 text-left">Fecha</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($enrollments as $enrollment)
        <tr class="border-t">
            <td class="p-2">{{ $enrollment->student->fullName() ?? '-' }}</td>
            <td class="p-2">{{ $enrollment->gradeSection->name ?? '-' }}</td>
            <td class="p-2">{{ $enrollment->academicYear->name ?? '-' }}</td>
            <td class="p-2">{{ ucfirst($enrollment->status) }}</td>
            <td class="p-2">{{ $enrollment->enrollment_date }}</td>
            <td class="p-2">
                <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $enrollments->links() }}
@endsection
