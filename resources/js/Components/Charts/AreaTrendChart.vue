<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
    points: { type: Array, required: true }, // [{ label, ...seriesKeys }]
    series: { type: Array, required: true }, // [{ key, name, color }]
    height: { type: Number, default: 260 },
});

const width = 720;
const padding = { top: 16, right: 12, bottom: 28, left: 12 };
const innerWidth = width - padding.left - padding.right;
const innerHeight = props.height - padding.top - padding.bottom;

const maxValue = computed(() => {
    const values = props.points.flatMap((p) => props.series.map((s) => Number(p[s.key]) || 0));
    const max = Math.max(1, ...values);
    // round up to a clean step
    const magnitude = Math.pow(10, Math.floor(Math.log10(max)));
    return Math.ceil(max / (magnitude / 2)) * (magnitude / 2);
});

const stepX = computed(() => (props.points.length > 1 ? innerWidth / (props.points.length - 1) : 0));

function xFor(i) {
    return padding.left + stepX.value * i;
}
function yFor(value) {
    const ratio = maxValue.value === 0 ? 0 : Number(value) / maxValue.value;
    return padding.top + innerHeight - ratio * innerHeight;
}

const linePaths = computed(() =>
    props.series.map((s) => {
        const d = props.points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${xFor(i)} ${yFor(p[s.key])}`).join(' ');
        return { key: s.key, d };
    })
);

const areaPaths = computed(() =>
    props.series.map((s) => {
        const top = props.points.map((p, i) => `${i === 0 ? 'M' : 'L'} ${xFor(i)} ${yFor(p[s.key])}`).join(' ');
        const baseY = padding.top + innerHeight;
        const last = props.points.length - 1;
        return { key: s.key, d: `${top} L ${xFor(last)} ${baseY} L ${xFor(0)} ${baseY} Z` };
    })
);

const yTicks = computed(() => {
    const steps = 4;
    return Array.from({ length: steps + 1 }, (_, i) => Math.round((maxValue.value / steps) * i));
});

const xLabelEvery = computed(() => Math.max(1, Math.ceil(props.points.length / 7)));

const hoverIndex = ref(null);

function handleMove(evt) {
    const svg = evt.currentTarget;
    const rect = svg.getBoundingClientRect();
    const px = ((evt.clientX - rect.left) / rect.width) * width;
    let idx = Math.round((px - padding.left) / (stepX.value || 1));
    idx = Math.min(props.points.length - 1, Math.max(0, idx));
    hoverIndex.value = idx;
}
function handleLeave() {
    hoverIndex.value = null;
}

const hoverPoint = computed(() => (hoverIndex.value === null ? null : props.points[hoverIndex.value]));
const tooltipStyle = computed(() => {
    if (hoverIndex.value === null) return {};
    const leftPct = (xFor(hoverIndex.value) / width) * 100;
    const align = leftPct > 70 ? 'right' : leftPct < 15 ? 'left' : 'center';
    return { leftPct, align };
});
</script>

<template>
    <div class="w-full">
        <div v-if="series.length > 1" class="mb-3 flex flex-wrap items-center gap-4">
            <div v-for="s in series" :key="s.key" class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: s.color }" />
                {{ s.name }}
            </div>
        </div>

        <div class="relative">
            <svg
                :viewBox="`0 0 ${width} ${height}`"
                class="w-full touch-none"
                :style="{ height: `${height}px` }"
                preserveAspectRatio="none"
                @pointermove="handleMove"
                @pointerleave="handleLeave"
            >
                <line
                    v-for="(tick, i) in yTicks"
                    :key="i"
                    :x1="padding.left"
                    :x2="width - padding.right"
                    :y1="yFor(tick)"
                    :y2="yFor(tick)"
                    stroke="currentColor"
                    class="text-slate-100 dark:text-slate-700"
                    stroke-width="1"
                />

                <text
                    v-for="(tick, i) in yTicks"
                    :key="`t-${i}`"
                    :x="padding.left"
                    :y="yFor(tick) - 4"
                    class="fill-slate-400 text-[9px] dark:fill-slate-500"
                >
                    {{ tick }}
                </text>

                <defs>
                    <linearGradient v-for="s in series" :id="`grad-${s.key}`" :key="s.key" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" :stop-color="s.color" stop-opacity="0.22" />
                        <stop offset="100%" :stop-color="s.color" stop-opacity="0" />
                    </linearGradient>
                </defs>

                <path v-for="p in areaPaths" :key="`area-${p.key}`" :d="p.d" :fill="`url(#grad-${p.key})`" stroke="none" />
                <path
                    v-for="p in linePaths"
                    :key="`line-${p.key}`"
                    :d="p.d"
                    fill="none"
                    :stroke="series.find((s) => s.key === p.key).color"
                    stroke-width="2"
                    stroke-linejoin="round"
                    stroke-linecap="round"
                />

                <g v-if="hoverIndex !== null">
                    <line
                        :x1="xFor(hoverIndex)"
                        :x2="xFor(hoverIndex)"
                        :y1="padding.top"
                        :y2="padding.top + innerHeight"
                        stroke="currentColor"
                        class="text-slate-300 dark:text-slate-600"
                        stroke-width="1"
                    />
                    <circle
                        v-for="s in series"
                        :key="`dot-${s.key}`"
                        :cx="xFor(hoverIndex)"
                        :cy="yFor(hoverPoint[s.key])"
                        r="4"
                        :fill="s.color"
                        stroke="white"
                        class="dark:stroke-slate-900"
                        stroke-width="2"
                    />
                </g>

                <text
                    v-for="(p, i) in points"
                    v-show="i % xLabelEvery === 0"
                    :key="`x-${i}`"
                    :x="xFor(i)"
                    :y="height - 6"
                    text-anchor="middle"
                    class="fill-slate-400 text-[9px] dark:fill-slate-500"
                >
                    {{ p.label }}
                </text>
            </svg>

            <div
                v-if="hoverPoint"
                class="pointer-events-none absolute top-0 z-10 w-40 -translate-y-1 rounded-lg border border-border bg-surface p-3 text-xs shadow-lg"
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
                        <span class="h-2 w-2 rounded-full" :style="{ backgroundColor: s.color }" />
                        {{ s.name }}
                    </span>
                    <span class="font-semibold text-slate-800 dark:text-slate-100">{{ hoverPoint[s.key] }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
