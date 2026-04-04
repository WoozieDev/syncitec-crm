<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProjectForm from '@/modules/projects/components/ProjectForm.vue';
import ProjectRecommendationsCard from '@/modules/projects/components/ProjectRecommendationsCard.vue';
import type { ProjectEditProps, ProjectFormData } from '@/modules/projects/types';
import { index, update } from '@/routes/projects';

const props = defineProps<ProjectEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Proyectos',
                href: index(),
            },
            {
                title: 'Editar proyecto',
                href: '#',
            },
        ],
    },
});

const form = useForm<ProjectFormData>({
    client_id: props.project.client.id ? String(props.project.client.id) : '',
    name: props.project.name,
    description: props.project.description,
    status: props.project.status,
    price: String(props.project.total_amount),
    start_date: props.project.start_date ?? '',
    due_date: props.project.due_date ?? '',
    notes: props.project.notes ?? '',
});

const submit = () => {
    form.submit(update(props.project.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar proyecto - ${project.name}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar proyecto"
                description="Actualiza el estado, el alcance o las fechas sin perder consistencia en la ficha del proyecto."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
                <ProjectForm
                    :form="form"
                    :clients="clients"
                    :status-options="status_options"
                    :cancel-href="index.url()"
                    submit-label="Actualizar proyecto"
                    processing-label="Guardando..."
                    description="Ajusta los datos operativos para que el equipo trabaje con informacion vigente."
                    @submit="submit"
                />

                <ProjectRecommendationsCard />
            </div>
        </div>
    </div>
</template>
