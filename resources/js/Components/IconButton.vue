<script setup>
const props = defineProps({
    icon: { type: [Object, Function], required: true },
    tone: { type: String, default: 'brand', validator: (v) => ['brand', 'danger', 'warning', 'success', 'neutral'].includes(v) },
    title: { type: String, required: true },
    href: { type: String, default: '' },
});

defineEmits(['click']);

const toneClasses = {
    brand: 'text-slate-500 hover:bg-brand-50 hover:text-brand-600 dark:text-slate-400 dark:hover:bg-brand-900/40 dark:hover:text-brand-300',
    danger: 'text-slate-500 hover:bg-danger-50 hover:text-danger-600 dark:text-slate-400 dark:hover:bg-danger-900/40 dark:hover:text-danger-300',
    warning: 'text-slate-500 hover:bg-warning-50 hover:text-warning-600 dark:text-slate-400 dark:hover:bg-warning-900/40 dark:hover:text-warning-300',
    success: 'text-slate-500 hover:bg-success-50 hover:text-success-600 dark:text-slate-400 dark:hover:bg-success-900/40 dark:hover:text-success-300',
    neutral: 'text-slate-500 hover:bg-surface-muted hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200',
};
</script>

<template>
    <a
        v-if="href"
        :href="href"
        :title="title"
        :aria-label="title"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition"
        :class="toneClasses[tone]"
    >
        <component :is="icon" class="h-4 w-4" />
    </a>
    <button
        v-else
        type="button"
        :title="title"
        :aria-label="title"
        class="inline-flex h-8 w-8 items-center justify-center rounded-lg transition"
        :class="toneClasses[tone]"
        @click="$emit('click')"
    >
        <component :is="icon" class="h-4 w-4" />
    </button>
</template>
