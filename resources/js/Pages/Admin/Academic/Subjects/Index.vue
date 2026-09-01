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
import TextareaField from '@/Components/TextareaField.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    subjects: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.academic.subjects.index'),
        { search: search.value || undefined, per_page: perPage.value, sort_by: sortBy.value || undefined, sort_dir: sortDir.value },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    router.get(route('admin.academic.subjects.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'code', label: 'Código', sortable: true },
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    name: '',
    code: '',
    description: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(subject) {
    editing.value = subject;
    form.clearErrors();
    form.name = subject.name;
    form.code = subject.code ?? '';
    form.description = subject.description ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.academic.subjects.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.academic.subjects.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(subject) {
    if (confirm(`¿Eliminar la materia ${subject.name}?`)) {
        router.delete(route('admin.academic.subjects.destroy', subject.id));
    }
}
</script>

<template>
    <Head title="Materias" />

    <AppLayout title="Materias">
        <div class="space-y-4">
            <PageHeader eyebrow="Académico" title="Materias" description="Gestiona las materias que se dictan en el colegio." />

            <ListToolbar v-model:search="search" v-model:per-page="perPage" placeholder="Nombre, código..." @submit="applyFilters" @clear="clearFilters">
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nueva materia
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="subjects.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay materias registradas."
                @sort="onSort"
            >
                <template #cell-code="{ value }">{{ value ?? '-' }}</template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="subjects" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar materia' : 'Nueva materia'" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                <FormField v-model="form.code" label="Código" :error="form.errors.code" />
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
