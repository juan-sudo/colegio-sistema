<script setup>
defineProps({
    eyebrow: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <div class="mb-2 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">{{ eyebrow }}</p>
            <h1 class="mt-1 text-xl font-semibold text-slate-900 dark:text-white">{{ title }}</h1>
            <p v-if="description" class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ description }}</p>
        </div>
        <slot />
    </div>
</template>
