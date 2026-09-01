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
import StatusBadge from '@/Components/StatusBadge.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    guardians: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.guardians.index'),
        {
            search: search.value || undefined,
            status: status.value === '' ? undefined : status.value,
            per_page: perPage.value,
            sort_by: sortBy.value || undefined,
            sort_dir: sortDir.value,
        },
        { preserveState: true, replace: true }
    );
}

function clearFilters() {
    search.value = '';
    status.value = '';
    router.get(route('admin.guardians.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'name', label: 'Nombre', sortable: true, sortKey: 'first_name' },
    { key: 'phone_whatsapp', label: 'WhatsApp', sortable: true },
    { key: 'email', label: 'Email' },
    { key: 'status', label: 'Estado' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    phone_whatsapp: '',
    phone: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    showModal.value = true;
}

function openEdit(guardian) {
    editing.value = guardian;
    form.clearErrors();
    form.first_name = guardian.first_name;
    form.last_name = guardian.last_name;
    form.email = guardian.user?.email ?? '';
    form.password = '';
    form.phone_whatsapp = guardian.phone_whatsapp;
    form.phone = guardian.user?.phone ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.guardians.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.guardians.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(guardian) {
    if (confirm(`¿Eliminar a ${guardian.first_name} ${guardian.last_name}?`)) {
        router.delete(route('admin.guardians.destroy', guardian.id));
    }
}
</script>

<template>
    <Head title="Apoderados" />

    <AppLayout title="Apoderados">
        <div class="space-y-4">
            <PageHeader
                eyebrow="Personas"
                title="Apoderados"
                description="Gestiona los padres y apoderados registrados, y su vínculo con los estudiantes."
            />

            <ListToolbar
                v-model:search="search"
                v-model:status="status"
                v-model:per-page="perPage"
                placeholder="Nombre, WhatsApp, email..."
                show-status
                @submit="applyFilters"
                @clear="clearFilters"
            >
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo apoderado
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="guardians.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay apoderados registrados."
                @sort="onSort"
            >
                <template #cell-name="{ row }">{{ row.first_name }} {{ row.last_name }}</template>
                <template #cell-email="{ row }">{{ row.user?.email }}</template>
                <template #cell-status="{ row }">
                    <StatusBadge :label="row.user?.active ? 'Activo' : 'Inactivo'" :tone="row.user?.active ? 'success' : 'danger'" />
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="guardians" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar apoderado' : 'Nuevo apoderado'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <FormField v-model="form.first_name" label="Nombre" required :error="form.errors.first_name" />
                    <FormField v-model="form.last_name" label="Apellido" required :error="form.errors.last_name" />
                    <FormField v-model="form.email" type="email" label="Correo electrónico" required :error="form.errors.email" />
                    <FormField
                        v-if="!editing"
                        v-model="form.password"
                        type="password"
                        label="Contraseña"
                        required
                        :error="form.errors.password"
                    />
                    <FormField v-model="form.phone_whatsapp" label="WhatsApp" required :error="form.errors.phone_whatsapp" />
                    <FormField v-model="form.phone" label="Teléfono" :error="form.errors.phone" />
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
