<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ClipboardList, ClipboardCheck, Users, Wallet, Download } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import DateField from '@/Components/DateField.vue';

const props = defineProps({
    courses: Array,
    gradeSections: Array,
});

const courseOptions = props.courses.map((c) => ({ value: c.id, label: `${c.name} - ${c.grade_section?.name ?? ''}` }));
const gradeSectionOptions = props.gradeSections.map((gs) => ({ value: gs.id, label: `${gs.name} - ${gs.level}` }));

const today = new Date().toISOString().substring(0, 10);
const monthStart = `${today.substring(0, 7)}-01`;

const attendance = ref({ course_id: '', date_from: monthStart, date_to: today });
const grades = ref({ course_id: '' });
const students = ref({ grade_section_id: '' });
const payments = ref({ type: '', status: '' });

function view(routeName, params) {
    router.get(route(routeName), params);
}
</script>

<template>
    <Head title="Reportes" />

    <AppLayout title="Reportes del sistema">
        <div class="space-y-6">
            <p class="text-sm text-slate-500">Selecciona un tipo de reporte para visualizarlo o exportarlo.</p>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <ClipboardList class="h-8 w-8 text-brand-600" />
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Asistencia</h2>
                            <p class="text-sm text-slate-500">Reporte de asistencia por curso y fecha</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <SelectField v-model="attendance.course_id" label="Curso" required placeholder="Seleccionar curso..." :options="courseOptions" />
                        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                            <DateField v-model="attendance.date_from" label="Desde" required />
                            <DateField v-model="attendance.date_to" label="Hasta" required />
                        </div>
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="flex-1 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                                @click="view('admin.reports.attendance', attendance)"
                            >
                                Ver reporte
                            </button>
                            <a :href="route('admin.reports.attendance.export', attendance)" class="flex items-center gap-1 rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700">
                                <Download class="h-4 w-4" /> Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <ClipboardCheck class="h-8 w-8 text-brand-600" />
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Notas</h2>
                            <p class="text-sm text-slate-500">Reporte de notas por curso</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <SelectField v-model="grades.course_id" label="Curso" required placeholder="Seleccionar curso..." :options="courseOptions" />
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="flex-1 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                                @click="view('admin.reports.grades', grades)"
                            >
                                Ver reporte
                            </button>
                            <a :href="route('admin.reports.grades.export', grades)" class="flex items-center gap-1 rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700">
                                <Download class="h-4 w-4" /> Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <Users class="h-8 w-8 text-brand-600" />
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Estudiantes</h2>
                            <p class="text-sm text-slate-500">Listado de estudiantes por grado/sección</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <SelectField
                            v-model="students.grade_section_id"
                            label="Grado/Sección"
                            required
                            placeholder="Seleccionar grado/sección..."
                            :options="gradeSectionOptions"
                        />
                        <button
                            type="button"
                            class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                            @click="view('admin.reports.students', students)"
                        >
                            Ver reporte
                        </button>
                    </div>
                </div>

                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <Wallet class="h-8 w-8 text-brand-600" />
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Pagos</h2>
                            <p class="text-sm text-slate-500">Reporte de pagos y estados de cuenta</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <SelectField
                            v-model="payments.type"
                            label="Tipo"
                            placeholder="Todos"
                            :options="[{ value: 'matricula', label: 'Matrícula' }, { value: 'pension', label: 'Pensión' }]"
                        />
                        <SelectField
                            v-model="payments.status"
                            label="Estado"
                            placeholder="Todos"
                            :options="[
                                { value: 'pagado', label: 'Pagado' },
                                { value: 'pendiente', label: 'Pendiente' },
                                { value: 'vencido', label: 'Vencido' },
                            ]"
                        />
                        <div class="flex gap-2">
                            <button
                                type="button"
                                class="flex-1 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"
                                @click="view('admin.reports.payments', payments)"
                            >
                                Ver reporte
                            </button>
                            <a :href="route('admin.reports.payments.export', payments)" class="flex items-center gap-1 rounded-lg bg-success-600 px-4 py-2 text-sm font-medium text-white hover:bg-success-700">
                                <Download class="h-4 w-4" /> Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
