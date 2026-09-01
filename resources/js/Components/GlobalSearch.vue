<script setup>
import { router } from '@inertiajs/vue3';
import { Search, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';

const props = defineProps({
    placeholder: { type: String, default: 'Buscar en todo el sistema...' },
    items: { type: Array, default: () => [] },
});

const open = ref(false);
const query = ref('');
const inputRef = ref(null);
const rootRef = ref(null);

const filtered = computed(() => {
    const q = query.value.trim().toLowerCase();
    if (!q) return props.items.slice(0, 8);
    return props.items
        .filter((it) => it.label.toLowerCase().includes(q) || (it.section && it.section.toLowerCase().includes(q)))
        .slice(0, 10);
});

function openSearch() {
    open.value = true;
    setTimeout(() => inputRef.value?.focus(), 50);
}

function close() {
    open.value = false;
    query.value = '';
}

function goTo(item) {
    close();
    if (item.route) router.visit(item.href);
    else if (item.href) window.location.href = item.href;
}

function handleClickOutside(event) {
    if (!rootRef.value) return;
    if (!rootRef.value.contains(event.target)) close();
}

function onKeydown(event) {
    if ((event.metaKey || event.ctrlKey) && event.key === 'k') {
        event.preventDefault();
        openSearch();
    }
    if (event.key === 'Escape') close();
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    document.addEventListener('keydown', onKeydown);
});
</script>

<template>
    <div ref="rootRef" class="relative w-full max-w-xl">
        <button
            type="button"
            class="flex w-full items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-left text-sm text-slate-400 transition hover:border-slate-300 hover:bg-white sm:pl-4"
            @click="openSearch"
        >
            <Search class="h-4 w-4 text-slate-400" />
            <span class="flex-1 truncate text-slate-400">{{ placeholder }}</span>
            <kbd class="hidden rounded border border-slate-200 bg-white px-1.5 py-0.5 text-[10px] font-mono text-slate-500 sm:inline-block">
                ⌘K
            </kbd>
        </button>

        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm"
                @click="close"
            />
        </transition>

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="open"
                class="fixed left-1/2 top-24 z-50 w-full max-w-xl -translate-x-1/2 overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                    <Search class="h-5 w-5 text-slate-400" />
                    <input
                        ref="inputRef"
                        v-model="query"
                        type="text"
                        :placeholder="placeholder"
                        class="flex-1 bg-transparent text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none dark:text-slate-100"
                    >
                    <button type="button" class="rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 dark:hover:bg-slate-700" @click="close">
                        <X class="h-4 w-4" />
                    </button>
                </div>

                <ul v-if="filtered.length" class="max-h-96 overflow-y-auto py-2">
                    <li v-for="(item, idx) in filtered" :key="idx">
                        <button
                            type="button"
                            class="flex w-full items-center gap-3 px-4 py-2.5 text-left transition hover:bg-slate-50 dark:hover:bg-slate-700/50"
                            @click="goTo(item)"
                        >
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                                <span class="text-base">{{ item.icon || '🔍' }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-slate-800 dark:text-slate-100">
                                    {{ item.label }}
                                </span>
                                <span class="block truncate text-xs text-slate-500 dark:text-slate-400">
                                    {{ item.section }}
                                </span>
                            </span>
                            <span class="text-xs text-slate-400">Ir →</span>
                        </button>
                    </li>
                </ul>

                <div v-else class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                    Sin resultados para “{{ query }}”
                </div>
            </div>
        </transition>
    </div>
</template>
