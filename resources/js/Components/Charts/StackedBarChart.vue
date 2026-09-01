<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    points: { type: Array, required: true }, // [{ label, ...seriesKeys }]
    series: { type: Array, required: true }, // [{ key, name, color }]
    height: { type: Number, default: 260 },
    valuePrefix: { type: String, default: '' },
});

const width = 720;
const padding = { top: 16, right: 12, bottom: 28, left: 36 };
const innerWidth = width - padding.left - padding.right;
const innerHeight = props.height - padding.top - padding.bottom;

const totals = computed(() => props.points.map((p) => props.series.reduce((sum, s) => sum + (Number(p[s.key]) || 0), 0)));

const maxValue = computed(() => {
    const max = Math.max(1, ...totals.value);
    const magnitude = Math.pow(10, Math.floor(Math.log10(max)));
    return Math.ceil(max / (magnitude / 2)) * (magnitude / 2);
});

const bandWidth = computed(() => innerWidth / props.points.length);
const barWidth = computed(() => Math.min(28, bandWidth.value * 0.45));

function scaleY(value) {
    return maxValue.value === 0 ? 0 : (Number(value) / maxValue.value) * innerHeight;
}

const yTicks = computed(() => {
    const steps = 4;
    return Array.from({ length: steps + 1 }, (_, i) => Math.round((maxValue.value / steps) * i));
});

function formatValue(v) {
    if (props.valuePrefix && v >= 1000) return `${props.valuePrefix}${(v / 1000).toFixed(1)}k`;
    return `${props.valuePrefix}${Math.round(v).toLocaleString('es-PE')}`;
}

const hoverIndex = ref(null);
const hoverPoint = computed(() => (hoverIndex.value === null ? null : props.points[hoverIndex.value]));
const gap = 2;

function barX(i) {
    return padding.left + bandWidth.value * i + (bandWidth.value - barWidth.value) / 2;
}
const tooltipStyle = computed(() => {
    if (hoverIndex.value === null) return {};
    const leftPct = ((barX(hoverIndex.value) + barWidth.value / 2) / width) * 100;
    const align = leftPct > 70 ? 'right' : leftPct < 15 ? 'left' : 'center';
    return { leftPct, align };
});
</script>

<template>
    <div class="w-full">
        <div v-if="series.length > 1" class="mb-3 flex flex-wrap items-center gap-4">
            <div v-for="s in series" :key="s.key" class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="h-2 w-2 rounded-sm" :style="{ backgroundColor: s.color }" />
                {{ s.name }}
            </div>
        </div>

        <div class="relative">
            <svg :viewBox="`0 0 ${width} ${height}`" class="w-full" :style="{ height: `${height}px` }" preserveAspectRatio="none">
                <line
                    v-for="(tick, i) in yTicks"
                    :key="i"
                    :x1="padding.left"
                    :x2="width - padding.right"
                    :y1="padding.top + innerHeight - scaleY(tick)"
                    :y2="padding.top + innerHeight - scaleY(tick)"
                    stroke="currentColor"
                    class="text-slate-100 dark:text-slate-700"
                    stroke-width="1"
                />
                <text
                    v-for="(tick, i) in yTicks"
                    :key="`t-${i}`"
                    :x="padding.left - 6"
                    :y="padding.top + innerHeight - scaleY(tick) + 3"
                    text-anchor="end"
                    class="fill-slate-400 text-[9px] dark:fill-slate-500"
                >
                    {{ formatValue(tick) }}
                </text>

                <g v-for="(p, i) in points" :key="i">
                    <g v-for="(s, si) in series" :key="s.key">
                        <rect
                            :x="barX(i)"
                            :y="
                                padding.top +
                                innerHeight -
                                series.slice(0, si + 1).reduce((sum, ss) => sum + scaleY(p[ss.key]), 0) +
                                (si > 0 ? gap : 0)
                            "
                            :width="barWidth"
                            :height="Math.max(0, scaleY(p[s.key]) - (si > 0 ? gap : 0))"
                            :fill="s.color"
                            :rx="si === series.length - 1 ? 4 : 1"
                            class="transition-opacity"
                            :class="{ 'opacity-50': hoverIndex !== null && hoverIndex !== i }"
                        />
                    </g>
                    <rect
                        :x="padding.left + bandWidth * i"
                        :y="padding.top"
                        :width="bandWidth"
                        :height="innerHeight"
                        fill="transparent"
                        pointer-events="all"
                        class="cursor-pointer"
                        @pointerenter="hoverIndex = i"
                        @pointerleave="hoverIndex = null"
                    />
                    <text
                        :x="padding.left + bandWidth * i + bandWidth / 2"
                        :y="height - 6"
                        text-anchor="middle"
                        class="fill-slate-400 text-[9px] dark:fill-slate-500"
                    >
                        {{ p.label }}
                    </text>
                </g>
            </svg>

            <div
                v-if="hoverPoint"
                class="pointer-events-none absolute top-0 z-10 w-44 -translate-y-1 rounded-lg border border-border bg-surface p-3 text-xs shadow-lg"
                :style="{
                    left: `${tooltipStyle.leftPct}%`,
                    transform:
                        tooltipStyle.align === 'right'
                            ? 'translateX(-100%)'
                            : tooltipStyle.align === 'left'
                              ? 'translateX(0)'
                              : 'translateX(-50%)',
                }"
            >
                <p class="mb-1.5 font-medium text-slate-500 dark:text-slate-400">{{ hoverPoint.label }}</p>
                <div v-for="s in series" :key="s.key" class="flex items-center justify-between gap-3 py-0.5">
                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                        <span class="h-2 w-2 rounded-sm" :style="{ backgroundColor: s.color }" />
                        {{ s.name }}
                    </span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ formatValue(hoverPoint[s.key]) }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
