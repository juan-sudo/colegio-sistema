<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import {
    BellDot,
    ChevronDown,
    GraduationCap,
    LogOut,
    Menu,
    MoonStar,
    PanelLeftClose,
    PanelLeftOpen,
    SunMedium,
    X,
} from 'lucide-vue-next';
import NavItem from '@/Components/NavItem.vue';
import Toast from '@/Components/Toast.vue';
import GlobalSearch from '@/Components/GlobalSearch.vue';
import NotificationsMenu from '@/Components/NotificationsMenu.vue';
import RecentVisitsMenu from '@/Components/RecentVisitsMenu.vue';
import { useToast } from '@/composables/useToast';
import { navigationFor } from '@/navigation';
import { pushRecentVisit } from '@/composables/useRecentVisits';

defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const { push } = useToast();

const user = computed(() => page.props.auth.user);
const sections = computed(() => navigationFor(user.value?.role ?? 'student'));
const isDark = ref(false);
const sidebarCollapsed = ref(false);
const mobileSidebarOpen = ref(false);
const userMenuOpen = ref(false);
const userMenuRef = ref(null);

const globalSearchItems = computed(() => {
    const items = [];
    for (const section of sections.value) {
        for (const item of section.items) {
            items.push({
                label: item.label,
                section: section.title || 'Módulo',
                icon: typeof item.icon === 'string' ? item.icon : '🔎',
                href: route(item.route),
                route: item.route,
            });
        }
    }
    return items;
});

const initialNotifications = computed(() => page.props.notifications ?? []);

function handleClickOutside(event) {
    if (!userMenuRef.value) return;

    if (!userMenuRef.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
}

function recordCurrentVisit() {
    const current = route().current();
    if (!current) return;
    const matched = globalSearchItems.value.find((it) => it.route === current);
    if (!matched) return;
    pushRecentVisit({
        route: matched.route,
        label: matched.label,
        section: matched.section,
        icon: matched.icon,
        ago: 'ahora',
        ts: Date.now(),
    });
}

onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    isDark.value = savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.addEventListener('click', handleClickOutside);
    recordCurrentVisit();
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

watch(
    isDark,
    (value) => {
        document.documentElement.classList.toggle('dark', value);
        localStorage.setItem('theme', value ? 'dark' : 'light');
    },
    { immediate: true }
);

const userInitials = computed(() => {
    const fullName = user.value?.name ?? 'Usuario';
    return fullName
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? '')
        .join('') || 'U';
});

const userRole = computed(() => {
    const role = user.value?.role ?? 'student';
    const labels = {
        admin: 'Administrador',
        teacher: 'Profesor',
        student: 'Estudiante',
        parent: 'Apoderado',
    };

    return labels[role] ?? 'Usuario';
});

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) push(flash.success, 'success');
        if (flash?.error) push(flash.error, 'error');
    },
    { immediate: true, deep: true }
);

const legacyBladeRoutes = [];

