@extends('layouts.admin')
@section('title', 'Asistencia en aula')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">📋 Asistencia en aula - Por curso</h2>
        <p class="text-sm text-gray-500 mb-4">Selecciona un curso y fecha para registrar asistencia. El sistema marcará automáticamente: Presente (antes de 7:00 AM), Tarde (7:00 AM - 7:10 AM), Falta (después de 7:10 AM).</p>
        
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Curso</label>
                <select name="course_id" class="w-full border rounded p-2" onchange="this.form.submit()" required>
                    <option value="">Seleccionar curso...</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} - {{ $course->gradeSection->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Fecha</label>
                <input type="date" name="date" value="{{ $date }}" class="w-full border rounded p-2" onchange="this.form.submit()" required>
            </div>
        </form>
    </div>

    @if($course && $students->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold">{{ $course->name }} - {{ $course->gradeSection->name }}</h2>
                <p class="text-sm text-gray-500">Fecha: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }} · Profesor: {{ $course->teacher->fullName() ?? 'Sin profesor' }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="markAllPresent()" class="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700">
                    Marcar todos presentes
                </button>
                <button onclick="markAllAbsent()" class="bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700">
                    Marcar todos falta
                </button>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4">
            <p class="text-sm text-blue-800">
                <strong>Reglas de asistencia:</strong> Antes de 7:00 AM → Presente | 7:00 AM - 7:10 AM → Tarde | Después de 7:10 AM → Falta
            </p>
        </div>

        <div class="grid grid-cols-3 gap-4 mb-4">
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

        <form method="POST" action="{{ route('admin.attendance.store-manual') }}" class="overflow-x-auto">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
            
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Alumno</th>
                        <th class="p-2 text-left">DNI</th>
                        <th class="p-2 text-center">Presente</th>
                        <th class="p-2 text-center">Tarde</th>
                        <th class="p-2 text-center">Falta</th>
                        <th class="p-2 text-left">Hora entrada</th>
                        <th class="p-2 text-left">Justificación/Observación</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                    @php
                        $attendance = $attendances[$student->id] ?? null;
                        $currentStatus = $attendance->status ?? 'falta';
                        $currentTime = $attendance->time_in ?? date('H:i');
                    @endphp
                    <tr class="border-t">
                        <td class="p-2">{{ $student->fullName() }}</td>
                        <td class="p-2">{{ $student->dni }}</td>
                        <td class="p-2 text-center">
                            <input type="radio" name="attendances[{{ $student->id }}][status]" value="presente" {{ $currentStatus == 'presente' ? 'checked' : '' }} class="w-4 h-4">
                        </td>
                        <td class="p-2 text-center">
                            <input type="radio" name="attendances[{{ $student->id }}][status]" value="tardanza" {{ $currentStatus == 'tardanza' ? 'checked' : '' }} class="w-4 h-4">
                        </td>
                        <td class="p-2 text-center">
                            <input type="radio" name="attendances[{{ $student->id }}][status]" value="falta" {{ $currentStatus == 'falta' ? 'checked' : '' }} class="w-4 h-4">
                        </td>
                        <td class="p-2">
                            <input type="time" name="attendances[{{ $student->id }}][time_in]" value="{{ $currentTime }}" class="w-24 border rounded p-1">
                            <input type="hidden" name="attendances[{{ $student->id }}][course_id]" value="{{ $selectedCourseId }}">
                        </td>
                        <td class="p-2">
                            <input type="text" name="attendances[{{ $student->id }}][observation]" value="{{ $attendance->observation ?? '' }}" placeholder="Justificación..." class="w-full border rounded p-1 text-xs">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    💾 Guardar asistencia
                </button>
                <button type="button" onclick="markAbsences()" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    ⚠️ Marcar faltas y notificar
                </button>
            </div>
        </form>
    </div>
    @endif

    @if($selectedCourseId && $students->count() == 0)
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-500">Este curso no tiene estudiantes matriculados.</p>
    </div>
    @endif
</div>

<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
    function markAllPresent() {
        const radios = document.querySelectorAll('input[type="radio"][value="presente"]');
        radios.forEach(radio => radio.checked = true);
        showToast('Todos marcados como presentes', 'success');
    }

    function markAllAbsent() {
        const radios = document.querySelectorAll('input[type="radio"][value="falta"]');
        radios.forEach(radio => radio.checked = true);
        showToast('Todos marcados como falta', 'success');
    }

    function markAbsences() {
        if (!confirm('¿Marcar como falta a todos los alumnos no registrados y enviar alertas por WhatsApp?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.attendance.mark-absences") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
            <input type="hidden" name="date" value="{{ $date }}">
        `;
        document.body.appendChild(form);

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Faltas marcadas correctamente', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Error al marcar faltas', 'error');
            }
        })
        .catch(() => {
            showToast('Error al marcar faltas', 'error');
        })
        .finally(() => {
            document.body.removeChild(form);
        });
    }

    function showToast(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `px-4 py-3 rounded shadow-lg text-white ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    @if(session('success'))
    showToast('{{ session('success') }}', 'success');
    @endif
</script>
@endsection