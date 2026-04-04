<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import PersonalTaskForm from '@/modules/personalTasks/components/PersonalTaskForm.vue';
import PersonalTaskRecommendationsCard from '@/modules/personalTasks/components/PersonalTaskRecommendationsCard.vue';
import type {
    PersonalTaskEditProps,
    PersonalTaskFormData,
} from '@/modules/personalTasks/types';
import { index, update } from '@/routes/personal-tasks';

const props = defineProps<PersonalTaskEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tareas personales',
                href: index(),
            },
            {
                title: 'Editar tarea',
                href: '#',
            },
        ],
    },
});

const form = useForm<PersonalTaskFormData>({
    title: props.task.title,
    description: props.task.description ?? '',
    status: props.task.status,
    priority: props.task.priority ?? '',
    order: String(props.task.order),
});

const submit = () => {
    form.submit(update(props.task.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar tarea - ${task.title}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar tarea"
                description="Actualiza estado, prioridad, orden o descripcion sin cambiar la estructura real de datos."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
                <PersonalTaskForm
                    :form="form"
                    :status-options="status_options"
                    :priority-options="priority_options"
                    :cancel-href="index.url()"
                    submit-label="Actualizar tarea"
                    processing-label="Guardando..."
                    description="Ajusta los datos de la tarea personal seleccionada."
                    @submit="submit"
                />

                <PersonalTaskRecommendationsCard />
            </div>
        </div>
    </div>
</template>
