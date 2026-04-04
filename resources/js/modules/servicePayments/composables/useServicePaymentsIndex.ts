import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedServicePayments,
    ServicePayment,
    ServicePaymentIndexProps,
    ServicePaymentOverview,
    ServicePaymentProviderOption,
    ServicePaymentServiceOption,
} from '@/modules/servicePayments/types';
import { destroy, index } from '@/routes/service-payments';

export type UseServicePaymentsIndexReturn = {
    payments: ComputedRef<PaginatedServicePayments>;
    overview: ComputedRef<ServicePaymentOverview>;
    services: ComputedRef<ServicePaymentServiceOption[]>;
    providers: ComputedRef<ServicePaymentProviderOption[]>;
    paymentMethods: ComputedRef<string[]>;
    search: Ref<string>;
    serviceId: Ref<string>;
    paymentMethod: Ref<string>;
    providerId: Ref<string>;
    totalPayments: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (payment: ServicePayment) => void;
};

export const useServicePaymentsIndex = (
    props: ServicePaymentIndexProps,
): UseServicePaymentsIndexReturn => {
    const payments = computed(() => props.payments);
    const overview = computed(() => props.overview);
    const services = computed(() => props.services);
    const providers = computed(() => props.providers);
    const paymentMethods = computed(() => props.payment_methods);

    const search = ref(props.filters.search ?? '');
    const serviceId = ref(props.filters.service_id ?? '');
    const paymentMethod = ref(props.filters.payment_method ?? '');
    const providerId = ref(props.filters.provider_id ?? '');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                service_id: serviceId.value || undefined,
                payment_method: paymentMethod.value || undefined,
                provider_id: providerId.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch([serviceId, paymentMethod, providerId], () => {
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

    const handleDelete = (payment: ServicePayment) => {
        if (
            !confirm(
                `Seguro que deseas eliminar el pago de servicio #${payment.id}?`,
            )
        ) {
            return;
        }

        router.delete(destroy(payment.id), {
            preserveScroll: true,
        });
    };

    return {
        payments,
        overview,
        services,
        providers,
        paymentMethods,
        search,
        serviceId,
        paymentMethod,
        providerId,
        totalPayments,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
