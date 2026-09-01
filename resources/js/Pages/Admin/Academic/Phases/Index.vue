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
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    phases: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.academic.phases.index'),
        { search: search.value || undefined, per_page: perPage.value, sort_by: sortBy.value || undefined, sort_dir: sortDir.value },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    router.get(route('admin.academic.phases.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'order', label: 'Orden', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const showModal = ref(false);
const editing = ref(null);

const form = useForm({ name: '', order: 1 });

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(phase) {
    editing.value = phase;
    form.clearErrors();
    form.name = phase.name;
    form.order = phase.order;
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.academic.phases.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.academic.phases.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(phase) {
    if (confirm(`¿Eliminar la fase ${phase.name}?`)) {
        router.delete(route('admin.academic.phases.destroy', phase.id));
    }
}
</script>

<template>
    <Head title="Fases escolares" />

    <AppLayout title="Fases escolares">
        <div class="space-y-4">
            <PageHeader eyebrow="Académico" title="Fases escolares" description="Gestiona las fases o bimestres en los que se organiza el año escolar." />

            <ListToolbar v-model:search="search" v-model:per-page="perPage" placeholder="Nombre..." @submit="applyFilters" @clear="clearFilters">
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nueva fase
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="phases.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay fases escolares registradas."
                @sort="onSort"
            >
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="phases" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar fase escolar' : 'Nueva fase escolar'" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                <FormField v-model="form.order" type="number" label="Orden" required :error="form.errors.order" />

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
