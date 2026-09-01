<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Upload } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
    course: Object,
    periods: Array,
});

const periodOptions = props.periods.map((p) => ({ value: p.id, label: p.name }));

const form = useForm({
    grade_period_id: props.periods[0]?.id ?? '',
    file: null,
});

function submit() {
    form.post(route('teacher.grades.import', props.course.id));
}
</script>

<template>
    <Head title="Importar notas desde Excel" />

    <AppLayout :title="`Carga masiva de notas — ${course.name}`">
        <div class="max-w-lg rounded-xl border border-border bg-surface p-6 shadow-sm">
            <p class="mb-4 text-sm text-slate-600">
                1. Descarga la <a class="text-brand-600 underline" :href="route('teacher.grades.template', course.id)">plantilla Excel</a>.<br>
                2. Llena la columna "nota" para cada alumno.<br>
                3. Sube el archivo aquí.
            </p>
            <form class="space-y-4" @submit.prevent="submit">
                <SelectField v-model="form.grade_period_id" label="Periodo" required :options="periodOptions" :error="form.errors.grade_period_id" />

                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Archivo Excel (.xlsx)<span class="text-danger-600"> *</span></label>
                    <input
                        type="file"
                        required
                        accept=".xlsx,.xls,.csv"
                        class="w-full rounded-lg border border-border px-3 py-2 text-sm"
                        @change="form.file = $event.target.files[0]"
                    >
                    <p v-if="form.errors.file" class="mt-1 text-sm text-danger-600">{{ form.errors.file }}</p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60"
                >
                    <Upload class="h-4 w-4" /> Importar
                </button>
            </form>
        </div>
    </AppLayout>
</template>
