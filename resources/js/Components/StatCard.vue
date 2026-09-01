<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: [Object, Function], default: null },
    tone: {
        type: String,
        default: 'brand',
        validator: (v) => ['brand', 'success', 'danger', 'warning', 'info'].includes(v),
    },
});

const toneClasses = {
    brand: 'bg-brand-50 text-brand-700',
    success: 'bg-success-50 text-success-700',
    danger: 'bg-danger-50 text-danger-700',
    warning: 'bg-warning-50 text-warning-700',
    info: 'bg-info-50 text-info-700',
};

const iconWrapClass = computed(() => toneClasses[props.tone] ?? toneClasses.brand);
</script>

<template>
    <div
        class="flex items-center gap-4 rounded-xl border border-border bg-surface p-5 shadow-sm transition-shadow hover:shadow-md"
    >
        <div v-if="icon" class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full" :class="iconWrapClass">
            <component :is="icon" class="h-5 w-5" />
        </div>
        <div class="min-w-0">
            <p class="truncate text-sm text-slate-500 dark:text-slate-400">{{ label }}</p>
            <p class="truncate text-2xl font-semibold text-slate-900 dark:text-white">{{ value }}</p>
        </div>
    </div>
</template>
