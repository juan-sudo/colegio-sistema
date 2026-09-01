<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import StatCard from '@/Components/StatCard.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    gradeSections: Array,
    selectedGradeSectionId: [String, Number, null],
    students: Array,
    stats: Object,
});

const gradeSectionOptions = props.gradeSections.map((gs) => ({ value: gs.id, label: `${gs.name} - ${gs.level}` }));
const gradeSectionId = ref(props.selectedGradeSectionId ?? '');

function filter() {
    router.get(route('admin.reports.students'), { grade_section_id: gradeSectionId.value });
}
</script>

<template>
    <Head title="Reporte de estudiantes" />

    <AppLayout title="Reporte de estudiantes">
        <div class="space-y-6">
            <div class="flex justify-end">
                <a :href="route('admin.reports.index')" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted">← Volver a reportes</a>
            </div>

            <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                <div class="flex items-end gap-4">
                    <SelectField
                        class="flex-1"
                        v-model="gradeSectionId"
                        label="Grado/Sección"
                        required
                        placeholder="Seleccionar grado/sección..."
                        :options="gradeSectionOptions"
                    />
                    <button type="button" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="filter">
                        Filtrar
                    </button>
                </div>
            </div>

            <template v-if="selectedGradeSectionId && students.length > 0">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <StatCard label="Total estudiantes" :value="stats.total" tone="info" />
                    <StatCard label="Activos" :value="stats.active" tone="success" />
                    <StatCard label="Inactivos" :value="stats.inactive" tone="danger" />
                </div>

                <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-surface-muted">
                                <th class="p-2 text-left font-medium text-slate-500">Código</th>
                                <th class="p-2 text-left font-medium text-slate-500">DNI</th>
                                <th class="p-2 text-left font-medium text-slate-500">Nombre</th>
                                <th class="p-2 text-left font-medium text-slate-500">Email</th>
                                <th class="p-2 text-left font-medium text-slate-500">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="s in students" :key="s.id" class="border-t border-border">
                                <td class="p-2">{{ s.code }}</td>
                                <td class="p-2">{{ s.dni }}</td>
                                <td class="p-2">{{ s.first_name }} {{ s.last_name }}</td>
                                <td class="p-2">{{ s.user?.email ?? '-' }}</td>
                                <td class="p-2"><StatusBadge :label="s.active ? 'Activo' : 'Inactivo'" :tone="s.active ? 'success' : 'danger'" /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </template>

            <div v-else-if="selectedGradeSectionId" class="rounded-xl border border-border bg-surface p-6 text-slate-500">
                No hay estudiantes registrados en este grado/sección.
            </div>
        </div>
    </AppLayout>
</template>
