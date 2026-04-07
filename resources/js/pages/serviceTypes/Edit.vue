<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ServiceTypeForm from '@/modules/serviceTypes/components/ServiceTypeForm.vue';
import type { ServiceTypeEditProps, ServiceTypeFormData } from '@/modules/serviceTypes/types';
import { index, update } from '@/routes/serviceTypes';

const props = defineProps<ServiceTypeEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tipos de servicio',
                href: index(),
            },
            {
                title: 'Editar tipo',
                href: '#',
            },
        ],
    },
});

const form = useForm<ServiceTypeFormData>({
    name: props.service_type.name,
});

const submit = () => {
    form.submit(update(props.service_type.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar tipo - ${service_type.name}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar tipo de servicio"
                description="Actualiza la nomenclatura para mantener un catalogo ordenado."
            />

            <ServiceTypeForm
                :form="form"
                :cancel-href="index.url()"
                submit-label="Actualizar tipo"
                processing-label="Guardando..."
                description="Este cambio impacta la forma en que se muestra el tipo en servicios."
                @submit="submit"
            />
        </div>
    </div>
</template>
