<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, BookOpen, Save } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import ListToolbar from '@/Components/ListToolbar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import FormField from '@/Components/FormField.vue';
import TextareaField from '@/Components/TextareaField.vue';

const props = defineProps({
    criteria: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 20);

function applyFilters() {
    router.get(
        route('admin.academic.evaluation-criteria.index'),
        { search: search.value || undefined, per_page: perPage.value },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    router.get(route('admin.academic.evaluation-criteria.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

const showModal = ref(false);
const form = useForm({ name: '', description: '' });

function openCreate() {
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function submit() {
    form.post(route('admin.academic.evaluation-criteria.store'), { onSuccess: () => (showModal.value = false) });
}

const gradeForms = {};
function gradeForm(criterionId) {
    if (!gradeForms[criterionId]) {
        gradeForms[criterionId] = useForm({ course_id: null, scores: {} });
    }
    return gradeForms[criterionId];
}

function saveGrades(criterion, ac) {
    const form = gradeForm(`${criterion.id}-${ac.id}`);
    form.course_id = ac.course_id;
    form.scores = Object.fromEntries(ac.students.map((s) => [s.id, ac._scores?.[s.id] ?? '']));
    form.post(route('admin.academic.evaluation-criteria.store-grades', criterion.id), { preserveScroll: true });
}

function scoreModel(ac, studentId) {
    ac._scores ??= {};
    if (!(studentId in ac._scores)) {
        ac._scores[studentId] = ac.grades?.[studentId]?.score ?? '';
    }
    return ac._scores;
}
</script>

<template>
    <Head title="Criterios de evaluación" />

    <AppLayout title="Criterios de evaluación">
        <div class="space-y-6">
            <PageHeader
                eyebrow="Académico"
                title="Criterios de evaluación"
                description="Define los criterios de evaluación y registra las notas por curso."
            />

            <ListToolbar v-model:search="search" v-model:per-page="perPage" placeholder="Nombre..." @submit="applyFilters" @clear="clearFilters">
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo criterio
                </button>
            </ListToolbar>

            <div v-if="criteria.data.length === 0" class="rounded-xl border border-border bg-surface p-6 text-slate-500">
                No hay criterios de evaluación creados.
            </div>

            <div v-for="criterion in criteria.data" :key="criterion.id" class="rounded-xl border border-border bg-surface shadow-sm">
                <div class="border-b border-border p-4">
                    <h2 class="text-lg font-semibold text-slate-900">{{ criterion.name }}</h2>
                    <p class="text-sm text-slate-500">{{ criterion.description ?? 'Sin descripción' }}</p>
                </div>

                <div v-if="criterion.assessment_criteria.length > 0">
                    <div v-for="ac in criterion.assessment_criteria" :key="ac.id" class="border-t border-border p-4">
                        <h3 class="mb-3 flex items-center gap-2 text-sm font-medium text-slate-700">
                            <BookOpen class="h-4 w-4 text-brand-600" />
                            {{ ac.course?.name ?? 'Sin curso' }} - {{ ac.course?.grade_section?.name ?? '' }}
                        </h3>

                        <div v-if="ac.students.length > 0" class="overflow-x-auto">
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
                                    <tr v-for="student in ac.students" :key="student.id" class="border-t border-border">
                                        <td class="p-2 font-medium">{{ student.code ?? '-' }}</td>
                                        <td class="p-2">{{ student.first_name }}</td>
                                        <td class="p-2">{{ student.last_name }}</td>
                                        <td class="p-2">
                                            <input
                                                v-model="scoreModel(ac, student.id)[student.id]"
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
                            <button
                                type="button"
                                class="mt-4 flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                                @click="saveGrades(criterion, ac)"
                            >
                                <Save class="h-4 w-4" /> Guardar notas
                            </button>
                        </div>
                        <p v-else class="text-sm text-slate-500">Este curso no tiene estudiantes matriculados.</p>
                    </div>
                </div>
                <div v-else class="border-t border-border p-4">
                    <p class="text-sm text-slate-500">Este criterio no está asociado a ningún curso.</p>
                    <a
                        :href="route('admin.academic.evaluation-criteria.grades', criterion.id)"
                        class="mt-2 inline-block text-sm text-brand-600 hover:text-brand-800"
                    >
                        + Asociar curso y cargar notas
                    </a>
                </div>
            </div>

            <Pagination :meta="criteria" />
        </div>

        <Modal :show="showModal" title="Nuevo criterio de evaluación" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                <TextareaField v-model="form.description" label="Descripción" :error="form.errors.description" />

                <div class="flex justify-end gap-2 border-t border-border pt-4">
                    <button type="button" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted" @click="showModal = false">
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60"
                    >
                        Guardar
                    </button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
