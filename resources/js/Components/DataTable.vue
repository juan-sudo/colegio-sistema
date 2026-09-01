<script setup>
import { computed } from 'vue';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next';

const props = defineProps({
    columns: { type: Array, required: true }, // [{ key, label, align: 'left'|'right'|'center', sortable, sortKey }]
    rows: { type: Array, required: true },
    rowKey: { type: String, default: 'id' },
    emptyText: { type: String, default: 'No hay registros para mostrar.' },
    selectable: { type: Boolean, default: true },
    sortBy: { type: String, default: '' },
    sortDir: { type: String, default: 'asc' },
});

const emit = defineEmits(['sort']);

const selected = defineModel('selected', { type: Array, default: () => [] });

const alignClass = { left: 'text-left', right: 'text-right', center: 'text-center' };

const allSelected = computed(() => props.rows.length > 0 && props.rows.every((r) => selected.value.includes(r[props.rowKey])));

function toggleAll() {
    const ids = props.rows.map((r) => r[props.rowKey]);
    selected.value = allSelected.value ? selected.value.filter((id) => !ids.includes(id)) : [...new Set([...selected.value, ...ids])];
}

function toggleRow(id) {
    selected.value = selected.value.includes(id) ? selected.value.filter((x) => x !== id) : [...selected.value, id];
}

function sortKeyFor(col) {
    return col.sortKey ?? col.key;
}
</script>

<template>
    <div class="overflow-x-auto rounded-xl border border-border bg-surface shadow-sm">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-border bg-surface-muted">
                    <th v-if="selectable" scope="col" class="w-10 px-4 py-3">
                        <input
                            type="checkbox"
                            class="h-4 w-4 cursor-pointer rounded-md border-2 border-slate-300 accent-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-slate-600"
                            :checked="allSelected"
                            aria-label="Seleccionar todos"
                            @change="toggleAll"
                        >
                    </th>
                    <th
                        v-for="col in columns"
                        :key="col.key"
                        scope="col"
                        class="px-4 py-3 font-medium text-slate-500 dark:text-slate-400"
                        :class="alignClass[col.align ?? 'left']"
                    >
                        <button
                            v-if="col.sortable"
                            type="button"
                            class="inline-flex items-center gap-1 hover:text-slate-700 dark:hover:text-slate-200"
                            @click="emit('sort', sortKeyFor(col))"
                        >
                            {{ col.label }}
                            <ArrowUp v-if="sortBy === sortKeyFor(col) && sortDir === 'asc'" class="h-3.5 w-3.5 text-brand-600" />
                            <ArrowDown v-else-if="sortBy === sortKeyFor(col) && sortDir === 'desc'" class="h-3.5 w-3.5 text-brand-600" />
                            <ArrowUpDown v-else class="h-3.5 w-3.5 text-slate-300 dark:text-slate-600" />
                        </button>
                        <span v-else>{{ col.label }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="rows.length === 0">
                    <td :colspan="columns.length + (selectable ? 1 : 0)" class="px-4 py-10 text-center text-slate-400">
                        {{ emptyText }}
                    </td>
                </tr>
                <tr
                    v-for="row in rows"
                    :key="row[rowKey]"
                    class="border-b border-border last:border-0 hover:bg-surface-muted"
                >
                    <td v-if="selectable" class="w-10 px-4 py-3">
                        <input
                            type="checkbox"
                            class="h-4 w-4 cursor-pointer rounded-md border-2 border-slate-300 accent-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-200 dark:border-slate-600"
                            :checked="selected.includes(row[rowKey])"
                            :aria-label="`Seleccionar fila ${row[rowKey]}`"
                            @change="toggleRow(row[rowKey])"
                        >
                    </td>
                    <td
                        v-for="col in columns"
                        :key="col.key"
                        class="px-4 py-3 text-slate-700 dark:text-slate-200"
                        :class="alignClass[col.align ?? 'left']"
                    >
                        <slot :name="`cell-${col.key}`" :row="row" :value="row[col.key]">
                            {{ row[col.key] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
