<script setup>
import { reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Save } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
    courses: Array,
    selectedCourse: { type: Object, default: null },
    students: { type: Array, default: () => [] },
    courseCriteria: { type: Array, default: () => [] },
    grades: { type: Object, default: () => ({}) },
});

const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));

function selectCourse(courseId) {
    router.get(route('admin.grades.index'), { course_id: courseId });
}

const scoresByCriterion = reactive(
    Object.fromEntries(
        props.courseCriteria.map((ac) => [
            ac.id,
            Object.fromEntries(props.students.map((s) => [s.id, props.grades[`${ac.id}-${s.id}`]?.score ?? ''])),
        ])
    )
);

function saveGrades(ac) {
    const form = useForm({ course_id: props.selectedCourse.id, scores: scoresByCriterion[ac.id] });
    form.post(route('admin.academic.assessment-criteria.store-grades', ac.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Notas" />

    <AppLayout title="Registro de notas por curso">
        <div class="space-y-6">
            <p class="text-sm text-slate-500">Selecciona un curso para ver sus criterios de evaluación y registrar notas por alumno.</p>

            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <SelectField
                    :model-value="selectedCourse?.id ?? ''"
                    label="Seleccionar curso"
                    placeholder="Seleccionar curso..."
                    :options="courseOptions"
                    @update:model-value="selectCourse"
                />
            </div>

            <div v-if="selectedCourse" class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-lg font-semibold text-slate-900">{{ selectedCourse.name }}</h2>
                    <p class="text-sm text-slate-500">
                        {{ selectedCourse.grade_section?.name ?? '' }} ·
                        {{ selectedCourse.teacher ? `${selectedCourse.teacher.first_name} ${selectedCourse.teacher.last_name}` : 'Sin profesor' }}
                    </p>
                </div>

                <template v-if="students.length > 0">
                    <div v-if="courseCriteria.length === 0" class="text-sm text-slate-500">
                        Este curso no tiene criterios de evaluación asociados.
                    </div>
                    <div v-for="ac in courseCriteria" :key="ac.id" class="mb-4 rounded-lg border border-border p-4">
                        <div class="mb-3">
                            <h3 class="font-medium text-slate-900">{{ ac.evaluation_criteria?.name ?? 'Criterio' }}</h3>
                            <p class="text-xs text-slate-500">{{ ac.description ?? '' }} · Puntaje máximo: {{ ac.maximum_score }}</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-surface-muted">
                                        <th class="p-2 text-left font-medium text-slate-500">DNI / Código</th>
                                        <th class="p-2 text-left font-medium text-slate-500">Nombres</th>
                                        <th class="p-2 text-left font-medium text-slate-500">Apellidos</th>
                                        <th class="w-40 p-2 text-left font-medium text-slate-500">Nota (0-{{ ac.maximum_score }})</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="student in students" :key="student.id" class="border-t border-border">
                                        <td class="p-2 font-medium">{{ student.code ?? '-' }}</td>
                                        <td class="p-2">{{ student.first_name }}</td>
                                        <td class="p-2">{{ student.last_name }}</td>
                                        <td class="p-2">
                                            <input
                                                v-model="scoresByCriterion[ac.id][student.id]"
                                                type="number"
                                                min="0"
                                                :max="ac.maximum_score"
                                                step="0.01"
                                                placeholder="0"
                                                class="w-full rounded-lg border border-border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-200"
                                            >
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <button
                            type="button"
                            class="mt-3 flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                            @click="saveGrades(ac)"
                        >
                            <Save class="h-4 w-4" /> Guardar notas
                        </button>
                    </div>
                </template>
                <p v-else class="text-sm text-slate-500">Este curso no tiene estudiantes matriculados.</p>
            </div>
        </div>
    </AppLayout>
</template>
