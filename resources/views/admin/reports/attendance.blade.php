@extends('layouts.admin')
@section('title', 'Reporte de asistencia')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">📋 Reporte de asistencia</h1>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
            ← Volver a reportes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.reports.attendance') }}" class="flex gap-4 items-end">
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
                <label class="block text-sm font-medium mb-1">Desde</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Hasta</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full border rounded p-2" required>
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Filtrar
                </button>
            </div>
            <div>
                <a href="{{ route('admin.reports.attendance.export', ['course_id' => $selectedCourseId, 'date_from' => $dateFrom, 'date_to' => $dateTo]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    📥 Excel
                </a>
            </div>
        </form>
    </div>

    @if($selectedCourseId && $attendances->count() > 0)
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-green-50 border border-green-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['present'] }}</p>
            <p class="text-sm text-green-700">Presentes</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['late'] }}</p>
            <p class="text-sm text-yellow-700">Tardanzas</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
            <p class="text-sm text-red-700">Faltas</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Fecha</th>
                    <th class="p-2 text-left">Estudiante</th>
                    <th class="p-2 text-left">Curso</th>
                    <th class="p-2 text-center">Estado</th>
                    <th class="p-2 text-left">Hora entrada</th>
                    <th class="p-2 text-left">Método</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                <tr class="border-t">
                    <td class="p-2">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                    <td class="p-2">{{ $attendance->student->fullName() ?? '-' }}</td>
                    <td class="p-2">{{ $attendance->course->name ?? '-' }}</td>
                    <td class="p-2 text-center">
                        @if($attendance->status == 'presente')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs">Presente</span>
                        @elseif($attendance->status == 'tardanza')
                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">Tarde</span>
                        @else
                            <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs">Falta</span>
                        @endif
                    </td>
                    <td class="p-2">{{ $attendance->time_in ?? '-' }}</td>
                    <td class="p-2">{{ ucfirst($attendance->method) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($selectedCourseId && $attendances->count() == 0)
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500">No hay registros de asistencia para este curso en el rango de fechas seleccionado.</p>
    </div>
    @endif
</div>
@endsection