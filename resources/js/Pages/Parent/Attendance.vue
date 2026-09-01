<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

defineProps({
    student: Object,
    attendances: Object,
});

const statusLabel = { presente: 'Presente', tardanza: 'Tardanza', falta: 'Falta', justificado: 'Justificado' };
const statusTone = { presente: 'success', tardanza: 'warning', falta: 'danger', justificado: 'info' };
</script>

<template>
    <Head :title="`Asistencia de ${student.first_name} ${student.last_name}`" />

    <AppLayout :title="`Asistencia — ${student.first_name} ${student.last_name}`">
        <div class="rounded-xl border border-border bg-surface shadow-sm">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border bg-surface-muted text-left">
                        <th class="p-2 font-medium text-slate-500">Fecha</th>
                        <th class="p-2 font-medium text-slate-500">Estado</th>
                        <th class="p-2 font-medium text-slate-500">Hora</th>
                        <th class="p-2 font-medium text-slate-500">Método</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="a in attendances.data" :key="a.id" class="border-b border-border">
                        <td class="p-2">{{ a.date.substring(0, 10).split('-').reverse().join('/') }}</td>
                        <td class="p-2"><StatusBadge :label="statusLabel[a.status]" :tone="statusTone[a.status]" /></td>
                        <td class="p-2">{{ a.time_in ?? '-' }}</td>
                        <td class="p-2">{{ a.method }}</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :meta="attendances" />
        </div>
    </AppLayout>
</template>
