<script setup>
import { reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Save, AlertTriangle } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import DateField from '@/Components/DateField.vue';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps({
    courses: Array,
    selectedCourseId: [String, Number, null],
    date: String,
    course: { type: Object, default: null },
    students: { type: Array, default: () => [] },
    attendances: { type: Object, default: () => ({}) },
    stats: Object,
});

const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));

function updateSelection(courseId, date) {
    router.get(route('admin.attendance.index'), { course_id: courseId, date });
}

const rows = reactive(
    Object.fromEntries(
        props.students.map((s) => {
            const a = props.attendances[s.id];
            return [
                s.id,
                {
                    status: a?.status ?? 'falta',
                    time_in: a?.time_in?.substring(0, 5) ?? new Date().toTimeString().substring(0, 5),
                    observation: a?.observation ?? '',
                },
            ];
        })
    )
);

function markAllPresent() {
    Object.values(rows).forEach((r) => (r.status = 'presente'));
}

function markAllAbsent() {
    Object.values(rows).forEach((r) => (r.status = 'falta'));
}

function save() {
    const form = useForm({
        date: props.date,
        course_id: props.selectedCourseId,
        attendances: Object.fromEntries(
            Object.entries(rows).map(([studentId, r]) => [studentId, { ...r, course_id: props.selectedCourseId }])
        ),
    });
    form.post(route('admin.attendance.store-manual'), { preserveScroll: true });
}

function markAbsences() {
    if (confirm('¿Marcar como falta a todos los alumnos no registrados y enviar alertas por WhatsApp?')) {
        router.post(
            route('admin.attendance.mark-absences'),
            { course_id: props.selectedCourseId, date: props.date },
            { preserveScroll: true }
        );
    }
}
</script>

<template>
    <Head title="Asistencia en aula" />

    <AppLayout title="Asistencia en aula - Por curso">
        <div class="space-y-6">
            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <p class="mb-4 text-sm text-slate-500">
                    Selecciona un curso y fecha para registrar asistencia. El sistema marcará automáticamente: Presente (antes de 7:00 AM),
                    Tarde (7:00 AM - 7:10 AM), Falta (después de 7:10 AM).
                </p>
                <div class="flex items-end gap-4">
                    <SelectField
                        class="flex-1"
                        :model-value="selectedCourseId ?? ''"
                        label="Curso"
                        placeholder="Seleccionar curso..."
                        :options="courseOptions"
                        @update:model-value="(v) => updateSelection(v, date)"
                    />
                    <DateField :model-value="date" label="Fecha" @update:model-value="(v) => updateSelection(selectedCourseId, v)" />
                </div>
            </div>

            <div v-if="course && students.length > 0" class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">{{ course.name }} - {{ course.grade_section?.name }}</h2>
                        <p class="text-sm text-slate-500">
                            Fecha: {{ date.split('-').reverse().join('/') }} · Profesor:
                            {{ course.teacher ? `${course.teacher.first_name} ${course.teacher.last_name}` : 'Sin profesor' }}
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="rounded-lg bg-success-600 px-3 py-1.5 text-sm text-white hover:bg-success-700" @click="markAllPresent">
                            Marcar todos presentes
                        </button>
                        <button type="button" class="rounded-lg bg-danger-600 px-3 py-1.5 text-sm text-white hover:bg-danger-700" @click="markAllAbsent">
                            Marcar todos falta
                        </button>
                    </div>
                </div>

                <div class="mb-4 rounded-lg border border-info-100 bg-info-50 p-3 text-sm text-info-800">
                    <strong>Reglas de asistencia:</strong> Antes de 7:00 AM → Presente | 7:00 AM - 7:10 AM → Tarde | Después de 7:10 AM → Falta
                </div>

                <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <StatCard label="Presentes" :value="stats.present" tone="success" />
                    <StatCard label="Tardanzas" :value="stats.late" tone="warning" />
                    <StatCard label="Faltas" :value="stats.absent" tone="danger" />
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-muted">
                                <th class="p-2 text-left font-medium text-slate-500">Alumno</th>
                                <th class="p-2 text-left font-medium text-slate-500">DNI</th>
                                <th class="p-2 text-center font-medium text-slate-500">Presente</th>
                                <th class="p-2 text-center font-medium text-slate-500">Tarde</th>
                                <th class="p-2 text-center font-medium text-slate-500">Falta</th>
                                <th class="p-2 text-left font-medium text-slate-500">Hora entrada</th>
                                <th class="p-2 text-left font-medium text-slate-500">Justificación/Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in students" :key="student.id" class="border-t border-border">
                                <td class="p-2">{{ student.first_name }} {{ student.last_name }}</td>
                                <td class="p-2">{{ student.dni }}</td>
                                <td class="p-2 text-center">
                                    <input v-model="rows[student.id].status" type="radio" value="presente" class="h-4 w-4">
                                </td>
                                <td class="p-2 text-center">
                                    <input v-model="rows[student.id].status" type="radio" value="tardanza" class="h-4 w-4">
                                </td>
                                <td class="p-2 text-center">
                                    <input v-model="rows[student.id].status" type="radio" value="falta" class="h-4 w-4">
                                </td>
                                <td class="p-2">
                                    <input v-model="rows[student.id].time_in" type="time" class="w-28 rounded border border-border p-1">
                                </td>
                                <td class="p-2">
                                    <input
                                        v-model="rows[student.id].observation"
                                        type="text"
                                        placeholder="Justificación..."
                                        class="w-full rounded border border-border p-1 text-xs"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex gap-2">
                    <button type="button" class="flex items-center gap-2 rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700" @click="save">
                        <Save class="h-4 w-4" /> Guardar asistencia
                    </button>
                    <button type="button" class="flex items-center gap-2 rounded-lg bg-danger-600 px-4 py-2 text-sm font-medium text-white hover:bg-danger-700" @click="markAbsences">
                        <AlertTriangle class="h-4 w-4" /> Marcar faltas y notificar
                    </button>
                </div>
            </div>

            <div v-else-if="selectedCourseId && students.length === 0" class="rounded-xl border border-border bg-surface p-6 text-slate-500">
                Este curso no tiene estudiantes matriculados.
            </div>
        </div>
    </AppLayout>
</template>
