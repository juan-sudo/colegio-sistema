<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Download } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import DateField from '@/Components/DateField.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    courses: Array,
    selectedCourseId: [String, Number, null],
    dateFrom: String,
    dateTo: String,
    attendances: Array,
    stats: Object,
});

const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));
const courseId = ref(props.selectedCourseId ?? '');
const dateFrom = ref(props.dateFrom);
const dateTo = ref(props.dateTo);

function filter() {
    router.get(route('admin.reports.attendance'), { course_id: courseId.value, date_from: dateFrom.value, date_to: dateTo.value });
}

const statusLabel = { presente: 'Presente', tardanza: 'Tarde', falta: 'Falta' };
const statusTone = { presente: 'success', tardanza: 'warning', falta: 'danger' };
</script>

<template>
    <Head title="Reporte de asistencia" />

    <AppLayout title="Reporte de asistencia">
        <div class="space-y-6">
            <div class="flex justify-end">
                <a :href="route('admin.reports.index')" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted">← Volver a reportes</a>
            </div>

            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <div class="flex items-end gap-4">
                    <SelectField class="flex-1" v-model="courseId" label="Curso" required placeholder="Seleccionar curso..." :options="courseOptions" />
                    <DateField v-model="dateFrom" label="Desde" required />
                    <DateField v-model="dateTo" label="Hasta" required />
                    <button type="button" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="filter">
                        Filtrar
                    </button>
                    <a
                        :href="route('admin.reports.attendance.export', { course_id: courseId, date_from: dateFrom, date_to: dateTo })"
                        class="flex items-center gap-1 rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700"
                    >
                        <Download class="h-4 w-4" /> Excel
                    </a>
                </div>
            </div>

            <template v-if="selectedCourseId && attendances.length > 0">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <StatCard label="Presentes" :value="stats.present" tone="success" />
                    <StatCard label="Tardanzas" :value="stats.late" tone="warning" />
                    <StatCard label="Faltas" :value="stats.absent" tone="danger" />
                </div>

                <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-muted">
                                <th class="p-2 text-left font-medium text-slate-500">Fecha</th>
                                <th class="p-2 text-left font-medium text-slate-500">Estudiante</th>
                                <th class="p-2 text-left font-medium text-slate-500">Curso</th>
                                <th class="p-2 text-center font-medium text-slate-500">Estado</th>
                                <th class="p-2 text-left font-medium text-slate-500">Hora entrada</th>
                                <th class="p-2 text-left font-medium text-slate-500">Método</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="a in attendances" :key="a.id" class="border-t border-border">
                                <td class="p-2">{{ a.date.substring(0, 10).split('-').reverse().join('/') }}</td>
                                <td class="p-2">{{ a.student ? `${a.student.first_name} ${a.student.last_name}` : '-' }}</td>
                                <td class="p-2">{{ a.course?.name ?? '-' }}</td>
                                <td class="p-2 text-center"><StatusBadge :label="statusLabel[a.status]" :tone="statusTone[a.status]" /></td>
                                <td class="p-2">{{ a.time_in ?? '-' }}</td>
                                <td class="p-2">{{ a.method.charAt(0).toUpperCase() + a.method.slice(1) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <div v-else-if="selectedCourseId" class="rounded-xl border border-border bg-surface p-6 text-slate-500">
                No hay registros de asistencia para este curso en el rango de fechas seleccionado.
            </div>
        </div>
    </AppLayout>
</template>
