<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import FormField from '@/Components/FormField.vue';
import SelectField from '@/Components/SelectField.vue';
import DateField from '@/Components/DateField.vue';
import TextareaField from '@/Components/TextareaField.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    enrollments: Object,
    students: Array,
    gradeSections: Array,
    academicYears: Array,
    filters: { type: Object, default: () => ({}) },
});

const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? '');

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    router.get(
        route('admin.enrollments.index'),
        { sort_by: sortBy.value, sort_dir: sortDir.value },
        { preserveState: true, replace: true }
    );
}

const columns = [
    { key: 'student_name', label: 'Estudiante' },
    { key: 'section', label: 'Grado' },
    { key: 'year', label: 'Año' },
    { key: 'status', label: 'Estado', sortable: true },
    { key: 'enrollment_date', label: 'Fecha', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const studentOptions = props.students.map((s) => ({ value: s.id, label: `${s.first_name} ${s.last_name}` }));
const gradeSectionOptions = props.gradeSections.map((gs) => ({ value: gs.id, label: `${gs.name} - ${gs.level}` }));
const academicYearOptions = props.academicYears.map((y) => ({ value: y.id, label: y.name }));

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    student_id: '',
    grade_section_id: '',
    academic_year_id: '',
    status: 'matriculado',
    enrollment_date: '',
    notes: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.status = 'matriculado';
    showModal.value = true;
}

function openEdit(enrollment) {
    editing.value = enrollment;
    form.clearErrors();
    form.student_id = enrollment.student_id;
    form.grade_section_id = enrollment.grade_section_id;
    form.academic_year_id = enrollment.academic_year_id;
    form.status = enrollment.status;
    form.enrollment_date = enrollment.enrollment_date?.substring(0, 10) ?? '';
    form.notes = enrollment.notes ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.enrollments.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.enrollments.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(enrollment) {
    if (confirm('¿Eliminar esta matrícula?')) {
        router.delete(route('admin.enrollments.destroy', enrollment.id));
    }
}
</script>

<template>
    <Head title="Matrículas" />

    <AppLayout title="Matrículas">
        <div class="space-y-4">
            <div class="flex items-center justify-end">
                <button
                    type="button"
                    class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nueva matrícula
                </button>
            </div>

            <DataTable
                :columns="columns"
                :rows="enrollments.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay matrículas registradas."
                @sort="onSort"
            >
                <template #cell-student_name="{ row }">{{ row.student ? `${row.student.first_name} ${row.student.last_name}` : '-' }}</template>
                <template #cell-section="{ row }">{{ row.grade_section?.name ?? '-' }}</template>
                <template #cell-year="{ row }">{{ row.academic_year?.name ?? '-' }}</template>
                <template #cell-status="{ value }">{{ value.charAt(0).toUpperCase() + value.slice(1) }}</template>
                <template #cell-enrollment_date="{ value }">{{ value?.substring(0, 10).split('-').reverse().join('/') }}</template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="enrollments" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar matrícula' : 'Nueva matrícula'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SelectField v-model="form.student_id" label="Estudiante" required :options="studentOptions" :error="form.errors.student_id" />
                    <SelectField
                        v-model="form.grade_section_id"
                        label="Grado/Sección"
                        required
                        :options="gradeSectionOptions"
                        :error="form.errors.grade_section_id"
                    />
                    <SelectField
                        v-model="form.academic_year_id"
                        label="Año escolar"
                        required
                        :options="academicYearOptions"
                        :error="form.errors.academic_year_id"
                    />
                    <FormField v-model="form.status" label="Estado" required :error="form.errors.status" />
                    <DateField v-if="!editing" v-model="form.enrollment_date" label="Fecha de matrícula" required :error="form.errors.enrollment_date" />
                </div>
                <TextareaField v-model="form.notes" label="Notas" :error="form.errors.notes" />

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
