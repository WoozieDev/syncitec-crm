import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedServices,
    Service,
    ServiceClientOption,
    ServiceIndexProps,
    ServiceOverview,
    ServiceSelectOption,
    ServiceTypeOption,
} from '@/modules/services/types';
import { destroy, index } from '@/routes/services';

export type UseServicesIndexReturn = {
    services: ComputedRef<PaginatedServices>;
    overview: ComputedRef<ServiceOverview>;
    clients: ComputedRef<ServiceClientOption[]>;
    serviceTypes: ComputedRef<ServiceTypeOption[]>;
    statusOptions: ComputedRef<ServiceSelectOption[]>;
    search: Ref<string>;
    clientId: Ref<string>;
    status: Ref<string>;
    serviceTypeId: Ref<string>;
    totalServices: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (service: Service) => void;
};

export const useServicesIndex = (
    props: ServiceIndexProps,
): UseServicesIndexReturn => {
    const services = computed(() => props.services);
    const overview = computed(() => props.overview);
    const clients = computed(() => props.clients);
    const serviceTypes = computed(() => props.service_types);
    const statusOptions = computed(() => props.status_options);

    const search = ref(props.filters.search ?? '');
    const clientId = ref(props.filters.client_id ?? '');
    const status = ref(props.filters.status ?? '');
    const serviceTypeId = ref(props.filters.service_type_id ?? '');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                client_id: clientId.value || undefined,
                status: status.value || undefined,
                service_type_id: serviceTypeId.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch([clientId, status, serviceTypeId], () => {
        applyFilters();
    });

    watchDebounced(
        search,
        () => {
            applyFilters();
        },
        { debounce: 300, maxWait: 900 },
    );

    const hasServices = computed(() => services.value.data.length > 0);
    const totalServices = computed(
        () => services.value.total ?? services.value.data.length,
    );
    const pageFrom = computed(
        () => services.value.from ?? (hasServices.value ? 1 : 0),
    );
    const pageTo = computed(() => services.value.to ?? services.value.data.length);

    const handleDelete = (service: Service) => {
        if (!confirm(`Seguro que deseas eliminar "${service.name}"?`)) {
            return;
        }

        router.delete(destroy(service.id), {
            preserveScroll: true,
        });
    };

    return {
        services,
        overview,
        clients,
        serviceTypes,
        statusOptions,
        search,
        clientId,
        status,
        serviceTypeId,
        totalServices,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
