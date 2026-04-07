import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';
import { computed, ref, watch } from 'vue';
import type { ComputedRef, Ref } from 'vue';
import type {
    PersonalTask,
    PersonalTaskGroup,
    PersonalTaskIndexProps,
    PersonalTaskOverview,
    PersonalTaskPriorityOption,
} from '@/modules/personalTasks/types';
import {
    completion as completionRoute,
    destroy,
    index,
} from '@/routes/personal-tasks';

export type UsePersonalTasksIndexReturn = {
    groupedTasks: ComputedRef<PersonalTaskGroup[]>;
    overview: ComputedRef<PersonalTaskOverview>;
    priorityOptions: ComputedRef<PersonalTaskPriorityOption[]>;
    search: Ref<string>;
    priority: Ref<string>;
    completion: Ref<string>;
    handleDelete: (task: PersonalTask) => void;
    toggleCompletion: (task: PersonalTask, value: boolean) => void;
};

export const usePersonalTasksIndex = (
    props: PersonalTaskIndexProps,
): UsePersonalTasksIndexReturn => {
    const groupedTasks = computed(() => props.grouped_tasks);
    const overview = computed(() => props.overview);
    const priorityOptions = computed(() => props.priority_options);
    const search = ref(props.filters.search ?? '');
    const priority = ref(props.filters.priority ?? '');
    const completion = ref(props.filters.completion ?? '');

    const applyFilters = () => {
        router.get(
            index(),
            {
                search: search.value || undefined,
                priority: priority.value || undefined,
                completion: completion.value || undefined,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    watch([priority, completion], () => {
        applyFilters();
    });

    watchDebounced(
        search,
        () => {
            applyFilters();
        },
        { debounce: 300, maxWait: 900 },
    );

    const handleDelete = (task: PersonalTask) => {
        if (!confirm(`Seguro que deseas eliminar la tarea "${task.title}"?`)) {
            return;
        }

        router.delete(destroy(task.id), {
            preserveScroll: true,
        });
    };

    const toggleCompletion = (task: PersonalTask, value: boolean) => {
        router.patch(
            completionRoute(task.id),
            {
                is_completed: value,
            },
            {
                preserveScroll: true,
            },
        );
    };

    return {
        groupedTasks,
        overview,
        priorityOptions,
        search,
        priority,
        completion,
        handleDelete,
        toggleCompletion,
    };
};
