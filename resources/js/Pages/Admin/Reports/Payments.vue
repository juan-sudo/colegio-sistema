<script setup>
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    payments: Array,
    stats: Object,
    type: [String, null],
    status: [String, null],
});

function money(v) {
    return `S/ ${Number(v).toFixed(2)}`;
}

const statusTone = { pagado: 'success', pendiente: 'warning', vencido: 'danger' };
</script>

<template>
    <Head title="Reporte de pagos" />

    <AppLayout title="Reporte de pagos">
        <div class="space-y-6">
            <div class="flex justify-end">
                <a :href="route('admin.reports.index')" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted">← Volver a reportes</a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard label="Total cobrado" :value="money(stats.total_amount)" tone="info" />
                <StatCard label="Total pagado" :value="money(stats.total_paid)" tone="success" />
                <StatCard label="Saldo pendiente" :value="money(stats.total_balance)" tone="warning" />
                <StatCard label="Total registros" :value="stats.total_count" tone="brand" />
            </div>

            <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-surface-muted">
                            <th class="p-2 text-left font-medium text-slate-500">Estudiante</th>
                            <th class="p-2 text-left font-medium text-slate-500">Tipo</th>
                            <th class="p-2 text-right font-medium text-slate-500">Monto</th>
                            <th class="p-2 text-right font-medium text-slate-500">Descuento</th>
                            <th class="p-2 text-right font-medium text-slate-500">Pagado</th>
                            <th class="p-2 text-right font-medium text-slate-500">Saldo</th>
                            <th class="p-2 text-center font-medium text-slate-500">Estado</th>
                            <th class="p-2 text-left font-medium text-slate-500">Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="p in payments" :key="p.id" class="border-t border-border">
                            <td class="p-2">{{ p.student ? `${p.student.first_name} ${p.student.last_name}` : '-' }}</td>
                            <td class="p-2">{{ p.type.charAt(0).toUpperCase() + p.type.slice(1) }}</td>
                            <td class="p-2 text-right">{{ money(p.amount) }}</td>
                            <td class="p-2 text-right">{{ money(p.discount) }}</td>
                            <td class="p-2 text-right">{{ money(p.paid) }}</td>
                            <td class="p-2 text-right">{{ money(p.balance) }}</td>
                            <td class="p-2 text-center">
                                <StatusBadge :label="p.status.charAt(0).toUpperCase() + p.status.slice(1)" :tone="statusTone[p.status] ?? 'neutral'" />
                            </td>
                            <td class="p-2">{{ p.due_date?.substring(0, 10).split('-').reverse().join('/') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>
