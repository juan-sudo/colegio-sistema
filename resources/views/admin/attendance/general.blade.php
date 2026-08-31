@extends('layouts.admin')
@section('title', 'Asistencia general del colegio')
@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold">Registrar asistencia general - {{ $date }}</h2>
            <div class="flex gap-2">
                <span class="px-3 py-1 rounded bg-green-100 text-green-800 text-sm">
                    ✅ Presentes: {{ $stats['present'] }}
                </span>
                <span class="px-3 py-1 rounded bg-yellow-100 text-yellow-800 text-sm">
                    ⏰ Tardanzas: {{ $stats['late'] }}
                </span>
                <span class="px-3 py-1 rounded bg-red-100 text-red-800 text-sm">
                    ❌ Faltas: {{ $stats['absent'] }}
                </span>
                <span class="px-3 py-1 rounded bg-gray-100 text-gray-800 text-sm">
                    📊 Total: {{ $stats['total'] }}
                </span>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded p-3 mb-4">
            <p class="text-sm text-blue-800">
                <strong>Reglas de asistencia:</strong> Antes de 7:00 AM → Presente | 7:00 AM - 7:10 AM → Tarde | Después de 7:10 AM → Falta
            </p>
        </div>

        <form id="scanForm" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Método</label>
                    <select id="scanMethod" class="w-full border rounded p-2">
                        <option value="qr">📷 QR</option>
                        <option value="barcode">📊 Código de barras</option>
                        <option value="biometric">👁 Biométrico</option>
                        <option value="manual">✏️ Manual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Código / Huella</label>
                    <input type="text" id="scanCode" class="w-full border rounded p-2" placeholder="Escanea o ingresa el código..." autofocus autocomplete="off">
                </div>
                <div>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 w-full">
                        📷 Registrar asistencia
                    </button>
                </div>
            </div>
        </form>

        <div id="scanResult" class="mb-4 hidden">
            <div id="scanResultContent" class="p-4 rounded"></div>
        </div>

        <form method="POST" action="{{ route('admin.attendance.store-manual') }}" class="overflow-x-auto">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 text-left">Alumno</th>
                        <th class="p-2 text-left">Grado/Sección</th>
                        <th class="p-2 text-left">Curso</th>
                        <th class="p-2 text-center">Presente</th>
                        <th class="p-2 text-center">Tarde</th>
                        <th class="p-2 text-center">Falta</th>
                        <th class="p-2 text-left">Hora entrada</th>
                        <th class="p-2 text-left">Justificación/Observación</th>
                    </tr>
                </thead>
                <tbody id="attendanceTableBody">
                    @foreach($students as $student)
                    @php
                        $attendance = $attendances[$student->id] ?? null;
                        $currentStatus = $attendance->status ?? 'falta';
                        $currentTime = $attendance->time_in ?? date('H:i');
                        $course = $student->courses->first();
                    @endphp
                    <tr class="border-t" data-student-id="{{ $student->id }}" data-status="{{ $currentStatus }}">
                        <td class="p-2">{{ $student->fullName() }}</td>
                        <td class="p-2">{{ $student->gradeSection->name ?? '-' }}</td>
                        <td class="p-2">{{ $course->name ?? '-' }}</td>
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
                            <input type="hidden" name="attendances[{{ $student->id }}][course_id]" value="{{ $course->id ?? '' }}">
                        </td>
                        <td class="p-2">
                            <input type="text" name="attendances[{{ $student->id }}][observation]" value="{{ $attendance->observation ?? '' }}" placeholder="Justificación..." class="w-full border rounded p-1 text-xs">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    💾 Guardar asistencia general
                </button>
                <a href="{{ route('admin.attendance.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    ← Volver
                </a>
            </div>
        </form>
    </div>
</div>

<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
    const scanForm = document.getElementById('scanForm');
    const scanCodeInput = document.getElementById('scanCode');
    const scanResult = document.getElementById('scanResult');
    const scanResultContent = document.getElementById('scanResultContent');

    scanForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const code = scanCodeInput.value.trim();
        const method = document.getElementById('scanMethod').value;
        
        if (!code) {
            showToast('Ingresa un código o escanea', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('code', code);
        formData.append('method', method);

        try {
            const response = await fetch('{{ route("admin.attendance.registrar-general") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (data.ok) {
                scanResultContent.className = 'p-4 rounded bg-green-100 text-green-800';
                scanResultContent.innerHTML = `
                    <p class="font-semibold">✅ ${data.student}</p>
                    <p class="text-sm">Grado: ${data.grade_section}</p>
                    <p class="text-sm">Estado: ${data.status === 'presente' ? 'Presente' : data.status === 'tardanza' ? 'Tarde' : 'Falta'}</p>
                    <p class="text-sm">Hora: ${data.time}</p>
                `;
                scanResult.classList.remove('hidden');
                
                updateStudentRow(data.student, data.status, data.time);
                showToast(`${data.student} registrado como ${data.status === 'presente' ? 'Presente' : data.status === 'tardanza' ? 'Tarde' : 'Falta'}`, 'success');
                
                scanCodeInput.value = '';
                scanCodeInput.focus();
            } else {
                scanResultContent.className = 'p-4 rounded bg-red-100 text-red-800';
                scanResultContent.innerHTML = `<p class="font-semibold">❌ Error</p><p>${data.message || 'No se pudo registrar'}</p>`;
                scanResult.classList.remove('hidden');
                showToast(data.message || 'Error al registrar', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Error al registrar asistencia', 'error');
        }
    });

    function updateStudentRow(studentName, status, time) {
        const rows = document.querySelectorAll('#attendanceTableBody tr');
        rows.forEach(row => {
            const nameCell = row.querySelector('td');
            if (nameCell && nameCell.textContent.includes(studentName)) {
                const statusInputs = row.querySelectorAll('input[type="radio"]');
                statusInputs.forEach(input => {
                    input.checked = (input.value === status);
                });
                const timeInput = row.querySelector('input[type="time"]');
                if (timeInput) {
                    timeInput.value = time.substring(0, 5);
                }
                row.setAttribute('data-status', status);
            }
        });
        
        updateStats();
    }

    function updateStats() {
        const rows = document.querySelectorAll('#attendanceTableBody tr');
        let present = 0, late = 0, absent = 0;
        
        rows.forEach(row => {
            const status = row.getAttribute('data-status');
            if (status === 'presente') present++;
            else if (status === 'tardanza') late++;
            else if (status === 'falta') absent++;
        });
        
        const stats = {
            present: {{ $stats['present'] }},
            late: {{ $stats['late'] }},
            absent: {{ $stats['absent'] }},
            total: {{ $stats['total'] }}
        };
        
        stats.present = present;
        stats.late = late;
        stats.absent = absent;
        
        const container = document.querySelector('.bg-white.rounded-lg.shadow.p-6 .flex.gap-2');
        if (container) {
            const spans = container.querySelectorAll('span');
            if (spans[0]) spans[0].textContent = `✅ Presentes: ${present}`;
            if (spans[1]) spans[1].textContent = `⏰ Tardanzas: ${late}`;
            if (spans[2]) spans[2].textContent = `❌ Faltas: ${absent}`;
        }
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

    scanCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            scanForm.dispatchEvent(new Event('submit'));
        }
    });

    @if(session('success'))
    showToast('{{ session('success') }}', 'success');
    @endif
</script>
@endsection