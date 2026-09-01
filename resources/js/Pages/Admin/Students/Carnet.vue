<script setup>
import { computed, ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ArrowLeft, Barcode, GraduationCap, Hash, IdCard, Printer, QrCode, ScanFace } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    student: Object,
    showPdf: Boolean,
    attendanceMethod: String,
});

const page = usePage();
const showModal = ref(false);

onMounted(() => {
    if (props.showPdf) showModal.value = true;
});

const fullName = computed(() => `${props.student.first_name} ${props.student.last_name}`);

const initials = computed(() =>
    [props.student.first_name, props.student.last_name]
        .filter(Boolean)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('') || 'E'
);

const identifiers = computed(() => {
    const rows = [
        { icon: Hash, label: 'DNI', value: props.student.dni },
        { icon: IdCard, label: 'Código', value: props.student.code },
    ];
    if (props.attendanceMethod === 'qr' || props.attendanceMethod === 'both') {
        rows.push({ icon: QrCode, label: 'QR', value: props.student.qr_token });
    }
    if (props.attendanceMethod === 'barcode' || props.attendanceMethod === 'both') {
        rows.push({ icon: Barcode, label: 'Barras', value: props.student.barcode });
    }
    if (props.attendanceMethod === 'biometric') {
        rows.push({ icon: ScanFace, label: 'Biométrico', value: props.student.biometric_id });
    }
    return rows;
});
</script>

<template>
    <Head title="Carnet" />

    <AppLayout :title="`Carnet de ${fullName}`">
        <div class="space-y-6">
            <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">
                        Carnet estudiantil
                    </p>
                    <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ fullName }}</h1>
                </div>
                <a
                    :href="route('admin.students.index')"
                    class="inline-flex w-fit items-center gap-1.5 rounded-full border border-border bg-surface px-3 py-1.5 text-xs font-medium text-slate-600 shadow-sm transition hover:bg-surface-muted dark:text-slate-300"
                >
                    <ArrowLeft class="h-3.5 w-3.5" /> Volver a estudiantes
                </a>
            </div>

            <div class="mx-auto w-full max-w-sm overflow-hidden rounded-2xl border border-border bg-surface shadow-md">
                <div class="relative bg-gradient-to-br from-brand-700 to-brand-900 px-6 pb-12 pt-5 text-white">
                    <div class="flex items-center justify-center gap-2">
                        <GraduationCap class="h-5 w-5 text-brand-200" />
                        <p class="truncate text-sm font-semibold">{{ page.props.appName }}</p>
                    </div>
                    <p class="mt-0.5 text-center text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-200">
                        Carnet estudiantil
                    </p>
                </div>

                <div class="-mt-10 flex justify-center">
                    <img
                        v-if="student.photo"
                        :src="`/storage/${student.photo}`"
                        class="h-20 w-20 rounded-full object-cover ring-4 ring-surface"
                    >
                    <div
                        v-else
                        class="flex h-20 w-20 items-center justify-center rounded-full bg-brand-100 text-xl font-semibold text-brand-700 ring-4 ring-surface dark:bg-brand-900 dark:text-brand-200"
                    >
                        {{ initials }}
                    </div>
                </div>

                <div class="px-6 pb-6 pt-3 text-center">
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">{{ fullName }}</h2>
                    <p class="mt-1 inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 dark:bg-brand-900/50 dark:text-brand-200">
                        {{ student.grade_section?.name ?? 'Sin grado asignado' }}
                    </p>

                    <dl class="mt-5 divide-y divide-border border-y border-border text-left">
                        <div v-for="row in identifiers" :key="row.label" class="flex items-center justify-between gap-3 py-2.5">
                            <dt class="flex items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <component :is="row.icon" class="h-3.5 w-3.5" /> {{ row.label }}
                            </dt>
                            <dd class="truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ row.value }}</dd>
                        </div>
                    </dl>

                    <p class="mt-4 text-[10px] uppercase tracking-[0.15em] text-slate-400 dark:text-slate-500">
                        Documento de identificación oficial
                    </p>
                </div>
            </div>

            <div class="text-center">
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
                    @click="showModal = true"
                >
                    <Printer class="h-4 w-4" /> Imprimir carnet
                </button>
            </div>
        </div>

        <Modal :show="showModal" :title="`Carnet - ${fullName}`" max-width="max-w-4xl" @close="showModal = false">
            <iframe v-if="showModal" :src="route('admin.students.carnet.pdf', student.id)" class="h-[600px] w-full border-0" />
            <div class="mt-4 flex justify-end gap-2 border-t border-border pt-4">
                <button type="button" class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="window.print()">
                    <Printer class="h-4 w-4" /> Imprimir
                </button>
                <button type="button" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted dark:text-slate-300" @click="showModal = false">
                    Cerrar
                </button>
            </div>
        </Modal>
    </AppLayout>
</template>
