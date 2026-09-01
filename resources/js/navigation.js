import {
    Home,
    GraduationCap,
    Users,
    UserSquare2,
    BookOpen,
    School,
    UserCog,
    ClipboardList,
    CalendarCheck,
    CalendarDays,
    CalendarRange,
    Clock,
    BookMarked,
    ClipboardCheck,
    ClipboardEdit,
    Clock3,
    Wallet,
    Landmark,
    BarChart3,
    Settings,
    ClipboardSignature,
} from 'lucide-vue-next';

// Each item's `route` is a Ziggy route name; `match` is the wildcard pattern
// used to highlight the item as active (defaults to `route`).
export function navigationFor(role) {
    if (role === 'admin') {
        return [
            {
                items: [{ label: 'Inicio', icon: Home, route: 'admin.dashboard' }],
            },
            {
                title: 'Personas',
                items: [
                    { label: 'Estudiantes', icon: GraduationCap, route: 'admin.students.index', match: 'admin.students.*' },
                    { label: 'Profesores', icon: UserSquare2, route: 'admin.teachers.index', match: 'admin.teachers.*' },
                    { label: 'Padres/Apoderados', icon: Users, route: 'admin.guardians.index', match: 'admin.guardians.*' },
                    { label: 'Usuarios', icon: UserCog, route: 'admin.users.index', match: 'admin.users.*' },
                ],
            },
            {
                title: 'Académico',
                items: [
                    { label: 'Cursos', icon: BookOpen, route: 'admin.courses.index', match: 'admin.courses.*' },
                    { label: 'Grados/Secciones', icon: School, route: 'admin.grade-sections.index', match: 'admin.grade-sections.*' },
                    { label: 'Año escolar', icon: CalendarDays, route: 'admin.academic.years.index', match: 'admin.academic.years.*' },
                    { label: 'Fases escolares', icon: CalendarRange, route: 'admin.academic.phases.index', match: 'admin.academic.phases.*' },
                    { label: 'Turnos', icon: Clock, route: 'admin.academic.shifts.index', match: 'admin.academic.shifts.*' },
                    { label: 'Materias', icon: BookMarked, route: 'admin.academic.subjects.index', match: 'admin.academic.subjects.*' },
                    { label: 'Criterios de evaluación', icon: ClipboardEdit, route: 'admin.academic.evaluation-criteria.index', match: 'admin.academic.evaluation-criteria.*' },
                    { label: 'Notas', icon: ClipboardCheck, route: 'admin.grades.index', match: 'admin.grades.*' },
                    { label: 'Horarios', icon: Clock3, route: 'admin.academic.schedules.index', match: 'admin.academic.schedules.*' },
                    { label: 'Horario del colegio', icon: Clock, route: 'admin.academic.school-schedule.index', match: 'admin.academic.school-schedule.*' },
                ],
            },
            {
                title: 'Asistencia',
                items: [
                    { label: 'Asistencia en aula', icon: ClipboardList, route: 'admin.attendance.index', match: 'admin.attendance.index' },
                    { label: 'Asistencia al colegio', icon: CalendarCheck, route: 'admin.attendance.general', match: 'admin.attendance.general' },
                ],
            },
            {
                title: 'Finanzas',
                items: [
                    { label: 'Matrículas', icon: ClipboardSignature, route: 'admin.enrollments.index', match: 'admin.enrollments.*' },
                    { label: 'Pagos', icon: Wallet, route: 'admin.payments.index', match: 'admin.payments.*' },
                    { label: 'Contabilidad', icon: Landmark, route: 'admin.accounting.index', match: 'admin.accounting.*' },
                ],
            },
            {
                title: 'Sistema',
                items: [
                    { label: 'Reportes', icon: BarChart3, route: 'admin.reports.index', match: 'admin.reports.*' },
                    { label: 'Configuraciones', icon: Settings, route: 'admin.settings.index', match: 'admin.settings.*' },
                ],
            },
        ];
    }

    if (role === 'teacher') {
        return [{ items: [{ label: 'Inicio', icon: Home, route: 'teacher.dashboard' }] }];
    }

    if (role === 'parent') {
        return [{ items: [{ label: 'Inicio', icon: Home, route: 'parent.dashboard' }] }];
    }

    return [{ items: [{ label: 'Inicio', icon: Home, route: 'student.dashboard' }] }];
}
