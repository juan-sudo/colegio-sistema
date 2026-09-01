<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Trash2, Download } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import StatCard from '@/Components/StatCard.vue';
import SelectField from '@/Components/SelectField.vue';
import FormField from '@/Components/FormField.vue';
import DateField from '@/Components/DateField.vue';
import TextareaField from '@/Components/TextareaField.vue';
import IconButton from '@/Components/IconButton.vue';

const props = defineProps({
    entries: Object,
    totalIncome: [String, Number],
    totalExpense: [String, Number],
    totalFixedCost: [String, Number],
    filters: Object,
});

const filterType = ref(props.filters.type ?? '');
const dateFrom = ref(props.filters.date_from ?? '');
const dateTo = ref(props.filters.date_to ?? '');
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? '');

function applyFilters() {
    router.get(
        route('admin.accounting.index'),
        {
            type: filterType.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            sort_by: sortBy.value || undefined,
            sort_dir: sortDir.value || undefined,
        },
        { preserveState: true }
    );
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

function money(v) {
    return `S/ ${Number(v).toFixed(2)}`;
}

const columns = [
    { key: 'date', label: 'Fecha', sortable: true },
    { key: 'type', label: 'Tipo', sortable: true },
    { key: 'category', label: 'Categoría', sortable: true },
    { key: 'description', label: 'Descripción', sortable: true },
    { key: 'amount', label: 'Monto', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

function typeLabel(type) {
    return type.replace('_', ' ').replace(/^./, (c) => c.toUpperCase());
}

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    type: 'ingreso',
    category: '',
    description: '',
    amount: '',
    date: '',
    reference: '',
    notes: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.type = 'ingreso';
    showModal.value = true;
}

function openEdit(entry) {
    editing.value = entry;
    form.clearErrors();
    form.type = entry.type;
    form.category = entry.category;
    form.description = entry.description;
    form.amount = entry.amount;
    form.date = entry.date?.substring(0, 10) ?? '';
    form.reference = entry.reference ?? '';
    form.notes = entry.notes ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.accounting.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.accounting.store'), { onSuccess: () => (showModal.value = false) });
    }
}

function destroy(entry) {
    if (confirm('¿Eliminar este asiento contable?')) {
        router.delete(route('admin.accounting.destroy', entry.id));
    }
}
</script>

<template>
    <Head title="Contabilidad" />

    <AppLayout title="Registro contable">
        <div class="space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <StatCard label="Ingresos" :value="money(totalIncome)" tone="success" />
                <StatCard label="Egresos" :value="money(totalExpense)" tone="danger" />
                <StatCard label="Gastos fijos" :value="money(totalFixedCost)" tone="warning" />
            </div>

            <div class="flex flex-wrap items-end justify-between gap-4 rounded-xl border border-border bg-surface p-4 shadow-sm">
                <div class="flex flex-wrap items-end gap-4">
                    <SelectField
                        v-model="filterType"
                        label="Tipo"
                        placeholder="Todos"
                        :options="[
                            { value: 'ingreso', label: 'Ingreso' },
                            { value: 'egreso', label: 'Egreso' },
                            { value: 'gasto_fijo', label: 'Gasto fijo' },
                        ]"
                        @update:model-value="applyFilters"
                    />
                    <DateField v-model="dateFrom" label="Desde" @update:model-value="applyFilters" />
                    <DateField v-model="dateTo" label="Hasta" @update:model-value="applyFilters" />
                </div>
                <div class="flex gap-2">
                    <a
                        :href="route('admin.reports.accounting.export', filters)"
                        class="flex items-center gap-2 rounded-lg bg-success-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-success-700"
                    >
                        <Download class="h-4 w-4" /> Exportar Excel
                    </a>
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700"
                        @click="openCreate"
                    >
                        <Plus class="h-4 w-4" /> Nuevo asiento
                    </button>
                </div>
            </div>

            <DataTable
                :columns="columns"
                :rows="entries.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay asientos contables registrados."
                @sort="onSort"
            >
                <template #cell-date="{ value }">{{ value?.substring(0, 10).split('-').reverse().join('/') }}</template>
                <template #cell-type="{ value }">{{ typeLabel(value) }}</template>
                <template #cell-amount="{ value }">{{ money(value) }}</template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                        <IconButton :icon="Trash2" tone="danger" title="Eliminar" @click="destroy(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="entries" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar asiento' : 'Nuevo asiento'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SelectField
                        v-model="form.type"
                        label="Tipo"
                        required
                        :options="[
                            { value: 'ingreso', label: 'Ingreso' },
                            { value: 'egreso', label: 'Egreso' },
                            { value: 'gasto_fijo', label: 'Gasto fijo' },
                        ]"
                        :error="form.errors.type"
                    />
                    <FormField v-model="form.category" label="Categoría" required :error="form.errors.category" />
                    <FormField v-model="form.amount" type="number" label="Monto" required :error="form.errors.amount" />
                    <DateField v-model="form.date" label="Fecha" required :error="form.errors.date" />
                    <FormField v-model="form.reference" label="Referencia" :error="form.errors.reference" />
                </div>
                <TextareaField v-model="form.description" label="Descripción" required :error="form.errors.description" />
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
