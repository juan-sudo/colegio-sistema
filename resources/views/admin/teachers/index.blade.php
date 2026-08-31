@extends('layouts.admin')
@section("title", "Profesores")
@section("content")
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Profesores</h1>
        <button onclick="openTeacherModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo profesor
        </button>
    </div>

    <form method="GET" action="{{ route('admin.teachers.index') }}" class="bg-white rounded shadow p-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Código, nombre, especialidad..." class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Buscar
            </button>
            <a href="{{ route('admin.teachers.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Código</th>
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Especialidad</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="teachers-table">
                @foreach($teachers as $teacher)
                <tr class="border-t" data-teacher="{{ $teacher->toJson() }}">
                    <td class="p-2">{{ $teacher->code }}</td>
                    <td class="p-2">{{ $teacher->fullName() }}</td>
                    <td class="p-2">{{ $teacher->specialty ?? '-' }}</td>
                    <td class="p-2">
                        <button onclick="editTeacher({{ $teacher }})" class="text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button onclick="deleteTeacher({{ $teacher->id }})" class="text-red-600 ml-2 hover:text-red-800">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $teachers->links() }}
    </div>
</div>

<div id="teacherModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="teacherModalTitle">Nuevo profesor</h3>
            <button onclick="closeTeacherModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="teacherForm" method="POST" class="p-4">
            @csrf
            <input type="hidden" name="_method" id="teacherFormMethod" value="POST">
            <input type="hidden" name="teacher_id" id="teacherId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="first_name" id="teacherFirstName" class="w-full border rounded p-2" required>
                    @error('first_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apellido</label>
                    <input type="text" name="last_name" id="teacherLastName" class="w-full border rounded p-2" required>
                    @error('last_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="teacherEmail" class="w-full border rounded p-2" required>
                    @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div id="teacherPasswordField">
                    <label class="block text-sm font-medium mb-1">Contraseña</label>
                    <input type="password" name="password" id="teacherPassword" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la actual (solo edición).</p>
                    @error('password')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Código</label>
                    <input type="text" name="code" id="teacherCode" class="w-full border rounded p-2" required>
                    @error('code')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Especialidad</label>
                    <input type="text" name="specialty" id="teacherSpecialty" class="w-full border rounded p-2">
                    @error('specialty')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="phone" id="teacherPhone" class="w-full border rounded p-2">
                    @error('phone')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="active" id="teacherActive" value="1" checked class="w-4 h-4">
                    <label class="text-sm">Activo</label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeTeacherModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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
    const teacherForm = document.getElementById('teacherForm');
    const teacherModalTitle = document.getElementById('teacherModalTitle');
    const teacherPasswordField = document.getElementById('teacherPasswordField');
    const teacherFormMethod = document.getElementById('teacherFormMethod');

    function openTeacherModal() {
        teacherModalTitle.textContent = 'Nuevo profesor';
        teacherForm.reset();
        document.getElementById('teacherId').value = '';
        document.getElementById('teacherActive').checked = true;
        teacherFormMethod.value = 'POST';
        teacherPasswordField.style.display = 'block';
        document.getElementById('teacherPassword').required = true;
        document.getElementById('teacherModal').classList.remove('hidden');
        document.getElementById('teacherModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeTeacherModal() {
        document.getElementById('teacherModal').classList.add('hidden');
        document.getElementById('teacherModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function editTeacher(teacher) {
        teacherModalTitle.textContent = 'Editar profesor';
        document.getElementById('teacherId').value = teacher.id;
        document.getElementById('teacherFirstName').value = teacher.first_name;
        document.getElementById('teacherLastName').value = teacher.last_name;
        document.getElementById('teacherEmail').value = teacher.user?.email || '';
        document.getElementById('teacherCode').value = teacher.code;
        document.getElementById('teacherSpecialty').value = teacher.specialty || '';
        document.getElementById('teacherPhone').value = teacher.user?.phone || '';
        document.getElementById('teacherActive').checked = teacher.user?.active ?? true;
        teacherFormMethod.value = 'PUT';
        teacherPasswordField.style.display = 'none';
        document.getElementById('teacherPassword').required = false;
        document.getElementById('teacherModal').classList.remove('hidden');
        document.getElementById('teacherModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function deleteTeacher(id) {
        if (!confirm('¿Eliminar este profesor?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/teachers/${id}`;
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

    teacherForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(teacherForm);
        const teacherId = document.getElementById('teacherId').value;
        const url = teacherId ? `/admin/teachers/${teacherId}` : '/admin/teachers';

        if (teacherId) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                closeTeacherModal();
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