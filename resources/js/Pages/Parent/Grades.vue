<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    student: Object,
    grades: Object,
});
</script>

<template>
    <Head :title="`Notas de ${student.first_name} ${student.last_name}`" />

    <AppLayout :title="`Notas — ${student.first_name} ${student.last_name}`">
        <div v-for="(items, courseName) in grades" :key="courseName" class="mb-3 rounded-xl border border-border bg-surface p-4 shadow-sm">
            <h2 class="mb-2 font-semibold text-slate-900">{{ courseName }}</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-border text-left text-slate-500">
                        <th class="py-1">Periodo</th>
                        <th class="py-1">Evaluación</th>
                        <th class="py-1">Nota</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="g in items" :key="g.id" class="border-b border-border">
                        <td class="py-1">{{ g.grade_period?.name }}</td>
                        <td class="py-1">{{ g.evaluation }}</td>
                        <td class="py-1">{{ g.score }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
