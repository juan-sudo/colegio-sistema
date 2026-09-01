<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import DateField from '@/Components/DateField.vue';

const props = defineProps({
    course: { type: Object, default: null },
    courses: Array,
    date: String,
});

const courseId = ref(props.course?.id ?? '');
const date = ref(props.date);
const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));

function submit() {
    router.get(route('admin.attendance.manual'), { course_id: courseId.value, date: date.value });
}
</script>

<template>
    <Head title="Registro manual de asistencia" />

    <AppLayout title="Registro manual de asistencia">
        <div class="space-y-4">
            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Registro manual por curso</h2>
                <form class="flex items-end gap-4" @submit.prevent="submit">
                    <SelectField
                        v-model="courseId"
                        class="flex-1"
                        label="Curso"
                        required
                        placeholder="Seleccionar curso..."
                        :options="courseOptions"
                    />
                    <DateField v-model="date" label="Fecha" required />
                    <button type="submit" class="rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700">
                        Ver curso
                    </button>
                </form>
            </div>

            <div v-if="course && date" class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Registro de asistencia</h2>
                <p class="text-sm text-slate-500">
                    Usa el módulo principal de
                    <a :href="route('admin.attendance.index', { course_id: course.id, date })" class="text-brand-600 underline">Asistencia en aula</a>
                    para registrar la asistencia.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
