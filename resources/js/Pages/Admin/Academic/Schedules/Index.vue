<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    Building2,
    Calendar,
    ChevronLeft,
    ChevronRight,
    DoorOpen,
    MapPin,
    Pencil,
    Plus,
    Printer,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SelectField from '@/Components/SelectField.vue';
import TimeField from '@/Components/TimeField.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    classroom: { type: [String, null], default: null },
    classrooms: { type: Array, default: () => [] },
    weeks: { type: Array, default: () => [] },
    schedulesByDay: { type: Object, default: () => ({}) },
    courses: { type: Array, default: () => [] },
    shifts: { type: Array, default: () => [] },
    teachers: { type: Array, default: () => [] },
    teacherId: { type: [Number, null], default: null },
    schoolHours: { type: Object, default: () => null },
    schoolYear: { type: Object, default: () => ({ start: null, end: null }) },
    holidayDates: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const page = usePage();

const days = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

const SLOT_MINUTES = 60;
const selectedShift = ref('');

// Default view: the school's official entry/exit hours (Horario del colegio),
// not an arbitrary 7am-6pm window — falls back to that only if no official
// schedule is configured yet.
const officialRange = computed(() => {
    const start = Number(props.schoolHours?.start?.split(':')[0]);
    const end = Number(props.schoolHours?.end?.split(':')[0]);

    if (Number.isFinite(start) && Number.isFinite(end) && end > start) {
        return { start, end };
    }

    return { start: 7, end: 18 };
});

const activeShift = computed(() => {
    if (!selectedShift.value) {
        return {
            id: '',
            start: officialRange.value.start,
            end: officialRange.value.end,
            name: props.schoolHours?.name ?? 'Horario del colegio',
        };
    }

    const shift = props.shifts.find((item) => String(item.id) === String(selectedShift.value));
    if (!shift) {
        return {
            id: '',
            start: officialRange.value.start,
            end: officialRange.value.end,
            name: props.schoolHours?.name ?? 'Horario del colegio',
        };
    }

    const start = Number(shift.start_time?.split(':')[0] ?? officialRange.value.start);
    const end = Number(shift.end_time?.split(':')[0] ?? officialRange.value.end);

    return { ...shift, start, end, name: shift.name };
});

const timeSlots = computed(() => {
    const slots = [];
    const startHour = activeShift.value.start;
    const endHour = activeShift.value.end;

    for (let h = startHour; h <= endHour; h++) {
        slots.push(`${String(h).padStart(2, '0')}:00`);
    }

    return slots;
});

function visibleSchedulesForDay(day) {
    return (props.schedulesByDay[day] ?? []).filter((schedule) => {
        if (selectedClassroom.value && schedule.classroom !== selectedClassroom.value) {
            return false;
        }

        if (selectedTeacher.value) {
            const teacherId = Number(schedule.course?.teacher?.id ?? schedule.teacher_id ?? 0);
            if (teacherId !== Number(selectedTeacher.value)) {
                return false;
            }
        }

        if (!selectedShift.value) return true;
        if (!schedule.shift_id) return true;
        return String(schedule.shift_id) === String(selectedShift.value);
    });
}

function formatTime(time) {
    if (!time) return '';
    return time.substring(0, 5);
}

function timeToMinutes(time) {
    if (!time) return 0;
    const [h, m] = time.split(':').map(Number);
    return h * 60 + m;
}

const classroomOptions = computed(() =>
    props.classrooms.map((c) => ({ value: c, label: `Aula ${c}` }))
);

const teacherOptions = computed(() =>
    props.teachers.map((t) => ({
        value: t.id,
        label: t.code ? `${t.name} (${t.code})` : t.name,
    }))
);

const selectedClassroom = ref(props.classroom || '');
const selectedTeacher = ref(props.teacherId ? String(props.teacherId) : '');

const selectedTeacherInfo = computed(() => {
    if (!selectedTeacher.value) return null;
    return props.teachers.find((t) => String(t.id) === String(selectedTeacher.value)) ?? null;
});

