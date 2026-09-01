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
import StatusBadge from '@/Components/StatusBadge.vue';
import FormField from '@/Components/FormField.vue';
import DateField from '@/Components/DateField.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    years: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.academic.years.index'),
        { search: search.value || undefined, per_page: perPage.value, sort_by: sortBy.value || undefined, sort_dir: sortDir.value },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    router.get(route('admin.academic.years.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'start_date', label: 'Inicio', sortable: true },
    { key: 'end_date', label: 'Fin', sortable: true },
    { key: 'is_current', label: 'Actual', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    name: '',
    start_date: '',
    end_date: '',
    is_current: false,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(year) {
    editing.value = year;
    form.clearErrors();
    form.name = year.name;
    form.start_date = year.start_date.substring(0, 10);
    form.end_date = year.end_date.substring(0, 10);
    form.is_current = !!year.is_current;
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.academic.years.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.academic.years.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(year) {
    if (confirm(`¿Eliminar el año ${year.name}?`)) {
        router.delete(route('admin.academic.years.destroy', year.id));
    }
}

function formatDate(value) {
    return value.substring(0, 10).split('-').reverse().join('/');
}
</script>

<template>
    <Head title="Años escolares" />

    <AppLayout title="Años escolares">
        <div class="space-y-4">
            <PageHeader eyebrow="Académico" title="Años escolares" description="Gestiona los años escolares y marca cuál está vigente." />

            <ListToolbar v-model:search="search" v-model:per-page="perPage" placeholder="Nombre..." @submit="applyFilters" @clear="clearFilters">
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo año
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="years.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay años escolares registrados."
                @sort="onSort"
            >
                <template #cell-start_date="{ value }">{{ formatDate(value) }}</template>
                <template #cell-end_date="{ value }">{{ formatDate(value) }}</template>
                <template #cell-is_current="{ row }">
                    <StatusBadge v-if="row.is_current" label="Actual" tone="success" />
                    <span v-else class="text-slate-400">-</span>
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="years" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar año escolar' : 'Nuevo año escolar'" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <DateField v-model="form.start_date" label="Inicio" required :error="form.errors.start_date" />
                    <DateField v-model="form.end_date" label="Fin" required :error="form.errors.end_date" />
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input v-model="form.is_current" type="checkbox" class="rounded border-border text-brand-600 focus:ring-brand-300">
                    Año escolar actual
                </label>

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
