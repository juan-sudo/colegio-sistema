import { reactive } from 'vue';

const toasts = reactive([]);
let nextId = 1;

function push(message, type = 'success', duration = 4000) {
    if (!message) return;

    const id = nextId++;
    toasts.push({ id, message, type });

    setTimeout(() => dismiss(id), duration);
}

function dismiss(id) {
    const index = toasts.findIndex((t) => t.id === id);
    if (index !== -1) toasts.splice(index, 1);
}

export function useToast() {
    return { toasts, push, dismiss };
}