function isActive(item) {
    return route().current(item.match ?? item.route);
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-surface-muted print:block print:h-auto print:overflow-visible">
        <div
            v-if="mobileSidebarOpen"
            class="fixed inset-0 z-40 bg-slate-950/50 lg:hidden"
            @click="mobileSidebarOpen = false"
        />

        <aside
            class="fixed inset-y-0 left-0 z-50 flex h-full w-64 shrink-0 flex-col overflow-hidden bg-brand-900 text-white transition-transform duration-200 print:hidden lg:static lg:translate-x-0 lg:transition-[width]"
            :class="[
                mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                sidebarCollapsed ? 'lg:w-20' : 'lg:w-64',
            ]"
        >
            <div class="flex items-center justify-between border-b border-brand-800 px-3 py-3.5">
                <div v-if="!sidebarCollapsed" class="flex min-w-0 items-center gap-2 overflow-hidden">
                    <GraduationCap class="h-6 w-6 shrink-0 text-brand-200" />
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold leading-tight">{{ page.props.appName }}</p>
                        <p class="text-xs capitalize text-brand-300">{{ user?.role }}</p>
                    </div>
                </div>

                <button
                    type="button"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-brand-100 transition hover:bg-brand-800/80 lg:hidden"
                    @click="mobileSidebarOpen = false"
                    title="Cerrar menú"
                >
                    <X class="h-4 w-4" />
                </button>

                <button
                    v-if="sidebarCollapsed"
                    type="button"
                    class="hidden h-9 w-9 items-center justify-center rounded-lg text-brand-100 transition hover:bg-brand-800/80 lg:flex"
                    @click="sidebarCollapsed = false"
                    :title="'Expandir menú'"
                >
                    <PanelLeftOpen class="h-4 w-4" />
                </button>

                <button
                    v-else
                    type="button"
                    class="hidden h-9 w-9 items-center justify-center rounded-lg text-brand-100 transition hover:bg-brand-800/80 lg:flex"
                    @click="sidebarCollapsed = true"
                    :title="'Contraer menú'"
                >
                    <PanelLeftClose class="h-4 w-4" />
                </button>
            </div>

            <nav
                class="flex-1 space-y-5 overflow-y-auto overflow-x-hidden px-2 py-4 scrollbar-thin scrollbar-thumb-brand-700 scrollbar-track-brand-950"
                @click="mobileSidebarOpen = false"
            >
                <div v-for="(section, i) in sections" :key="i" class="space-y-1">
                    <p v-if="section.title && !sidebarCollapsed" class="px-3 pb-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-brand-400">
                        {{ section.title }}
                    </p>
                    <NavItem
                        v-for="item in section.items"
                        :key="item.route"
                        :label="item.label"
                        :icon="item.icon"
                        :href="route(item.route)"
                        :active="isActive(item)"
                        :use-inertia="!legacyBladeRoutes.includes(item.route)"
                        :collapsed="sidebarCollapsed"
                    />
                </div>
            </nav>

        </aside>

        <div class="flex min-w-0 flex-1 flex-col overflow-hidden print:block print:overflow-visible">
            <header class="shrink-0 border-b border-border bg-surface px-3 py-3 print:hidden sm:px-6 sm:py-4">
                <div class="flex items-center justify-between gap-2 sm:gap-4">
                    <button
                        type="button"
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700 lg:hidden"
                        @click="mobileSidebarOpen = true"
                        title="Abrir menú"
                    >
                        <Menu class="h-5 w-5" />
                    </button>

                    <div class="relative min-w-0 flex-1 sm:max-w-xl">
                        <GlobalSearch :items="globalSearchItems" />
                    </div>

                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <button
                            type="button"
                            class="relative hidden h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 sm:flex"
                            @click="isDark = !isDark"
                            :title="isDark ? 'Activar modo claro' : 'Activar modo oscuro'"
                        >
                            <SunMedium v-if="!isDark" class="h-5 w-5" />
                            <MoonStar v-else class="h-5 w-5" />
                        </button>

                        <RecentVisitsMenu />

                        <NotificationsMenu :initial-notifications="initialNotifications" />

                        <div ref="userMenuRef" class="relative">
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-full bg-slate-100 px-1.5 py-1.5 text-left transition hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 sm:gap-3 sm:px-2"
                                @click.stop="userMenuOpen = !userMenuOpen"
                            >
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-brand-700 text-sm font-semibold text-white sm:h-10 sm:w-10">
                                    {{ userInitials }}
                                </div>
                                <div class="hidden text-left sm:block">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ user?.name ?? 'Usuario' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-300">{{ userRole }}</p>
                                </div>
                                <ChevronDown class="hidden h-4 w-4 text-slate-500 dark:text-slate-300 sm:block" />
                            </button>

                            <div
                                v-if="userMenuOpen"
                                class="absolute right-0 top-full z-20 mt-2 w-56 overflow-hidden rounded-xl bg-white shadow-lg ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700"
                            >
                                <div class="border-b border-slate-200 px-3 py-3 dark:border-slate-700">
                                    <p class="truncate text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        {{ user?.name ?? 'Usuario' }}
                                    </p>
                                    <p class="truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ user?.email }}
                                    </p>
                                </div>
                                <div class="p-1">
                                    <Link
                                        :href="route('profile.show')"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700"
                                    >
                                        Mi perfil
                                    </Link>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-red-50 hover:text-red-600 dark:text-slate-200 dark:hover:bg-red-900/30 dark:hover:text-red-400"
                                    >
                                        <LogOut class="h-4 w-4" />
                                        Cerrar sesión
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 print:overflow-visible print:p-0 sm:p-6">
                <slot />
            </main>
        </div>

        <Toast />
    </div>
</template>
