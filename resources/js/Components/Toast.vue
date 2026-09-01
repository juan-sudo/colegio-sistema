<script setup>
import { CheckCircle2, AlertCircle, X } from 'lucide-vue-next';
import { useToast } from '@/composables/useToast';

const { toasts, dismiss } = useToast();

const styles = {
    success: {
        wrap: 'bg-success-50 border-success-100 text-success-800',
        icon: 'text-success-600',
    },
    error: {
        wrap: 'bg-danger-50 border-danger-100 text-danger-800',
        icon: 'text-danger-600',
    },
};
</script>

<template>
    <div
        class="fixed top-4 right-4 z-50 flex w-full max-w-sm flex-col gap-2"
        aria-live="polite"
        role="status"
    >
        <TransitionGroup name="toast">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="flex items-start gap-3 rounded-lg border p-4 shadow-lg"
                :class="styles[toast.type]?.wrap ?? styles.success.wrap"
            >
                <component
                    :is="toast.type === 'error' ? AlertCircle : CheckCircle2"
                    class="mt-0.5 h-5 w-5 shrink-0"
                    :class="styles[toast.type]?.icon ?? styles.success.icon"
                />
                <p class="flex-1 text-sm font-medium">{{ toast.message }}</p>
                <button
                    type="button"
                    class="shrink-0 rounded p-0.5 opacity-60 hover:opacity-100"
                    aria-label="Cerrar notificación"
                    @click="dismiss(toast.id)"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.2s ease;
}
.toast-enter-from {
    opacity: 0;
    transform: translateX(1rem);
}
.toast-leave-to {
    opacity: 0;
    transform: translateX(1rem);
}
</style>
