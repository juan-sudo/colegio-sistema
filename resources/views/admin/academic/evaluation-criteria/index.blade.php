@extends('layouts.admin')
@section('title', 'Criterios de evaluación')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Criterios de evaluación</h1>
        <a href="{{ route('admin.academic.evaluation-criteria.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo criterio
        </a>
    </div>

    @forelse($criteria as $criterion)
    <div class="bg-white rounded-lg shadow">
        <div class="p-4 border-b">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold">{{ $criterion->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $criterion->description ?? 'Sin descripción' }}</p>
                </div>
            </div>
        </div>

        @if($criterion->assessmentCriteria->count() > 0)
            @foreach($criterion->assessmentCriteria as $ac)
                <div class="p-4 border-t">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">
                        📚 {{ $ac->course->name ?? 'Sin curso' }} - {{ $ac->course->gradeSection->name ?? '' }}
                    </h3>
                    
                    @if($ac->students->count() > 0)
                        <form method="POST" action="{{ route('admin.academic.evaluation-criteria.store-grades', $criterion) }}" class="overflow-x-auto">
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
                                    @foreach($ac->students as $student)
                                    @php
                                        $grade = $ac->grades[$student->id] ?? null;
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

                            <div class="mt-4">
                                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                                    💾 Guardar notas
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-sm text-gray-500">Este curso no tiene estudiantes matriculados.</p>
                    @endif
                </div>
            @endforeach
        @else
            <div class="p-4 border-t">
                <p class="text-sm text-gray-500">Este criterio no está asociado a ningún curso.</p>
                <a href="{{ route('admin.academic.evaluation-criteria.grades', $criterion) }}" class="text-indigo-600 text-sm mt-2 inline-block">
                    + Asociar curso y cargar notas
                </a>
            </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500">No hay criterios de evaluación creados.</p>
    </div>
    @endforelse
</div>
@endsection