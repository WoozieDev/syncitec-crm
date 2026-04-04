<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProjectForm from '@/modules/projects/components/ProjectForm.vue';
import ProjectRecommendationsCard from '@/modules/projects/components/ProjectRecommendationsCard.vue';
import type { ProjectCreateProps, ProjectFormData } from '@/modules/projects/types';
import { create, index, store } from '@/routes/projects';

const props = defineProps<ProjectCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Proyectos',
                href: index(),
            },
            {
                title: 'Nuevo proyecto',
                href: create(),
            },
        ],
    },
});

const form = useForm<ProjectFormData>({
    client_id: '',
    name: '',
    description: '',
    status: props.status_options[0]?.value ?? 'draft',
    price: '',
    start_date: '',
    due_date: '',
    notes: '',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo proyecto" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nuevo proyecto"
                description="Registra el proyecto con su cliente, alcance y contexto operativo desde el inicio."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_300px]">
                <ProjectForm
                    :form="form"
                    :clients="clients"
                    :status-options="status_options"
                    :cancel-href="index.url()"
                    submit-label="Crear proyecto"
                    processing-label="Guardando..."
                    description="Completa los datos esenciales para dejar listo el alta del proyecto."
                    @submit="submit"
                />

                <ProjectRecommendationsCard />
            </div>
        </div>
    </div>
</template>
