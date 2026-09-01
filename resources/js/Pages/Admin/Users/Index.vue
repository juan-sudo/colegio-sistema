<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Power, Trash2 } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import ListToolbar from '@/Components/ListToolbar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import IconButton from '@/Components/IconButton.vue';
import FormField from '@/Components/FormField.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.users.index'),
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
    router.get(route('admin.users.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'name', label: 'Nombre', sortable: true },
    { key: 'email', label: 'Email', sortable: true },
    { key: 'role', label: 'Rol', sortable: true },
    { key: 'phone', label: 'Teléfono', sortable: true },
    { key: 'status', label: 'Estado', sortable: true, sortKey: 'active' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const roleOptions = props.roles.map((r) => ({ value: r, label: r.charAt(0).toUpperCase() + r.slice(1) }));

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: 'admin',
    phone: '',
    active: true,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.role = 'admin';
    form.active = true;
    showModal.value = true;
}

function openEdit(user) {
    editing.value = user;
    form.clearErrors();
    form.name = user.name;
    form.email = user.email;
    form.password = '';
    form.role = user.role;
    form.phone = user.phone ?? '';
    form.active = !!user.active;
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.users.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.users.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function toggleActive(user) {
    const action = user.active ? 'desactivar' : 'activar';
    if (confirm(`¿Estás seguro de que deseas ${action} a ${user.name}?`)) {
        router.post(route('admin.users.toggle-active', user.id));
    }
}

function destroy(user) {
    if (confirm(`¿Eliminar a ${user.name}?`)) {
        router.delete(route('admin.users.destroy', user.id));
    }
}
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout title="Usuarios">
        <div class="space-y-4">
            <PageHeader
                eyebrow="Personas"
                title="Usuarios"
                description="Administra las cuentas de acceso al sistema y su rol dentro del colegio."
            />

            <ListToolbar
                v-model:search="search"
                v-model:status="status"
                v-model:per-page="perPage"
                placeholder="Nombre, email, rol..."
                show-status
                @submit="applyFilters"
                @clear="clearFilters"
            >
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo usuario
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="users.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay usuarios registrados."
                @sort="onSort"
            >
                <template #cell-role="{ value }">{{ value.charAt(0).toUpperCase() + value.slice(1) }}</template>
                <template #cell-phone="{ value }">{{ value ?? '-' }}</template>
                <template #cell-status="{ row }">
                    <StatusBadge :label="row.active ? 'Activo' : 'Inactivo'" :tone="row.active ? 'success' : 'danger'" />
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Power" tone="warning" :title="row.active ? 'Desactivar' : 'Activar'" @click="toggleActive(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="users" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar usuario' : 'Nuevo usuario'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <FormField v-model="form.name" label="Nombre" required :error="form.errors.name" />
                    <FormField v-model="form.email" type="email" label="Correo electrónico" required :error="form.errors.email" />
                    <FormField
                        v-model="form.password"
                        type="password"
                        :label="editing ? 'Nueva contraseña' : 'Contraseña'"
                        :required="!editing"
                        :error="form.errors.password"
                    />
                    <SelectField v-model="form.role" label="Rol" required :options="roleOptions" :error="form.errors.role" />
                    <FormField v-model="form.phone" label="Teléfono" :error="form.errors.phone" />
                    <label class="flex items-center gap-2 pt-6 text-sm text-slate-700">
                        <input v-model="form.active" type="checkbox" class="rounded border-border text-brand-600 focus:ring-brand-300">
                        Activo
                    </label>
                </div>
                <p v-if="editing" class="text-xs text-slate-500">Deja la contraseña vacía para mantener la actual.</p>

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
