@extends('layouts.admin')
@section('title', 'Cargar notas')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold">Cargar notas - {{ $evaluationCriterion->name }}</h2>
                <p class="text-sm text-gray-500">
                    {{ $course->name }} - {{ $course->gradeSection->name ?? 'Sin grado' }} · 
                    {{ $course->teacher->fullName() ?? 'Sin profesor' }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.academic.evaluation-criteria.grades', $evaluationCriterion) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    ← Cambiar curso
                </a>
                <a href="{{ route('admin.academic.evaluation-criteria.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Volver a criterios
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.academic.evaluation-criteria.store-grades', $evaluationCriterion) }}">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="p-2 text-left">Alumno</th>
                            <th class="p-2 text-left">Nota (0-{{ $assessmentCriterion->maximum_score }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                        <tr class="border-t">
                            <td class="p-2">{{ $student->fullName() }}</td>
                            <td class="p-2">
                                <input type="number" 
                                    name="scores[{{ $student->id }}]" 
                                    value="{{ $grades[$student->id]->score ?? '' }}"
                                    min="0" 
                                    max="{{ $assessmentCriterion->maximum_score }}"
                                    step="0.01"
                                    class="w-32 border rounded p-1"
                                    placeholder="0">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    💾 Guardar notas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
