import { router, usePage } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedUsers,
    UserIndexProps,
    UserRecord,
} from '@/modules/users/types';
import { destroy, index } from '@/routes/users';
import type { User as AuthUser } from '@/types/auth';

export type UseUsersIndexReturn = {
    users: ComputedRef<PaginatedUsers>;
    search: Ref<string>;
    totalUsers: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (user: UserRecord) => void;
};

export const useUsersIndex = (
    props: UserIndexProps,
): UseUsersIndexReturn => {
    const page = usePage();
    const users = computed(() => props.users);
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

    const hasUsers = computed(() => users.value.data.length > 0);
    const totalUsers = computed(() => users.value.total ?? users.value.data.length);
    const pageFrom = computed(() => users.value.from ?? (hasUsers.value ? 1 : 0));
    const pageTo = computed(() => users.value.to ?? users.value.data.length);
    const currentUser = computed(() => page.props.auth.user as AuthUser);

    const handleDelete = (user: UserRecord) => {
        if (Number(currentUser.value.id) === user.id) {
            window.alert('No puedes eliminar tu propio usuario desde este modulo.');

            return;
        }

        if (totalUsers.value <= 1) {
            window.alert('Debe permanecer al menos un usuario registrado en el sistema.');

            return;
        }

        if (!confirm(`Seguro que deseas eliminar al usuario "${user.name}"?`)) {
            return;
        }

        router.delete(destroy(user.id), {
            preserveScroll: true,
        });
    };

    return {
        users,
        search,
        totalUsers,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