function formatClassroomLabel(classroom) {
    if (!classroom) return 'Sin aula';
    const value = String(classroom).trim();
    return value.toLowerCase().startsWith('aula ') ? value : `Aula ${value}`;
}

const teacherSummary = computed(() => {
    if (!selectedTeacher.value) return null;

    const all = Object.values(props.schedulesByDay).flat();
    const own = all.filter((sch) => Number(sch.course?.teacher?.id) === Number(selectedTeacher.value));

    const classrooms = Array.from(new Set(own.map((sch) => sch.classroom).filter(Boolean))).sort().map(formatClassroomLabel);
    const dayCount = new Set(own.map((sch) => sch.day_of_week)).size;
    const totalMinutes = own.reduce((acc, sch) => {
        return acc + Math.max(0, timeToMinutes(sch.end_time) - timeToMinutes(sch.start_time));
    }, 0);

    return {
        count: own.length,
        classrooms,
        dayCount,
        totalMinutes,
        totalHours: Math.round((totalMinutes / 60) * 10) / 10,
        blocks: own,
    };
});

const displayedClassroomGroups = computed(() => {
    const groups = [];

    if (selectedClassroom.value) {
        const blocks = Object.values(props.schedulesByDay).flat().filter((sch) => sch.classroom === selectedClassroom.value);
        groups.push({ classroom: selectedClassroom.value, blocks });
        return groups;
    }

    if (selectedTeacher.value && teacherSummary.value) {
        if (!selectedClassroom.value) {
            return [{ classroom: null, blocks: teacherSummary.value.blocks }];
        }

        const byClassroom = new Map();
        for (const sch of teacherSummary.value.blocks) {
            const key = sch.classroom || 'Sin aula';
            if (!byClassroom.has(key)) byClassroom.set(key, []);
            byClassroom.get(key).push(sch);
        }
        const sortedKeys = Array.from(byClassroom.keys()).sort((a, b) => {
            if (a === 'Sin aula') return 1;
            if (b === 'Sin aula') return -1;
            return String(a).localeCompare(String(b));
        });
        for (const key of sortedKeys) {
            groups.push({ classroom: key === 'Sin aula' ? null : key, blocks: byClassroom.get(key) });
        }
        return groups;
    }

    return [];
});

function groupTitle(classroom) {
    if (selectedTeacher.value && selectedTeacherInfo.value) {
        if (!classroom) return selectedTeacherInfo.value.name + ' · Todas las aulas';
        return selectedTeacherInfo.value.name + ' · Aula ' + classroom;
    }
    return 'Aula ' + (classroom ?? '-');
}

const courseOptions = computed(() =>
    props.courses.map((c) => ({
        value: c.id,
        label: `${c.subject?.name ?? c.name} - ${c.grade_section?.name ?? 'Sin sección'}${c.teacher ? ` (${c.teacher.first_name} ${c.teacher.last_name})` : ' (sin profesor)'}`,
    }))
);

const shiftOptions = computed(() =>
    props.shifts.map((s) => ({ value: s.id, label: s.name }))
);

const dayOptions = days.map((d) => ({ value: d, label: d }));

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    course_id: '',
    shift_id: '',
    day_of_week: '',
    start_time: '',
    end_time: '',
    classroom: '',
});

function openCreate(day = '', start = '') {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.classroom = selectedClassroom.value || '';
    form.day_of_week = day;
    form.start_time = start;
    showModal.value = true;
}

function openEdit(schedule) {
    editing.value = schedule;
    form.clearErrors();
    form.course_id = schedule.course_id;
    form.shift_id = schedule.shift_id ?? '';
    form.day_of_week = schedule.day_of_week;
    form.start_time = schedule.start_time;
    form.end_time = schedule.end_time;
    form.classroom = schedule.classroom ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.academic.schedules.update', editing.value.id), {
            onSuccess: () => (showModal.value = false),
        });
    } else {
        form.post(route('admin.academic.schedules.store'), {
            onSuccess: () => (showModal.value = false),
        });
    }
}

