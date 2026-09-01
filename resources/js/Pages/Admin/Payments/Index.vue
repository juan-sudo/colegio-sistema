<script setup>
import { ref } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Plus, Pencil, Download } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import IconButton from '@/Components/IconButton.vue';
import SelectField from '@/Components/SelectField.vue';
import FormField from '@/Components/FormField.vue';
import DateField from '@/Components/DateField.vue';
import TextareaField from '@/Components/TextareaField.vue';

const props = defineProps({
    payments: Object,
    students: Array,
    filters: Object,
});

const filterType = ref(props.filters.type ?? '');
const filterStatus = ref(props.filters.status ?? '');
const sortBy = ref(props.filters.sort_by ?? '');
const sortDir = ref(props.filters.sort_dir ?? '');

function applyFilters() {
    router.get(
        route('admin.payments.index'),
        { type: filterType.value, status: filterStatus.value, sort_by: sortBy.value || undefined, sort_dir: sortDir.value || undefined },
        { preserveState: true }
    );
}

function onSort(key) {
    sortDir.value = sortBy.value === key && sortDir.value === 'asc' ? 'desc' : 'asc';
    sortBy.value = key;
    applyFilters();
}

const columns = [
    { key: 'student_name', label: 'Estudiante' },
    { key: 'type', label: 'Tipo', sortable: true },
    { key: 'amount', label: 'Monto', sortable: true },
    { key: 'paid', label: 'Pagado', sortable: true },
    { key: 'balance', label: 'Saldo' },
    { key: 'due_date', label: 'Vence', sortable: true },
    { key: 'status', label: 'Estado', sortable: true },
    { key: 'actions', label: 'Acciones', align: 'right' },
];

const studentOptions = props.students.map((s) => ({ value: s.id, label: `${s.first_name} ${s.last_name}` }));

function money(v) {
    return `S/ ${Number(v).toFixed(2)}`;
}

function statusTone(payment) {
    if (payment.status === 'pagado') return 'success';
    if (payment.is_overdue) return 'danger';
    return 'warning';
}

const showModal = ref(false);
const editing = ref(null);

const form = useForm({
    student_id: '',
    type: '',
    amount: '',
    discount: '',
    paid: '',
    status: 'pendiente',
    due_date: '',
    paid_date: '',
    payment_method: '',
    notes: '',
});

function openCreate() {
    editing.value = null;
    form.reset();
    form.clearErrors();
    form.status = 'pendiente';
    showModal.value = true;
}

function openEdit(payment) {
    editing.value = payment;
    form.clearErrors();
    form.student_id = payment.student_id;
    form.type = payment.type;
    form.amount = payment.amount;
    form.discount = payment.discount;
    form.paid = payment.paid;
    form.status = payment.status;
    form.due_date = payment.due_date?.substring(0, 10) ?? '';
    form.paid_date = payment.paid_date?.substring(0, 10) ?? '';
    form.payment_method = payment.payment_method ?? '';
    form.notes = payment.notes ?? '';
    showModal.value = true;
}

function submit() {
    if (editing.value) {
        form.put(route('admin.payments.update', editing.value.id), { onSuccess: () => (showModal.value = false) });
    } else {
        form.post(route('admin.payments.store'), { onSuccess: () => (showModal.value = false) });
    }
}
</script>

<template>
    <Head title="Pagos" />

    <AppLayout title="Gestión de pagos">
        <div class="space-y-4">
            <div class="flex flex-wrap items-end justify-between gap-4 rounded-xl border border-border bg-surface p-4 shadow-sm">
                <div class="flex flex-wrap items-end gap-4">
                    <SelectField
                        v-model="filterType"
                        label="Tipo"
                        placeholder="Todos"
                        :options="[{ value: 'matricula', label: 'Matrícula' }, { value: 'pension', label: 'Pensión' }]"
                        @update:model-value="applyFilters"
                    />
                    <SelectField
                        v-model="filterStatus"
                        label="Estado"
                        placeholder="Todos"
                        :options="[
                            { value: 'pendiente', label: 'Pendiente' },
                            { value: 'pagado', label: 'Pagado' },
                            { value: 'vencido', label: 'Vencido' },
                        ]"
                        @update:model-value="applyFilters"
                    />
                </div>
                <div class="flex gap-2">
                    <a
                        :href="route('admin.reports.payments.export', filters)"
                        class="flex items-center gap-2 rounded-lg bg-success-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-success-700"
                    >
                        <Download class="h-4 w-4" /> Exportar Excel
                    </a>
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-700"
                        @click="openCreate"
                    >
                        <Plus class="h-4 w-4" /> Nuevo pago
                    </button>
                </div>
            </div>

            <DataTable
                :columns="columns"
                :rows="payments.data"
                :sort-by="sortBy"
                :sort-dir="sortDir"
                empty-text="No hay pagos registrados."
                @sort="onSort"
            >
                <template #cell-student_name="{ row }">{{ row.student ? `${row.student.first_name} ${row.student.last_name}` : '-' }}</template>
                <template #cell-type="{ value }">{{ value.charAt(0).toUpperCase() + value.slice(1) }}</template>
                <template #cell-amount="{ value }">{{ money(value) }}</template>
                <template #cell-paid="{ value }">{{ money(value) }}</template>
                <template #cell-balance="{ row }">{{ money(row.balance) }}</template>
                <template #cell-due_date="{ value }">{{ value?.substring(0, 10).split('-').reverse().join('/') }}</template>
                <template #cell-status="{ row }">
                    <StatusBadge :label="row.status.charAt(0).toUpperCase() + row.status.slice(1)" :tone="statusTone(row)" />
                </template>
                <template #cell-actions="{ row }">
                    <div class="flex items-center justify-end gap-1">
                        <IconButton :icon="Pencil" title="Editar" @click="openEdit(row)" />
                    </div>
                </template>
            </DataTable>

            <Pagination :meta="payments" />
        </div>

        <Modal :show="showModal" :title="editing ? 'Editar pago' : 'Nuevo pago'" max-width="max-w-2xl" @close="showModal = false">
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <SelectField v-model="form.student_id" label="Estudiante" required :options="studentOptions" :error="form.errors.student_id" />
                    <FormField v-model="form.type" label="Tipo" required :error="form.errors.type" />
                    <FormField v-model="form.amount" type="number" label="Monto" required :error="form.errors.amount" />
                    <FormField v-model="form.discount" type="number" label="Descuento" :error="form.errors.discount" />
                    <FormField v-model="form.paid" type="number" label="Pagado" :error="form.errors.paid" />
                    <FormField v-model="form.status" label="Estado" required :error="form.errors.status" />
                    <DateField v-model="form.due_date" label="Vence" required :error="form.errors.due_date" />
                    <DateField v-model="form.paid_date" label="Fecha de pago" :error="form.errors.paid_date" />
                    <FormField v-model="form.payment_method" label="Método de pago" :error="form.errors.payment_method" />
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
