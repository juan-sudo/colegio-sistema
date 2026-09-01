<script setup>
import { computed } from 'vue';

const props = defineProps({
    items: { type: Array, required: true }, // [{ label, value }]
    color: { type: String, default: '#2a78d6' },
});

const maxValue = computed(() => Math.max(1, ...props.items.map((i) => Number(i.value) || 0)));
</script>

<template>
    <div class="space-y-3">
        <div v-for="item in items" :key="item.label" class="group">
            <div class="mb-1 flex items-center justify-between text-sm">
                <span class="truncate text-slate-600 dark:text-slate-300">{{ item.label }}</span>
                <span class="font-semibold tabular-nums text-slate-800 dark:text-slate-100">{{ item.value }}</span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-surface-muted">
                <div
                    class="h-full rounded-full transition-all"
                    :style="{ width: `${(item.value / maxValue) * 100}%`, backgroundColor: color }"
                />
            </div>
        </div>
        <p v-if="!items.length" class="py-6 text-center text-sm text-slate-400">Sin datos disponibles.</p>
    </div>
</template>
