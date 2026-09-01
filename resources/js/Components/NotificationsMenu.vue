<script setup>
import { router } from '@inertiajs/vue3';
import { BellRing, Check, CheckCheck, Clock, Trash2, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import {
    clearNotifications,
    getStoredNotifications,
    markAllNotificationsRead,
    markNotificationRead,
    setStoredNotifications,
} from '@/composables/useNotifications';

const props = defineProps({
    initialNotifications: { type: Array, default: () => [] },
    globalSearchEnabled: { type: Boolean, default: false },
});

const open = ref(false);
const items = ref([]);
const root = ref(null);

const unreadCount = computed(() => items.value.filter((n) => !n.read).length);

function loadFromStorage() {
    const stored = getStoredNotifications();
    if (stored.length === 0 && props.initialNotifications.length > 0) {
        setStoredNotifications(props.initialNotifications);
        items.value = [...props.initialNotifications];
    } else {
        items.value = stored;
    }
}

function onItemClick(item) {
    if (!item.read) {
        const next = markNotificationRead(item.id);
        items.value = next;
    }
    if (item.href) {
        router.visit(item.href);
    }
    open.value = false;
}

function onMarkAllRead() {
    const next = markAllNotificationsRead();
    items.value = next;
}

function onClear() {
    clearNotifications();
    items.value = [];
}

function handleClickOutside(event) {
    if (!root.value) return;
    if (!root.value.contains(event.target)) open.value = false;
}

function relativeTime(date) {
    if (!date) return '';
    const now = new Date();
    const then = new Date(date);
    const diff = Math.floor((now - then) / 1000);
    if (diff < 60) return 'hace un momento';
    if (diff < 3600) return `hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `hace ${Math.floor(diff / 3600)} h`;
    return `hace ${Math.floor(diff / 86400)} d`;
}

onMounted(() => {
    loadFromStorage();
    document.addEventListener('click', handleClickOutside);
});

watch(
    () => props.initialNotifications,
    (newVal) => {
        if (newVal && newVal.length) {
            const stored = getStoredNotifications();
            const next = [...newVal, ...stored].slice(0, 20);
            setStoredNotifications(next);
            items.value = next;
        }
    },
    { deep: true }
);

function tone(t) {
    const tones = {
        info: 'bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300',
        success: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
        warning: 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        danger: 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
    };
    return tones[t] || tones.info;
}
</script>

<template>
    <div ref="root" class="relative">
        <button
            type="button"
            class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 sm:h-12 sm:w-12"
            :title="'Notificaciones'"
            @click.stop="open = !open"
        >
            <BellRing class="h-5 w-5" />
            <span
                v-if="unreadCount"
                class="absolute right-1 top-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-semibold text-white ring-2 ring-surface"
            >
                {{ unreadCount }}
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
                class="absolute right-0 top-full z-30 mt-2 w-96 overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
            >
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3 dark:border-slate-700">
                    <div class="flex items-center gap-2">
                        <BellRing class="h-4 w-4 text-brand-600" />
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">Notificaciones</h3>
                        <span
                            v-if="unreadCount"
                            class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-600 dark:bg-red-900/40 dark:text-red-300"
                        >
                            {{ unreadCount }} nuevas
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            v-if="unreadCount"
                            type="button"
                            class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-brand-600 dark:hover:bg-slate-700"
                            title="Marcar todas como leídas"
                            @click="onMarkAllRead"
                        >
                            <CheckCheck class="h-4 w-4" />
                        </button>
                        <button
                            v-if="items.length"
                            type="button"
                            class="rounded-lg p-1.5 text-slate-500 transition hover:bg-slate-100 hover:text-red-600 dark:hover:bg-slate-700"
                            title="Borrar todo"
                            @click="onClear"
                        >
                            <Trash2 class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <ul v-if="items.length" class="max-h-96 divide-y divide-slate-100 overflow-y-auto dark:divide-slate-700">
                    <li v-for="item in items" :key="item.id">
                        <button
                            type="button"
                            class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-700/50"
                            :class="{ 'bg-brand-50/50 dark:bg-brand-900/10': !item.read }"
                            @click="onItemClick(item)"
                        >
                            <span
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl"
                                :class="tone(item.tone)"
                            >
                                <span class="text-base">{{ item.icon || '🔔' }}</span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-slate-800 dark:text-slate-100">
                                    {{ item.title }}
                                </span>
                                <span class="block text-xs text-slate-600 dark:text-slate-300">
                                    {{ item.message }}
                                </span>
                                <span class="mt-1 flex items-center gap-1 text-[11px] text-slate-400 dark:text-slate-500">
                                    <Clock class="h-3 w-3" /> {{ relativeTime(item.created_at) }}
                                </span>
                            </span>
                            <span
                                v-if="!item.read"
                                class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-red-500"
                            />
                        </button>
                    </li>
                </ul>

                <div v-else class="flex flex-col items-center gap-2 px-4 py-10 text-sm text-slate-500 dark:text-slate-400">
                    <BellRing class="h-8 w-8 text-slate-300 dark:text-slate-600" />
                    <span>Sin notificaciones por ahora.</span>
                </div>
            </div>
        </transition>
    </div>
</template>
