@extends('layouts.admin')
@section("title", "Usuarios")
@section("content")
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Usuarios</h1>
        <button onclick="openUserModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo usuario
        </button>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="bg-white rounded shadow p-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, email, rol..." class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Buscar
            </button>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Rol</th>
                    <th class="p-2 text-left">Teléfono</th>
                    <th class="p-2 text-left">Estado</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="users-table">
                @foreach($users as $user)
                <tr class="border-t" data-user="{{ $user->toJson() }}">
                    <td class="p-2">{{ $user->name }}</td>
                    <td class="p-2">{{ $user->email }}</td>
                    <td class="p-2">{{ ucfirst($user->role) }}</td>
                    <td class="p-2">{{ $user->phone ?? '-' }}</td>
                    <td class="p-2">
                        <span class="px-2 py-1 rounded text-xs {{ $user->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="p-2">
                        <button onclick="editUser({{ $user }})" class="text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button onclick="toggleUserActive({{ $user->id }}, {{ $user->active ? 'true' : 'false' }})" class="text-yellow-600 ml-2 hover:text-yellow-800">
                            {{ $user->active ? 'Desactivar' : 'Activar' }}
                        </button>
                        <button onclick="deleteUser({{ $user->id }})" class="text-red-600 ml-2 hover:text-red-800">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $users->links() }}
    </div>
</div>

<div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="userModalTitle">Nuevo usuario</h3>
            <button onclick="closeUserModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="userForm" method="POST" class="p-4">
            @csrf
            <input type="hidden" name="_method" id="userFormMethod" value="POST">
            <input type="hidden" name="user_id" id="userId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="name" id="userName" class="w-full border rounded p-2" required>
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="email" id="userEmail" class="w-full border rounded p-2" required>
                    @error('email')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div id="userPasswordField">
                    <label class="block text-sm font-medium mb-1">Contraseña</label>
                    <input type="password" name="password" id="userPassword" class="w-full border rounded p-2">
                    <p class="text-xs text-gray-500 mt-1">Dejar vacío para mantener la actual (solo edición).</p>
                    @error('password')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Rol</label>
                    <select name="role" id="userRole" class="w-full border rounded p-2" required>
                        @foreach($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input type="text" name="phone" id="userPhone" class="w-full border rounded p-2">
                    @error('phone')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="active" id="userActive" value="1" checked class="w-4 h-4">
                    <label class="text-sm">Activo</label>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeUserModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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
    const userForm = document.getElementById('userForm');
    const userModalTitle = document.getElementById('userModalTitle');
    const userPasswordField = document.getElementById('userPasswordField');
    const userFormMethod = document.getElementById('userFormMethod');

    function openUserModal() {
        userModalTitle.textContent = 'Nuevo usuario';
        userForm.reset();
        document.getElementById('userId').value = '';
        document.getElementById('userActive').checked = true;
        userFormMethod.value = 'POST';
        userPasswordField.style.display = 'block';
        document.getElementById('userPassword').required = true;
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function editUser(user) {
        userModalTitle.textContent = 'Editar usuario';
        document.getElementById('userId').value = user.id;
        document.getElementById('userName').value = user.name;
        document.getElementById('userEmail').value = user.email;
        document.getElementById('userRole').value = user.role;
        document.getElementById('userPhone').value = user.phone || '';
        document.getElementById('userActive').checked = user.active;
        userFormMethod.value = 'PUT';
        userPasswordField.style.display = 'none';
        document.getElementById('userPassword').required = false;
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function toggleUserActive(id, currentStatus) {
        const action = currentStatus ? 'desactivar' : 'activar';
        if (!confirm(`¿Estás seguro de que deseas ${action} este usuario?`)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}/toggle-active`;
        form.innerHTML = `
            @csrf
            <input type="hidden" name="_method" value="POST">
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
                showToast(data.message || 'Error al cambiar estado', 'error');
            }
        })
        .catch(() => {
            showToast('Error al cambiar estado', 'error');
        })
        .finally(() => {
            document.body.removeChild(form);
        });
    }

    function deleteUser(id) {
        if (!confirm('¿Eliminar este usuario?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${id}`;
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

    userForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(userForm);
        const userId = document.getElementById('userId').value;
        const url = userId ? `/admin/users/${userId}` : '/admin/users';

        if (userId) {
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
                closeUserModal();
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