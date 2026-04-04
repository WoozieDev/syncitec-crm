<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ServicePaymentForm from '@/modules/servicePayments/components/ServicePaymentForm.vue';
import ServicePaymentRecommendationsCard from '@/modules/servicePayments/components/ServicePaymentRecommendationsCard.vue';
import ServicePaymentServiceContextCard from '@/modules/servicePayments/components/ServicePaymentServiceContextCard.vue';
import type {
    ServicePaymentEditProps,
    ServicePaymentFormData,
} from '@/modules/servicePayments/types';
import { index, update } from '@/routes/service-payments';

const props = defineProps<ServicePaymentEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos de servicios',
                href: index(),
            },
            {
                title: 'Editar pago',
                href: '#',
            },
        ],
    },
});

const form = useForm<ServicePaymentFormData>({
    service_id: props.payment.service ? String(props.payment.service.id) : '',
    amount: String(props.payment.amount),
    payment_date: props.payment.payment_date ?? '',
    payment_method: props.payment.payment_method ?? '',
    notes: props.payment.notes ?? '',
});

const submit = () => {
    form.submit(update(props.payment.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Editar pago de servicio - #${payment.id}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar pago de servicio"
                description="Actualiza el registro del pago manteniendo consistencia con el servicio asociado."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
                <ServicePaymentForm
                    :form="form"
                    :services="services"
                    :cancel-href="index.url()"
                    submit-label="Actualizar pago"
                    processing-label="Guardando..."
                    description="Ajusta monto, fecha, metodo o notas del pago seleccionado."
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
