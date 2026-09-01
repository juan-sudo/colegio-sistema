<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, required: true },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
});

defineEmits(['update:modelValue']);

const inputId = computed(() => `field-${props.label.toLowerCase().replace(/\s+/g, '-')}`);
</script>

<template>
    <div>
        <label :for="inputId" class="mb-1 block text-sm font-medium text-slate-700">
            {{ label }}<span v-if="required" class="text-danger-600"> *</span>
        </label>
        <input
            :id="inputId"
            type="date"
            :value="modelValue"
            :required="required"
            :aria-invalid="!!error"
            :aria-describedby="error ? `${inputId}-error` : undefined"
            class="w-full rounded-lg border px-3 py-2 text-sm shadow-sm focus:outline-none focus:ring-2"
            :class="error ? 'border-danger-400 focus:ring-danger-200' : 'border-border focus:ring-brand-200'"
            @input="$emit('update:modelValue', $event.target.value)"
        >
        <p v-if="error" :id="`${inputId}-error`" class="mt-1 text-sm text-danger-600">{{ error }}</p>
    </div>
</template>
