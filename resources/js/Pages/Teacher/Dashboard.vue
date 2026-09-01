<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { BookOpen, ClipboardCheck, ClipboardList, Inbox, Paperclip } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';

const props = defineProps({
    courses: { type: Array, default: () => [] },
});

const modules = [
    { label: 'Tomar asistencia', icon: ClipboardList, route: 'teacher.attendance.scanner' },
    { label: 'Ingresar notas', icon: ClipboardCheck, route: 'teacher.grades.index' },
    { label: 'Tareas', icon: Paperclip, route: 'teacher.assignments.index' },
];

const courseCount = computed(() => props.courses.length);
</script>

<template>
    <Head title="Panel del profesor" />

    <AppLayout title="Mis cursos">
        <div class="space-y-6">
            <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">
                        Panel del profesor
                    </p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">Mis cursos</h1>
                </div>
            </div>

            <div class="max-w-xs">
                <StatCard label="Cursos asignados" :value="courseCount" :icon="BookOpen" tone="brand" />
            </div>

            <div v-if="courses.length === 0" class="flex flex-col items-center gap-2 rounded-xl border border-dashed border-border bg-surface p-10 text-center">
                <Inbox class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                <p class="text-sm text-slate-500 dark:text-slate-400">Aún no tienes cursos asignados.</p>
            </div>

            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="course in courses"
                    :key="course.id"
                    class="rounded-xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
                >
                    <div class="mb-3 flex items-start gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-700 dark:bg-brand-900/50 dark:text-brand-200">
                            <BookOpen class="h-5 w-5" />
                        </div>
                        <div class="min-w-0">
                            <h2 class="truncate font-semibold text-slate-900 dark:text-white">{{ course.name }}</h2>
                            <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ course.grade_section?.name ?? 'Sin sección' }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5 border-t border-border pt-3">
                        <a
                            v-for="mod in modules"
                            :key="mod.label"
                            :href="route(mod.route, course.id)"
                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-slate-600 transition hover:bg-surface-muted hover:text-brand-700 dark:text-slate-300 dark:hover:text-brand-300"
                        >
                            <component :is="mod.icon" class="h-4 w-4 shrink-0" /> {{ mod.label }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
