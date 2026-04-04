<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProjectPaymentForm from '@/modules/projectPayments/components/ProjectPaymentForm.vue';
import ProjectPaymentProjectContextCard from '@/modules/projectPayments/components/ProjectPaymentProjectContextCard.vue';
import ProjectPaymentRecommendationsCard from '@/modules/projectPayments/components/ProjectPaymentRecommendationsCard.vue';
import type {
    ProjectPaymentEditProps,
    ProjectPaymentFormData,
} from '@/modules/projectPayments/types';
import { index, update } from '@/routes/project-payments';

const props = defineProps<ProjectPaymentEditProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos',
                href: index(),
            },
            {
                title: 'Editar pago',
                href: '#',
            },
        ],
    },
});

const form = useForm<ProjectPaymentFormData>({
    project_id: props.payment.project ? String(props.payment.project.id) : '',
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
    <Head :title="`Editar pago - #${payment.id}`" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Editar pago"
                description="Actualiza los datos del pago y conserva el historial consistente."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
                <ProjectPaymentForm
                    :form="form"
                    :projects="projects"
                    :cancel-href="index.url()"
                    submit-label="Actualizar pago"
                    processing-label="Guardando..."
                    description="Ajusta monto, fecha, metodo o notas del pago seleccionado."
                    @submit="submit"
                />

                <div class="space-y-6">
                    <ProjectPaymentProjectContextCard
                        :projects="projects"
                        :selected-project-id="form.project_id"
                    />
                    <ProjectPaymentRecommendationsCard />
                </div>
            </div>
        </div>
    </div>
</template>
