@extends('layouts.admin')
@section('title', 'Reporte de estudiantes')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">👥 Reporte de estudiantes</h1>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
            ← Volver a reportes
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.reports.students') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Grado/Sección</label>
                <select name="grade_section_id" class="w-full border rounded p-2" required>
                    <option value="">Seleccionar grado/sección...</option>
                    @foreach($gradeSections as $gs)
                    <option value="{{ $gs->id }}" {{ $selectedGradeSectionId == $gs->id ? 'selected' : '' }}>
                        {{ $gs->name }} - {{ $gs->level }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    @if($selectedGradeSectionId && $students->count() > 0)
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
            <p class="text-sm text-blue-700">Total estudiantes</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
            <p class="text-sm text-green-700">Activos</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded p-3 text-center">
            <p class="text-2xl font-bold text-red-600">{{ $stats['inactive'] }}</p>
            <p class="text-sm text-red-700">Inactivos</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Código</th>
                    <th class="p-2 text-left">DNI</th>
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr class="border-t">
                    <td class="p-2">{{ $student->code }}</td>
                    <td class="p-2">{{ $student->dni }}</td>
                    <td class="p-2">{{ $student->fullName() }}</td>
                    <td class="p-2">{{ $student->user->email ?? '-' }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs {{ $student->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $student->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($selectedGradeSectionId && $students->count() == 0)
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500">No hay estudiantes registrados en este grado/sección.</p>
    </div>
    @endif
</div>
@endsection