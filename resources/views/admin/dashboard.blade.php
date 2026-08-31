@extends('layouts.admin')
@section('title', 'Inicio')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Estudiantes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['students'] }}</p>
                </div>
                <div class="text-3xl">👨‍🎓</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Profesores</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['teachers'] }}</p>
                </div>
                <div class="text-3xl">👩‍🏫</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Cursos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['courses'] }}</p>
                </div>
                <div class="text-3xl">📚</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Matrículas activas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['enrollments'] }}</p>
                </div>
                <div class="text-3xl">📋</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pagos pendientes</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['payments_pending'] }}</p>
                </div>
                <div class="text-3xl">💵</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pagos vencidos</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['payments_overdue'] }}</p>
                </div>
                <div class="text-3xl">⚠️</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Asistencia hoy</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['attendance_today'] }}</p>
                </div>
                <div class="text-3xl">✅</div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Notas registradas hoy</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['grades_recorded'] }}</p>
                </div>
                <div class="text-3xl">📝</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Ingresos</h3>
            <p class="text-2xl font-bold text-green-600">S/ {{ number_format($financial['income'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Egresos</h3>
            <p class="text-2xl font-bold text-red-600">S/ {{ number_format($financial['expense'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Gastos fijos</h3>
            <p class="text-2xl font-bold text-orange-600">S/ {{ number_format($financial['fixed_cost'], 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Últimos pagos</h3>
            <div class="space-y-3">
                @forelse($recentPayments as $payment)
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <p class="text-sm font-medium">{{ $payment->student->fullName() ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ ucfirst($payment->type) }} · {{ $payment->due_date }}</p>
                    </div>
                    <span class="text-sm font-semibold {{ $payment->status == 'pagado' ? 'text-green-600' : 'text-red-600' }}">
                        S/ {{ number_format($payment->paid, 2) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500">Sin pagos recientes</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Asistencia reciente</h3>
            <div class="space-y-3">
                @forelse($recentAttendances as $attendance)
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <p class="text-sm font-medium">{{ $attendance->student->fullName() ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $attendance->course->name ?? '-' }} · {{ $attendance->date }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded {{ $attendance->status == 'presente' ? 'bg-green-100 text-green-800' : ($attendance->status == 'tardanza' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($attendance->status) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500">Sin asistencias recientes</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Notas recientes</h3>
            <div class="space-y-3">
                @forelse($recentGrades as $grade)
                <div class="flex items-center justify-between border-b pb-2">
                    <div>
                        <p class="text-sm font-medium">{{ $grade->student->fullName() ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $grade->course->name ?? '-' }} · {{ $grade->gradePeriod->name ?? '-' }}</p>
                    </div>
                    <span class="text-sm font-semibold">{{ $grade->score }}</span>
                </div>http://localhost:8000/admin/academic/evaluation-criteria/
                @empty
                <p class="text-sm text-gray-500">Sin notas recientes</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection