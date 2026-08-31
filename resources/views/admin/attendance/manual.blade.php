@extends('layouts.admin')
@section('title', 'Registro manual de asistencia')
@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">✏️ Registro manual por curso</h2>
        <form method="GET" action="{{ route('admin.attendance.manual') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Curso</label>
                <select name="course_id" class="w-full border rounded p-2" required>
                    <option value="">Seleccionar curso...</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} - {{ $course->gradeSection->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Fecha</label>
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Ver curso
                </button>
            </div>
        </form>
    </div>

    @if(request('course_id') && request('date'))
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Registro de asistencia</h2>
        <p class="text-sm text-gray-500 mb-4">
            Usa el módulo principal de <a href="{{ route('admin.attendance.index', ['course_id' => request('course_id'), 'date' => request('date')]) }}" class="text-indigo-600 underline">Asistencia en aula</a> para registrar la asistencia.
        </p>
    </div>
    @endif
</div>
@endsection