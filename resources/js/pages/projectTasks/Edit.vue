<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProjectTaskForm from '@/modules/projectTasks/components/ProjectTaskForm.vue';
import ProjectTaskProjectContextCard from '@/modules/projectTasks/components/ProjectTaskProjectContextCard.vue';
import ProjectTaskRecommendationsCard from '@/modules/projectTasks/components/ProjectTaskRecommendationsCard.vue';
import type {
    ProjectTaskEditProps,
    ProjectTaskFormData,
} from '@/modules/projectTasks/types';
import { index, update } from '@/routes/project-tasks';

const props = defineProps<ProjectTaskEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tareas por proyecto',
                href: index(),
            },
            {
                title: 'Editar tarea',
                href: '#',
            },
        ],
    },
});

const form = useForm<ProjectTaskFormData>({
    project_id: props.task.project ? String(props.task.project.id) : '',
    module_id: props.task.module ? String(props.task.module.id) : '',
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
                description="Actualiza el estado, prioridad, modulo o descripcion sin salir del flujo actual del proyecto."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
                <ProjectTaskForm
                    :form="form"
                    :projects="projects"
                    :status-options="status_options"
                    :priority-options="priority_options"
                    :cancel-href="index.url()"
                    submit-label="Actualizar tarea"
                    processing-label="Guardando..."
                    description="Ajusta la tarea respetando las relaciones reales entre proyecto y modulo."
                    @submit="submit"
                />

                <div class="space-y-6">
                    <ProjectTaskProjectContextCard
                        :projects="projects"
                        :selected-project-id="form.project_id"
                    />
                    <ProjectTaskRecommendationsCard />
                </div>
            </div>
        </div>
    </div>
</template>
