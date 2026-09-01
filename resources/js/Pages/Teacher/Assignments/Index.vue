<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowRight } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormField from '@/Components/FormField.vue';
import TextareaField from '@/Components/TextareaField.vue';

const props = defineProps({
    course: Object,
    assignments: Array,
});

const form = useForm({
    title: '',
    description: '',
    due_date: '',
    file: null,
});

function submit() {
    form.post(route('teacher.assignments.store', props.course.id), { onSuccess: () => form.reset() });
}

function formatDate(value) {
    if (!value) return 'Sin fecha';
    const [datePart, timePart] = value.split('T');
    return `${datePart.split('-').reverse().join('/')} ${timePart?.substring(0, 5) ?? ''}`.trim();
}
</script>

<template>
    <Head :title="`Tareas - ${course.name}`" />

    <AppLayout :title="`Tareas — ${course.name}`">
        <form class="mb-6 space-y-3 rounded-xl border border-border bg-surface p-4 shadow-sm" @submit.prevent="submit">
            <FormField v-model="form.title" label="Título de la tarea" required :error="form.errors.title" />
            <TextareaField v-model="form.description" label="Descripción / instrucciones" :error="form.errors.description" />
            <div class="flex gap-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Vence</label>
                    <input v-model="form.due_date" type="datetime-local" class="rounded-lg border border-border px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Archivo</label>
                    <input type="file" class="rounded-lg border border-border px-3 py-2 text-sm" @change="form.file = $event.target.files[0]">
                </div>
            </div>
            <button type="submit" :disabled="form.processing" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60">
                Publicar tarea
            </button>
        </form>

        <div class="space-y-3">
            <div v-for="a in assignments" :key="a.id" class="flex items-center justify-between rounded-xl border border-border bg-surface p-4 shadow-sm">
                <div>
                    <p class="font-semibold text-slate-900">{{ a.title }}</p>
                    <p class="text-sm text-slate-500">Vence: {{ formatDate(a.due_date) }} — {{ a.submissions_count }} entregas</p>
                </div>
                <a :href="route('teacher.assignments.submissions', a.id)" class="flex items-center gap-1 text-sm text-brand-600 hover:text-brand-800">
                    Ver entregas <ArrowRight class="h-4 w-4" />
                </a>
            </div>
        </div>
    </AppLayout>
</template>
