<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import ProjectPaymentForm from '@/modules/projectPayments/components/ProjectPaymentForm.vue';
import ProjectPaymentProjectContextCard from '@/modules/projectPayments/components/ProjectPaymentProjectContextCard.vue';
import ProjectPaymentRecommendationsCard from '@/modules/projectPayments/components/ProjectPaymentRecommendationsCard.vue';
import type {
    ProjectPaymentCreateProps,
    ProjectPaymentFormData,
} from '@/modules/projectPayments/types';
import { create, index, store } from '@/routes/project-payments';

const props = defineProps<ProjectPaymentCreateProps>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Pagos',
                href: index(),
            },
            {
                title: 'Nuevo pago',
                href: create(),
            },
        ],
    },
});

const form = useForm<ProjectPaymentFormData>({
    project_id: props.selected_project_id
        ? String(props.selected_project_id)
        : '',
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
    <Head title="Nuevo pago" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4 sm:p-6">
        <div class="flex flex-col space-y-6">
            <Heading
                variant="small"
                title="Nuevo pago"
                description="Registra un nuevo pago vinculado a un proyecto existente."
            />

            <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
                <ProjectPaymentForm
                    :form="form"
                    :projects="projects"
                    :cancel-href="index.url()"
                    submit-label="Guardar pago"
                    processing-label="Guardando..."
                    description="Completa los datos del pago con base en la transaccion real."
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
