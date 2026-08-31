@extends('layouts.admin')
@section("title", "Estudiantes")
@section("content")
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Estudiantes</h1>
        <button onclick="openStudentModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo estudiante
        </button>
    </div>

    <form method="GET" action="{{ route('admin.students.index') }}" class="bg-white rounded shadow p-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Código, DNI, nombre..." class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Buscar
            </button>
            <a href="{{ route('admin.students.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Código</th>
                    <th class="p-2 text-left">DNI</th>
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Grado/Sección</th>
                    <th class="p-2 text-left">Estado</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="students-table">
                @foreach($students as $student)
                <tr class="border-t" data-student="{{ $student->toJson() }}">
                    <td class="p-2">{{ $student->code }}</td>
                    <td class="p-2">{{ $student->dni }}</td>
                    <td class="p-2">{{ $student->fullName() }}</td>
                    <td class="p-2">{{ $student->gradeSection->name ?? '-' }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs {{ $student->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $student->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="p-2">
                        <button onclick="editStudent({{ $student }})" class="text-indigo-600 hover:text-indigo-800">Editar</button>
                        <a href="{{ route('admin.students.carnet', $student) }}" class="text-indigo-600 ml-2 hover:text-indigo-800">Carnet</a>
                        <button onclick="deleteStudent({{ $student->id }})" class="text-red-600 ml-2 hover:text-red-800">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $students->links() }}
    </div>
</div>

<div id="studentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="modalTitle">Nuevo estudiante</h3>
            <button onclick="closeStudentModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="studentForm" method="POST" class="p-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="student_id" id="studentId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">DNI</label>
                    <input type="text" name="dni" id="dni" class="w-full border rounded p-2" required>
                    <p class="text-xs text-gray-500 mt-1">Se usará para generar el código, QR, código de barras y biométrico.</p>
                    @error('dni')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="first_name" id="firstName" class="w-full border rounded p-2" required>
                    @error('first_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apellido</label>
                    <input type="text" name="last_name" id="lastName" class="w-full border rounded p-2" required>
                    @error('last_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="email" class="w-full border rounded p-2" required>
                    @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div id="passwordField">
                    <label class="block text-sm font-medium mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la actual (solo edición).</p>
                    @error('password')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Grado/Sección</label>
                    <select name="grade_section_id" id="gradeSectionId" class="w-full border rounded p-2" required>
                        <option value="">Seleccionar...</option>
                        @foreach($gradeSections as $gs)
                        <option value="{{ $gs->id }}">{{ $gs->name }} - {{ $gs->level }}</option>
                        @endforeach
                    </select>
                    @error('grade_section_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" id="birthDate" class="w-full border rounded p-2">
                    @error('birth_date')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="phone" id="phone" class="w-full border rounded p-2">
                    @error('phone')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="active" id="active" value="1" checked class="w-4 h-4">
                    <label class="text-sm">Activo</label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeStudentModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                    Cancelar
                </button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

<script>
    const studentForm = document.getElementById('studentForm');
    const modalTitle = document.getElementById('modalTitle');
    const passwordField = document.getElementById('passwordField');
    const formMethod = document.getElementById('formMethod');

    function openStudentModal() {
        modalTitle.textContent = 'Nuevo estudiante';
        studentForm.reset();
        document.getElementById('studentId').value = '';
        document.getElementById('active').checked = true;
        formMethod.value = 'POST';
        passwordField.style.display = 'block';
        document.getElementById('password').required = true;
        document.getElementById('studentModal').classList.remove('hidden');
        document.getElementById('studentModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeStudentModal() {
        document.getElementById('studentModal').classList.add('hidden');
        document.getElementById('studentModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function editStudent(student) {
        modalTitle.textContent = 'Editar estudiante';
        document.getElementById('studentId').value = student.id;
        document.getElementById('dni').value = student.dni;
        document.getElementById('firstName').value = student.first_name;
        document.getElementById('lastName').value = student.last_name;
        document.getElementById('email').value = student.user?.email || '';
        document.getElementById('gradeSectionId').value = student.grade_section_id;
        document.getElementById('birthDate').value = student.birth_date || '';
        document.getElementById('phone').value = student.user?.phone || '';
        document.getElementById('active').checked = student.active;
        formMethod.value = 'PUT';
        passwordField.style.display = 'none';
        document.getElementById('password').required = false;
        document.getElementById('studentModal').classList.remove('hidden');
        document.getElementById('studentModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function deleteStudent(id) {
        if (!confirm('¿Eliminar este estudiante?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/students/${id}`;
        form.innerHTML = `
            @csrf
            <input type="hidden" name="_method" value="DELETE">
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
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Error al eliminar', 'error');
            }
        })
        .catch(() => {
            showToast('Error al eliminar', 'error');
        })
        .finally(() => {
            document.body.removeChild(form);
        });
    }

    studentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(studentForm);
        const studentId = document.getElementById('studentId').value;
        const url = studentId ? `/admin/students/${studentId}` : '/admin/students';
        const method = studentId ? 'POST' : 'POST';

        if (studentId) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                closeStudentModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Error al guardar', 'error');
            }
        })
        .catch(() => {
            showToast('Error al guardar', 'error');
        });
    });

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
</script>
@endsection