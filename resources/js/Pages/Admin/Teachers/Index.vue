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
    teachers: Object,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.teachers.index'),
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
    router.get(route('admin.teachers.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'code', label: 'Código', sortable: true },
    { key: 'name', label: 'Nombre', sortable: true, sortKey: 'first_name' },
    { key: 'specialty', label: 'Especialidad', sortable: true },
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
    code: '',
    specialty: '',
    phone: '',
    active: true,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.active = true;
    showModal.value = true;
}

function openEdit(teacher) {
    editing.value = teacher;
    form.clearErrors();
    form.first_name = teacher.first_name;
    form.last_name = teacher.last_name;
    form.email = teacher.user?.email ?? '';
    form.password = '';
    form.code = teacher.code;
    form.specialty = teacher.specialty ?? '';
    form.phone = teacher.user?.phone ?? '';
    form.active = teacher.user?.active ?? true;
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.teachers.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.teachers.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(teacher) {
    if (confirm(`¿Eliminar a ${teacher.first_name} ${teacher.last_name}?`)) {
        router.delete(route('admin.teachers.destroy', teacher.id));
    }
}
</script>

<template>
    <Head title="Profesores" />

    <AppLayout title="Profesores">
        <div class="space-y-4">
            <PageHeader
                eyebrow="Personas"
                title="Profesores"
                description="Gestiona el cuerpo docente del colegio: datos de contacto, especialidad y estado de la cuenta."
            />

            <ListToolbar
                v-model:search="search"
                v-model:status="status"
                v-model:per-page="perPage"
                placeholder="Código, nombre, especialidad..."
                show-status
                @submit="applyFilters"
                @clear="clearFilters"
            >
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo profesor
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="teachers.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay profesores registrados."
                @sort="onSort"
            >
                <template #cell-name="{ row }">{{ row.first_name }} {{ row.last_name }}</template>
                <template #cell-specialty="{ row }">{{ row.specialty ?? '-' }}</template>
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

            <Pagination :meta="teachers" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar profesor' : 'Nuevo profesor'" max-width="max-w-2xl" @close="showModal = false">
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
                    <FormField v-model="form.code" label="Código" required :error="form.errors.code" />
                    <FormField v-model="form.specialty" label="Especialidad" :error="form.errors.specialty" />
                    <FormField v-model="form.phone" label="Teléfono" :error="form.errors.phone" />
                    <label v-if="editing" class="flex items-center gap-2 pt-6 text-sm text-slate-700">
                        <input v-model="form.active" type="checkbox" class="rounded border-border text-brand-600 focus:ring-brand-300">
                        Activo
                    </label>
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
