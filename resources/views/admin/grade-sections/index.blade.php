@extends('layouts.admin')
@section("title", "Grados/Secciones")
@section("content")
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Grados y Secciones</h1>
        <button onclick="openGradeSectionModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo grado/sección
        </button>
    </div>

    <form method="GET" action="{{ route('admin.grade-sections.index') }}" class="bg-white rounded shadow p-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, nivel, año escolar..." class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Buscar
            </button>
            <a href="{{ route('admin.grade-sections.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Nivel</th>
                    <th class="p-2 text-left">Año escolar</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="gradeSections-table">
                @foreach($gradeSections as $gs)
                <tr class="border-t" data-grade-section="{{ $gs->toJson() }}">
                    <td class="p-2">{{ $gs->name }}</td>
                    <td class="p-2">{{ $gs->level }}</td>
                    <td class="p-2">{{ $gs->school_year }}</td>
                    <td class="p-2">
                        <button onclick="editGradeSection({{ $gs }})" class="text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button onclick="deleteGradeSection({{ $gs->id }})" class="text-red-600 ml-2 hover:text-red-800">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $gradeSections->links() }}
    </div>
</div>

<div id="gradeSectionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="gradeSectionModalTitle">Nuevo grado/sección</h3>
            <button onclick="closeGradeSectionModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="gradeSectionForm" method="POST" class="p-4">
            @csrf
            <input type="hidden" name="_method" id="gradeSectionFormMethod" value="POST">
            <input type="hidden" name="grade_section_id" id="gradeSectionId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="name" id="gsName" class="w-full border rounded p-2" required>
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nivel</label>
                    <input type="text" name="level" id="gsLevel" class="w-full border rounded p-2" required>
                    @error('level')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Año escolar</label>
                    <input type="text" name="school_year" id="gsSchoolYear" class="w-full border rounded p-2" required>
                    @error('school_year')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeGradeSectionModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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
    const gradeSectionForm = document.getElementById('gradeSectionForm');
    const gradeSectionModalTitle = document.getElementById('gradeSectionModalTitle');
    const gradeSectionFormMethod = document.getElementById('gradeSectionFormMethod');

    function openGradeSectionModal() {
        gradeSectionModalTitle.textContent = 'Nuevo grado/sección';
        gradeSectionForm.reset();
        document.getElementById('gradeSectionId').value = '';
        gradeSectionFormMethod.value = 'POST';
        document.getElementById('gradeSectionModal').classList.remove('hidden');
        document.getElementById('gradeSectionModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeGradeSectionModal() {
        document.getElementById('gradeSectionModal').classList.add('hidden');
        document.getElementById('gradeSectionModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function editGradeSection(gs) {
        gradeSectionModalTitle.textContent = 'Editar grado/sección';
        document.getElementById('gradeSectionId').value = gs.id;
        document.getElementById('gsName').value = gs.name;
        document.getElementById('gsLevel').value = gs.level;
        document.getElementById('gsSchoolYear').value = gs.school_year;
        gradeSectionFormMethod.value = 'PUT';
        document.getElementById('gradeSectionModal').classList.remove('hidden');
        document.getElementById('gradeSectionModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function deleteGradeSection(id) {
        if (!confirm('¿Eliminar este grado/sección?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/grade-sections/${id}`;
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

    gradeSectionForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(gradeSectionForm);
        const gsId = document.getElementById('gradeSectionId').value;
        const url = gsId ? `/admin/grade-sections/${gsId}` : '/admin/grade-sections';

        if (gsId) {
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
                closeGradeSectionModal();
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