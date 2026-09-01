<script setup>
import { reactive } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowLeft } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    assignment: Object,
    submissions: Array,
});

const rows = reactive(
    Object.fromEntries(props.submissions.map((s) => [s.id, { grade: s.grade ?? '', feedback: s.feedback ?? '' }]))
);

function gradeSubmission(submission) {
    const form = useForm({ grade: rows[submission.id].grade, feedback: rows[submission.id].feedback });
    form.post(route('teacher.submissions.grade', submission.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Entregas" />

    <AppLayout :title="`Entregas - ${assignment.title}`">
        <a :href="route('teacher.assignments.index', assignment.course_id)" class="mb-4 flex w-fit items-center gap-1 text-sm text-brand-600 hover:text-brand-800">
            <ArrowLeft class="h-4 w-4" /> Volver a tareas
        </a>

        <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-surface-muted">
                        <th class="p-2 text-left font-medium text-slate-500">Estudiante</th>
                        <th class="p-2 text-left font-medium text-slate-500">Estado</th>
                        <th class="p-2 text-left font-medium text-slate-500">Nota</th>
                        <th class="p-2 text-left font-medium text-slate-500">Retroalimentación</th>
                        <th class="p-2 text-left font-medium text-slate-500">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="submission in submissions" :key="submission.id" class="border-t border-border">
                        <td class="p-2">{{ submission.student.first_name }} {{ submission.student.last_name }}</td>
                        <td class="p-2">{{ submission.status.charAt(0).toUpperCase() + submission.status.slice(1) }}</td>
                        <td class="p-2">{{ submission.grade ?? '-' }}</td>
                        <td class="p-2">{{ submission.feedback ?? '-' }}</td>
                        <td class="p-2">
                            <div class="flex items-center gap-2">
                                <input v-model="rows[submission.id].grade" type="number" step="0.01" min="0" max="20" required class="w-16 rounded border border-border p-1">
                                <input v-model="rows[submission.id].feedback" type="text" placeholder="Retroalimentación" class="w-32 rounded border border-border p-1">
                                <button type="button" class="rounded bg-brand-600 px-2 py-1 text-xs text-white hover:bg-brand-700" @click="gradeSubmission(submission)">
                                    Calificar
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
