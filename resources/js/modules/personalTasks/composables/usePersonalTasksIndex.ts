import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PaginatedPersonalTasks,
    PersonalTask,
    PersonalTaskIndexProps,
    PersonalTaskOverview,
    PersonalTaskPriorityOption,
    PersonalTaskStatusOption,
} from '@/modules/personalTasks/types';
import { destroy, index } from '@/routes/personal-tasks';

export type UsePersonalTasksIndexReturn = {
    tasks: ComputedRef<PaginatedPersonalTasks>;
    overview: ComputedRef<PersonalTaskOverview>;
    statusOptions: ComputedRef<PersonalTaskStatusOption[]>;
    priorityOptions: ComputedRef<PersonalTaskPriorityOption[]>;
    search: Ref<string>;
    status: Ref<string>;
    priority: Ref<string>;
    totalTasks: ComputedRef<number>;
    pageFrom: ComputedRef<number>;
    pageTo: ComputedRef<number>;
    handleDelete: (task: PersonalTask) => void;
};

export const usePersonalTasksIndex = (
    props: PersonalTaskIndexProps,
): UsePersonalTasksIndexReturn => {
    const tasks = computed(() => props.tasks);
    const overview = computed(() => props.overview);
    const statusOptions = computed(() => props.status_options);
    const priorityOptions = computed(() => props.priority_options);
    const search = ref(props.filters.search ?? '');
    const status = ref(props.filters.status ?? '');
    const priority = ref(props.filters.priority ?? '');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                status: status.value || undefined,
                priority: priority.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch([status, priority], () => {
        applyFilters();
    });

    watchDebounced(
        search,
        () => {
            applyFilters();
        },
        { debounce: 300, maxWait: 900 },
    );

    const hasTasks = computed(() => tasks.value.data.length > 0);
    const totalTasks = computed(() => tasks.value.total ?? tasks.value.data.length);
    const pageFrom = computed(() => tasks.value.from ?? (hasTasks.value ? 1 : 0));
    const pageTo = computed(() => tasks.value.to ?? tasks.value.data.length);

    const handleDelete = (task: PersonalTask) => {
        if (!confirm(`Seguro que deseas eliminar la tarea "${task.title}"?`)) {
            return;
        }

        router.delete(destroy(task.id), {
            preserveScroll: true,
        });
    };

    return {
        tasks,
        overview,
        statusOptions,
        priorityOptions,
        search,
        status,
        priority,
        totalTasks,
        pageFrom,
        pageTo,
        handleDelete,
    };
};
