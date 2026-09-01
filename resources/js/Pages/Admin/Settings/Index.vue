<script setup>
import { watch } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import AppLayout from '@/Layouts/AppLayout.vue';
import FormField from '@/Components/FormField.vue';
import SelectField from '@/Components/SelectField.vue';

const props = defineProps({
    settings: Array,
    groups: Array,
    selectedGroup: String,
});

const form = useForm(Object.fromEntries(props.settings.map((s) => [s.key, s.value])));

watch(
    () => props.settings,
    (settings) => {
        form.defaults(Object.fromEntries(settings.map((s) => [s.key, s.value])));
        form.reset();
    }
);

function selectGroup(group) {
    router.get(route('admin.settings.index', { group }));
}

function label(key) {
    return key
        .split('_')
        .map((w) => w.charAt(0).toUpperCase() + w.slice(1))
        .join(' ');
}

function submit() {
    form.put(route('admin.settings.update'));
}

const yesNoOptions = [
    { value: '1', label: 'Sí' },
    { value: '0', label: 'No' },
];

const attendanceMethodOptions = [
    { value: 'qr', label: 'QR' },
    { value: 'barcode', label: 'Código de barras' },
    { value: 'biometric', label: 'Biométrico' },
    { value: 'both', label: 'Ambos' },
];
</script>

<template>
    <Head title="Configuraciones" />

    <AppLayout title="Configuraciones">
        <form @submit.prevent="submit">
            <div class="mb-4 flex gap-2">
                <button
                    v-for="group in groups"
                    :key="group"
                    type="button"
                    class="rounded-lg px-3 py-1.5 text-sm"
                    :class="selectedGroup === group ? 'bg-brand-600 text-white' : 'bg-surface-muted text-slate-700'"
                    @click="selectGroup(group)"
                >
                    {{ group.charAt(0).toUpperCase() + group.slice(1) }}
                </button>
            </div>

            <div class="max-w-2xl space-y-4 rounded-xl border border-border bg-surface p-6 shadow-sm">
                <template v-for="setting in settings" :key="setting.key">
                    <SelectField
                        v-if="setting.key === 'attendance_method'"
                        v-model="form[setting.key]"
                        :label="label(setting.key)"
                        :options="attendanceMethodOptions"
                        :error="form.errors[setting.key]"
                    />
                    <SelectField
                        v-else-if="setting.type === 'boolean'"
                        v-model="form[setting.key]"
                        :label="label(setting.key)"
                        :options="yesNoOptions"
                        :error="form.errors[setting.key]"
                    />
                    <FormField
                        v-else-if="setting.type === 'number'"
                        v-model="form[setting.key]"
                        type="number"
                        :label="label(setting.key)"
                        :error="form.errors[setting.key]"
                    />
                    <FormField v-else v-model="form[setting.key]" :label="label(setting.key)" :error="form.errors[setting.key]" />
                </template>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60"
                >
                    Guardar cambios
                </button>
            </div>
        </form>
    </AppLayout>
</template>
