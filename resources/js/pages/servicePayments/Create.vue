<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ServicePaymentForm from '@/modules/servicePayments/components/ServicePaymentForm.vue';
import ServicePaymentRecommendationsCard from '@/modules/servicePayments/components/ServicePaymentRecommendationsCard.vue';
import ServicePaymentServiceContextCard from '@/modules/servicePayments/components/ServicePaymentServiceContextCard.vue';
import type {
    ServicePaymentCreateProps,
    ServicePaymentFormData,
} from '@/modules/servicePayments/types';
import { create, index, store } from '@/routes/service-payments';

const props = defineProps<ServicePaymentCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos de servicios',
                href: index(),
            },
            {
                title: 'Nuevo pago',
                href: create(),
            },
        ],
    },
});

const form = useForm<ServicePaymentFormData>({
    service_id: props.selected_service_id ? String(props.selected_service_id) : '',
    amount: '',
    payment_date: '',
    payment_method: '',
    notes: '',
});

const submit = () => {
    form.submit(store(), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Nuevo pago de servicio" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nuevo pago de servicio"
                description="Registra un pago real asociado a un servicio existente."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
                <ServicePaymentForm
                    :form="form"
                    :services="services"
                    :cancel-href="index.url()"
                    submit-label="Guardar pago"
                    processing-label="Guardando..."
                    description="Completa el pago usando solo la informacion operativa existente del CRM."
                    @submit="submit"
                />

                <div class="space-y-6">
                    <ServicePaymentServiceContextCard
                        :services="services"
                        :selected-service-id="form.service_id"
                    />
                    <ServicePaymentRecommendationsCard />
                </div>
            </div>
        </div>
    </div>
</template>
