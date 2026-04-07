<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ServiceTypeForm from '@/modules/serviceTypes/components/ServiceTypeForm.vue';
import type { ServiceTypeFormData } from '@/modules/serviceTypes/types';
import { create, index, store } from '@/routes/serviceTypes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tipos de servicio',
                href: index(),
            },
            {
                title: 'Nuevo tipo',
                href: create(),
            },
        ],
    },
});

const form = useForm<ServiceTypeFormData>({
    name: '',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo tipo de servicio" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nuevo tipo de servicio"
                description="Crea una categoria simple para organizar los servicios del CRM."
            />

            <ServiceTypeForm
                :form="form"
                :cancel-href="index.url()"
                submit-label="Guardar tipo"
                processing-label="Guardando..."
                description="Usa nombres claros y cortos para que el equipo los identifique rapido."
                @submit="submit"
            />
        </div>
    </div>
</template>
