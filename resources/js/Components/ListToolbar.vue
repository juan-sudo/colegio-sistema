<script setup>
import { ChevronDown, Rows3, Search, UserCheck, X } from 'lucide-vue-next';

const search = defineModel('search', { type: String, default: '' });
const status = defineModel('status', { type: [String, Number], default: '' });
const perPage = defineModel('perPage', { type: [String, Number], default: 20 });

defineProps({
    placeholder: { type: String, default: 'Buscar…' },
    showStatus: { type: Boolean, default: false },
    statusOptions: {
        type: Array,
        default: () => [
            { value: '', label: 'Todos' },
            { value: '1', label: 'Activo' },
            { value: '0', label: 'Inactivo' },
        ],
    },
    perPageOptions: { type: Array, default: () => [10, 20, 50, 100] },
});

const emit = defineEmits(['submit', 'clear']);

function clearSearch() {
    search.value = '';
    emit('clear');
}
</script>

<template>
    <div class="flex flex-col gap-3 rounded-xl border border-border bg-surface p-3 shadow-sm lg:flex-row lg:items-center lg:justify-between">
        <form class="flex flex-1 flex-wrap items-center gap-2" @submit.prevent="emit('submit')">
            <div class="relative min-w-[200px] flex-1">
                <button
                    type="submit"
                    class="absolute left-3 top-1/2 flex -translate-y-1/2 items-center justify-center text-slate-400 hover:text-brand-600"
                    title="Buscar"
                >
                    <Search class="h-4 w-4" />
                </button>
                <input
                    v-model="search"
                    type="search"
                    :placeholder="placeholder"
                    class="w-full rounded-lg border border-border bg-surface py-2 pl-9 pr-8 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:text-slate-200"
                >
                <button
                    v-if="search"
                    type="button"
                    class="absolute right-2 top-1/2 flex h-5 w-5 -translate-y-1/2 items-center justify-center rounded-full text-slate-400 hover:bg-surface-muted hover:text-slate-600"
                    title="Limpiar búsqueda"
                    @click="clearSearch"
                >
                    <X class="h-3.5 w-3.5" />
                </button>
            </div>

            <slot />
        </form>

        <div class="flex flex-wrap items-center gap-2">
            <span class="hidden shrink-0 text-sm font-medium text-slate-500 dark:text-slate-400 sm:inline">Filtrar por:</span>

            <div v-if="showStatus" class="relative">
                <UserCheck class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                <select
                    v-model="status"
                    class="appearance-none rounded-lg border border-border bg-surface py-1.5 pl-8 pr-7 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:text-slate-200"
                    @change="emit('submit')"
                >
                    <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
                <ChevronDown class="pointer-events-none absolute right-2 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
            </div>

            <div class="relative">
                <Rows3 class="pointer-events-none absolute left-2.5 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
                <select
                    v-model="perPage"
                    class="appearance-none rounded-lg border border-border bg-surface py-1.5 pl-8 pr-7 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:text-slate-200"
                    @change="emit('submit')"
                >
                    <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} / página</option>
                </select>
                <ChevronDown class="pointer-events-none absolute right-2 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-slate-400" />
            </div>
        </div>
    </div>
</template>
