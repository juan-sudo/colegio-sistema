<script setup>
import { computed } from 'vue';

const props = defineProps({
    label: { type: String, required: true },
    tone: {
        type: String,
        default: 'neutral',
        validator: (v) => ['success', 'danger', 'warning', 'info', 'neutral'].includes(v),
    },
});

const toneClasses = {
    success: 'bg-success-100 text-success-800',
    danger: 'bg-danger-100 text-danger-800',
    warning: 'bg-warning-100 text-warning-800',
    info: 'bg-info-100 text-info-800',
    neutral: 'bg-slate-100 text-slate-700',
};

const classes = computed(() => toneClasses[props.tone] ?? toneClasses.neutral);
</script>

<template>
    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium" :class="classes">
        {{ label }}
    </span>
</template>
