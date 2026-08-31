@extends('layouts.admin')
@section('title', 'Notas')
@section('content')
<div class="space-y-6">
    <h1 class="text-xl font-bold">Registro de notas por curso</h1>
    <p class="text-sm text-gray-500">Selecciona un curso para ver sus criterios de evaluación y registrar notas por alumno.</p>

    <form method="GET" action="{{ route('admin.grades.index') }}" class="bg-white rounded-lg shadow p-6">
        <label class="block text-sm font-medium mb-2">Seleccionar curso</label>
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <select name="course_id" class="w-full border rounded p-2" onchange="this.form.submit()" required>
                    <option value="">Seleccionar curso...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ ($selectedCourse->id ?? null) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} - {{ $course->gradeSection->name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Ver curso
            </button>
        </div>
    </form>

    @if($selectedCourse)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold">{{ $selectedCourse->name }}</h2>
                <p class="text-sm text-gray-500">{{ $selectedCourse->gradeSection->name ?? '' }} · {{ $selectedCourse->teacher->fullName() ?? 'Sin profesor' }}</p>
            </div>
        </div>

        @if($students->count() > 0)
            @forelse($courseCriteria as $ac)
            <div class="border rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h3 class="font-medium">{{ $ac->evaluationCriteria->name ?? 'Criterio' }}</h3>
                        <p class="text-xs text-gray-500">{{ $ac->description ?? '' }} · Puntaje máximo: {{ $ac->maximum_score }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.academic.assessment-criteria.store-grades', $ac) }}" class="overflow-x-auto">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $ac->course_id }}">

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="p-2 text-left">DNI / Código</th>
                                <th class="p-2 text-left">Nombres</th>
                                <th class="p-2 text-left">Apellidos</th>
                                <th class="p-2 text-left w-40">Nota (0-{{ $ac->maximum_score }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            @php
                                $grade = $grades[$ac->id . '-' . $student->id] ?? null;
                            @endphp
                            <tr class="border-t">
                                <td class="p-2 font-medium">{{ $student->code ?? '-' }}</td>
                                <td class="p-2">{{ $student->first_name }}</td>
                                <td class="p-2">{{ $student->last_name }}</td>
                                <td class="p-2">
                                    <input type="number"
                                        name="scores[{{ $student->id }}]"
                                        value="{{ $grade->score ?? '' }}"
                                        min="0"
                                        max="{{ $ac->maximum_score }}"
                                        step="0.01"
                                        class="w-full border rounded p-2"
                                        placeholder="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                            💾 Guardar notas
                        </button>
                    </div>
                </form>
            </div>
            @empty
            <p class="text-sm text-gray-500">Este curso no tiene criterios de evaluación asociados.</p>
            @endforelse
        @else
            <p class="text-sm text-gray-500">Este curso no tiene estudiantes matriculados.</p>
        @endif
    </div>
    @endif
</div>
@endsection