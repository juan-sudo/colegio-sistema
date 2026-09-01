<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Download } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    courses: Array,
    selectedCourseId: [String, Number, null],
    grades: Array,
    stats: Object,
});

const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));
const courseId = ref(props.selectedCourseId ?? '');

function filter() {
    router.get(route('admin.reports.grades'), { course_id: courseId.value });
}
</script>

<template>
    <Head title="Reporte de notas" />

    <AppLayout title="Reporte de notas">
        <div class="space-y-6">
            <div class="flex justify-end">
                <a :href="route('admin.reports.index')" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted">← Volver a reportes</a>
            </div>

            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <div class="flex items-end gap-4">
                    <SelectField class="flex-1" v-model="courseId" label="Curso" required placeholder="Seleccionar curso..." :options="courseOptions" />
                    <button type="button" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="filter">
                        Filtrar
                    </button>
                    <a
                        :href="route('admin.reports.grades.export', { course_id: courseId })"
                        class="flex items-center gap-1 rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700"
                    >
                        <Download class="h-4 w-4" /> Excel
                    </a>
                </div>
            </div>

            <template v-if="selectedCourseId && grades.length > 0">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <StatCard label="Promedio general" :value="stats.average" tone="info" />
                    <StatCard label="Aprobados" :value="stats.approved" tone="success" />
                    <StatCard label="Desaprobados" :value="stats.failed" tone="danger" />
                </div>

                <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-muted">
                                <th class="p-2 text-left font-medium text-slate-500">Estudiante</th>
                                <th class="p-2 text-left font-medium text-slate-500">Evaluación</th>
                                <th class="p-2 text-center font-medium text-slate-500">Nota</th>
                                <th class="p-2 text-left font-medium text-slate-500">Periodo</th>
                                <th class="p-2 text-left font-medium text-slate-500">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="g in grades" :key="g.id" class="border-t border-border">
                                <td class="p-2">{{ g.student ? `${g.student.first_name} ${g.student.last_name}` : '-' }}</td>
                                <td class="p-2">{{ g.evaluation ?? '-' }}</td>
                                <td class="p-2 text-center">
                                    <StatusBadge :label="String(g.score)" :tone="g.score >= 11 ? 'success' : 'danger'" />
                                </td>
                                <td class="p-2">{{ g.grade_period?.name ?? '-' }}</td>
                                <td class="p-2">{{ g.created_at.substring(0, 10).split('-').reverse().join('/') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <div v-else-if="selectedCourseId" class="rounded-xl border border-border bg-surface p-6 text-slate-500">
                No hay notas registradas para este curso.
            </div>
        </div>
    </AppLayout>
</template>
