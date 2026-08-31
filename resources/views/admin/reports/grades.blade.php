@extends('layouts.admin')
@section('title', 'Reporte de notas')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">📝 Reporte de notas</h1>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
            ← Volver a reportes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.reports.grades') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Curso</label>
                <select name="course_id" class="w-full border rounded p-2" required>
                    <option value="">Seleccionar curso...</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} - {{ $course->gradeSection->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Filtrar
                </button>
            </div>
            <div>
                <a href="{{ route('admin.reports.grades.export', ['course_id' => $selectedCourseId]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    📥 Excel
                </a>
            </div>
        </form>
    </div>

    @if($selectedCourseId && $grades->count() > 0)
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['average'] }}</p>
            <p class="text-sm text-blue-700">Promedio general</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            <p class="text-sm text-green-700">Aprobados</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
            <p class="text-sm text-red-700">Desaprobados</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Estudiante</th>
                    <th class="p-2 text-left">Evaluación</th>
                    <th class="p-2 text-center">Nota</th>
                    <th class="p-2 text-left">Periodo</th>
                    <th class="p-2 text-left">Fecha</th>
                </tr>
            </thead>
            <tbody>
                @foreach($grades as $grade)
                <tr class="border-t">
                    <td class="p-2">{{ $grade->student->fullName() ?? '-' }}</td>
                    <td class="p-2">{{ $grade->evaluation ?? '-' }}</td>
                    <td class="p-2 text-center">
                        <span class="px-2 py-1 rounded text-xs {{ $grade->score >= 11 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $grade->score }}
                        </span>
                    </td>
                    <td class="p-2">{{ $grade->gradePeriod->name ?? '-' }}</td>
                    <td class="p-2">{{ $grade->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($selectedCourseId && $grades->count() == 0)
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500">No hay notas registradas para este curso.</p>
    </div>
    @endif
</div>
@endsection