<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", "Sistema Escolar")</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-indigo-800 text-white flex flex-col">
            <div class="p-4 border-b border-indigo-700">
                <h1 class="text-xl font-bold">🏫 Sistema Escolar</h1>
                <p class="text-xs text-indigo-300 mt-1">Administrador (Admin)</p>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <div class="px-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-900' : '' }}">
                        <span>🏠</span>
                        <span class="text-sm">Inicio</span>
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.students.*') ? 'bg-indigo-900' : '' }}">
                        <span>👨‍🎓</span>
                        <span class="text-sm">Estudiantes</span>
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.teachers.*') ? 'bg-indigo-900' : '' }}">
                        <span>👩‍🏫</span>
                        <span class="text-sm">Profesores</span>
                    </a>
                    <a href="{{ route('admin.guardians.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.guardians.*') ? 'bg-indigo-900' : '' }}">
                        <span>👪</span>
                        <span class="text-sm">Padres/Apoderados</span>
                    </a>
                    <a href="{{ route('admin.courses.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.courses.*') ? 'bg-indigo-900' : '' }}">
                        <span>📚</span>
                        <span class="text-sm">Cursos</span>
                    </a>
                    <a href="{{ route('admin.grade-sections.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.grade-sections.*') ? 'bg-indigo-900' : '' }}">
                        <span>🏫</span>
                        <span class="text-sm">Grados/Secciones</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.users.*') ? 'bg-indigo-900' : '' }}">
                        <span>👤</span>
                        <span class="text-sm">Usuarios</span>
                    </a>
                    <a href="{{ route('admin.attendance.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.attendance.index') || request()->routeIs('admin.attendance.scanner') || request()->routeIs('admin.attendance.manual') || request()->routeIs('admin.attendance.report') ? 'bg-indigo-900' : '' }}">
                        <span>📋</span>
                        <span class="text-sm">Asistencia en aula</span>
                    </a>
                    <a href="{{ route('admin.attendance.general') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.attendance.general') ? 'bg-indigo-900' : '' }}">
                        <span>🏫</span>
                        <span class="text-sm">Asistencia al colegio</span>
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Gestión académica</p>
                    </div>

                    <a href="{{ route('admin.academic.years.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.academic.years.*') ? 'bg-indigo-900' : '' }}">
                        <span>📅</span>
                        <span class="text-sm">Año escolar</span>
                    </a>
                    <a href="{{ route('admin.academic.phases.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.academic.phases.*') ? 'bg-indigo-900' : '' }}">
                        <span>📆</span>
                        <span class="text-sm">Fases escolares</span>
                    </a>
                    <a href="{{ route('admin.academic.shifts.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.academic.shifts.*') ? 'bg-indigo-900' : '' }}">
                        <span>🕐</span>
                        <span class="text-sm">Turnos</span>
                    </a>
                    <a href="{{ route('admin.academic.subjects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.academic.subjects.*') ? 'bg-indigo-900' : '' }}">
                        <span>📖</span>
                        <span class="text-sm">Materias</span>
                    </a>
                    <a href="{{ route('admin.academic.evaluation-criteria.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.academic.evaluation-criteria.*') || request()->routeIs('admin.grades.*') ? 'bg-indigo-900' : '' }}">
                        <span>📝</span>
                        <span class="text-sm">Criterios de evaluación</span>
                    </a>
                    <a href="{{ route('admin.grades.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.grades.*') ? 'bg-indigo-900' : '' }}">
                        <span>🎓</span>
                        <span class="text-sm">Notas</span>
                    </a>
                    <a href="{{ route('admin.academic.schedules.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.academic.schedules.*') ? 'bg-indigo-900' : '' }}">
                        <span>🕒</span>
                        <span class="text-sm">Horarios</span>
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Finanzas</p>
                    </div>

                    <a href="{{ route('admin.enrollments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.enrollments.*') ? 'bg-indigo-900' : '' }}">
                        <span>📋</span>
                        <span class="text-sm">Matrículas</span>
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.payments.*') ? 'bg-indigo-900' : '' }}">
                        <span>💵</span>
                        <span class="text-sm">Pagos</span>
                    </a>
                    <a href="{{ route('admin.accounting.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.accounting.*') ? 'bg-indigo-900' : '' }}">
                        <span>💰</span>
                        <span class="text-sm">Contabilidad</span>
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider">Sistema</p>
                    </div>

                    <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-900' : '' }}">
                        <span>📊</span>
                        <span class="text-sm">Reportes</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-indigo-700 {{ request()->routeIs('admin.settings.*') ? 'bg-indigo-900' : '' }}">
                        <span>⚙️</span>
                        <span class="text-sm">Configuraciones</span>
                    </a>
                </div>
            </nav>

            <div class="p-4 border-t border-indigo-700">
                <form method="POST" action="{{ route("logout") }}">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 bg-indigo-900 hover:bg-indigo-950 text-white px-3 py-2 rounded-md text-sm">
                        <span>🚪</span>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-8">
                @if(session("success"))
                    <div class="bg-green-100 text-green-800 border border-green-300 rounded p-3 mb-4">
                        {{ session("success") }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-100 text-red-800 border border-red-300 rounded p-3 mb-4">
                        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                @yield("content")
            </div>
        </main>
    </div>
</body>
</html>
