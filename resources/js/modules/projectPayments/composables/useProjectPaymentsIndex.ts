import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedProjectPayments,
    ProjectPayment,
    ProjectPaymentIndexProps,
    ProjectPaymentOverview,
    ProjectPaymentProjectOption,
} from '@/modules/projectPayments/types';
import { destroy, index } from '@/routes/project-payments';

export type UseProjectPaymentsIndexReturn = {
    payments: ComputedRef<PaginatedProjectPayments>;
    overview: ComputedRef<ProjectPaymentOverview>;
    projects: ComputedRef<ProjectPaymentProjectOption[]>;
    paymentMethods: ComputedRef<string[]>;
    search: Ref<string>;
    projectId: Ref<string>;
    paymentMethod: Ref<string>;
    totalPayments: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (payment: ProjectPayment) => void;
};

export const useProjectPaymentsIndex = (
    props: ProjectPaymentIndexProps,
): UseProjectPaymentsIndexReturn => {
    const payments = computed(() => props.payments);
    const overview = computed(() => props.overview);
    const projects = computed(() => props.projects);
    const paymentMethods = computed(() => props.payment_methods);

    const search = ref(props.filters.search ?? '');
    const projectId = ref(props.filters.project_id ?? '');
    const paymentMethod = ref(props.filters.payment_method ?? '');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                project_id: projectId.value || undefined,
                payment_method: paymentMethod.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch([projectId, paymentMethod], () => {
        applyFilters();
    });

    watchDebounced(
        search,
        () => {
            applyFilters();
        },
        { debounce: 300, maxWait: 900 },
    );

    const hasPayments = computed(() => payments.value.data.length > 0);
    const totalPayments = computed(
        () => payments.value.total ?? payments.value.data.length,
    );
    const pageFrom = computed(
        () => payments.value.from ?? (hasPayments.value ? 1 : 0),
    );
    const pageTo = computed(() => payments.value.to ?? payments.value.data.length);

    const handleDelete = (payment: ProjectPayment) => {
        if (!confirm(`Seguro que deseas eliminar el pago #${payment.id}?`)) {
            return;
        }

        router.delete(destroy(payment.id), {
            preserveScroll: true,
        });
    };

    return {
        payments,
        overview,
        projects,
        paymentMethods,
        search,
        projectId,
        paymentMethod,
        totalPayments,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
