<script setup>
import { computed, reactive } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { BookOpen, CalendarClock, Inbox, Upload } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps({
    courses: { type: Array, default: () => [] },
});

const files = reactive({});

function submit(assignment) {
    const form = useForm({ file: files[assignment.id] });
    form.post(route('student.assignments.submit', assignment.id), { forceFormData: true });
}

function formatDate(value) {
    if (!value) return 'Sin fecha';
    const [datePart, timePart] = value.split('T');
    return `${datePart.split('-').reverse().join('/')} ${timePart?.substring(0, 5) ?? ''}`.trim();
}

const courseCount = computed(() => props.courses.length);
const pendingCount = computed(() => props.courses.reduce((total, c) => total + c.assignments.length, 0));
</script>

<template>
    <Head title="Mis cursos y tareas" />

    <AppLayout title="Mis cursos y tareas">
        <div class="space-y-6">
            <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">
                        Panel del estudiante
                    </p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">Mis cursos y tareas</h1>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:max-w-lg">
                <StatCard label="Cursos matriculados" :value="courseCount" :icon="BookOpen" tone="brand" />
                <StatCard label="Tareas publicadas" :value="pendingCount" :icon="CalendarClock" tone="info" />
            </div>

            <div v-if="courses.length === 0" class="flex flex-col items-center gap-2 rounded-xl border border-dashed border-border bg-surface p-10 text-center">
                <Inbox class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                <p class="text-sm text-slate-500 dark:text-slate-400">Aún no tienes cursos matriculados.</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="course in courses" :key="course.id" class="rounded-xl border border-border bg-surface p-5 shadow-sm">
                    <div class="mb-3 flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700 dark:bg-brand-900/50 dark:text-brand-200">
                            <BookOpen class="h-4.5 w-4.5" />
                        </div>
                        <h2 class="font-semibold text-slate-900 dark:text-white">{{ course.name }}</h2>
                    </div>

                    <p v-if="course.assignments.length === 0" class="text-sm text-slate-400 dark:text-slate-500">
                        Sin tareas publicadas aún.
                    </p>

                    <div v-for="assignment in course.assignments" :key="assignment.id" class="border-t border-border py-3 first:pt-0">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-medium text-slate-900 dark:text-white">{{ assignment.title }}</p>
                            <span class="inline-flex items-center gap-1 rounded-full bg-surface-muted px-2.5 py-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <CalendarClock class="h-3.5 w-3.5" /> {{ formatDate(assignment.due_date) }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ assignment.description }}</p>

                        <form class="mt-3 flex flex-wrap items-center gap-2" @submit.prevent="submit(assignment)">
                            <input
                                type="file"
                                required
                                class="min-w-0 flex-1 rounded-lg border border-border bg-surface px-3 py-1.5 text-sm text-slate-700 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-2.5 file:py-1 file:text-xs file:font-medium file:text-brand-700 dark:text-slate-200 dark:file:bg-brand-900/50 dark:file:text-brand-200"
                                @change="files[assignment.id] = $event.target.files[0]"
                            >
                            <button type="submit" class="flex items-center gap-1.5 rounded-lg bg-success-600 px-3 py-1.5 text-sm font-medium text-white transition hover:bg-success-700">
                                <Upload class="h-3.5 w-3.5" /> Entregar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
