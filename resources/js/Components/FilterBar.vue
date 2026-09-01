<script setup>
import { Search, X } from 'lucide-vue-next';

const modelValue = defineModel({ type: String, default: '' });

defineProps({
    placeholder: { type: String, default: 'Buscar…' },
});

const emit = defineEmits(['submit', 'clear']);
</script>

<template>
    <form class="flex flex-wrap items-center gap-2 rounded-xl border border-border bg-surface p-3 shadow-sm" @submit.prevent="emit('submit')">
        <div class="relative min-w-[160px] flex-1">
            <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
            <input
                v-model="modelValue"
                type="search"
                :placeholder="placeholder"
                class="w-full rounded-lg border border-border py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-200"
            >
        </div>
        <slot />
        <button type="submit" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
            Buscar
        </button>
        <button
            type="button"
            class="flex items-center gap-1 rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-surface-muted"
            @click="modelValue = ''; emit('clear')"
        >
            <X class="h-4 w-4" /> Limpiar
        </button>
    </form>
</template>
