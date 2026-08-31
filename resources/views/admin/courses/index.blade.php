@extends('layouts.admin')
@section("title", "Cursos")
@section("content")
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold">Cursos</h1>
        <button onclick="openCourseModal()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            + Nuevo curso
        </button>
    </div>

    <form method="GET" action="{{ route('admin.courses.index') }}" class="bg-white rounded shadow p-4">
        <div class="flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium mb-1">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, año escolar..." class="w-full border rounded p-2">
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Buscar
            </button>
            <a href="{{ route('admin.courses.index') }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                Limpiar
            </a>
        </div>
    </form>

    <div class="bg-white rounded shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Nombre</th>
                    <th class="p-2 text-left">Grado/Sección</th>
                    <th class="p-2 text-left">Año escolar</th>
                    <th class="p-2 text-left">Profesor</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody id="courses-table">
                @foreach($courses as $course)
                <tr class="border-t" data-course="{{ $course->toJson() }}">
                    <td class="p-2">{{ $course->name }}</td>
                    <td class="p-2">{{ $course->gradeSection->name ?? '-' }}</td>
                    <td class="p-2">{{ $course->school_year }}</td>
                    <td class="p-2">{{ $course->teacher->fullName() ?? '-' }}</td>
                    <td class="p-2">
                        <button onclick="editCourse({{ $course }})" class="text-indigo-600 hover:text-indigo-800">Editar</button>
                        <button onclick="deleteCourse({{ $course->id }})" class="text-red-600 ml-2 hover:text-red-800">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-center">
        {{ $courses->links() }}
    </div>
</div>

<div id="courseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold" id="courseModalTitle">Nuevo curso</h3>
            <button onclick="closeCourseModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="courseForm" method="POST" class="p-4">
            @csrf
            <input type="hidden" name="_method" id="courseFormMethod" value="POST">
            <input type="hidden" name="course_id" id="courseId">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre</label>
                    <input type="text" name="name" id="courseName" class="w-full border rounded p-2" required>
                    @error('name')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Año escolar</label>
                    <input type="text" name="school_year" id="courseSchoolYear" class="w-full border rounded p-2" required>
                    @error('school_year')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Grado/Sección</label>
                    <select name="grade_section_id" id="courseGradeSectionId" class="w-full border rounded p-2" required>
                        <option value="">Seleccionar...</option>
                        @foreach($gradeSections as $gs)
                        <option value="{{ $gs->id }}">{{ $gs->name }} - {{ $gs->level }}</option>
                        @endforeach
                    </select>
                    @error('grade_section_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Profesor</label>
                    <select name="teacher_id" id="courseTeacherId" class="w-full border rounded p-2">
                        <option value="">Sin profesor</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->fullName() }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')<span class="text-red-600 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeCourseModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
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
    const courseForm = document.getElementById('courseForm');
    const courseModalTitle = document.getElementById('courseModalTitle');
    const courseFormMethod = document.getElementById('courseFormMethod');

    function openCourseModal() {
        courseModalTitle.textContent = 'Nuevo curso';
        courseForm.reset();
        document.getElementById('courseId').value = '';
        courseFormMethod.value = 'POST';
        document.getElementById('courseModal').classList.remove('hidden');
        document.getElementById('courseModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeCourseModal() {
        document.getElementById('courseModal').classList.add('hidden');
        document.getElementById('courseModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function editCourse(course) {
        courseModalTitle.textContent = 'Editar curso';
        document.getElementById('courseId').value = course.id;
        document.getElementById('courseName').value = course.name;
        document.getElementById('courseSchoolYear').value = course.school_year;
        document.getElementById('courseGradeSectionId').value = course.grade_section_id;
        document.getElementById('courseTeacherId').value = course.teacher_id || '';
        courseFormMethod.value = 'PUT';
        document.getElementById('courseModal').classList.remove('hidden');
        document.getElementById('courseModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function deleteCourse(id) {
        if (!confirm('¿Eliminar este curso?')) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/courses/${id}`;
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

    courseForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(courseForm);
        const courseId = document.getElementById('courseId').value;
        const url = courseId ? `/admin/courses/${courseId}` : '/admin/courses';

        if (courseId) {
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
                closeCourseModal();
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