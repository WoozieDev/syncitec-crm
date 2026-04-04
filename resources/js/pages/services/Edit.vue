<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ServiceForm from '@/modules/services/components/ServiceForm.vue';
import type { ServiceEditProps, ServiceFormData } from '@/modules/services/types';
import { index, update } from '@/routes/services';

const props = defineProps<ServiceEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Servicios',
                href: index(),
            },
            {
                title: 'Editar servicio',
                href: '#',
            },
        ],
    },
});

const form = useForm<ServiceFormData>({
    client_id: props.service.client.id ? String(props.service.client.id) : '',
    service_type_id: props.service.service_type.id
        ? String(props.service.service_type.id)
        : '',
    provider_id: props.service.provider?.id
        ? String(props.service.provider.id)
        : '',
    name: props.service.name,
    description: props.service.description ?? '',
    billing_type: props.service.billing_type,
    billing_cycle: props.service.billing_cycle ?? '',
    price: String(props.service.price),
    cost:
        props.service.cost !== null && props.service.cost !== undefined
            ? String(props.service.cost)
            : '',
    start_date: props.service.start_date ?? '',
    next_renewal_date: props.service.next_renewal_date ?? '',
    status: props.service.status,
    notes: props.service.notes ?? '',
});

const submit = () => {
    form.submit(update(props.service.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar servicio - ${service.name}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar servicio"
                description="Actualiza fechas, costos o estado manteniendo consistencia con la ficha del servicio."
            />

            <ServiceForm
                :form="form"
                :clients="clients"
                :service-types="service_types"
                :providers="providers"
                :billing-type-options="billing_type_options"
                :billing-cycle-options="billing_cycle_options"
                :status-options="status_options"
                :cancel-href="index.url()"
                submit-label="Actualizar servicio"
                processing-label="Guardando..."
                description="Ajusta solo la informacion vigente del servicio."
                @submit="submit"
            />
        </div>
    </div>
</template>
