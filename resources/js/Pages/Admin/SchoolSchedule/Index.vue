<script setup>
import { reactive, ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { Save, Clock, Plus, Pencil, X } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import FormField from '@/Components/FormField.vue';

const props = defineProps({
    schedules: { type: Array, default: () => [] },
});

const selectedScheduleId = ref(props.schedules[0]?.id ?? null);
const showModal = ref(false);

const emptyForm = () => ({
    name: 'Turno mañana',
    entry_window_start: '07:40',
    entry_start: '08:00',
    late_until: '08:10',
    exit_time: '14:00',
    active: true,
});

const form = reactive(emptyForm());

function syncFormFromSchedule(schedule = null) {
    const source = schedule ?? props.schedules.find((item) => item.id === selectedScheduleId.value) ?? props.schedules[0] ?? null;

    if (!source) {
        Object.assign(form, emptyForm());
        return;
    }

    Object.assign(form, {
        name: source.name ?? 'Turno mañana',
        entry_window_start: source.entry_window_start ?? '07:40',
        entry_start: source.entry_start ?? '08:00',
        late_until: source.late_until ?? '08:10',
        exit_time: source.exit_time ?? '14:00',
        active: source.active ?? true,
    });
}

watch(
    () => props.schedules,
    (schedules) => {
        if (!schedules.length) {
            selectedScheduleId.value = null;
            syncFormFromSchedule();
            return;
        }

        if (!selectedScheduleId.value || !schedules.some((item) => item.id === selectedScheduleId.value)) {
            selectedScheduleId.value = schedules[0].id;
        }

        syncFormFromSchedule(schedules.find((item) => item.id === selectedScheduleId.value));
    },
    { immediate: true }
);

const errors = ref({});
const processing = ref(false);

const availableViewOptions = computed(() => {
    const hasAfternoonSchedule = props.schedules.some((schedule) => {
        const name = (schedule.name ?? '').toLowerCase();
        if (name.includes('tarde')) return true;
        if (!schedule.entry_start) return false;
        return Number(schedule.entry_start.split(':')[0]) >= 12;
    });

    return hasAfternoonSchedule ? ['Todos', 'Tarde'] : ['Todos'];
});

const preview = computed(() => {
    const minutes = (a, b) => {
        const [ah, am] = a.split(':').map(Number);
        const [bh, bm] = b.split(':').map(Number);
        return Math.max(0, (bh * 60 + bm) - (ah * 60 + am));
    };
    return {
        present: minutes(form.entry_window_start, form.entry_start),
        late: minutes(form.entry_start, form.late_until),
        class: minutes(form.entry_start, form.exit_time),
    };
});

function openModal(schedule = null) {
    selectedScheduleId.value = schedule?.id ?? props.schedules[0]?.id ?? null;
    syncFormFromSchedule(schedule ?? props.schedules.find((item) => item.id === selectedScheduleId.value) ?? null);
    showModal.value = true;
}

function closeModal() {
    showModal.value = false;
}

function submit() {
    processing.value = true;
    errors.value = {};

    const target = props.schedules.find((item) => item.id === selectedScheduleId.value) ?? props.schedules[0];
    if (!target) {
        processing.value = false;
        return;
    }

    router.put(route('admin.academic.school-schedule.update', target.id), form, {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <Head title="Horario del colegio" />

    <AppLayout title="Horario del colegio">
        <div class="space-y-4">
            <PageHeader
                eyebrow="Académico"
                title="Horario del colegio"
                description="Configura los horarios oficiales de entrada y salida. La asistencia se calculará automáticamente a partir de estos valores."
            />

            <div v-if="schedules.length === 0" class="rounded-xl border border-warning-200 bg-warning-50 p-4 text-sm text-warning-800">
                No hay un horario configurado. Ejecuta <code>php artisan db:seed</code> o crea uno desde la base de datos.
            </div>

            <div v-else class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-4 rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-semibold text-slate-800">Turnos registrados</h3>
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-lg bg-brand-600 px-3 py-2 text-sm font-medium text-white hover:bg-brand-700"
                            @click="openModal()"
                        >
                            <Plus class="h-4 w-4" /> Agregar
                        </button>
                    </div>

                    <div v-if="availableViewOptions.length" class="flex flex-wrap gap-2">
                        <button
                            v-for="option in availableViewOptions"
                            :key="option"
                            type="button"
                            class="rounded-full px-3 py-1.5 text-xs font-medium transition"
                            :class="option === 'Todos'
                                ? 'bg-brand-600 text-white shadow-sm'
                                : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-100'"
                        >
                            {{ option }}
                        </button>
                    </div>

                    <div v-if="schedules.length" class="space-y-3">
                        <div
                            v-for="schedule in schedules"
                            :key="schedule.id"
                            class="flex items-center justify-between rounded-xl border border-border bg-white p-3 shadow-sm"
                        >
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ schedule.name }}</p>
                                <p class="text-xs text-slate-500">
                                    {{ schedule.entry_window_start }} - {{ schedule.entry_start }} · {{ schedule.late_until }} · {{ schedule.exit_time }}
                                </p>
                            </div>

                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg border border-border bg-slate-50 px-3 py-2 text-xs font-medium text-slate-700 hover:bg-slate-100"
                                @click="openModal(schedule)"
                            >
                                <Pencil class="h-3.5 w-3.5" /> Editar
                            </button>
                        </div>
                    </div>
                </div>

                <aside class="space-y-4 lg:sticky lg:top-6 lg:self-start">
                    <div class="rounded-xl border border-info-100 bg-info-50 p-4 text-sm text-info-800">
                        <p class="mb-2 flex items-center gap-2 font-semibold">
                            <Clock class="h-4 w-4" /> Reglas de asistencia
                        </p>
                        <ul class="space-y-1.5 text-xs">
                            <li>
                                <span class="font-semibold">{{ form.entry_window_start }} – {{ form.entry_start }}</span>
                                → <span class="rounded bg-success-100 px-1.5 py-0.5 text-success-800">Presente</span>
                                <span class="ml-1 text-slate-500">({{ preview.present }} min)</span>
                            </li>
                            <li>
                                <span class="font-semibold">{{ form.entry_start }} – {{ form.late_until }}</span>
                                → <span class="rounded bg-warning-100 px-1.5 py-0.5 text-warning-800">Tarde</span>
                                <span class="ml-1 text-slate-500">({{ preview.late }} min)</span>
                            </li>
                            <li>
                                <span class="font-semibold">Después de {{ form.late_until }}</span>
                                → <span class="rounded bg-danger-100 px-1.5 py-0.5 text-danger-800">Falta</span>
                            </li>
                            <li class="pt-2 text-slate-500">
                                Duración de clase: <strong>{{ preview.class }} min</strong> (de {{ form.entry_start }} a {{ form.exit_time }})
                            </li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>

        <Teleport to="body">
            <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class..="opacity-0"
            >
                <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm" @click.self="closeModal">
                    <div class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-2xl">
                        <div class="mb-4 flex items-center justify-between border-b border-slate-200 pb-3">
                            <h3 class="text-base font-semibold text-slate-800">{{ selectedScheduleId ? 'Editar horario' : 'Nuevo horario' }}</h3>
                            <button type="button" class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100" @click="closeModal">
                                <X class="h-4 w-4" />
                            </button>
                        </div>

                        <form class="space-y-4" @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <FormField v-model="form.name" label="Nombre del turno" required />
                                <label class="flex items-center gap-2 pt-6 text-sm text-slate-700">
                                    <input v-model="form.active" type="checkbox" class="rounded border-border text-brand-600 focus:ring-brand-300">
                                    Turno activo
                                </label>
                                <FormField v-model="form.entry_window_start" type="time" label="Apertura de registro (entrada)" required />
                                <FormField v-model="form.entry_start" type="time" label="Hora oficial de entrada" required />
                                <FormField v-model="form.late_until" type="time" label="Tolerancia de tardanza hasta" required />
                                <FormField v-model="form.exit_time" type="time" label="Hora oficial de salida" required />
                            </div>

                            <div class="flex justify-end gap-2 border-t border-slate-200 pt-4">
                                <button type="button" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-100" @click="closeModal">
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60"
                                >
                                    <Save class="h-4 w-4" /> Guardar horario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </transition>
        </Teleport>
    </AppLayout>
</template>
