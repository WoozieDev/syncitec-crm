import { router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import type { ServiceType, ServiceTypeIndexProps } from '@/modules/serviceTypes/types';
import { destroy, index } from '@/routes/serviceTypes';

export const useServiceTypesIndex = (props: ServiceTypeIndexProps) => {
    const serviceTypes = computed(() => props.service_types);
    const search = ref(props.filters.search ?? '');

    const hasItems = computed(() => serviceTypes.value.data.length > 0);
    const total = computed(() => serviceTypes.value.total ?? serviceTypes.value.data.length);
    const from = computed(() => serviceTypes.value.from ?? (hasItems.value ? 1 : 0));
    const to = computed(() => serviceTypes.value.to ?? serviceTypes.value.data.length);

    watch(
        search,
        (value) => {
            router.get(
                index(),
                {
                    search: value || undefined,
                },
                {
                    preserveState: true,
                    replace: true,
                    preserveScroll: true,
                },
            );
        },
        { debounce: 300 },
    );

    const handleDelete = (serviceType: ServiceType) => {
        if (!window.confirm(`¿Eliminar el tipo de servicio \"${serviceType.name}\"?`)) {
            return;
        }

        router.delete(destroy(serviceType.id), {
            preserveScroll: true,
        });
    };

    return {
        serviceTypes,
        search,
        total,
        from,
        to,
        handleDelete,
    };
};
