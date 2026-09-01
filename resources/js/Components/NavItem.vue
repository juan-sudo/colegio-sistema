<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: { type: String, required: true },
    label: { type: String, required: true },
    icon: { type: [Object, Function], required: true },
    active: { type: Boolean, default: false },
    useInertia: { type: Boolean, default: false },
    collapsed: { type: Boolean, default: false },
});
</script>

<template>
    <component
        :is="useInertia ? Link : 'a'"
        :href="href"
        class="flex items-center rounded-lg text-sm transition-colors"
        :class="[
            active ? 'bg-brand-800 text-white shadow-sm' : 'text-brand-100 hover:bg-brand-800/60 hover:text-white',
            collapsed ? 'justify-center px-2 py-2.5' : 'gap-3 px-3 py-2'
        ]"
        :title="collapsed ? label : undefined"
    >
        <component :is="icon" class="h-4 w-4 shrink-0" />
        <span v-if="!collapsed" class="truncate">{{ label }}</span>
    </component>
</template>
