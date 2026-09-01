<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import {
    GraduationCap,
    UserSquare2,
    Users,
    BookOpen,
    ClipboardCheck,
    Wallet,
    AlertTriangle,
    TrendingUp,
} from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import AreaTrendChart from '@/Components/Charts/AreaTrendChart.vue';
import StackedBarChart from '@/Components/Charts/StackedBarChart.vue';
import RankedBarList from '@/Components/Charts/RankedBarList.vue';

const props = defineProps({
    stats: { type: Object, required: true },
    attendanceTrend: { type: Array, default: () => [] },
    paymentsTrend: { type: Array, default: () => [] },
    sectionsBreakdown: { type: Array, default: () => [] },
    paymentsStatus: { type: Array, default: () => [] },
});

const attendanceSeries = [
    { key: 'presente', name: 'Presente', color: 'var(--color-success-600)' },
    { key: 'tardanza', name: 'Tardanza', color: 'var(--color-warning-600)' },
    { key: 'falta', name: 'Falta', color: 'var(--color-danger-600)' },
];

const paymentsSeries = [
    { key: 'cobrado', name: 'Cobrado', color: 'var(--color-brand-600)' },
    { key: 'pendiente', name: 'Por cobrar', color: 'var(--color-brand-200)' },
];

function money(value) {
    return `S/ ${Number(value).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
}

const attendanceTotalToday = computed(() => {
    const today = props.attendanceTrend[props.attendanceTrend.length - 1];
    return today ? today.presente + today.tardanza + today.falta : 0;
});

const attendanceRateToday = computed(() => {
    const today = props.attendanceTrend[props.attendanceTrend.length - 1];
    if (!today || attendanceTotalToday.value === 0) return null;
    return Math.round((today.presente / attendanceTotalToday.value) * 100);
});

const statusToneMap = {
    pagado: { dot: 'bg-success-600', bar: 'var(--color-success-600)' },
    pendiente: { dot: 'bg-warning-600', bar: 'var(--color-warning-600)' },
    vencido: { dot: 'bg-danger-600', bar: 'var(--color-danger-600)' },
};

const paymentsStatusMax = computed(() => Math.max(1, ...props.paymentsStatus.map((s) => s.count)));
</script>

<template>
    <Head title="Resumen general" />

    <AppLayout>
        <div class="space-y-6">
            <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">
                        Resumen general
                    </p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">Panel general del sistema</h1>
                </div>

                <div class="inline-flex items-center rounded-full border border-border bg-surface px-3 py-1.5 text-xs font-medium text-slate-600 shadow-sm dark:text-slate-300">
                    {{ new Date().toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' }) }}
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <StatCard label="Estudiantes activos" :value="stats.students" :icon="GraduationCap" tone="brand" />
                <StatCard label="Profesores" :value="stats.teachers" :icon="UserSquare2" tone="info" />
                <StatCard label="Apoderados" :value="stats.guardians" :icon="Users" tone="info" />
                <StatCard label="Cursos" :value="stats.courses" :icon="BookOpen" tone="brand" />
                <StatCard label="Matrículas activas" :value="stats.enrollments" :icon="ClipboardCheck" tone="success" />
                <StatCard label="Ingresos del mes" :value="money(stats.income_month)" :icon="TrendingUp" tone="success" />
                <StatCard label="Pagos pendientes" :value="stats.payments_pending" :icon="Wallet" tone="warning" />
                <StatCard label="Pagos vencidos" :value="stats.payments_overdue" :icon="AlertTriangle" tone="danger" />
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm lg:col-span-2">
                    <div class="mb-4 flex items-start justify-between">
                        <div>
                            <h2 class="text-base font-semibold text-slate-900 dark:text-white">Asistencia diaria</h2>
                            <p class="text-sm text-slate-500 dark:text-slate-400">Últimos 14 días</p>
                        </div>
                        <div v-if="attendanceRateToday !== null" class="text-right">
                            <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ attendanceRateToday }}%</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Asistencia hoy</p>
                        </div>
                    </div>
                    <AreaTrendChart :points="attendanceTrend" :series="attendanceSeries" :height="260" />
                </div>

                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <h2 class="mb-1 text-base font-semibold text-slate-900 dark:text-white">Estudiantes por sección</h2>
                    <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Secciones con más matrículas</p>
                    <RankedBarList :items="sectionsBreakdown" color="var(--color-brand-600)" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm lg:col-span-2">
                    <div class="mb-4">
                        <h2 class="text-base font-semibold text-slate-900 dark:text-white">Cobros mensuales</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400">Últimos 6 meses</p>
                    </div>
                    <StackedBarChart :points="paymentsTrend" :series="paymentsSeries" :height="260" value-prefix="S/ " />
                </div>

                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <h2 class="mb-1 text-base font-semibold text-slate-900 dark:text-white">Estado de pagos</h2>
                    <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Distribución general</p>

                    <div class="space-y-4">
                        <div v-for="item in paymentsStatus" :key="item.status">
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                    <span class="h-2.5 w-2.5 rounded-full" :class="statusToneMap[item.status]?.dot" />
                                    {{ item.label }}
                                </span>
                                <span class="font-semibold tabular-nums text-slate-800 dark:text-slate-100">{{ item.count }}</span>
                            </div>
                            <div class="h-2 w-full overflow-hidden rounded-full bg-surface-muted">
                                <div
                                    class="h-full rounded-full transition-all"
                                    :style="{
                                        width: `${(item.count / paymentsStatusMax) * 100}%`,
                                        backgroundColor: statusToneMap[item.status]?.bar,
                                    }"
                                />
                            </div>
                            <p class="mt-1 text-xs text-slate-400">{{ money(item.amount) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
