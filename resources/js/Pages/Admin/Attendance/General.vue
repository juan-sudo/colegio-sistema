<script setup>
import { reactive, ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ScanLine, Save, CheckCircle2, XCircle, Camera, CameraOff } from 'lucide-vue-next';
import AppLayout from '@/Layouts/AppLayout.vue';
import SelectField from '@/Components/SelectField.vue';
import StatCard from '@/Components/StatCard.vue';
import { useToast } from '@/composables/useToast';

const props = defineProps({
    students: Array,
    attendances: Object,
    stats: Object,
    date: String,
    schedule: {
        type: Object,
        default: () => ({
            name: 'Turno',
            entry_window_start: '07:40',
            entry_start: '08:00',
            late_until: '08:10',
            exit_time: '14:00',
        }),
    },
});

const rulesText = computed(() => {
    const s = props.schedule;
    return `Antes de ${s.entry_start} → Presente | ${s.entry_start} - ${s.late_until} → Tarde | Después de ${s.late_until} → Falta`;
});

const { push } = useToast();

const stats = reactive({ ...props.stats });

const rows = reactive(
    Object.fromEntries(
        props.students.map((s) => {
            const a = props.attendances[s.id];
            return [
                s.id,
                {
                    name: `${s.first_name} ${s.last_name}`,
                    section: s.grade_section?.name ?? '-',
                    course: s.courses?.[0]?.name ?? '-',
                    course_id: s.courses?.[0]?.id ?? null,
                    status: a?.status ?? 'falta',
                    time_in: a?.time_in?.substring(0, 5) ?? props.schedule.entry_start ?? '08:00',
                    observation: a?.observation ?? '',
                    justified: Boolean(a?.justified),
                },
            ];
        })
    )
);

const scanMethod = ref('qr');
const scanCode = ref('');
const scanResult = ref(null);
const cameraOn = ref(false);
const cameraError = ref('');
const justificationModalOpen = ref(false);
const selectedJustificationId = ref(null);
let html5Qrcode = null;
let cameraScanner = null;

const statusLabels = { presente: 'Presente', tardanza: 'Tarde', falta: 'Falta' };
const registrationLocked = true;

const selectedJustificationRow = computed(() => {
    if (!selectedJustificationId.value) return null;
    return rows[selectedJustificationId.value] ?? null;
});

function recalcStats() {
    const values = Object.values(rows);
    stats.present = values.filter((r) => r.status === 'presente').length;
    stats.late = values.filter((r) => r.status === 'tardanza').length;
    stats.absent = values.filter((r) => r.status === 'falta').length;
}

async function startCamera() {
    cameraError.value = '';
    if (scanMethod.value !== 'qr') {
        cameraError.value = 'La cámara solo aplica para QR.';
        return;
    }
    try {
        const { Html5Qrcode } = await import('html5-qrcode');
        if (!html5Qrcode) {
            html5Qrcode = new Html5Qrcode('qr-camera-reader');
        }
        cameraOn.value = true;
        await new Promise((resolve) => setTimeout(resolve, 50));
        await html5Qrcode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 240, height: 240 } },
            (decodedText) => {
                scanCode.value = decodedText;
                stopCamera();
                submitScan();
            },
            () => {}
        );
    } catch (err) {
        cameraOn.value = false;
        cameraError.value = err?.message || 'No se pudo acceder a la cámara.';
    }
}

async function stopCamera() {
    try {
        if (html5Qrcode && html5Qrcode.isScanning) {
            await html5Qrcode.stop();
            html5Qrcode.clear();
        }
    } catch {}
    cameraOn.value = false;
}

onBeforeUnmount(() => {
    stopCamera();
});

async function submitScan() {
    if (!scanCode.value.trim()) {
        push('Ingresa un código o escanea', 'error');
        return;
    }

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const formData = new FormData();
    formData.append('_token', csrf);
    formData.append('code', scanCode.value.trim());
    formData.append('method', scanMethod.value);

    try {
        const response = await fetch(route('admin.attendance.registrar-general'), {
            method: 'POST',
            body: formData,
            headers: { Accept: 'application/json' },
        });
        const data = await response.json();

        if (data.ok) {
            scanResult.value = { ok: true, ...data };
            const entry = Object.entries(rows).find(([, r]) => r.name === data.student);
            if (entry) {
                const [, r] = entry;
                r.status = data.status;
                r.time_in = data.time.substring(0, 5);
                recalcStats();
            }
            push(`${data.student} registrado como ${statusLabels[data.status]}`, 'success');
            scanCode.value = '';
        } else {
            scanResult.value = { ok: false, message: data.message };
            push(data.message || 'Error al registrar', 'error');
        }
    } catch {
        push('Error al registrar asistencia', 'error');
    }
}

function openJustification(studentId) {
    selectedJustificationId.value = studentId;
    justificationModalOpen.value = true;
}

