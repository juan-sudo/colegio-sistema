@extends('layouts.admin')
@section('title', 'Horarios')
@section('content')
<h1 class="text-xl font-bold mb-4">Horarios</h1>
<a href="{{ route('admin.academic.schedules.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Nuevo horario</a>
<table class="w-full bg-white rounded shadow text-sm">
    <thead>
        <tr class="bg-gray-100">
            <th class="p-2 text-left">Grado</th>
            <th class="p-2 text-left">Materia</th>
            <th class="p-2 text-left">Profesor</th>
            <th class="p-2 text-left">Día</th>
            <th class="p-2 text-left">Inicio</th>
            <th class="p-2 text-left">Fin</th>
            <th class="p-2 text-left">Aula</th>
            <th class="p-2 text-left">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($schedules as $schedule)
        <tr class="border-t">
            <td class="p-2">{{ $schedule->gradeSection->name ?? '-' }}</td>
            <td class="p-2">{{ $schedule->subject->name ?? '-' }}</td>
            <td class="p-2">{{ $schedule->teacher->fullName() ?? '-' }}</td>
            <td class="p-2">{{ $schedule->day_of_week }}</td>
            <td class="p-2">{{ $schedule->start_time }}</td>
            <td class="p-2">{{ $schedule->end_time }}</td>
            <td class="p-2">{{ $schedule->classroom ?? '-' }}</td>
            <td class="p-2">
                <a href="{{ route('admin.academic.schedules.edit', $schedule) }}" class="text-indigo-600">Editar</a>
                <form action="{{ route('admin.academic.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                    @csrf @method("DELETE")
                    <button class="text-red-600 ml-2">Eliminar</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $schedules->links() }}
@endsection
