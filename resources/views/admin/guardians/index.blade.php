@extends('layouts.admin')
@section("title", "Apoderados")
@section("content")
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Apoderados</h1>
        <button onclick="openGuardianModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo apoderado
        </button>
    </div>

    <form method="GET" action="{{ route('admin.guardians.index') }}" class="bg-white rounded shadow p-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, WhatsApp, email..." class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Buscar
            </button>
            <a href="{{ route('admin.guardians.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">WhatsApp</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="guardians-table">
                @foreach($guardians as $guardian)
                <tr class="border-t" data-guardian="{{ $guardian->toJson() }}">
                    <td class="p-2">{{ $guardian->fullName() }}</td>
                    <td class="p-2">{{ $guardian->phone_whatsapp }}</td>
                    <td class="p-2">{{ $guardian->user->email }}</td>
                    <td class="p-2">
                        <button onclick="editGuardian({{ $guardian }})" class="text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button onclick="deleteGuardian({{ $guardian->id }})" class="text-red-600 ml-2 hover:text-red-800">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $guardians->links() }}
    </div>
</div>

<div id="guardianModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="guardianModalTitle">Nuevo apoderado</h3>
            <button onclick="closeGuardianModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="guardianForm" method="POST" class="p-4">
            @csrf
            <input type="hidden" name="_method" id="guardianFormMethod" value="POST">
            <input type="hidden" name="guardian_id" id="guardianId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="first_name" id="guardianFirstName" class="w-full border rounded p-2" required>
                    @error('first_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Apellido</label>
                    <input type="text" name="last_name" id="guardianLastName" class="w-full border rounded p-2" required>
                    @error('last_name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="guardianEmail" class="w-full border rounded p-2" required>
                    @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div id="guardianPasswordField">
                    <label class="block text-sm font-medium mb-1">Contraseña</label>
                    <input type="password" name="password" id="guardianPassword" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la actual (solo edición).</p>
                    @error('password')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">WhatsApp</label>
                    <input type="text" name="phone_whatsapp" id="guardianWhatsApp" class="w-full border rounded p-2" required>
                    @error('phone_whatsapp')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="phone" id="guardianPhone" class="w-full border rounded p-2">
                    @error('phone')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeGuardianModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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
    const guardianForm = document.getElementById('guardianForm');
    const guardianModalTitle = document.getElementById('guardianModalTitle');
    const guardianPasswordField = document.getElementById('guardianPasswordField');
    const guardianFormMethod = document.getElementById('guardianFormMethod');

    function openGuardianModal() {
        guardianModalTitle.textContent = 'Nuevo apoderado';
        guardianForm.reset();
        document.getElementById('guardianId').value = '';
        guardianFormMethod.value = 'POST';
        guardianPasswordField.style.display = 'block';
        document.getElementById('guardianPassword').required = true;
        document.getElementById('guardianModal').classList.remove('hidden');
        document.getElementById('guardianModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeGuardianModal() {
        document.getElementById('guardianModal').classList.add('hidden');
        document.getElementById('guardianModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function editGuardian(guardian) {
        guardianModalTitle.textContent = 'Editar apoderado';
        document.getElementById('guardianId').value = guardian.id;
        document.getElementById('guardianFirstName').value = guardian.first_name;
        document.getElementById('guardianLastName').value = guardian.last_name;
        document.getElementById('guardianEmail').value = guardian.user?.email || '';
        document.getElementById('guardianWhatsApp').value = guardian.phone_whatsapp;
        document.getElementById('guardianPhone').value = guardian.user?.phone || '';
        guardianFormMethod.value = 'PUT';
        guardianPasswordField.style.display = 'none';
        document.getElementById('guardianPassword').required = false;
        document.getElementById('guardianModal').classList.remove('hidden');
        document.getElementById('guardianModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function deleteGuardian(id) {
        if (!confirm('¿Eliminar este apoderado?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/guardians/${id}`;
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

    guardianForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(guardianForm);
        const guardianId = document.getElementById('guardianId').value;
        const url = guardianId ? `/admin/guardians/${guardianId}` : '/admin/guardians';

        if (guardianId) {
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
                closeGuardianModal();
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