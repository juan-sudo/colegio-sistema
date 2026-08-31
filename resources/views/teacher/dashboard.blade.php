@extends("layouts.app")
@section("title", "Panel del profesor")
@section("content")
<h1 class="text-xl font-bold mb-4">Mis cursos</h1>
<div class="grid md:grid-cols-3 gap-4">
    @foreach($courses as $course)
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-2">{{ $course->name }}</h2>
        <p class="text-sm text-gray-500 mb-3">{{ $course->gradeSection->name }}</p>
        <div class="flex flex-col gap-1 text-sm">
            <a class="text-indigo-600" href="{{ route("teacher.attendance.scanner", $course) }}">📋 Tomar asistencia</a>
            <a class="text-indigo-600" href="{{ route("teacher.grades.index", $course) }}">📝 Ingresar notas</a>
            <a class="text-indigo-600" href="{{ route("teacher.assignments.index", $course) }}">📎 Tareas</a>
        </div>
    </div>
    @endforeach
</div>
@endsection
