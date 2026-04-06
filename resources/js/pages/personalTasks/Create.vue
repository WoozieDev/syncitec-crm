<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import PersonalTaskForm from '@/modules/personalTasks/components/PersonalTaskForm.vue';
import PersonalTaskRecommendationsCard from '@/modules/personalTasks/components/PersonalTaskRecommendationsCard.vue';
import type {
    PersonalTaskCreateProps,
    PersonalTaskFormData,
} from '@/modules/personalTasks/types';
import { create, index, store } from '@/routes/personal-tasks';

defineProps<PersonalTaskCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tareas personales',
                href: index(),
            },
            {
                title: 'Nueva tarea',
                href: create(),
            },
        ],
    },
});

const form = useForm<PersonalTaskFormData>({
    title: '',
    description: '',
    status: 'pendiente',
    priority: '',
    due_date: '',
    is_completed: false,
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nueva tarea personal" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nueva tarea personal"
                description="Crea una tarea personal con fecha objetivo, prioridad visible y descripcion enriquecida."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
                <PersonalTaskForm
                    :form="form"
                    :priority-options="priority_options"
                    :cancel-href="index.url()"
                    submit-label="Guardar tarea"
                    processing-label="Guardando..."
                    description="Captura solo lo que mueve la tarea: fecha, prioridad, estado y contexto."
                    @submit="submit"
                />

                <PersonalTaskRecommendationsCard />
            </div>
        </div>
    </div>
</template>