function destroy(schedule) {
    if (confirm('¿Eliminar este bloque del horario?')) {
        router.delete(route('admin.academic.schedules.destroy', schedule.id), {
            preserveScroll: true,
        });
    }
}

function applyClassroom(value) {
    selectedClassroom.value = value;
    router.get(
        route('admin.academic.schedules.index'),
        { classroom: value || undefined, teacher_id: selectedTeacher.value || undefined },
        { preserveState: true, replace: true }
    );
}

function applyTeacher(value) {
    selectedTeacher.value = value ? String(value) : '';
    router.get(
        route('admin.academic.schedules.index'),
        { classroom: selectedClassroom.value || undefined, teacher_id: selectedTeacher.value || undefined },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    selectedClassroom.value = '';
    selectedTeacher.value = '';
    router.get(
        route('admin.academic.schedules.index'),
        {},
        { preserveState: true, replace: true }
    );
}

function goToWeek(offset) {
    const base = props.weeks[0] || new Date().toISOString().substring(0, 10);
    const next = new Date(base);
    next.setDate(next.getDate() + offset * 7);
    router.get(
        route('admin.academic.schedules.index'),
        {
            classroom: selectedClassroom.value || undefined,
            teacher_id: selectedTeacher.value || undefined,
            week: next.toISOString().substring(0, 10),
        },
        { preserveState: true }
    );
}

function printSchedule() {
    window.print();
}

function todayLabel() {
    return new Date().toLocaleDateString('es-PE', { weekday: 'long', day: 'numeric', month: 'long' });
}

const currentWeek = computed(() => {
    if (!props.weeks.length) return '';
    const first = new Date(props.weeks[0]);
    const last = new Date(props.weeks[props.weeks.length - 1]);
    const fmt = (d) => d.toLocaleDateString('es-PE', { day: 'numeric', month: 'short' });
    return `${fmt(first)} - ${fmt(last)}`;
});

const weekDayDates = computed(() => {
    if (!props.weeks.length) {
        return days.map((name) => ({ name, date: '' }));
    }

    const base = new Date(props.weeks[0]);

    return days.map((name, index) => {
        const date = new Date(base);
        date.setDate(base.getDate() + index);
        return { name, date: date.toISOString().slice(0, 10) };
    });
});

const dateRangeLabel = computed(() => {
    const start = props.schoolYear?.start ? new Date(props.schoolYear.start) : null;
    const end = props.schoolYear?.end ? new Date(props.schoolYear.end) : null;

    if (!start || !end) return currentWeek.value || 'Calendario';

    const fmt = (d) => d.toLocaleDateString('es-PE', { day: 'numeric', month: 'short' });
    return `${fmt(start)} - ${fmt(end)}`;
});

function dateForDay(day) {
    return weekDayDates.value.find((item) => item.name === day)?.date ?? null;
}

function isHoliday(dateString) {
    if (!dateString || !props.holidayDates?.length) return false;
    return props.holidayDates.includes(dateString);
}

function dayCellClass(day) {
    const date = dateForDay(day);
    return isHoliday(date) ? 'bg-red-50 border-red-200' : 'bg-white';
}

// Only the slot a block *starts* at should render it — a block covering
// several slots (e.g. 105 min = 7 fifteen-minute slots) was previously drawn
// once per overlapping slot, producing duplicate stacked cards.
function schedulesStartingAt(day, slot) {
    const list = visibleSchedulesForDay(day);
    const slotMinutes = timeToMinutes(slot);
    return list.filter((sch) => timeToMinutes(sch.start_time) === slotMinutes);
}

// A slot "covered" by an earlier block's rowspan must not emit its own <td>,
// or the browser shifts every later column in that row over by one.
function isCovered(day, slot) {
    const list = visibleSchedulesForDay(day);
    const slotMinutes = timeToMinutes(slot);
    return list.some((sch) => timeToMinutes(sch.start_time) < slotMinutes && slotMinutes < timeToMinutes(sch.end_time));
}

function rowSpan(schedule) {
    const span = Math.round((timeToMinutes(schedule.end_time) - timeToMinutes(schedule.start_time)) / SLOT_MINUTES);
    return Math.max(1, span);
}

function scheduleColor(sch) {
    const palette = [
        { bg: 'bg-brand-50', border: 'border-brand-200', text: 'text-brand-700', accent: 'bg-brand-500' },
        { bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-700', accent: 'bg-emerald-500' },
        { bg: 'bg-sky-50', border: 'border-sky-200', text: 'text-sky-700', accent: 'bg-sky-500' },
        { bg: 'bg-violet-50', border: 'border-violet-200', text: 'text-violet-700', accent: 'bg-violet-500' },
        { bg: 'bg-amber-50', border: 'border-amber-200', text: 'text-amber-700', accent: 'bg-amber-500' },
        { bg: 'bg-rose-50', border: 'border-rose-200', text: 'text-rose-700', accent: 'bg-rose-500' },
    ];
    const idx = (sch.course_id ?? sch.id ?? 0) % palette.length;
    return palette[idx];
}
</script>

<template>
    <Head title="Horarios" />

    <AppLayout title="Horarios">
        <div class="space-y-5">
            <PageHeader
                class="print:hidden"
                eyebrow="Académico"
                title="Horarios por aula"
                description="Visualiza y edita el horario semanal de cada aula. Cada bloque muestra la materia, el profesor y el horario exacto."
            >
                <div class="flex items-center gap-2 print:hidden">
                    <button
                        type="button"
                        :disabled="!selectedClassroom && !selectedTeacher"
                        :title="selectedClassroom || selectedTeacher ? `Imprimir horario ${selectedTeacher ? 'del profesor' : 'del aula'} ${selectedTeacher ? selectedTeacherInfo?.name ?? 'seleccionado' : selectedClassroom}` : 'Selecciona un aula o profesor para imprimir'"
                        class="flex items-center gap-2 rounded-lg border border-border bg-surface px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-surface-muted disabled:cursor-not-allowed disabled:opacity-50 dark:text-slate-200"
                        @click="printSchedule"
                    >
                        <Printer class="h-4 w-4" /> Imprimir
                    </button>
                </div>
            </PageHeader>

            <div class="hidden print:block print:mb-2 print:rounded-none print:bg-white print:p-0">
                <div class="flex items-start justify-between gap-3 border-b border-slate-300 pb-2">
                    <div class="min-w-0">
                        <p class="text-sm font-bold uppercase tracking-wide text-slate-900">
                            {{ page.props.schoolSettings?.name || page.props.appName || 'Institución' }}
                        </p>
                        <p v-if="page.props.schoolSettings?.address" class="text-[9px] text-slate-600">
                            {{ page.props.schoolSettings.address }}
                        </p>
                        <p v-if="page.props.schoolSettings?.phone" class="text-[9px] text-slate-600">
                            {{ page.props.schoolSettings.phone }}
                        </p>
                    </div>
                    <div class="text-right text-[9px] text-slate-700">
                        <p class="font-bold uppercase tracking-wide text-slate-900">Horario académico</p>
                        <p><span class="font-semibold">Semana:</span> {{ currentWeek || '—' }}</p>
                        <p v-if="selectedTeacherInfo"><span class="font-semibold">Profesor:</span> {{ selectedTeacherInfo.name }}</p>
                        <p v-if="activeShift.name"><span class="font-semibold">Turno:</span> {{ activeShift.name }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm print:hidden dark:border-slate-700 dark:bg-slate-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div class="grid w-full flex-1 grid-cols-1 gap-3 sm:grid-cols-2">
                        <SelectField
                            v-model="selectedClassroom"
                            label="Filtrar por aula"
                            placeholder="Todas las aulas"
                            :options="classroomOptions"
                            @update:model-value="applyClassroom"
                        />
                        <SelectField
                            v-model="selectedTeacher"
                            label="Filtrar por profesor"
                            placeholder="Todos los profesores"
                            :options="teacherOptions"
                            @update:model-value="applyTeacher"
                        />
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            v-if="selectedClassroom || selectedTeacher"
                            type="button"
                            class="flex h-10 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            title="Limpiar filtros"
                            @click="clearFilters"
                        >
                            <X class="h-3.5 w-3.5" /> Limpiar
                        </button>
                        <button
                            type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            @click="goToWeek(-1)"
                        >
                            <ChevronLeft class="h-4 w-4" />
                        </button>
                        <div class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:bg-slate-700/50 dark:text-slate-200">
                            <Calendar class="h-4 w-4 text-brand-500" />
                            {{ currentWeek }}
                        </div>
                        <button
                            type="button"
                            class="flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            @click="goToWeek(1)"
                        >
                            <ChevronRight class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Mostrando de <strong class="text-slate-700 dark:text-slate-200">{{ String(activeShift.start).padStart(2, '0') }}:00</strong>
                        a <strong class="text-slate-700 dark:text-slate-200">{{ String(activeShift.end).padStart(2, '0') }}:00</strong>
                        ({{ activeShift.name }})
                    </p>
                    <div v-if="props.shifts.length" class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-1 dark:border-slate-700 dark:bg-slate-700/40">
                        <button
                            type="button"
                            class="rounded-md px-3 py-1.5 text-xs font-medium transition"
                            :class="selectedShift ? 'bg-white text-slate-700 shadow-sm dark:bg-slate-800 dark:text-slate-100' : 'bg-brand-600 text-white shadow-sm'"
                            @click="selectedShift = ''"
                        >
                            Horario oficial
                        </button>
                        <button
                            v-for="shift in props.shifts"
                            :key="shift.id"
                            type="button"
                            class="rounded-md px-3 py-1.5 text-xs font-medium transition"
                            :class="String(selectedShift) === String(shift.id) ? 'bg-brand-600 text-white shadow-sm' : 'text-slate-600 hover:bg-white/70 dark:text-slate-200 dark:hover:bg-slate-800'"
                            @click="selectedShift = String(shift.id)"
                        >
                            {{ shift.name }}
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="!selectedClassroom && !selectedTeacher" class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center print:hidden dark:border-slate-700 dark:bg-slate-800">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                    <DoorOpen class="h-6 w-6" />
                </div>
                <h3 class="mt-4 text-base font-semibold text-slate-800 dark:text-slate-100">Selecciona un aula o profesor</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Elige un aula para ver su horario semanal, o un profesor para ver todos los bloques que dicta esta semana.
                </p>
            </div>

            <div v-else-if="selectedClassroom || selectedTeacher" class="space-y-6 print:space-y-0">
                <div
                    v-for="group in displayedClassroomGroups"
                    :key="group.classroom || 'sin-aula'"
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm print:rounded-none print:border-0 print:shadow-none dark:border-slate-700 dark:bg-slate-800"
                >
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 print:hidden dark:border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                                <Building2 class="h-5 w-5" />
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                                    {{ groupTitle(group.classroom) }}
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ group.blocks.length }} bloque{{ group.blocks.length === 1 ? '' : 's' }} esta semana
                                </p>
                            </div>
                        </div>
                        <button
                            v-if="selectedClassroom === group.classroom || (!selectedClassroom && selectedTeacher)"
                            type="button"
                            class="flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700"
                            @click="clearFilters"
                        >
                            <X class="h-3.5 w-3.5" /> Quitar filtro
                        </button>
                    </div>

                    <div class="overflow-x-auto print:overflow-visible">
                        <table class="w-full min-w-[1000px] border-collapse print:w-full print:min-w-0">
                            <thead>
                                <tr>
                                    <th class="sticky left-0 z-10 w-20 border-b border-r border-slate-200 bg-slate-50 px-3 py-3 text-left text-[10px] font-semibold uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                                        Hora
                                    </th>
                                    <th
                                        v-for="dayInfo in weekDayDates"
                                        :key="dayInfo.date || dayInfo.name"
                                        class="border-b border-slate-200 bg-slate-50 px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="flex h-7 w-7 items-center justify-center rounded-md bg-white text-brand-600 shadow-sm dark:bg-slate-800">
                                                <Calendar class="h-3.5 w-3.5" />
                                            </span>
                                            <div>
                                                <div>{{ dayInfo.name }}</div>
                                                <div class="text-[10px] font-medium text-slate-500 dark:text-slate-400">
                                                    {{ dayInfo.date ? new Date(dayInfo.date).toLocaleDateString('es-PE', { day: 'numeric', month: 'short' }) : '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="slot in timeSlots" :key="slot" class="group align-top">
                                    <td
                                        class="sticky left-0 z-10 h-[72px] w-20 border-b border-r border-slate-200 bg-white px-3 align-middle text-[11px] font-semibold text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-400"
                                        :class="slot.endsWith(':00') ? 'py-2' : 'py-1 text-slate-300 dark:text-slate-600'"
                                    >
                                        {{ slot.endsWith(':00') ? slot : '' }}
                                    </td>
                                    <template v-for="dayInfo in weekDayDates" :key="`cell-${dayInfo.date || dayInfo.name}-${slot}`">
                                        <td
                                            v-if="!isCovered(dayInfo.name, slot)"
                                            class="relative h-[72px] min-h-[72px] border-b border-r border-slate-100 p-1 align-top dark:border-slate-700"
                                            :class="dayCellClass(dayInfo.name)"
                                            :rowspan="schedulesStartingAt(dayInfo.name, slot).length ? rowSpan(schedulesStartingAt(dayInfo.name, slot)[0]) : 1"
                                        >
                                            <template v-if="schedulesStartingAt(dayInfo.name, slot).length">
                                                <article
                                                    v-for="(sch, idx) in schedulesStartingAt(dayInfo.name, slot)"
                                                    v-show="idx === 0"
                                                    :key="sch.id"
                                                    class="group/card relative h-full cursor-pointer overflow-hidden rounded-lg border p-2 transition hover:shadow-md"
                                                    :class="[
                                                        scheduleColor(sch).bg,
                                                        scheduleColor(sch).border,
                                                        scheduleColor(sch).text,
                                                    ]"
                                                    @click="openEdit(sch)"
                                                >
                                                    <span
                                                        class="absolute left-0 top-0 h-full w-1"
                                                        :class="scheduleColor(sch).accent"
                                                    />
                                                    <div class="ml-2 flex flex-col gap-1">
                                                        <div class="flex items-center gap-1 text-[11px] font-semibold leading-tight">
                                                            <span class="line-clamp-2">
                                                                {{ sch.course?.subject?.name ?? sch.course?.name ?? 'Materia' }}
                                                            </span>
                                                        </div>
                                                        <div class="text-[10px] leading-tight text-gray-600">
                                                            {{ sch.course?.grade_section?.name ?? '-' }}
                                                        </div>
                                                        <div class="flex items-center gap-1 text-[10px] leading-tight">
                                                            <span class="font-mono font-semibold">
                                                                {{ formatTime(sch.start_time) }} - {{ formatTime(sch.end_time) }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-1 text-[10px] leading-tight text-gray-600">
                                                            <MapPin class="h-3 w-3" />
                                                            Aula {{ sch.classroom ?? '-' }}
                                                        </div>
                                                        <div v-if="sch.course?.teacher" class="truncate text-[10px] leading-tight text-gray-500">
                                                            {{ sch.course.teacher.first_name }} {{ sch.course.teacher.last_name }}
                                                        </div>
                                                    </div>

                                                    <div class="absolute right-1 top-1 flex items-center gap-0.5 opacity-0 transition group-hover/card:opacity-100 print:hidden">
                                                        <button
                                                            type="button"
                                                            class="rounded p-1 text-slate-500 transition hover:bg-white/80 hover:text-brand-600"
                                                            @click.stop="openEdit(sch)"
                                                        >
                                                            <Pencil class="h-3 w-3" />
                                                        </button>
                                                        <button
                                                            type="button"
                                                            class="rounded p-1 text-slate-500 transition hover:bg-white/80 hover:text-red-600"
                                                            @click.stop="destroy(sch)"
                                                        >
                                                            <Trash2 class="h-3 w-3" />
                                                        </button>
                                                    </div>
                                                </article>
                                            </template>
                                            <button
                                                v-else
                                                type="button"
                                                class="flex h-full w-full items-center justify-center rounded-lg border border-dashed border-transparent text-slate-300 transition hover:border-slate-200 hover:bg-slate-50 hover:text-brand-500 print:hidden dark:text-slate-600 dark:hover:border-slate-700 dark:hover:bg-slate-700/30"
                                                @click="openCreate(dayInfo.name, slot)"
                                            >
                                                <Plus class="h-3.5 w-3.5 opacity-0 transition group-hover:opacity-100" />
                                            </button>
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div
                    v-if="displayedClassroomGroups.length === 0"
                    class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center print:hidden dark:border-slate-700 dark:bg-slate-800"
                >
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        No hay bloques registrados para este profesor esta semana.
                    </p>
                </div>

                <div
                    v-if="selectedTeacher && teacherSummary"
                    class="grid grid-cols-1 gap-3 rounded-xl border border-brand-100 bg-brand-50/40 p-4 text-xs sm:grid-cols-4 print:hidden dark:border-brand-700/40 dark:bg-brand-900/20"
                >
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Profesor</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ selectedTeacherInfo?.name }}</p>
                        <p v-if="selectedTeacherInfo?.code" class="text-[11px] text-slate-500">{{ selectedTeacherInfo.code }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Bloques esta semana</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ teacherSummary.count }}</p>
                        <p class="text-[11px] text-slate-500">{{ teacherSummary.dayCount }} días con clase</p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Aulas</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ teacherSummary.classrooms.length ? teacherSummary.classrooms.map((c) => 'Aula ' + c).join(', ') : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-500">Horas semanales</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ teacherSummary.totalHours }} h</p>
                        <p class="text-[11px] text-slate-500">{{ teacherSummary.totalMinutes }} min</p>
                    </div>
                </div>
            </div>
        </div>

        <Teleport to="body">
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm print:hidden" @click.self="showModal = false">
                    <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-800">
                        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3 dark:border-slate-700">
                            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-100">
                                {{ editing ? 'Editar bloque' : 'Nuevo bloque' }}
                            </h3>
                            <button type="button" class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 dark:hover:bg-slate-700" @click="showModal = false">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                        <form class="space-y-4 p-5" @submit.prevent="submit">
                            <p v-if="schoolHours" class="rounded-lg bg-info-50 px-3 py-2 text-xs text-info-800 dark:bg-info-900/30 dark:text-info-200">
                                El horario debe estar dentro del horario oficial del colegio: <strong>{{ schoolHours.start }} - {{ schoolHours.end }}</strong>.
                            </p>
                            <SelectField
                                v-model="form.course_id"
                                label="Curso"
                                required
                                :options="courseOptions"
                                :error="form.errors.course_id"
                                placeholder="Seleccionar curso..."
                            />
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <FormField v-model="form.classroom" label="Aula" required :error="form.errors.classroom" />
                                <SelectField v-model="form.day_of_week" label="Día" required :options="dayOptions" :error="form.errors.day_of_week" />
                                <TimeField
                                    v-model="form.start_time"
                                    label="Inicio"
                                    required
                                    :min="schoolHours?.start"
                                    :max="schoolHours?.end"
                                    :error="form.errors.start_time"
                                />
                                <TimeField
                                    v-model="form.end_time"
                                    label="Fin"
                                    required
                                    :min="schoolHours?.start"
                                    :max="schoolHours?.end"
                                    :error="form.errors.end_time"
                                />
                                <SelectField
                                    v-model="form.shift_id"
                                    label="Turno"
                                    placeholder="Sin turno"
                                    :options="shiftOptions"
                                    :error="form.errors.shift_id"
                                />
                            </div>
                            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4 dark:border-slate-700">
                                <button type="button" class="rounded-lg px-4 py-2 text-sm text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700" @click="showModal = false">
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700 disabled:opacity-60"
                                >
                                    {{ editing ? 'Actualizar' : 'Guardar' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>
    </AppLayout>
</template>

<style>
@media print {
    @page {
        size: landscape;
        margin: 10mm;
    }
}
</style>
