<script setup>
import { reactive } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowLeft, Save } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    evaluationCriterion: Object,
    course: { type: Object, default: null },
    students: { type: Array, default: () => [] },
    grades: { type: Object, default: () => ({}) },
    assessmentCriterion: { type: Object, default: null },
    courses: { type: Array, default: () => [] },
});

const scores = reactive(
    Object.fromEntries(props.students.map((s) => [s.id, props.grades[s.id]?.score ?? '']))
);

const form = useForm({ course_id: props.course?.id ?? null, scores: {} });

function submit() {
    form.course_id = props.course.id;
    form.scores = scores;
    form.post(route('admin.academic.evaluation-criteria.store-grades', props.evaluationCriterion.id));
}
</script>

<template>
    <Head title="Cargar notas" />

    <AppLayout :title="`Cargar notas - ${evaluationCriterion.name}`">
        <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
            <template v-if="course">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">
                        {{ course.name }} - {{ course.grade_section?.name ?? 'Sin grado' }} ·
                        {{ course.teacher ? `${course.teacher.first_name} ${course.teacher.last_name}` : 'Sin profesor' }}
                    </p>
                    <div class="flex gap-2">
                        <a
                            :href="route('admin.academic.evaluation-criteria.grades', evaluationCriterion.id)"
                            class="flex items-center gap-1 rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted"
                        >
                            <ArrowLeft class="h-4 w-4" /> Cambiar curso
                        </a>
                        <a
                            :href="route('admin.academic.evaluation-criteria.index')"
                            class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                        >
                            Volver a criterios
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-muted">
                                <th class="p-2 text-left font-medium text-slate-500">Alumno</th>
                                <th class="p-2 text-left font-medium text-slate-500">Nota (0-{{ assessmentCriterion.maximum_score }})</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in students" :key="student.id" class="border-t border-border">
                                <td class="p-2">{{ student.first_name }} {{ student.last_name }}</td>
                                <td class="p-2">
                                    <input
                                        v-model="scores[student.id]"
                                        type="number"
                                        min="0"
                                        :max="assessmentCriterion.maximum_score"
                                        step="0.01"
                                        placeholder="0"
                                        class="w-32 rounded-lg border border-border px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-200"
                                    >
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button
                    type="button"
                    :disabled="form.processing"
                    class="mt-4 flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60"
                    @click="submit"
                >
                    <Save class="h-4 w-4" /> Guardar notas
                </button>
            </template>

            <template v-else>
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Cargar notas - {{ evaluationCriterion.name }}</h2>
                        <p class="text-sm text-slate-500">Selecciona el curso para registrar las notas</p>
                    </div>
                    <a
                        :href="route('admin.academic.evaluation-criteria.index')"
                        class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted"
                    >
                        ← Volver
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <a
                        v-for="c in courses"
                        :key="c.id"
                        :href="route('admin.academic.evaluation-criteria.grades', { evaluationCriterion: evaluationCriterion.id, course_id: c.id })"
                        class="rounded-xl border border-border p-4 hover:shadow-md"
                    >
                        <h3 class="mb-1 font-semibold text-slate-900">{{ c.name }}</h3>
                        <p class="text-sm text-slate-500">{{ c.grade_section?.name ?? 'Sin grado' }}</p>
                        <p class="text-sm text-slate-500">
                            {{ c.teacher ? `${c.teacher.first_name} ${c.teacher.last_name}` : 'Sin profesor' }}
                        </p>
                    </a>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
