<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, IdCard, Trash2 } from 'lucide-vue-next';
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
import DateField from '@/Components/DateField.vue';

const props = defineProps({
    students: Object,
    gradeSections: Array,
    guardians: { type: Array, default: () => [] },
    filters: Object,
});

const search = ref(props.filters.search ?? '');
const status = ref(props.filters.status ?? '');
const perPage = ref(props.filters.per_page ?? 20);
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? 'asc');

function applyFilters() {
    router.get(
        route('admin.students.index'),
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
    router.get(route('admin.students.index'), { per_page: perPage.value }, { preserveState: true, replace: true });
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'code', label: 'Código', sortable: true },
    { key: 'dni', label: 'DNI', sortable: true },
    { key: 'name', label: 'Nombre', sortable: true, sortKey: 'first_name' },
    { key: 'section', label: 'Grado/Sección' },
    { key: 'status', label: 'Estado', sortable: true, sortKey: 'active' },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const gradeSectionOptions = props.gradeSections.map((gs) => ({ value: gs.id, label: `${gs.name} - ${gs.level}` }));

const showModal = ref(false);
const editing = ref(null);
const showCarnetModal = ref(false);
const selectedStudent = ref(null);

const form = useForm({
    dni: '',
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    grade_section_id: '',
    birth_date: '',
    phone: '',
    guardian_id: '',
    active: true,
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.active = true;
    showModal.value = true;
}

function openEdit(student) {
    editing.value = student;
    form.clearErrors();
    form.dni = student.dni;
    form.first_name = student.first_name;
    form.last_name = student.last_name;
    form.email = student.user?.email ?? '';
    form.password = '';
    form.grade_section_id = student.grade_section_id;
    form.birth_date = student.birth_date ? student.birth_date.substring(0, 10) : '';
    form.phone = student.user?.phone ?? '';
    form.guardian_id = (student.guardians ?? [])[0]?.id ?? '';
    form.active = !!student.active;
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.students.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.students.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(student) {
    if (confirm(`¿Eliminar a ${student.first_name} ${student.last_name}?`)) {
        router.delete(route('admin.students.destroy', student.id));
    }
}

function openCarnet(student) {
    selectedStudent.value = student;
    showCarnetModal.value = true;
}
</script>

<template>
    <Head title="Estudiantes" />

    <AppLayout title="Estudiantes">
        <div class="space-y-4">
            <PageHeader
                eyebrow="Personas"
                title="Estudiantes"
                description="Gestiona los estudiantes matriculados en el colegio: datos personales, sección y credenciales de asistencia."
            />

            <ListToolbar
                v-model:search="search"
                v-model:status="status"
                v-model:per-page="perPage"
                placeholder="Código, DNI, nombre..."
                show-status
                @submit="applyFilters"
                @clear="clearFilters"
            >
                <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                    @click="openCreate"
                >
                    <Plus class="h-4 w-4" /> Nuevo estudiante
                </button>
            </ListToolbar>

            <DataTable
                :columns="columns"
                :rows="students.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay estudiantes registrados."
                @sort="onSort"
            >
                <template #cell-name="{ row }">{{ row.first_name }} {{ row.last_name }}</template>
                <template #cell-section="{ row }">{{ row.grade_section?.name ?? '-' }}</template>
                <template #cell-status="{ row }">
                    <StatusBadge :label="row.active ? 'Activo' : 'Inactivo'" :tone="row.active ? 'success' : 'danger'" />
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="IdCard" title="Carnet" @click="openCarnet(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="students" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar estudiante' : 'Nuevo estudiante'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <FormField v-model="form.dni" label="DNI" required :error="form.errors.dni" />
                        <p class="mt-1 text-xs text-slate-500">Se usará para generar el código, QR, código de barras y biométrico.</p>
                    </div>
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
                    <SelectField
                        v-model="form.grade_section_id"
                        label="Grado/Sección"
                        required
                        :options="gradeSectionOptions"
                        :error="form.errors.grade_section_id"
                    />
                    <DateField v-model="form.birth_date" label="Fecha de nacimiento" :error="form.errors.birth_date" />
                    <FormField v-model="form.phone" label="Teléfono" :error="form.errors.phone" />
                    <div class="md:col-span-2">
                        <SelectField
                            v-model="form.guardian_id"
                            label="Apoderado principal"
                            :options="guardians"
                            :error="form.errors.guardian_id"
                            placeholder="Seleccionar apoderado"
                        />
                        <p class="mt-1 text-xs text-slate-500">Elige el apoderado principal para notificar la falta por WhatsApp.</p>
                    </div>
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

        <Modal :show="showCarnetModal" :title="selectedStudent ? `Carnet - ${selectedStudent.first_name} ${selectedStudent.last_name}` : 'Carnet'" max-width="max-w-4xl" @close="showCarnetModal = false">
            <div v-if="selectedStudent" class="space-y-4">
                <iframe v-if="showCarnetModal" :src="route('admin.students.carnet.pdf', selectedStudent.id)" class="h-[600px] w-full border-0" />
                <div class="flex justify-end gap-2 border-t border-border pt-4">
                    <button type="button" class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="window.print()">
                        Imprimir
                    </button>
                    <button type="button" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted dark:text-slate-300" @click="showCarnetModal = false">
                        Cerrar
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
