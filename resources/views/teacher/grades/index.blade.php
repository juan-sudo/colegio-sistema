@extends('layouts.app')
@section('title', 'Notas - ' . $course->name)
@section('content')
<h1 class="text-xl font-bold mb-4">Notas — {{ $course->name }}</h1>

<div class="flex gap-3 mb-4">
    <a href="{{ route('teacher.grades.template', $course) }}" class="bg-gray-200 px-3 py-2 rounded text-sm">
        ⬇ Descargar plantilla Excel
    </a>
    <a href="{{ route('teacher.grades.import-form', $course) }}" class="bg-green-600 text-white px-3 py-2 rounded text-sm">
        ⬆ Carga masiva desde Excel
    </a>
</div>

<form method="POST" action="{{ route('teacher.grades.store', $course) }}" class="bg-white rounded shadow p-4">
    @csrf
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-sm font-medium mb-1">Periodo</label>
            <select name="grade_period_id" required class="w-full border rounded px-3 py-2">
                @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Evaluación</label>
            <input type="text" name="evaluation" placeholder="Examen 1, Práctica..." class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <table class="w-full text-sm">
        <thead>
            <tr class="text-left border-b">
                <th class="py-2">Alumno</th>
                <th class="py-2 w-32">Nota (0-20)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $student)
            <tr class="border-b">
                <td class="py-2">{{ $student->fullName() }}</td>
                <td class="py-2">
                    <input type="number" step="0.01" min="0" max="20"
                           name="scores[{{ $student->id }}]"
                           class="border rounded px-2 py-1 w-24">
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        Guardar notas
    </button>
</form>
@if($criteria->isNotEmpty())
<form method="POST" action="{{ route('teacher.grades.criteria.store', $course) }}" class="bg-white rounded shadow p-4 mt-6">
    @csrf
    <h2 class="font-bold mb-3">Notas por criterio</h2>
    <select name="assessment_criterion_id" class="border rounded px-3 py-2 mb-3" required>
        @foreach($criteria as $criterion)<option value="{{ $criterion->id }}">{{ $criterion->name }} · Máximo {{ $criterion->maximum_score }}</option>@endforeach
    </select>
    <div class="grid md:grid-cols-3 gap-2">
        @foreach($students as $student)<label class="text-sm">{{ $student->fullName() }}<input type="number" step="0.01" min="0" name="scores[{{ $student->id }}]" class="block border rounded px-2 py-1 w-full"></label>@endforeach
    </div>
    <button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded">Guardar por criterio</button>
</form>
@endif
@endsection
