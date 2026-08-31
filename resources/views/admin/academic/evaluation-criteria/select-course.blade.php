@extends('layouts.admin')
@section('title', 'Seleccionar curso')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold">Cargar notas - {{ $evaluationCriterion->name }}</h2>
                <p class="text-sm text-gray-500">Selecciona el curso para registrar las notas</p>
            </div>
            <a href="{{ route('admin.academic.evaluation-criteria.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                ← Volver
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($courses as $course)
            <a href="{{ route('admin.academic.evaluation-criteria.grades', [$evaluationCriterion, 'course_id' => $course->id]) }}" 
               class="bg-white border rounded-lg p-4 hover:shadow-md transition-shadow">
                <h3 class="font-semibold mb-1">{{ $course->name }}</h3>
                <p class="text-sm text-gray-500">{{ $course->gradeSection->name ?? 'Sin grado' }}</p>
                <p class="text-sm text-gray-500">{{ $course->teacher->fullName() ?? 'Sin profesor' }}</p>
            </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
