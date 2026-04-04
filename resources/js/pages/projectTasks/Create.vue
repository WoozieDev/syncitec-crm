<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProjectTaskForm from '@/modules/projectTasks/components/ProjectTaskForm.vue';
import ProjectTaskProjectContextCard from '@/modules/projectTasks/components/ProjectTaskProjectContextCard.vue';
import ProjectTaskRecommendationsCard from '@/modules/projectTasks/components/ProjectTaskRecommendationsCard.vue';
import type {
    ProjectTaskCreateProps,
    ProjectTaskFormData,
} from '@/modules/projectTasks/types';
import { create, index, store } from '@/routes/project-tasks';

const props = defineProps<ProjectTaskCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tareas por proyecto',
                href: index(),
            },
            {
                title: 'Nueva tarea',
                href: create(),
            },
        ],
    },
});

const form = useForm<ProjectTaskFormData>({
    project_id: props.selected_project_id ? String(props.selected_project_id) : '',
    module_id: '',
    title: '',
    description: '',
    status: props.status_options[0]?.value ?? 'pendiente',
    priority: '',
    order: '0',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nueva tarea por proyecto" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nueva tarea por proyecto"
                description="Registra una nueva tarea operativa vinculada a un proyecto y, si aplica, a uno de sus modulos."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
                <ProjectTaskForm
                    :form="form"
                    :projects="projects"
                    :status-options="status_options"
                    :priority-options="priority_options"
                    :cancel-href="index.url()"
                    submit-label="Guardar tarea"
                    processing-label="Guardando..."
                    description="Completa solo los campos reales de la tarea segun la estructura actual del CRM."
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
