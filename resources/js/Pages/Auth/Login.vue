<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { GraduationCap, LoaderCircle } from 'lucide-vue-next';
import FormField from '@/Components/FormField.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <div class="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand-900 to-brand-700 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-surface p-8 shadow-xl">
            <div class="mb-6 flex flex-col items-center text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-brand-100">
                    <GraduationCap class="h-6 w-6 text-brand-700" />
                </div>
                <h1 class="text-xl font-semibold text-slate-900">Sistema Escolar</h1>
                <p class="mt-1 text-sm text-slate-500">Ingresa a tu cuenta para continuar</p>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <FormField
                    v-model="form.email"
                    type="email"
                    label="Correo electrónico"
                    required
                    autofocus
                    :error="form.errors.email"
                />
                <FormField
                    v-model="form.password"
                    type="password"
                    label="Contraseña"
                    required
                    :error="form.errors.password"
                />
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input v-model="form.remember" type="checkbox" class="rounded border-border text-brand-600 focus:ring-brand-300">
                    Recordarme
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-600 py-2.5 text-sm font-medium text-white transition-colors hover:bg-brand-700 disabled:opacity-60"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Ingresar
                </button>
            </form>
        </div>
    </div>
</template>
