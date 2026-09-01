<script setup>
import { reactive, ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Download, Upload, Save } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    course: Object,
    periods: Array,
    students: Array,
    grades: Object,
    criteria: Array,
});

const periodOptions = props.periods.map((p) => ({ value: p.id, label: p.name }));

const gradePeriodId = ref(props.periods[0]?.id ?? '');
const evaluation = ref('');
const scores = reactive(Object.fromEntries(props.students.map((s) => [s.id, ''])));

function saveGrades() {
    const form = useForm({ grade_period_id: gradePeriodId.value, evaluation: evaluation.value, scores });
    form.post(route('teacher.grades.store', props.course.id), { preserveScroll: true });
}

const criterionId = ref(props.criteria[0]?.id ?? '');
const criterionScores = reactive(Object.fromEntries(props.students.map((s) => [s.id, ''])));

function saveCriterionGrades() {
    const form = useForm({ assessment_criterion_id: criterionId.value, scores: criterionScores });
    form.post(route('teacher.grades.criteria.store', props.course.id), { preserveScroll: true });
}

const criterionOptions = props.criteria.map((c) => ({ value: c.id, label: `${c.name} · Máximo ${c.maximum_score}` }));
</script>

<template>
    <Head :title="`Notas - ${course.name}`" />

    <AppLayout :title="`Notas — ${course.name}`">
        <div class="mb-4 flex gap-3">
            <a :href="route('teacher.grades.template', course.id)" class="flex items-center gap-2 rounded-lg bg-surface-muted px-3 py-2 text-sm text-slate-700 hover:bg-slate-200">
                <Download class="h-4 w-4" /> Descargar plantilla Excel
            </a>
            <a :href="route('teacher.grades.import-form', course.id)" class="flex items-center gap-2 rounded-lg bg-success-600 px-3 py-2 text-sm text-white hover:bg-success-700">
                <Upload class="h-4 w-4" /> Carga masiva desde Excel
            </a>
        </div>

        <div class="rounded-xl border border-border bg-surface p-4 shadow-sm">
            <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <SelectField v-model="gradePeriodId" label="Periodo" required :options="periodOptions" />
                <FormField v-model="evaluation" label="Evaluación" placeholder="Examen 1, Práctica..." />
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-left">
                        <th class="py-2 font-medium text-slate-500">Alumno</th>
                        <th class="w-32 py-2 font-medium text-slate-500">Nota (0-20)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in students" :key="student.id" class="border-b border-border">
                        <td class="py-2">{{ student.first_name }} {{ student.last_name }}</td>
                        <td class="py-2">
                            <input v-model="scores[student.id]" type="number" step="0.01" min="0" max="20" class="w-24 rounded border border-border px-2 py-1">
                        </td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="mt-4 flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="saveGrades">
                <Save class="h-4 w-4" /> Guardar notas
            </button>
        </div>

        <div v-if="criteria.length > 0" class="mt-6 rounded-xl border border-border bg-surface p-4 shadow-sm">
            <h2 class="mb-3 font-bold text-slate-900">Notas por criterio</h2>
            <SelectField v-model="criterionId" label="Criterio" required :options="criterionOptions" class="mb-3 max-w-sm" />
            <div class="grid gap-2 md:grid-cols-3">
                <label v-for="student in students" :key="student.id" class="text-sm">
                    {{ student.first_name }} {{ student.last_name }}
                    <input v-model="criterionScores[student.id]" type="number" step="0.01" min="0" class="mt-1 block w-full rounded border border-border px-2 py-1">
                </label>
            </div>
            <button type="button" class="mt-4 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="saveCriterionGrades">
                Guardar por criterio
            </button>
        </div>
    </AppLayout>
</template>