function closeJustificationModal() {
    justificationModalOpen.value = false;
    selectedJustificationId.value = null;
}

function save() {
    const form = useForm({
        date: props.date,
        attendances: Object.fromEntries(
            Object.entries(rows).map(([studentId, r]) => [
                studentId,
                {
                    status: r.status,
                    time_in: r.time_in || props.schedule.entry_start || '08:00',
                    observation: r.observation,
                    justified: r.justified ?? false,
                    course_id: r.course_id,
                },
            ])
        ),
    });
    form.post(route('admin.attendance.store-manual'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Asistencia al colegio" />

    <AppLayout :title="`Registrar asistencia general - ${date}`">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_320px]">
            <div class="space-y-6">
                <div class="rounded-xl border border-border bg-surface p-6 shadow-sm">
                    <div class="mb-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <StatCard label="Presentes" :value="stats.present" tone="success" />
                        <StatCard label="Tardanzas" :value="stats.late" tone="warning" />
                        <StatCard label="Faltas" :value="stats.absent" tone="danger" />
                        <StatCard label="Total" :value="stats.total" tone="brand" />
                    </div>

                    <div class="mb-4 rounded-lg border border-info-100 bg-info-50 p-3 text-sm text-info-800">
                        <strong>Reglas de asistencia ({{ schedule.name }}):</strong> {{ rulesText }}
                    </div>

                    <form class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-4" @submit.prevent="submitScan">
                        <SelectField
                            v-model="scanMethod"
                            label="Método"
                            :options="[
                                { value: 'qr', label: 'QR' },
                                { value: 'barcode', label: 'Código de barras' },
                                { value: 'biometric', label: 'Biométrico' },
                                { value: 'manual', label: 'Manual' },
                            ]"
                        />
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Código / Huella</label>
                            <input
                                v-model="scanCode"
                                type="text"
                                autofocus
                                autocomplete="off"
                                placeholder="Escanea o ingresa el código..."
                                class="w-full rounded-lg border border-border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-200"
                                @keyup.enter="submitScan"
                            >
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                                <ScanLine class="h-4 w-4" /> Registrar
                            </button>
                        </div>
                    </form>

                    <div
                        v-if="scanResult"
                        class="mb-4 flex items-start gap-3 rounded-lg p-4 text-sm"
                        :class="scanResult.ok ? 'bg-success-50 text-success-800' : 'bg-danger-50 text-danger-800'"
                    >
                        <component :is="scanResult.ok ? CheckCircle2 : XCircle" class="mt-0.5 h-5 w-5 shrink-0" />
                        <div v-if="scanResult.ok">
                            <p class="font-semibold">{{ scanResult.student }}</p>
                            <p>Grado: {{ scanResult.grade_section }}</p>
                            <p>Estado: {{ statusLabels[scanResult.status] }}</p>
                            <p>Hora: {{ scanResult.time }}</p>
                        </div>
                        <p v-else>{{ scanResult.message || 'No se pudo registrar' }}</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-surface-muted">
                                    <th class="p-2 text-left font-medium text-slate-500">Alumno</th>
                                    <th class="p-2 text-left font-medium text-slate-500">Grado/Sección</th>
                                    <th class="p-2 text-left font-medium text-slate-500">Curso</th>
                                    <th class="p-2 text-center font-medium text-slate-500">Presente</th>
                                    <th class="p-2 text-center font-medium text-slate-500">Tarde</th>
                                    <th class="p-2 text-center font-medium text-slate-500">Falta</th>
                                    <th class="p-2 text-left font-medium text-slate-500">Hora de registro</th>
                                    <th class="p-2 text-center font-medium text-slate-500">Justificado</th>
                                    <th class="p-2 text-left font-medium text-slate-500">Justificación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, studentId) in rows" :key="studentId" class="border-t border-border">
                                    <td class="p-2">{{ row.name }}</td>
                                    <td class="p-2">{{ row.section }}</td>
                                    <td class="p-2">{{ row.course }}</td>
                                    <td class="p-2 text-center">
                                        <input
                                            v-model="row.status"
                                            type="radio"
                                            value="presente"
                                            class="h-4 w-4 accent-green-600"
                                            :disabled="registrationLocked"
                                            @change="recalcStats"
                                        >
                                    </td>
                                    <td class="p-2 text-center">
                                        <input
                                            v-model="row.status"
                                            type="radio"
                                            value="tardanza"
                                            class="h-4 w-4 accent-orange-500"
                                            :disabled="registrationLocked"
                                            @change="recalcStats"
                                        >
                                    </td>
                                    <td class="p-2 text-center">
                                        <input
                                            v-model="row.status"
                                            type="radio"
                                            value="falta"
                                            class="h-4 w-4 accent-red-600"
                                            :disabled="registrationLocked"
                                            @change="recalcStats"
                                        >
                                    </td>
                                    <td class="p-2"><input v-model="row.time_in" type="time" class="w-28 rounded border border-border p-1" :disabled="registrationLocked"></td>
                                    <td class="p-2 text-center">
                                        <input v-model="row.justified" type="checkbox" class="h-4 w-4 accent-emerald-600" :disabled="registrationLocked">
                                    </td>
                                    <td class="p-2">
                                        <button
                                            type="button"
                                            class="rounded border border-slate-200 bg-surface-muted px-2 py-1 text-left text-xs text-slate-700 hover:bg-slate-100"
                                            @click="openJustification(studentId)"
                                        >
                                            {{ row.observation ? row.observation.slice(0, 28) + (row.observation.length > 28 ? '…' : '') : 'Agregar justificación' }}
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 flex gap-2">
                        <button type="button" class="flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700" @click="save">
                            <Save class="h-4 w-4" /> Guardar asistencia general
                        </button>
                        <a :href="route('admin.attendance.index')" class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-surface-muted">
                            ← Volver
                        </a>
                    </div>
                </div>
            </div>

            <div
                v-if="justificationModalOpen && selectedJustificationRow"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4"
                @click.self="closeJustificationModal"
            >
                <div class="w-full max-w-md rounded-xl bg-white p-4 shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Justificación</h3>
                        <button type="button" class="text-slate-500 hover:text-slate-700" @click="closeJustificationModal">✕</button>
                    </div>

                    <p class="mb-3 text-xs text-slate-500">
                        {{ selectedJustificationRow.name }} · {{ selectedJustificationRow.section }}
                    </p>

                    <div class="mb-3 flex items-center gap-2 rounded-md border border-emerald-200 bg-emerald-50 px-2.5 py-1.5 dark:border-emerald-700 dark:bg-emerald-950/30">
                        <input v-model="selectedJustificationRow.justified" type="checkbox" class="h-3.5 w-3.5 rounded accent-emerald-600" />
                        <label class="text-xs font-medium text-emerald-700 dark:text-emerald-200">Justificado</label>
                    </div>

                    <label class="mb-1 block text-xs font-medium text-slate-700 dark:text-slate-200">Motivo / observación</label>
                    <textarea
                        v-model="selectedJustificationRow.observation"
                        rows="3"
                        placeholder="Ingrese la justificación..."
                        class="w-full rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-xs text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-100"
                    ></textarea>

                    <div class="mt-3 flex justify-end gap-2">
                        <button type="button" class="rounded-md border border-slate-200 px-2.5 py-1.5 text-xs text-slate-600 hover:bg-slate-50" @click="closeJustificationModal">
                            Cerrar
                        </button>
                        <button type="button" class="rounded-md bg-brand-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-brand-700" @click="closeJustificationModal">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>

            <aside class="space-y-4 lg:sticky lg:top-6 lg:self-start">
                <div class="rounded-xl border border-border bg-surface p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-slate-700">Lector QR</h3>
                        <span v-if="cameraOn" class="rounded-full bg-success-100 px-2 py-0.5 text-xs text-success-800">Activo</span>
                        <span v-else class="rounded-full bg-surface-muted px-2 py-0.5 text-xs text-slate-500">Inactivo</span>
                    </div>

                    <div id="qr-camera-reader" class="mb-3 aspect-square w-full overflow-hidden rounded-lg border border-border bg-black"></div>

                    <div class="flex gap-2">
                        <button
                            v-if="!cameraOn"
                            type="button"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-info-600 px-3 py-2 text-sm font-medium text-white hover:bg-info-700 disabled:cursor-not-allowed disabled:opacity-50"
                            :disabled="scanMethod !== 'qr'"
                            @click="startCamera"
                        >
                            <Camera class="h-4 w-4" /> Iniciar cámara
                        </button>
                        <button
                            v-else
                            type="button"
                            class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-danger-600 px-3 py-2 text-sm font-medium text-white hover:bg-danger-700"
                            @click="stopCamera"
                        >
                            <CameraOff class="h-4 w-4" /> Detener
                        </button>
                    </div>

                    <p v-if="cameraError" class="mt-2 text-xs text-danger-700">{{ cameraError }}</p>
                    <p v-else-if="scanMethod !== 'qr'" class="mt-2 text-xs text-slate-500">Cambia el método a QR para usar la cámara.</p>
                    <p v-else class="mt-2 text-xs text-slate-500">Apunta la cámara al QR del carnet del estudiante.</p>
                </div>

                <div class="rounded-xl border border-info-100 bg-info-50 p-3 text-xs text-info-800">
                    <p class="font-semibold">Tip</p>
                    <p>El lector queda fijo a un lado. Puedes seguir marcando asistencia manual mientras la cámara está activa.</p>
                </div>
            </aside>
        </div>
    </AppLayout>
</template>
