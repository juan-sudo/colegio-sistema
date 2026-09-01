<script setup>
import { watch, ref, nextTick } from 'vue';
import { X } from 'lucide-vue-next';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: 'max-w-lg' },
});

const emit = defineEmits(['close']);

const panel = ref(null);

function close() {
    emit('close');
}

function onKeydown(e) {
    if (e.key === 'Escape' && props.show) close();
}

watch(
    () => props.show,
    async (visible) => {
        if (visible) {
            await nextTick();
            panel.value?.querySelector('[autofocus], input, select, textarea, button')?.focus();
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }
);
</script>

<template>
    <Teleport to="body" :disabled="false">
        <div
            v-if="show"
            class="modal-backdrop"
            @click.self="close"
            @keydown="onKeydown"
        >
            <div
                ref="panel"
                role="dialog"
                aria-modal="true"
                :aria-label="title"
                class="modal-panel"
                :class="maxWidth"
                @click.stop
            >
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">{{ title }}</h2>
                    <button
                        type="button"
                        class="rounded p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Cerrar"
                        @click="close"
                    >
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <slot />
            </div>
        </div>
    </Teleport>
</template>

<style>
.modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    overflow-y: auto;
    padding: 2rem 1rem;
    background-color: rgba(2, 6, 23, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    animation: modal-fade-in 0.18s ease-out;
}

.modal-panel {
    position: relative;
    width: 100%;
    border-radius: 0.75rem;
    background-color: var(--surface, #fff);
    padding: 1.5rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
    margin: auto 0;
    animation: modal-pop-in 0.18s cubic-bezier(0.16, 1, 0.3, 1);
    z-index: 10000;
}

@keyframes modal-fade-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}

@keyframes modal-pop-in {
    from { opacity: 0; transform: scale(0.96) translateY(4px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
</style>
