<script setup>
import { onMounted, onBeforeUnmount, ref } from 'vue';
import { Html5Qrcode } from 'html5-qrcode';
import { ScanLine } from 'lucide-vue-next';

const emit = defineEmits(['scan']);

const readerId = `qr-reader-${Math.random().toString(36).slice(2)}`;
const barcodeInput = ref('');
let scanner = null;

onMounted(async () => {
    scanner = new Html5Qrcode(readerId);
    try {
        const cameras = await Html5Qrcode.getCameras();
        if (cameras && cameras.length) {
            await scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 250 }, (decodedText) => {
                emit('scan', { code: decodedText, method: 'qr' });
            });
        }
    } catch {
        // No camera available — manual/barcode input below still works.
    }
});

onBeforeUnmount(() => {
    if (scanner?.isScanning) {
        scanner.stop().catch(() => {});
    }
});

function submitBarcode() {
    const code = barcodeInput.value.trim();
    if (code) {
        emit('scan', { code, method: 'barcode' });
        barcodeInput.value = '';
    }
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl border border-border bg-surface p-4 shadow-sm">
            <h2 class="mb-2 flex items-center gap-2 font-semibold text-slate-900">
                <ScanLine class="h-4 w-4 text-brand-600" /> Escanear QR con cámara
            </h2>
            <div :id="readerId" class="w-full" />
        </div>

        <div class="rounded-xl border border-border bg-surface p-4 shadow-sm">
            <h2 class="mb-2 font-semibold text-slate-900">Código de barras / código manual</h2>
            <input
                v-model="barcodeInput"
                type="text"
                autofocus
                placeholder="Escanea o escribe el código y presiona Enter"
                class="w-full rounded-lg border border-border px-3 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-brand-200"
                @keyup.enter="submitBarcode"
            >
            <p class="mt-2 text-sm text-slate-500">
                El lector de huella biométrica también puede enviar el ID capturado a este mismo cuadro (o directamente vía la API
                <code class="rounded bg-surface-muted px-1">/api/biometric/marcar</code>).
            </p>
        </div>
    </div>
</template>
