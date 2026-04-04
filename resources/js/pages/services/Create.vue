<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ServiceForm from '@/modules/services/components/ServiceForm.vue';
import type { ServiceCreateProps, ServiceFormData } from '@/modules/services/types';
import { create, index, store } from '@/routes/services';

const props = defineProps<ServiceCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Servicios',
                href: index(),
            },
            {
                title: 'Nuevo servicio',
                href: create(),
            },
        ],
    },
});

const form = useForm<ServiceFormData>({
    client_id: '',
    service_type_id: '',
    provider_id: '',
    name: '',
    description: '',
    billing_type: props.billing_type_options[0]?.value ?? 'unico',
    billing_cycle: '',
    price: '',
    cost: '',
    start_date: '',
    next_renewal_date: '',
    status: props.status_options[0]?.value ?? 'activo',
    notes: '',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo servicio" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nuevo servicio"
                description="Registra un nuevo servicio recurrente con su configuracion comercial y operativa."
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
                submit-label="Guardar servicio"
                processing-label="Guardando..."
                description="Completa los datos reales del servicio tal como estan definidos en el CRM."
                @submit="submit"
            />
        </div>
    </div>
</template>
