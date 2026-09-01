<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ChevronRight, Clock, History, Trash2 } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import { clearRecentVisits, getRecentVisits } from '@/composables/useRecentVisits';

const open = ref(false);
const visits = ref([]);
const root = ref(null);
const page = usePage();

function refresh() {
    visits.value = getRecentVisits(6);
}

function toggle() {
    open.value = !open.value;
    if (open.value) refresh();
}

function onClear() {
    clearRecentVisits();
    refresh();
}

function handleClickOutside(event) {
    if (!root.value) return;
    if (!root.value.contains(event.target)) open.value = false;
}

onMounted(() => {
    refresh();
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('recent-visit:changed', refresh);
});

function visitUrl(visit) {
    if (visit.href) return visit.href;
    if (visit.route) {
        try {
            return route(visit.route, visit.params ?? {});
        } catch (e) {
            return '#';
        }
    }
    return '#';
}
</script>

<template>
    <div ref="root" class="relative">
        <button
            type="button"
            class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 sm:h-12 sm:w-12"
            :title="'Últimas visitas'"
            @click.stop="toggle"
        >
            <History class="h-5 w-5" />
            <span
                v-if="visits.length"
                class="absolute right-1 top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-brand-600 px-1 text-[10px] font-semibold text-white ring-2 ring-surface"
            >
                {{ visits.length }}
            </span>
        </button>

        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="open"
                class="absolute right-0 top-full z-30 mt-2 w-80 overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <Clock class="h-4 w-4 text-brand-600" />
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Últimas visitas</h3>
                    </div>
                    <button
                        v-if="visits.length"
                        type="button"
                        class="flex items-center gap-1 rounded-lg px-2 py-1 text-xs text-slate-500 transition hover:bg-slate-100 hover:text-red-600 dark:hover:bg-slate-700"
                        @click="onClear"
                    >
                        <Trash2 class="h-3.5 w-3.5" /> Limpiar
                    </button>
                </div>

                <ul v-if="visits.length" class="max-h-80 divide-y divide-slate-100 overflow-y-auto dark:divide-slate-700">
                    <li v-for="(visit, idx) in visits" :key="idx" class="group">
                        <Link
                            :href="visitUrl(visit)"
                            class="flex items-start gap-3 px-4 py-3 transition hover:bg-slate-50 dark:hover:bg-slate-700/50"
                        >
                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-300">
                                <span class="text-sm">{{ visit.icon || '📌' }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-medium text-slate-800 dark:text-slate-100">{{ visit.label }}</span>
                                <span class="block text-xs text-slate-500 dark:text-slate-400">
                                    {{ visit.section || 'Módulo' }} · hace {{ visit.ago || 'un momento' }}
                                </span>
                            </span>
                            <ChevronRight class="mt-1 h-4 w-4 shrink-0 text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand-500" />
                        </Link>
                    </li>
                </ul>

                <div v-else class="px-4 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                    No hay visitas recientes todavía.
                </div>
            </div>
        </transition>
    </div>
</template>
