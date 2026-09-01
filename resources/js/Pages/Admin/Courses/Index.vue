<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import ListToolbar from '@/Components/ListToolbar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import FormField from '@/Components/FormField.vue';
import SelectField from '@/Components/SelectField.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    courses: Object,
    gradeSections: Array,
    subjects: Array,
    teachers: Array,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.courses.index'),
        { search: search.value || undefined, per_page: perPage.value, sort_by: sortBy.value || undefined, sort_dir: sortDir.value },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    router.get(route('admin.courses.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'subject_name', label: 'Materia' },
    { key: 'section', label: 'Grado/Sección' },
    { key: 'school_year', label: 'Año escolar', sortable: true },
    { key: 'teacher_name', label: 'Profesor' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const gradeSectionOptions = props.gradeSections.map((gs) => ({ value: gs.id, label: `${gs.name} - ${gs.level}` }));
const subjectOptions = props.subjects.map((s) => ({ value: s.id, label: s.name }));
const teacherOptions = props.teachers.map((t) => ({ value: t.id, label: `${t.first_name} ${t.last_name}` }));

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    name: '',
    subject_id: '',
    school_year: '',
    grade_section_id: '',
    teacher_id: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(course) {
    editing.value = course;
    form.clearErrors();
    form.name = course.name;
    form.subject_id = course.subject_id ?? '';
    form.school_year = course.school_year;
    form.grade_section_id = course.grade_section_id;
    form.teacher_id = course.teacher_id ?? '';
    showModal.value = true;
}

function onSubjectChange(subjectId) {
    if (!editing.value && !form.name) {
        form.name = props.subjects.find((s) => String(s.id) === String(subjectId))?.name ?? form.name;
    }
}

function submit() {
    if (editing.value) {
        form.put(route('admin.courses.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.courses.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(course) {
    if (confirm(`¿Eliminar el curso ${course.name}?`)) {
        router.delete(route('admin.courses.destroy', course.id));
    }
}
</script>

<template>
    <Head title="Cursos" />

    <AppLayout title="Cursos">
        <div class="space-y-4">
            <PageHeader
                eyebrow="Académico"
                title="Cursos"
                description="Gestiona los cursos: materia, grado/sección, año escolar y profesor asignado."
            />

            <ListToolbar
                v-model:search="search"
                v-model:per-page="perPage"
                placeholder="Nombre, año escolar..."
                @submit="applyFilters"
                @clear="clearFilters"
            >
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo curso
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="courses.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay cursos registrados."
                @sort="onSort"
            >
                <template #cell-subject_name="{ row }">{{ row.subject?.name ?? '-' }}</template>
                <template #cell-section="{ row }">{{ row.grade_section?.name ?? '-' }}</template>
                <template #cell-teacher_name="{ row }">
                    {{ row.teacher ? `${row.teacher.first_name} ${row.teacher.last_name}` : '-' }}
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="courses" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar curso' : 'Nuevo curso'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SelectField
                        v-model="form.subject_id"
                        label="Materia"
                        required
                        :options="subjectOptions"
                        :error="form.errors.subject_id"
                        @update:model-value="onSubjectChange"
                    />
                    <SelectField
                        v-model="form.grade_section_id"
                        label="Grado/Sección"
                        required
                        :options="gradeSectionOptions"
                        :error="form.errors.grade_section_id"
                    />
                    <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                    <FormField v-model="form.school_year" label="Año escolar" required :error="form.errors.school_year" />
                    <SelectField
                        v-model="form.teacher_id"
                        label="Profesor"
                        placeholder="Sin profesor"
                        :options="teacherOptions"
                        :error="form.errors.teacher_id"
                    />
                </div>

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
