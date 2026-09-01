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
import TimeField from '@/Components/TimeField.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    shifts: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.academic.shifts.index'),
        { search: search.value || undefined, per_page: perPage.value, sort_by: sortBy.value || undefined, sort_dir: sortDir.value },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    router.get(route('admin.academic.shifts.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'start_time', label: 'Inicio', sortable: true },
    { key: 'end_time', label: 'Fin', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    name: '',
    start_time: '',
    end_time: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(shift) {
    editing.value = shift;
    form.clearErrors();
    form.name = shift.name;
    form.start_time = shift.start_time ?? '';
    form.end_time = shift.end_time ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.academic.shifts.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.academic.shifts.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(shift) {
    if (confirm(`¿Eliminar el turno ${shift.name}?`)) {
        router.delete(route('admin.academic.shifts.destroy', shift.id));
    }
}
</script>

<template>
    <Head title="Turnos" />

    <AppLayout title="Turnos">
        <div class="space-y-4">
            <PageHeader eyebrow="Académico" title="Turnos" description="Gestiona los turnos de clases y sus horarios." />

            <ListToolbar v-model:search="search" v-model:per-page="perPage" placeholder="Nombre..." @submit="applyFilters" @clear="clearFilters">
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo turno
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="shifts.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay turnos registrados."
                @sort="onSort"
            >
                <template #cell-start_time="{ value }">{{ value ?? '-' }}</template>
                <template #cell-end_time="{ value }">{{ value ?? '-' }}</template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="shifts" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar turno' : 'Nuevo turno'" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <TimeField v-model="form.start_time" label="Inicio" :error="form.errors.start_time" />
                    <TimeField v-model="form.end_time" label="Fin" :error="form.errors.end_time" />
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
