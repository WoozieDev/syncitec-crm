import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    Client,
    ClientIndexProps,
    PaginatedClients,
} from '@/modules/clients/types';
import { destroy, index } from '@/routes/clients';

export type UseClientsIndexReturn = {
    clients: ComputedRef<PaginatedClients>;
    search: Ref<string>;
    totalClients: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (client: Client) => void;
};

export const useClientsIndex = (
    props: ClientIndexProps,
): UseClientsIndexReturn => {
    const clients = computed(() => props.clients);
    const search = ref(props.filters.search ?? '');

    watchDebounced(
        search,
        (value) => {
            router.get(
                index(),
                { search: value },
                {
                    preserveState: true,
                    preserveScroll: true,
                    replace: true,
                },
            );
        },
        { debounce: 300, maxWait: 900 },
    );

    const hasClients = computed(() => clients.value.data.length > 0);
    const totalClients = computed(
        () => clients.value.total ?? clients.value.data.length,
    );
    const pageFrom = computed(
        () => clients.value.from ?? (hasClients.value ? 1 : 0),
    );
    const pageTo = computed(() => clients.value.to ?? clients.value.data.length);

    const handleDelete = (client: Client) => {
        if (!confirm(`Seguro que deseas eliminar a "${client.name}"?`)) {
            return;
        }

        router.delete(destroy(client.id), {
            preserveScroll: true,
        });
    };

    return {
        // Props
        clients,
        search,

        // Computed
        totalClients,
        pageFrom,
        pageTo,

        // Methods
        handleDelete,
    };
};
