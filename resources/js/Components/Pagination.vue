<script setup>
import { Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';

// Expects a Laravel paginator shape: { data, links, from, to, total, prev_page_url, next_page_url }
defineProps({
    meta: { type: Object, required: true },
});
</script>

<template>
    <div v-if="meta.total > 0" class="flex flex-wrap items-center justify-between gap-3 border-t border-border px-4 py-3 text-sm text-slate-500">
        <p>
            Mostrando <span class="font-medium text-slate-700">{{ meta.from }}</span>–<span class="font-medium text-slate-700">{{ meta.to }}</span>
            de <span class="font-medium text-slate-700">{{ meta.total }}</span>
        </p>
        <div class="flex items-center gap-1">
            <Link
                v-if="meta.prev_page_url"
                :href="meta.prev_page_url"
                preserve-scroll
                class="flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 hover:bg-surface-muted"
            >
                <ChevronLeft class="h-4 w-4" /> Anterior
            </Link>
            <span v-else class="flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 text-slate-300">
                <ChevronLeft class="h-4 w-4" /> Anterior
            </span>

            <Link
                v-if="meta.next_page_url"
                :href="meta.next_page_url"
                preserve-scroll
                class="flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 hover:bg-surface-muted"
            >
                Siguiente <ChevronRight class="h-4 w-4" />
            </Link>
            <span v-else class="flex items-center gap-1 rounded-lg border border-border px-3 py-1.5 text-slate-300">
                Siguiente <ChevronRight class="h-4 w-4" />
            </span>
        </div>
    </div>
</template>
