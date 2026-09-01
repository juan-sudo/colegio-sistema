<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
    course: { type: Object, default: null },
    courses: Array,
});

const courseId = ref(props.course?.id ?? '');
const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));

function submit() {
    router.get(route('admin.attendance.scanner'), { course_id: courseId.value });
}
</script>

<template>
    <Head title="Escáner de asistencia" />

    <AppLayout title="Escáner de asistencia">
        <div class="space-y-4">
            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Escanear código</h2>
                <form class="flex items-end gap-4" @submit.prevent="submit">
                    <SelectField
                        v-model="courseId"
                        class="flex-1"
                        label="Curso"
                        required
                        placeholder="Seleccionar curso..."
                        :options="courseOptions"
                    />
                    <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                        Ver escáner
                    </button>
                </form>
            </div>

            <div v-if="course" class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <h2 class="mb-4 text-lg font-semibold text-slate-900">Escáner - {{ course.name }} - {{ course.grade_section?.name }}</h2>
                <p class="text-sm text-slate-500">
                    Usa el módulo principal de
                    <a :href="route('admin.attendance.index', { course_id: course.id })" class="text-brand-600 underline">Asistencia en aula</a>
                    para registrar asistencia por QR, código de barras o biométrico.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
