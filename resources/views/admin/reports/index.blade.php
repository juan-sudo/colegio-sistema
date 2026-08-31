@extends('layouts.admin')
@section('title', 'Reportes')
@section('content')
<div class="space-y-6">
    <h1 class="text-xl font-bold">📊 Reportes del sistema</h1>
    <p class="text-sm text-gray-500">Selecciona un tipo de reporte para visualizarlo o exportarlo.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl">📋</span>
                <div>
                    <h2 class="text-lg font-semibold">Asistencia</h2>
                    <p class="text-sm text-gray-500">Reporte de asistencia por curso y fecha</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.reports.attendance') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Curso</label>
                    <select name="course_id" class="w-full border rounded p-2" required>
                        <option value="">Seleccionar curso...</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }} - {{ $course->gradeSection->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium mb-1">Desde</label>
                        <input type="date" name="date_from" value="{{ now()->startOfMonth()->toDateString() }}" class="w-full border rounded p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Hasta</label>
                        <input type="date" name="date_to" value="{{ now()->toDateString() }}" class="w-full border rounded p-2" required>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Ver reporte
                    </button>
                    <a href="{{ route('admin.reports.attendance.export', ['course_id' => request('course_id'), 'date_from' => request('date_from'), 'date_to' => request('date_to')]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        📥 Excel
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl">📝</span>
                <div>
                    <h2 class="text-lg font-semibold">Notas</h2>
                    <p class="text-sm text-gray-500">Reporte de notas por curso</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.reports.grades') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Curso</label>
                    <select name="course_id" class="w-full border rounded p-2" required>
                        <option value="">Seleccionar curso...</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }} - {{ $course->gradeSection->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Ver reporte
                    </button>
                    <a href="{{ route('admin.reports.grades.export', ['course_id' => request('course_id')]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        📥 Excel
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl">👥</span>
                <div>
                    <h2 class="text-lg font-semibold">Estudiantes</h2>
                    <p class="text-sm text-gray-500">Listado de estudiantes por grado/sección</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.reports.students') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Grado/Sección</label>
                    <select name="grade_section_id" class="w-full border rounded p-2" required>
                        <option value="">Seleccionar grado/sección...</option>
                        @foreach($gradeSections as $gs)
                        <option value="{{ $gs->id }}">{{ $gs->name }} - {{ $gs->level }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Ver reporte
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl">💰</span>
                <div>
                    <h2 class="text-lg font-semibold">Pagos</h2>
                    <p class="text-sm text-gray-500">Reporte de pagos y estados de cuenta</p>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.reports.payments') }}" class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo</label>
                    <select name="type" class="w-full border rounded p-2">
                        <option value="">Todos</option>
                        <option value="matricula">Matrícula</option>
                        <option value="pension">Pensión</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Estado</label>
                    <select name="status" class="w-full border rounded p-2">
                        <option value="">Todos</option>
                        <option value="pagado">Pagado</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="vencido">Vencido</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Ver reporte
                    </button>
                    <a href="{{ route('admin.reports.payments.export', ['type' => request('type'), 'status' => request('status')]) }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        📥 Excel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection