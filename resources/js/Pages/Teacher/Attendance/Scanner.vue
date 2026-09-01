<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { AlertTriangle } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import QrBarcodeScanner from '@/Components/QrBarcodeScanner.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    course: Object,
});

const { push } = useToast();
const result = ref(null);

const statusLabel = { presente: 'PRESENTE', tardanza: 'TARDANZA', falta: 'FALTA' };
const statusTone = {
    presente: 'bg-success-50 border-success-100 text-success-800',
    tardanza: 'bg-warning-50 border-warning-100 text-warning-800',
    falta: 'bg-danger-50 border-danger-100 text-danger-800',
};

async function onScan({ code, method }) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    try {
        const res = await fetch(route('teacher.attendance.registrar', props.course.id), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            body: JSON.stringify({ code, method }),
        });
        const data = await res.json();

        if (data.ok) {
            result.value = data;
            push(`${data.student} — ${statusLabel[data.status]}`, 'success');
        } else {
            push('Código no encontrado', 'error');
        }
    } catch {
        push('Error al registrar asistencia', 'error');
    }
}

function markAbsences() {
    if (confirm('¿Marcar como falta a todos los alumnos que no registraron asistencia hoy y notificar a sus padres por WhatsApp?')) {
        router.post(route('teacher.attendance.marcar-faltas', props.course.id));
    }
}
</script>

<template>
    <Head :title="`Tomar asistencia - ${course.name}`" />

    <AppLayout :title="`Asistencia — ${course.name} (${course.grade_section?.name})`">
        <QrBarcodeScanner @scan="onScan" />

        <div v-if="result" class="mt-6 rounded-lg border p-4" :class="statusTone[result.status]">
            <strong>{{ result.student }}</strong> — {{ statusLabel[result.status] }} a las {{ result.time }}
        </div>

        <div class="mt-6">
            <button
                type="button"
                class="flex items-center gap-2 rounded-lg bg-danger-600 px-4 py-2 text-sm font-medium text-white hover:bg-danger-700"
                @click="markAbsences"
            >
                <AlertTriangle class="h-4 w-4" /> Marcar faltas del día y notificar por WhatsApp
            </button>
        </div>
    </AppLayout>
</template>
