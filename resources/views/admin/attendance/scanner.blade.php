@extends('layouts.admin')
@section('title', 'Escáner de asistencia')
@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">📷 Escanear código</h2>
        <form method="GET" action="{{ route('admin.attendance.scanner') }}" class="flex gap-4 items-end">
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
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Ver escáner
                </button>
            </div>
        </form>
    </div>

    @if(request('course_id') && isset($course))
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Escáner - {{ $course->name }} - {{ $course->gradeSection->name }}</h2>
        <p class="text-sm text-gray-500 mb-4">
            Usa el módulo principal de <a href="{{ route('admin.attendance.index', ['course_id' => $course->id]) }}" class="text-indigo-600 underline">Asistencia en aula</a> para registrar asistencia por QR, código de barras o biométrico.
        </p>
    </div>
    @endif
</div>
@endsection